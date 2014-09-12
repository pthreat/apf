<?php

	namespace apf\pattern{

		trait Mingleton{

			private $instances		=	Array();
			private $instanceCount	=	0;

			protected function getInstanceCount(){

				return self::$instanceCount;

			}

			public static &function getInstance($name=null){

				if(sizeof(self::$instances)==1||is_null($name)){

					return array_pop(self::$instances);

				} 

				if(self::instanceExists($name)){

					return self::$instances[$name];

				}

				throw new \Exception("No such instance with this name");

			}

			protected static function instanceExists($name=NULL){

				\apf\Validator::emptyString($name);
				return array_key_exists($name,self::$instances);
			}

			protected function addInstance($value=NULL,$name=NULL){

				if(self::instanceExists($name)){

					throw new \Exception("Instance already exists");

				}

				self::$instanceCount++;
				self::$instances[$name]=$value;

			}

		}

	}

?>
