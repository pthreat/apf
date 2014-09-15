<?php

	namespace apf\type{

		class Vector extends \ArrayObject{

			use \apf\traits\Type;

			public function __construct(Array $value=Array(),$cast=FALSE){

				$isArray	=	is_array($value);

				if(!$cast&&!$isArray){

					throw(new \Exception("Expected value must be of type Array,".gettype($value). " given"));

				}

				if($cast&&!$isArray){

					$this->value	=	Array($value);

				}else{

					$this->value	=	$value;

				}

				parent::__construct($this->value);

			}

			public function validateKeys(Array $expectedKeys){

				return \apf\Validator::arrayKeys($expectedKeys,$this->value,FALSE);
				
			}

			public function __toString(){

				return implode(',',$this->value);

			}

		}

	}

?>
