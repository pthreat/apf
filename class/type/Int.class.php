<?php

	namespace apf\type{

		class Int{

			use \apf\traits\Type;

			public function __construct(&$value='',$cast=FALSE){

				$isInt	=	is_int($value);

				if(!$cast&&!$isInt){

					throw(new \Exception("Expected value must be of type int,".gettype($value). " given"));

				}

				if($cast&&!is_int($value)){

					$this->value	=	(int)$value;

				}

				$this->value	=	$value;

			}

			public static function cast($num){

				return (int)$num;

			}

		}

	}

?>
