<?php

	/**
	*Ustream API for PHP5 by Federico Stange
	*for apf.
	*
	*Api doc: http://developer.ustream.tv/data_api/docs
	*
	*Code Example:
	*
	*$httpAdapter		=	new \apf\http\adapter\Ecurl();
	*$ustream			=	new \apf\web\service\ustream\Api($httpAdapter);
	*$ustream->setFormat("json");
	*$ustream->setKey(YOUR_API_KEY);
	*$ustream->setSubjectId("pthreat");
	*$user	=	$ustream->user();
	*
	*/

	namespace apf\web\service\ustream{

		class Api extends \apf\web\Service{

			protected	$config			=	Array(
														"baseUri"	=>	'http://api.ustream.tv',
														"key"			=>	NULL
			);

			private	$parameters		=	Array(
														"subject"	=>	NULL,
														"id"			=>	NULL,
														"format"		=>	NULL,
														"command"	=>	NULL,
														"page"		=>	NULL,
														"limit"		=>	NULL
			);

			private	$callArgs			=	NULL;

			private	$validFormats		=	Array("xml","json","html","php");

			public function __construct(\apf\http\Adapter $adapter,Array $config=Array(),$parameters=Array()){

				parent::__construct($adapter);

				if(sizeof($config)){

					$this->setConfig($config);

				}

				if(sizeof($parameters)){

					$this->setParameters($parameters);

				}

			}

			public function setParameters(Array $parameters){

				$this->parameters	=	$parameters;

			}

			public function getRequestUri(){

				if(!$this->getCommand()){

					throw(new \Exception("No command specified for ustream API"));

				}

				if(!$this->getSubject()){

					throw(new \Exception("No subject/scope specified"));

				}

				$uri	=	$this->getBaseUri()		.'/'.
							$this->getFormat()		.'/'.
							$this->getSubject()		.'/'.
							$this->getSubjectId()	.'/'.
							$this->getCommand();


				if(!is_null($this->callArgs)){

					$uri	.=	'/'.implode('',$this->callArgs);

				}

				$uri	.=	'?key='.$this->getKey();

				return new \apf\parser\Uri($uri);

			}

			public function setKey($key){

				$this->config["key"]	=	$key;

			}

			public function getKey(){

				return $this->config["key"];

			}

			public function getBaseUri(){

				return $this->config["baseUri"];

			}

			public function setBaseUri($baseUri){

				$this->config["baseUri"]	=	$baseUri;

			}

			public function user(){

				$class	=	__CLASS__;
				$obj		=	new $class($this->adapter,$this->config,$this->parameters);
				$obj->setSubject(__FUNCTION__);

				return $obj;

			}

			public function channel(){

				$class	=	__CLASS__;
				$obj		=	new $class($this->adapter,$this->config,$this->parameters);
				$obj->setSubject(__FUNCTION__);

				return $obj;

			}

			public function stream(){

				$class	=	__CLASS__;
				$obj		=	new $class($this->adapter,$this->config,$this->parameters);
				$obj->setSubject(__FUNCTION__);

				return $obj;

			}

			public function system(){

				$class	=	__CLASS__;
				$obj		=	new $class($this->adapter,$this->config,$this->parameters);
				$obj->setSubject(__FUNCTION__);

				return $obj;

			}

			public function video(){

				$class	=	__CLASS__;
				$obj		=	new $class($this->adapter,$this->config,$this->parameters);
				$obj->setSubject(__FUNCTION__);

				return $obj;

			}

			public function setSubject($subject){

				$this->parameters["subject"]	=	$this->removeNameSpace($subject);

			}

			public function getSubject(){

				return $this->parameters["subject"];

			}


			public function setCommand($command){

				$this->parameters["command"]	=	trim($command);

			}

			public function getCommand(){

				return $this->parameters["command"];

			}

			private function setLimit($limit){

				$this->parameters["limit"]	=	(int)$limit;

			}

			private function getLimit(){

				return $this->parameters["limit"];

			}

			public function setConfig(Array $config){

				$configKeys	=	array_keys($this->config);

				foreach($configKeys as $key=>$value){

					if(!isset($config[$value])){

						throw(new \Exception("Expecting \"$value\" in config array ".var_export($config,TRUE)));

					}

				}

				$this->config	=	$config;

			}

			public function getConfig(){

				return $this->config;

			}

			public function setSubjectId($id){

				$this->parameters["id"]	=	$id;

			}

			public function getSubjectId(){

				return $this->parameters["id"];

			}

			public function getId(){

				return $this->parameters["id"];

			}

			public function setFormat($format=NULL){

				$format	=	strtolower($format);

				if(!in_array($format,$this->validFormats)){

					throw(new \Exception("Invalid format specifided \"$format\""));

				}

				$this->parameters["format"]	=	$format;

			}

			public function getFormat(){

				return $this->parameters["format"];

			}

			public function setPage($page){

				$page	=	(int)$page;

				$this->parameters["page"]	=	$page;

			}

			public function getPage(){

				return $this->parameters["page"];

			}

			public function request(){

				$uri	=	$this->getRequestUri();
				$this->adapter->setUri($uri);

				return $this->adapter->connect();

			}

			protected function removeNameSpace($method){

				return basename(preg_replace("#\\\#",'/',strtolower($method)));
				
			}

			public function __call($method,$args){

				$method	=	substr($method,2);

				$this->setCommand($this->removeNameSpace($method));

				if(sizeof($args)){

					$this->callArgs	=	$args;

				}

				$response			=	$this->request();
	
				$this->callArgs	=	NULL;

				if($this->parameters["format"]=="json"||!$this->parameters["format"]){
					return new Response(json_decode($response));
				}

				return $response;

			}
			
			public function __toString(){

				return sprintf("%s",$this->getRequestUri());

			}
			
		}

	}

?>
