<?php

	namespace apf\type{

		class String {

			use \apf\traits\Type;

			public function __construct($value='',$cast=FALSE){

				$isString	=	is_string($value);

				if(!$cast&&!$isString){

					throw(new \Exception("Expected value must be of type string, ".gettype($value)." given"));

				}
				if($cast&&!$isString($value)){

					return $this->value	=	(string)$value;

				}

				$this->value	=	$value;

			}

		}

	}

?>
