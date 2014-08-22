<?php

	/**
	*livestream API for PHP5 by Federico Stange
	*for apolloFramework.
	*
	*Api doc: http://www.livestream.com/userguide/
	*
	*Code Example:
	*
	*$httpAdapter		=	new \apolloFramework\http\adapter\Ecurl();
	*$livestream			=	new \apolloFramework\web\service\livestream\Api($httpAdapter);
	*$livestream->setFormat("json");
	*$livestream->setKey(YOUR_API_KEY);
	*$livestream->setSubjectId("pthreat");
	*$user	=	$livestream->user();
	*
	*/

	namespace apolloFramework\web\service\livestream{

		class Api extends \apolloFramework\web\Service{

			private	$channel				=	NULL;
			private	$baseUri				=	"api.channel.livestream.com";
			private	$apiVersion			=	"2.0";

			private	$callBack			=	NULL;
			private	$callArgs			=	NULL;
			private	$context				=	NULL;

			public function __construct(\apolloFramework\http\Adapter $adapter=NULL,\apolloFramework\core\Config $config=NULL){

				parent::__construct($adapter,$config);
				parent::setSupportedFormats(Array("json","xml"));
				parent::setFormat("json");

			}

			public function channel($channelName=NULL){

				$class	=	__CLASS__;
				$obj		=	new $class($this->adapter,$this->config);
				$obj->setContext($this->removeNameSpace(__FUNCTION__));
				$obj->setContextId($channelName);

				return $obj;

			}

			public function setContext($context=NULL){

				$context	=	trim($context);

				if(empty($context)){

					throw(new \Exception("Context cant be empty"));

				}

				$this->context	=	$context;

			}

			public function getContext(){

				return $this->context;

			}

			public function setContextId($id=NULL){

				$id	=	trim($id);

				if(empty($id)){

					throw(new \Exception("Context ID cant be empty"));

				}

				$this->contextId	=	$id;

			}

			public function getContextId(){

				return $this->contextId;

			}

			protected function removeNameSpace($method){

				return basename(preg_replace("#\\\#",'/',strtolower($method)));
				
			}

			public function __call($method,$args){

				$context	=	strtolower($this->context);

				switch($context){

					case 'channel':

						$uri	=	'http://'.
									'x'.$this->contextId	.'x.'.
									$this->baseUri			.'/'.
									$this->apiVersion		.'/'.
									$method.'.'.$this->format;


					break;

				}

				if(sizeof($args)){

					$uriArgs	=	Array();

					foreach($args as $arg){

						if(!is_array($arg)){
							throw(new \Exception("Arguments should be passed as array key=>value pairs"));
						}

						foreach($arg as $key=>$value){
								$uriArgs[]	=	"$key=$value";
						}

						$uri.='?'.implode($uriArgs,'&');

					}

				}

				$this->setUri(new \apolloFramework\parser\Uri($uri));
				$this->adapter->setHttpMethod("GET");

				$response	=	parent::request();

				if(!$response){

					throw(new \Exception("Web service returned no response!"));

				}

				$response	=	json_decode($response);
				$response	=	new Response($response->channel);

				return $response;


			}
			
			public function __toString(){

				return sprintf("%s",$this->uri);

			}
			
		}

	}

?>
