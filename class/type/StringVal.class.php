<?php

	namespace apf\type{

		class StringVal{

			use \apf\traits\Type;

			public function __construct($value='',$cast=FALSE){

				if(!$cast&&!is_string($value)){

					throw(new \Exception("Expected value must be of type string, ".gettype($value)." given"));

				}

				$this->value	=	(string)$value;
				$this->value	=	trim($value);

				if($this->strlen()==0){

					throw(new \Exception("StringVal requires the string not to be empty"));

				}

			}

		}

	}

?>
