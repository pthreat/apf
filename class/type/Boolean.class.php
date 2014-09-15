<?php

	namespace apf\type{

		class Boolean{

			use \apf\traits\Type;

			public function __construct($value='',$cast=FALSE){

				$isBool	=	is_bool($value);

				if(!$cast&&!$isBool($value)){

					throw(new \Exception("Expected value must be of type boolean,".gettype($value). " given"));

				}

				if($cast&&!$isBool){

					return $this->value	=	(boolean)$value;

				}

				$this->value	=	$value;

			}

		}

	}

?>
