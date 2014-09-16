<?php

	namespace apf\db{

		abstract class Table{

			//connection id is necessary to perform queries
			private	$connectionId		=	NULL;
			private	$schema				=	NULL;
			private	$name					=	NULL;
			private	$alias				=	NULL;
			private	$columns				=	Array();

			public final function __construct($connectionId,$schema,$name){
			
				$this->setConnectionId($connectionId);
				$this->setSchema($schema);
				$this->setName($name);

			}

			public function setConnectionId($id){

				$this->connectionId	=	$id;

			}

			public function getConnectionId(){

				return $this->connectionId;

			}

			public final function setColumns(Array $columns=Array()){

				if(empty($columns)){

					throw new \Exception("Must provide an array with column values");

				}

				foreach($columns as $column){

					$this->addColumn($column);

				}

			}

			public final function getColumns(){

				return $this->columns;	

			}

			public function setSchema($schema){

				$this->schema	=	\apf\Validator::emptyString($schema,"Schema name can't be empty");

			}

			public function getSchema(){

				return $this->schema;

			}

			public final function setName($name=NULL){

				$this->name	=	\apf\Validator::emptyString($name,"Table name can't be empty");
				
			}

			public final function getName(){

				return $this->name;

			}

			public final function export(){

				$this->setColumns($this->dump());
				return $this->getColumns();

			}

			public final function addColumn($column){

				$requiredKeys	=	Array("name","type","extra","key","charset","maxlen","octlen","null","pdo");
				\apf\Validator::arrayKeys($requiredKeys,$column);
				$this->columns[]	=	$column;

			}

			public function setAlias($alias){

				$adapter			=	Adapter::getInstance($this->params);
				$alias			=	$adapter->real_escape_string($alias);
				$this->alias	=	$alias;

			}

			public final function getAlias(){

				return $this->alias;

			}

			public function save(){
			}

			public function truncate(){
			}

			public function drop(){
			}

			abstract public function dump();
			abstract public function exists();

			public function __toString(){

				return (string)$this->getName();

			}

		}

	}

	
?>
