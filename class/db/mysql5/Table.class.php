<?php

	namespace apf\db\mysql5{

		class Table{

			protected	$schema	=	NULL;
			protected	$name		=	NULL;
			protected	$fields	=	Array();
			protected	$params	=	NULL;
			protected	$alias	=	NULL;

			public function __construct($name=NULL,$params=NULL){

				$this->params	=	$params;

				if(!is_null($name)){

					$this->setName($name);

				}

			}

			public function setFields(Array $fields){

				$this->fields	=	$fields;

			}

			public function getFields(){

				if(sizeof($this->fields)){

					$tmpFields	=	$this->fields;

					foreach($tmpFields as &$field){

						$field	=	$this->getName().'.`'.$field["name"]."`";

					}

					return $this->fields;

				}

				return $this->fields	=	$this->getColumnsFromInformationSchema();

			}

			public function getColumnsFromInformationSchema(){

				$table	=	new Table("information_schema.columns",$this->params);
				$select	=	new Select($table,$this->params);
				$select->fields(Array("COLUMN_NAME","COLUMN_TYPE"));

				$where	=	Array(
										Array(
												"field"=>"TABLE_SCHEMA",
												"value"=>$this->schema
										),
										Array(
												"operator"=>"AND"
										),
										Array(
												"field"=>"TABLE_NAME",
												"value"=>$this->name
										)
				);

				$select->where($where);
				$res	=	$select->execute();

				if(is_null($res)){

					$msg	=	"Can't fetch columns for table ".$this->name." on schema `".
					$this->schema.'`, wrong connection, database or a temporary table?.'.
					'you might want to try specifying fields in your query so they\'re not '.
					'gathered dynamically through information_schema';

					throw(new \Exception($msg));

				}

				$columns	=	Array();

				foreach($res as $row){

					$columns[]	=	Array("name"=>$row["COLUMN_NAME"],"type"=>$row["COLUMN_TYPE"]);

				}

				return $columns;

			}

			public function getFieldsAsArray($prefixTableName=FALSE){

				if(!sizeof($this->fields)){

					throw(new \Exception("Can't getFieldsAsArray, no fields have been set"));

				}

				$tmpFields	=	Array();

				foreach($this->fields as $field){

					$field			=	($prefixTableName) ? $this->name.'.'.$field["name"] : $field["name"];
					$tmpFields[]	=	$field;

				}

				return $tmpFields;

			}

			public function getFieldsAsString($separator=',',$space=' '){

				if(!sizeof($this->fields)){

					throw(new \Exception("No fields have been set in ".get_class($this)));

				}

				foreach($this->fields as $field){

					$tmpField	=	$this->name.'.'.$field["name"];

					if(array_key_exists("alias",$field)){

						$tmpField.=$space.'AS'.$space.$field["alias"];

					}

					$string[]	=	$tmpField;


				}

				return implode($separator,$string);

			}

			public function setName($name=NULL){

				\apf\Validator::emptyString($name,"Table name can't be empty");

				$pos	=	strpos($name,'.');

				if($pos===FALSE){

					$adapter			=	Adapter::getInstance($this->params);
					$this->schema	=	$adapter->getDatabaseName();
					$this->name		=	$name;

					return;

				}

				$this->schema	=	substr($name,0,$pos);
				$this->name		=	substr($name,$pos+1);

			}

			public function setAlias($alias){

				$adapter			=	Adapter::getInstance($this->params);
				$alias			=	$adapter->real_escape_string($alias);
				$this->alias	=	$alias;

			}

			public function save(){

				$columns	=	$this->getColumnsFromInformationSchema();
				$code		=	"<?php namespace apf\\db\\mysql5{ class ".$this->name." extends Table{".
				"protected \$data=\"".serialize($columns)."\";}}".
				"?>";

				return $code;

			}

			public function getName(){

				if(is_null($this->alias)){

					return $this->schema.'.'.$this->name;

				}

				return	$this->schema.'.'.$this->name." AS ".$this->alias ;

			}

			public function getAlias(){

				return $this->alias;

			}


			public function getSchema(){
				return $this->schema;
			}

			public function truncate(){

				$adapter			=	Adapter::getInstance($this->params);
				$adapter->directQuery("TRUNCATE $this");

			}

			public function drop(){

				$adapter	=	Adapter::getInstance($this->params);
				$adapter->directQuery("DROP TABLE $this");

			}

			public function __set($var,$value){

					$this->fields[$var]	=	$value;
					
			}

			public function __toString(){

				return (string)$this->getName();

			}

		}

	}

	
?>
