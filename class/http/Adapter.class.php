<?php

	namespace apolloFramework\http{

		abstract class Adapter extends \apolloFramework\Adapter implements \apolloFramework\http\AdapterInterface{

			protected	$_uri						=	NULL;		//\apolloFramework\parser\Uri Object
			protected	$_httpMethod			=	"GET";	
			protected	$_config					=	Array();
			protected	$_logger					=	NULL;

			public function Log($msg = NULL,$color="white",$type="0",$toFile=FALSE){

				$logToFile			=	(isset($this->_config["log-all"]))	?	TRUE	:	$toFile;

				if(!is_null($this->_logger)){

					$this->_logger->setPrepend("[".get_class($this)."]");
					$this->_logger->log($msg,$color,$type,$logToFile);
					return TRUE;

				}

				return FALSE;

			}

			public function setLog(\apolloFramework\core\Logger &$log){

				$this->_logger=$log;

			}

			public function setConfig(Array $config){
				$this->_config	=	$config;
			}

			public function setUri(\apolloFramework\parser\Uri $uri=NULL){

				if(sizeof($this->_config) && $this->_config["verbose"]==2){
					$this->log("Normalized URI: ".$uri,0,"white");
				}

				$port	=	$uri->getPort();

				if($port){
					$this->setPort($port);
				}

				$this->_uri = $uri;

			}

			public function getUri(){

				return $this->_uri;

			}

			//Sets request to be POST or GET

			public function setHttpMethod ($method=NULL){

				switch ($method=strtoupper(trim($method))){

					case "POST":
					case "GET" :
						$this->log("Set method $method ..");
						$this->_method = $method;
						break;

					default:
						$msg = "Invalid method specified -> ". var_export ($method,TRUE) ." <-, method can only be one of POST or GET";
						throw (new \Exception($msg));
						break;

				}

				return TRUE;

			}

			public function getHttpMethod(){

				return $this->_httpMethod;

			}


			public function getType(){

				return "http";

			}

		}

	}

?>
