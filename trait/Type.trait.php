<?php

	namespace apf\traits{

		trait Type{

			protected $value	=	NULL;

			abstract public function __construct($value='',$cast=FALSE);

			public function __call($func,$params){

				array_unshift($params,$this->value);
				$return	=	call_user_func_array($func,$params);

				return $return;

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
