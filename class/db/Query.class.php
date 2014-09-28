<?php

	namespace apf\db{

		abstract class Query{

			private		$tables			=	Array();
			private		$tableCount		=	0;
			private		$bind				=	Array();

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

			protected function bindParams(Array $bindParams){

				foreach($bindParams as $bind=>$value){

					$this->bind[$bind]	=	$value;

				}

			}

			public function addTable(\apf\db\Table $table){

				$this->tableCount++;
				$table->setAlias(sprintf("t%d",$this->tableCount));
				$this->tables[$this->tableCount]	=	$table;

			}

			public function getTables(){

				return $this->tables;

			}

			public function getTableWithIndex($index){

				$index	=	(int)$index;

				if(!isset($this->tables[$index])){
					return FALSE;
				}

				return $this->tables[$index];
			}

			protected function parseQuery(&$sql){

				$hasBracket	=	strpos($sql,'[');

				if($hasBracket===FALSE){
					return;
				}

				$count	=	0;

				while($openingBracket=strpos($sql,'[')){

					$count++;

					$tmp					=	substr($sql,$openingBracket+1);
					$closingBracket	=	strpos($tmp,']');

					if($closingBracket===FALSE){

						throw new \Exception("Missing closing ]");

					}

					$tmp		=	substr($tmp,0,$closingBracket);
					$dotPos	=	strpos($tmp,'.');
					$column	=	substr($tmp,$dotPos+1);

					$eqPos	=	strpos($column,'=');

					if(!($eqPos===FALSE)){

						$value	=	substr($column,$eqPos+1);
						$column	=	substr($column,0,$eqPos);

					}

					if($dotPos===FALSE){

						throw new \Exception("Syntax error, expecting \".\"");

					}

					$table	=	substr($tmp,0,$dotPos);
					$tableNo	=	substr($table,1);

					if(!isset($this->tables[$tableNo])){

						throw new \Exception("No such table: $tableNo");

					}

					$table	=	$this->tables[$tableNo];

					$colObj	=	$table->getColumn($column);

					if(!$colObj){

						throw new \Exception("No such column $column in table $table");

					}

					if(isset($value)){

						$colObj->setValue($value);

					}

					$bind	=	sprintf(":%s_%s_%d",$table,$column,$count);
					$sql	=	preg_replace("/\[$tmp\]/",$bind,$sql);
					$this->bind[$bind]	=	$colObj;

				}

			}

			public function run($smart=TRUE){

				$sql		=	$this->getSQL();
				$this->parseQuery($sql);
				$conName	=	NULL;

				foreach($this->getTables() as $key=>$table){

					if(is_null($conName)){

						$conName	=	$table->getConnectionId();	
						continue;

					}

					//Different connections! Dump table into first table schema
					if($table->getConnectionId()!==$conName){

						//Make a copy of the table into the first connection schema
						$table->copyTo($this->getTableWithIndex($key-1));

					}

				}

				$db	=	\apf\db\Pool::getConnection($conName);
				$stmt	=	$db->prepare($sql);

				foreach($this->bind as $bindName=>$column){

					$stmt->bindParam($bindName,$column->getValue(),$column->getPDOType());	

				}

				$stmt->setFetchMode(\PDO::FETCH_ASSOC);

				if(!$stmt->execute()){

					throw new \Exception("Error preparing query: $sql");

				}

				return $stmt;

			}


		}

	}

?>
