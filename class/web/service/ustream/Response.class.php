<?php

	namespace apf\web\service\ustream{


		class Response{

			private	$response	=	NULL;

			public function __construct(\stdClass $obj){

				$this->setResponseObject($obj);

			}

			public function setResponseObject(\stdClass $obj){
				$this->response	=	$obj;
			}

			public function getResponseObject(){
				return $this->response;
			}

			public function __call($method,$args){

				$wantedValue	=	substr(strtolower($method),3);
				$method			=	substr(strtolower($method),0,3);

				switch($method){

					case "get":

						foreach($this->response->results as $key=>$value){

							if(strtolower($key)==$wantedValue){

								return $value;

							}

						}	

					break;

				}

			}

		}

	}

?>
