<?php

	namespace apf\traits{

		trait Type{

			use AutoCall;

			protected $value	=	NULL;

			abstract public function __construct($value='',$cast=FALSE);


			public function getValue(){

				return $this->value;

			}

			public function __toString(){

				return (string)$this->value;

			}

		}

	}

?>
