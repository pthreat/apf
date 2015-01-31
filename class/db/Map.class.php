<?php

	namespace db{

		class Map{

			private	$class		=	NULL;
			private	$method		=	NULL;
			private	$callable	=	NULL;

			public function toClass($class=NULL){

				\apf\Validator::emptyString($class,"Class name can't be empty");

				if(!class_exists($class)){

					throw new \Exception("Can not map to unexistent class $class");

				}

				$this->class	=	$class;

			}

			public function getClass(){

				return $this->class;

			}

			public function toClassMethod($class,$method)
			}

			public function getClassMethod(){
			}

			public function toCallable(callable $callable){
			}

		}

	}

?>
