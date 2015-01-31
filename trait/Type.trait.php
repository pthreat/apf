<?php

	namespace apf\traits{

		trait Type{

			protected $value	=	NULL;

			abstract public function __construct($value='',$cast=FALSE);

			public function set($value=NULL){

				$this->value	=	$value;

			}

			public function get(){

				return $this->value;

			}


			public function getValue(){

				return $this->value;

			}

			public function __toString(){

				return (string)$this->value;

			}

		}

	}

?>
