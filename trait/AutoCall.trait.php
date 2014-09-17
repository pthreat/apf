<?php

	namespace apf\traits{

		trait AutoCall{

			private $value	=	NULL;

			public function set($value=NULL){

				$this->value	=	$value;

			}

			public function get(){

				return $this->value;

			}

			public function __call($func,$params){

				array_unshift($params,$this->value);
				var_dump($params);
				$return	=	call_user_func_array($func,$params);

				return $return;

			}

		}

	}

?>
