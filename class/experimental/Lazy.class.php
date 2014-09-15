<?php

	namespace apf\base{

		abstract class Lazy{

			private $_lazyData	=	Array();

			public function __construct($data){

				if(!$data){
					return;
				}

				if(is_object($data)){
					$data	=	$data->toArray();
				}

				foreach($data as $key=>$value){
					$call	=	"set".$key;
					$this->$call($value);
				}

			}

			public function __call($method,$args){

				if(!isset($this->data)){

					throw(new \Exception("data array not found on child class"));

				}

				//Boolean is"Whatever" Call

				if(strtolower(substr($method,0,2))=="is"){

					$attr	=	strtolower(substr($method,2));

					foreach($this->lazyData as $name=>$value){

						$name	=	strtolower($name);

						if(($name=="is$attr"||$name=="is_$attr")&&$this->data[$name]["type"]=="boolean"){

							return $value;	

						}

					}

				}

				$method							=	strtolower($method);
				$isCallingSetterOrGetter	=	substr($method,0,3);

				if($isCallingSetterOrGetter=="get"||$isCallingSetterOrGetter=="set"){

					$method	=	preg_replace("/\W/",'',$method);
					$arg		=	strtolower($method);
					$arg		=	substr($arg,3);

					if(!isset($this->data[$arg])){

						throw(new \Exception("No such attribute $arg"));

					}

					if($isCallingSetterOrGetter=="set"){

						return $this->lazyData[$arg] = \apf\Validator::metaValidate($args[0],$this->data[$arg]);

					}

					if($this->data[$arg]["type"]=="date"){

						if(isset($args[0])&&$args[0]=="object"){
							return $this->lazyData[$arg];
						}

						if(isset($this->data[$arg]["getfmt"])){
							return $this->lazyData[$arg]->format($this->data[$arg]["getfmt"]);
						}

					}

					if(isset($args[0])){

						return \apf\Validator::metaValidate($this->lazyData[$arg],$this->data[$arg]);

					}

					return $this->lazyData[$arg];

				}

			}

		}

	}

?>
