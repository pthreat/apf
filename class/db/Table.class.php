<?php

	namespace apf\db{

		abstract class Table{

			//connection id is necessary to perform queries
			protected	$connectionId		=	NULL;
			protected	$schema				=	NULL;
			protected	$name					=	NULL;
			protected	$alias				=	NULL;
			protected	$columns				=	Array();

			public function __construct($connectionId=NULL,$schema=NULL,$name=NULL){
			
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

				foreach($columns as $name=>$value){

					$this->addColumn($name,$value);

				}

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

				$this->setColumns($this->getColumnsFromDbSchema());
				return $this->columns;

			}

			public final function addColumn($name,$value){

				$requiredKeys	=	Array("name","type","extra","key","charset","maxlen","octlen","null","pdo");
				\apf\Validator::arrayKeys($requiredKeys,$value);
				$this->columns[$name]	=	$value;

			}

			public function setAlias($alias){

				$this->alias	=	$alias;

			}

			public final function getAlias(){

				return $this->alias;

			}

			public final function getColumns(){

				if(!empty($this->columns)){

					return $this->columns;

				}

				return $this->getColumnsFromDbSchema();

			}

			public final function getColumnsAsString($aliased=FALSE){

				if(is_null($this->columns)){

					$this->getColumns();

				}

				$fields	=	Array();

				foreach($this->columns as $column){

					if($aliased&&!is_null($this->alias)){

						$column["name"]	=	$this->alias.'.'.$column["name"];
						
					}

					$fields[]	=	$column["name"];

				}

				return implode(',',$fields);

			}

			abstract public function getColumnsFromDbSchema();
			abstract public function exists();
			abstract public function select();

			public function __toString(){

				return (string)$this->getName();

			}

		}

	}

	
?>
