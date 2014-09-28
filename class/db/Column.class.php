<?php

	namespace apf\db{

		class Column{

			private	$table			=	NULL;
			private	$type				=	NULL;
			private	$alias			=	NULL;
			private	$isUnsigned		=	NULL;
			private	$name				=	NULL;
			private	$phpType			=	NULL;
			private	$pdoType			=	NULL;
			private	$isPrimaryKey	=	NULL;
			private	$charset			=	NULL;
			private	$extra			=	NULL;
			private	$maxLength		=	NULL;

			private	$value			=	NULL;

			public function __construct(Array $columnData){

				$expectedKeys	=	Array("name","type","php_type","pdo_type");

				\apf\Validator::arrayKeys($expectedKeys,$columnData);

				$this->setName($columnData["name"]);
				$this->setType($columnData["type"]);
				$this->setPHPType($columnData["php_type"]);
				$this->setPDOType($columnData["pdo_type"]);

				if(isset($columnData["unsigned"])){
					$this->setUnsigned($columnData["unsigned"]);
				}

				if(isset($columnData["table"])){

					$this->setTable($columnData["table"]);

				}

				if(isset($columnData["extra"])){

					$this->setExtra($columnData["extra"]);

				}

				if(isset($columnData["maxlen"])){

					$this->setMaxLength($columnData["maxlen"]);

				}

				if(isset($columnData["extra"])){

					$this->setExtra($columnData["extra"]);

				}

			}

			public function setPHPType($type){

				$type			=	\apf\Validator::emptyString($type,"PHP Type can't be empty");
				$validTypes	=	["int","double","string"];

				if(!in_array($type,$validTypes)){

					throw new \Exception(sprintf("Invalid PHP type specified, PHP type must be one of %s",implode(',',$validTypes)));

				}

				$this->phpType	=	$type;

			}

			public function getPHPType(){

				return $this->phpType;

			}

			public function setPDOType($type){

				$type			=	\apf\Validator::emptyString($type,"PDO Type can't be empty");
				$validTypes	=	["PDO::PARAM_STR","PDO::PARAM_LOB","PDO::PARAM_INT","PDO:PARAM_BOOL"];

				if(!in_array($type,$validTypes)){

					throw new \Exception(sprintf("Invalid PDO type specified, PDO type must be one of %s",implode(',',$validTypes)));

				}

				$this->pdoType	=	$type;

			}

			public function getPDOType($asString=FALSE){

				if($asString){

					return $this->pdoType;

				}

				return constant($this->pdoType);

			}


			public function setName($name=NULL){

				$this->name	=	\apf\Validator::emptyString($name,"Column name can't be empty");

			}

			public function getName(){

				return $this->name;

			}

			public function setExtra($extra=NULL){

				$this->extra	=	$extra;

			}

			public function getExtra(){

				return $this->extra;

			}

			public function setAlias($alias){

				$this->alias	=	\apf\Validator::emptyString("Column alias must not be empty");

			}

			public function getAlias(){

				return $this->alias;

			}

			public function setValue($value=NULL){

				switch($this->phpType){

					case "string":

						if(!is_null($this->maxLen)&&strlen($value)>$this->maxLen){

							throw new \Exception("Given value exceeds column length");

						}

						$this->value	=	$value;

					break;

					case "int":

						if($this->isUnsigned&&$value<0){

							throw new \Exception("Expected unsigned value");

						}

						$this->value	=	(int)$value;

					break;

					case "double":

						if($this->isUnsigned&&$value<0){

							throw new \Exception("Expected unsigned value");

						}

						$this->value	=	(double)$value;

					break;

					default:

						throw new \Exception(sprintf("Unknown PHP type %s",$this->phpType));

					break;

				}


			}

			public function getValue(){

				return $this->value;

			}

			public function setMaxLength($maxLength=NULL){

				$this->maxLength	=	(int)$maxLength;

			}


			public function getMaxLength(){

				return $this->maxLength;

			}

			public function setTable(\apf\db\Table $table){

				$this->table	=	$table;

			}

			public function getTable(){

				return $this->table;

			}

			public function setPrimaryKey($boolean=TRUE){

				$this->isPrimaryKey	=	(boolean)$boolean;

			}

			public function setUnsigned($boolean=TRUE){

				$this->isUnsigned	=	(boolean)$boolean;

			}

			public function isUnsigned(){

				return (boolean) $this->isUnsigned;

			}

			public function setType($type=NULL){

				$this->type	=	\apf\Validator::emptyString($type,"Column type can't be empty");

			}

			public function isPrimaryKey(){

				return (boolean)$this->isPrimaryKey;

			}

			public function setCharset($charset){

				$this->charset	=	$charset;

			}

			public function getCharset(){

				return $this->charset;

			}

			public function __toString(){

				$alias	=	$this->table->getAlias();
				$alias	=	empty($alias)	?	''	:	".$alias";
				return sprintf("%s%s",$alias,$this->name);

			}

		}

	}

?>
