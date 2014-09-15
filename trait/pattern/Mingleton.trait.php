<?php

	namespace apf\traits\pattern{

		trait Mingleton{

			private static $selected		=	NULL;
			private static $instances		=	Array();
			private static $instanceCount	=	0;

			public static function getInstanceCount(){

				return self::$instanceCount;

			}

			public static function getInstance($name=null){

				if(sizeof(self::$instances)==1||is_null($name)){

					return array_pop(self::$instances);

				} 

				if(self::instanceExists($name)){

					return self::$instances[$name];

				}

				throw new \Exception("No such instance with this name");

			}

			public static function getSelectedInstance(){

				return self::$selected;

			}

			public static function getInstanceNames(){

				$names	=	Array();

				foreach(self::$instances as $name=>$value){

					$names[]	=	$name;

				}

				return $names;

			}

			private static function instanceExists($name=NULL){

				\apf\Validator::emptyString($name);
				return array_key_exists($name,self::$instances);

			}

			private function addInstance($value=NULL,$name=NULL){

				if(self::instanceExists($name)){

					throw new \Exception("Instance already exists");

				}

				self::$instanceCount++;
				self::$instances[$name]	=	$value;
				self::$selected			=	$name;

			}

			public function select($name=NULL){

				$instance	=	self::getInstance($name);
				return $instance;

			}

			public static function __callStatic($method,$data){

				return call_user_func_array(array(self::$instances[self::$selected],$method),$data);

			}

		}

	}

?>
