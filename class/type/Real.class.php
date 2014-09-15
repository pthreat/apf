<?php

	namespace apf\type{

		class Real{

			use \apf\traits\Type;

			public function __construct($value='',$cast=FALSE){

				$isReal	=	is_real($value);

				if(!$cast&&!$isReal){

					throw(new \Exception("Expected value must be of type float/double, ".gettype($value)." given"));

				}

				if($cast&&!$isReal){

					return $this->value	=	(double)$value;

				}

				$this->value	=	$value;

			}

		}

	}

?>
