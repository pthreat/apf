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

				$expectedKeys	=	Array("name","type");

				\apf\Validator::arrayKeys($expectedKeys,$columnData);

				$this->setName($columnData["name"]);
				$this->setType($columnData["type"]);

				if(isset($columnData["table"])){

					$this->setTable($columnData["table"]);

				}

				if(isset($columnData["extra"])){

					$this->setExtra($columnData["extra"]);

				}

				if(isset($columnData["maxlen"])){

					$this->setMaxLen($columnData["maxlen"]);

				}

				if(isset($columnData["extra"])){

					$this->setExtra($columnData["extra"]);

				}

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

				//Validate value
				$this->value	=	$value;

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

			public function getPDOType(){

				return $this->pdoType;

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

			public function setPHPType($type=NULL){

				\apf\Validator::emptyString($type,"PHP Type can't be empty");

				$validTypes	=	Array("int","string","double");

				if(!in_array($type,$validTypes)){

					$msg	=	"$type is not a valid PHP type, must choose". 
								"one of ".implode(',',$validTypes);

					throw new \Exception($msg);

				}

				$this->phpType	=	$type;


			}

			public function getPHPType(){

				return $this->phpType;	

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

		}
	}

?>
