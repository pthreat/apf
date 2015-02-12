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

			public final function find($values,$columns=NULL){

				//If the user does not specifies a columns, assume we are looking up by primary key

				if(is_null($columns)){

					$columns	=	$this->getPrimaryKey();

					if(sizeof($columns)>1&&!is_array($columns)){

						$msg	=	"It seems that the primary key of this table is a composite ".
									"primary key composed of the following fields: ".implode(',',$columns)."\n".
									"so in order to fulfill this condition, you must ".
									"provide an array of values ordered as stated before";

						throw new \Exception($msg);

					}

				}

				//abstract method
				$this->_find($values,$columns);

			}

			public function getPrimaryKey(){

				//In case the primary key is a composite primary key ...
				$pk	=	Array();

				foreach($this->columns as $column){

					if($column["key"]=="PRI"){
						$pk[]	=	$column["name"];
					}

				}

				return $pk;

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

				$this->schema	=	\apf\validate\String::mustBeNotEmpty($schema,"Schema name can't be empty");

			}

			public function getSchema(){

				return $this->schema;

			}

			public final function setName($name=NULL){

				$this->name	=	\apf\validate\String::mustBeNotEmpty($name,"Table name can't be empty");
				
			}

			public final function getName(){

				return $this->name;

			}

			public final function export(){

				$this->setColumns($this->getColumnsFromDbSchema());
				return $this->columns;

			}

			public final function addColumn($name,$value){

				$requiredKeys	= ["name","type","extra","key","charset","maxlen","octlen","null","pdo_type","php_type"];
				\apf\validate\Vector::mustHaveKeys($requiredKeys,$value);
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

			public function getColumn($colName=NULL){

				foreach($this->getColumns() as $column){

					if($column["name"] == $colName){

						$column["table"]	=	&$this;
						return new Column($column);

					}

				}

				return FALSE;

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
			abstract protected function _find($values,$columns=NULL);

			public function __toString(){

				return sprintf("%s",$this->getName());

			}

		}

	}

	
?>
