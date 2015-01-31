<?php

	namespace apf\type{

		class Vector extends \ArrayObject{

			use \apf\traits\Type;

			public function __construct(Array $value=Array(),$cast=FALSE){

				$this->value	=	$value;
				parent::__construct($this->value);

			}

			public static function cast($val){

				return (Array)$val;

			}

			public function __toString(){

				return implode(',',$this->value);

			}

		}

	}

?>
