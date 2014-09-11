<?php

	namespace apf\web{

		abstract class Service{

			protected	$adapter				=	NULL;
			protected	$uri					=	NULL;
			protected	$apiKey				=	NULL;
			protected	$supportedFormats	=	Array();
			protected	$format				=	NULL;
			protected	$config				=	NULL;

			public function __construct(\apf\http\Adapter $adapter=NULL,\apf\core\Config $config=NULL){

				if(!is_null($adapter)){

					$this->setAdapter($adapter);

				}

				if(!is_null($config)){

					$this->setConfig($config);

				}

			}

			public function setAdapter(\apf\http\Adapter $adapter){

				$this->adapter	=	$adapter;

			}

			public function getAdapter(){

				return $this->adapter;

			}

			public function setUri(\apf\parser\Uri $uri){

				$this->uri	=	$uri;

			}

			public function getUri(){

				return $this->uri;

			}


			public function setConfig(\apf\core\Config $config){

				$class	=	strtolower(get_class($this));

				if(!isset($config->$class)){

					throw(new \Exception($class." section not found in config object"));

				}

				$this->config	=	$config;

			}

			public function getConfig(){

				return $this->config;

			}

			public function setApiKey($key=NULL){

				if(empty($key)){

					throw(new \Exception("API key can't be empty!"));

				}

				$this->apiKey	=	$key;

			}

			public function getApiKey(){

				return $this->apiKey;

			}

			public function setSupportedFormats(Array $formats){

				$this->supportedFormats	=	$formats;

			}

			public function getSupportedFormats(){

				return $this->supportedFormats;

			}

			public function setFormat($format=NULL){

				$format	=	trim($format);

				if(empty($format)){

					throw(new \Exception("Web service format can't be empty'"));

				}

				$this->format	=	$format;
							
			}

			public function getFormat(){

				return $this->format;

			}

			public function request(){

				$this->adapter->setUri($this->uri);

				$response	=	$this->adapter->connect();

				return $response;

			}

		}

	}

?>
