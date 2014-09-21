<?php

	namespace apf\db{

		abstract class Query{

			protected	$tables	=	Array();
			protected	$query	=	Array(
													 "fields"=>Array(),
													 "where"=>NULL,
													 "having"=>Array(),
													 "group"=>Array(),
													 "order"=>Array(),
													 "limit"=>Array(),
													 "offset"=>NULL,
													 "union"=>NULL,
													 "join"=>Array()
			);

			protected	$tableColumns	=	Array();

			abstract public function getSQL();

			public final function __construct($tables=NULL){

				$amountOfConnections	=	\apf\db\Pool::getConnectionCount();

				//"simple" string parameter

				if(is_string($tables)){

					//tables delimited by commas
					if(strpos($tables,',')){

						$tables	=	explode(',',$tables);

					}else{

						$tables	=	Array($tables);

					}

					$c=0;

					foreach($tables as $table){

						$c++;

						if($amountOfConnections>1){

							//if more than one connection, the user must specify connectionid:table 
							//format in order to know in which connection is he trying to make this query

							//Note that the return value of strpos doesn't really matters here if it's false or 0

							if(!strpos($table,':')){

								$msg	=	"Since you have more than one database connection, ".
											"you need to specify connectionId:tableName";

								throw new \Exception($msg);

							}

							$connectionId	=	substr($table,0,strpos($table,':'));

							if(empty($connectionId)){

								throw new \Exception("Unspecified connection!");

							}

							if(!\apf\db\Pool::connectionExists($connectionId)){

								throw new \Exception("No such connection $connectionId");

							}

							$table		=	substr($table,strpos($table,':')+1);
							$tableClass	=	"\\apf\\dbc\\$connectionId\\$schema\\$table";
							$table		=	new $tableClass;
							$table->setAlias($c);
							$this->addTable($table);

							continue;

						}//else

						$connectionId	=	\apf\db\Pool::getConnection()->getConnectionData()->getId();

						if(strpos($table,':')){

							$table	=	substr($table,strpos($table,':')+1);

						}

						$tableClass		=	"\\apf\\dbc\\$connectionId\\$table";
						$table			=	new $tableClass;
						$table->setAlias($c);
						$this->addTable($table);

					}

					return;

				}

				//Array of tables

				if(is_array($tables)){

					$c=0;

					foreach($tables as $k=>$table){

						$c++;

						if(is_string($table)){

							$table	=	new Table($table);

						}

						if(!($table instanceof \apf\db\Table)){

							$msg	=	"Argument specified for table must be a string or an instance of \\apf\\db\\TABLE";
							throw new \Exception($msg);

						}

						$table->setAlias("t$c");
						$this->addTable($table);

					}

					return;

				}

				//Instance of table

				if(!($tables instanceof \apf\db\Table)){

					$msg	=	"Argument specified for table must be a string or an instance of \\apf\\db\\TABLE";
					throw new \Exception($msg);

				}

			}

			public function addTable(\apf\db\Table $table){

				$this->tables[]	=	$table;

			}

			public function addField($field,$setValue,$escape="PDO::PARAM_STR"){

				if(is_null($this->tables)){

					$this->query["fields"][]	=	Array(
																	"field"	=>	$var,
																	"value"	=>	$setValue,
																	"pdo"		=>	$escape
					);

					return;	

				}

				$this->validateFieldForTable($field,$setValue);
				$this->query["fields"][]	=	$setValue;

			}

			public function getFieldsFromAllTables(){

				$fields	=	Array();

				foreach($this->tables as $table){

					$fields[]	=	$table->getColumns();	

				}

				return $fields;

			}

			private function validateFieldForTable($field,$setValue){

				$found	=	FALSE;

				foreach($this->table->getColumns() as $colName=>$colValue){

					if($field==$colName){

						$found	=	TRUE;

					}

				}

				if(!$found){

					throw new \Exception("No such field $field for table ".$this->table);

				}

				switch($colValue["type"]){

					case "int":

						$value	=	(int)$setValue;

						if(array_key_exists("unsigned",$colValue)&&$colValue["unsigned"]===TRUE&&$value<0){

							$msg	=	"Column $colName is of type int unsigned, attempted to use ".
										"$value as column value";

							throw new \Exception($msg);
							
						}

					break;
					case "float":
					case "double":

						$value	=	(double)$setValue;

						if(array_key_exists("unsigned",$colValue)&&$colValue["unsigned"]===TRUE&&$value<0){

							$msg	=	"Column $colName is of type int unsigned, attempted to use ".
										"$value as column value";

							throw new \Exception($msg);
							
						}

					break;

					case "string":
					default:

						if(array_key_exists("maxlen",$colValue)&&strlen($setValue)>$colValue["maxlen"]){

							$msg	=	"Column $colName is of type int unsigned, attempted to use ".
										"$value as column value";

							throw new \Exception($msg);
							
						}

					break;

				}

			}

			public function getTables(){

				return $this->tables;

			}

			public function execute($smart=TRUE){

				$sql		=	sprintf("%s",$this);

			}

			public function __set($var,$value){

				$this->addField($var,$value);

			}

		}

	}

?>
