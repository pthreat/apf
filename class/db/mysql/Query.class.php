<?php

	namespace apf\db\mysql{

			abstract class Query extends \apd\db\Query{

				protected	$table						=	NULL;
				protected	$fieldDelimiter			=	',';
				protected	$fieldEqualityChar		=	'=';
				protected	$space						=	" ";
				protected	$commentOpen				=	"/*";
				protected	$commentClose				=	"*/";
				protected	$fields						=	Array();
				protected	$result						=	NULL;
				protected	$params						=	NULL;
				protected	$error						=	NULL;
				protected	$adapter		   			=	NULL;
				protected   $sqlArray               =	Array(
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

				private static $instanceCount	=	0;

				abstract public function getResult();
				abstract public function getSQL();

				public function __construct($connectionName=NULL,$table=NULL){

					self::$instanceCount++;
					$this->adapter	=	\apf\db\Pool::getConnection($connectionName);

					if(is_string($table)){

						$table	=	new Table($table);
						$table->setAlias("t".self::$instanceCount);
						$this->setTable($table);

					}elseif(!is_null($table)){

						$this->setTable($table);

					}

				}

				public function where(){
				}

				public function setSpaceCharacter($space){

					$this->space	=	$space;

				}

				public function getSpaceCharacter(){

					return $this->space;

				}	

				public function setCommentOpen($commentOpen){

					$this->commentOpen	=	$commentOpen;

				}

				public function setCommentClose($commentClose){

					$this->commentClose	=	$commentClose;

				}

				public function setFieldEqualityCharacter($equalityCharacter){

					$this->fieldEqualityCharacter	=	$equalityCharacter;

				}

				public function setTable(Table $table){

					$tableName	=	$table->getName();

					if(empty($tableName)){

						throw(new \Exception("Table name can't be empty"));

					}

					$this->table	=	$table;

				}

				public function getTable(){

					return $this->table;

				}

				public function reset(){

					$this->sql	=	array();	

				}
				
				public function execute($smart=TRUE){

					$sql		=	sprintf("%s",$this);

					if($this->error){

						throw(new \Exception($this->error));

					}

					$this->result	=	$this->adapter->query($sql);

					if(!$this->result){

						throw(new \Exception("QUERY FAILED: $sql (".$this->adapter->error.' | '.$this->adapter->errno.')'));

					}

					return $this->getResult($smart);

				}

				public function getQueryResult(){

					return $this->result;

				}

				public function fields(Array $fields,$quote=TRUE){

					foreach($fields as $key=>$value){

						$this->addField($key,$value);

					}

				}

				public function join($join,$type=NULL){

					$this->sqlArray["join"][]	=	$join;

				}

				public function __set($var,$value){

					return $this->addField($var,$value);
					
				}

				public function __toString(){

					try{

						$sql	=	$this->getSQL();
						$this->error	=	FALSE;
						return $sql;

					}catch(\Exception $e){

						$this->error	=	$e->getMessage();
						return '';

					}

				}

			}

		}
?>
