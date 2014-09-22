<?php

	namespace apf\db{

		abstract class Query{

			private		$tables			=	Array();
			private		$columns			=	Array();
			private		$where			=	'';
			protected	$query			=	Array(
													 "where"		=>NULL,
													 "having"	=>Array(),
													 "group"		=>Array(),
													 "order"		=>Array(),
													 "limit"		=>Array(),
													 "offset"	=>NULL,
													 "union"		=>NULL,
													 "join"		=>Array()
			);

			abstract public function getSQL();

			public final function __construct($tables=NULL,$columns=NULL){

				$this->setColumns($columns);

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
						//Note that the return value of strpos doesn't really matters 
						//here if it's false or 0

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

				$this->addTable($tables);

			}

			protected function getWhere(){

				return $this->where;

			}

			public function where(Array $clause=Array()){

				if(empty($clause)){

					throw new \Exception("Where clause can't be empty");

				}

				$columns	=	array_keys($clause);

				foreach($columns as $col){

					$found	=	FALSE;

					foreach($this->tables as $table){

						$tmpCol	=	$table->getColumn($col);

						if($tmpCol){

							$found	=	TRUE;
							$value	=	$clause[$col];
							$col		=	$tmpCol;
							break;

						}

					}

					if(!$found){

						throw new \Exception("No such column $col");

					}

					$col->setValue($value);
					$this->where[]	=	$col;

				}

				var_dump($this->where);
				die();

			}

			public function setColumns($columns=NULL){

				$this->columns	=	$columns;

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


			}

			public function getFieldsFromAllTables(){

				$fields	=	Array();

				foreach($this->tables as $table){

					$fields[]	=	$table->getColumns();	

				}

				return $fields;

			}

			public function getColumn($column=NULL){

				$column	=	\apf\Validator::emptyString($column,"Must provide column name");

				foreach($this->table->getColumns() as $colName=>$colValue){

					if($column==$colName){

						return $colValue;

					}

				}

				return Array();

			}

			private function validateFieldForTable($field,$setValue){

				$found	=	FALSE;

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

			public function run($smart=TRUE){

				$sql		=	$this->getSQL();
				$conName	=	NULL;

				foreach($this->tables as $key=>$table){

					if(is_null($conName)){

						$conName	=	$table->getConnectionId();	
						continue;

					}

					//Different connections! Dump table into first table schema
					if($table->getConnectionId()!==$conName){

						//Make a copy of the table into the first connection schema
						$table->copyTo($this->tables[$key-1]);

					}

				}

				$db	=	\apf\db\Pool::getConnection($conName);
				$stmt	=	$db->prepare($sql);
			
				if(!$stmt->execute()){

					throw new \Exception("Error preparing query: $sql");

				}

				return $stmt;

			}

			public function __set($var,$value){

				$this->addField($var,$value);

			}

			public final function getColumns(){

				if(sizeof($this->columns)){

					return $this->columns;

				}

				foreach($this->tables as $table){

					$alias	=	$table->getAlias();
					$columns	=	Array();

					foreach($table->getColumns() as $col){

						$columns[]	=	$alias	?	$alias.'.'.$col["name"]	:	$col["name"];

					}

					return $columns;

				}

			}

		}

	}

?>
