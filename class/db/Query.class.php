<?php

	namespace apf\db{

		abstract class Query{

			protected	$table						=	NULL;
			protected	$connectionId				=	NULL;
			protected	$params						=	NULL;
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

			protected	$result	=	NULL;

			abstract public function getResult();
			abstract public function getSQL();

			public final function __construct($table=NULL,$connectionId=NULL){

				if(is_string($table)){

					if(is_null($connectionId)&&\apf\db\Pool::getConnectionCount()>1){

							$msg	=	"Hey! It seems that you have more than one connection! ":
										"I have no idea where are you trying to execute this ".
										"query, please specify a connectionId so I know in which ". 
										"connection I shall run it.";
							throw new \Exception($msg);

					}

					if(!\apf\db\Pool::connectionExists($connectionId)){

						throw new \Exception("Unknown connection id $connectionId");

					}

					$this->connectionId	=	$connectionId;
					$table					=	new Table($table);
					$this->setTable($table);

				}elseif(!is_null($table)){

					$this->setTable($table);

				}

			}

			public function setTable(Table $table){

				$this->table	=	$table;

			}

			public function getTable(){

				return $this->table;

			}

			public function execute($smart=TRUE){

				$sql		=	sprintf("%s",$this);

			}

		}

	}

?>
