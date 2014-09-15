<?php

	namespace apf\traits\pattern{

		trait Singleton{

			protected static $instance	=	NULL;

			public function __construct(){

				throw(new \Exception("Can't make instance of Singleton class"));

			}

			public function __clone(){

				throw(new \Exception("Can't clone Singleton class"));

			}

			//To be redefined by child classes but added by default
			abstract public static function getInstance();

			public static function __callStatic($method,$data){

				return call_user_func_array(array(self::$instance,$method),$data);

			}

		}

	}

?>
