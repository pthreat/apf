<?php

	namespace apf\traits\validate{

		trait Fluent{

			private	$value	=	NULL;

			public function __call($method,$args){

				array_unshift($args,$this->value);

				call_user_func_array(sprintf('\apf\validate\%s::%s',\apf\util\Class_::removeNamespace(__CLASS__),$method),$args);

				return $this;

			}

		}

	}

?>
