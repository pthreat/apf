<?php

	namespace apf\db{

		abstract class Query{

			private		$tables			=	Array();
			private		$tableCount		=	0;
			private		$columns			=	Array();
			private		$where			=	'';
			private		$bound			=	Array();
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

			private function bindParams(Array $bindParams){

				foreach($bindParams as $bind=>$value){

					$this->bound[$bind]	=	$value;

				}

			}

			public function where($clause=NULL,$bindParams=Array()){

				$this->where	=	\apf\Validator::emptyString($clause,"Where clause can't be empty");

				$this->bindParams($bindParams);

				return $this;

			}

			public function setColumns($columns=NULL){

				$this->columns	=	$columns;

			}

			public function addTable(\apf\db\Table $table){

				$this->tableCount++;
				$table->setAlias(sprintf("t%d",$this->tableCount));
				$this->tables[$this->tableCount]	=	$table;

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

				foreach($this->bound as $value){

					foreach($value as $bind=>$val){

						$hasTable	=	strpos($bind,':');

						if(!$hasTable){

							$stmt->bindParam($bind,$val);

						}

						$table	=	substr($bind,0,$hasTable);
						$tableNo	=	substr($bind,1,$hasTable-1);
						$column	=	substr($bind,$hasTable+1);
						$column	=	$this->tables[$tableNo]->getColumn($column);
						$column->setValue($val);

						$stmt->bindParam($bind,$val,constant($column->getPDOType()));

					}

				}

				var_dump($stmt);
				die();

				$stmt->setFetchMode(\PDO::FETCH_ASSOC);

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
