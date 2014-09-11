<?php
	/**
	*	Apollo Framework Flickr API
	*
	*	Author: Federico Stange
	*	Some parts of this code have been taken from Carl Henderson's Flickr API.
	*	This class has been completely modified to suit the Apollo Framework 
	*	namespaces and coding standards.
	*
	*/


	namespace apolloframework\web\service{

		class Flickr {

			private $_config			=	Array(
													'api_key'				=> NULL,
													'api_secret'			=> NULL,
													'rest_endpoint'		=> "http://ycpi.api.flickr.com/services/rest/",
													'upload_endpoint'		=> "http://ycpi.api.flickr.com/services/upload/",
													'replace_endpoint'	=> 'http://ycpi.api.flickr.com/services/replace/',
													'auth_endpoint'		=> 'http://ycpi.api.flickr.com/services/auth/',
													'format'					=>	'json'
			);

			private	$_errorCode		=	0;
			private	$_errorMessage	=	NULL;
			private	$_tree			=	NULL;
			private	$_adapter		=	NULL;

			private	$_validResponseFormats	=	Array("json","rest","soap","xmlrpc");
			private	$_lastCalledMethod		=	NULL;

			public function __construct(\apf\http\Adapter $adapter=NULL,Array $config = Array()){

				if(!is_null($adapter)){

					$this->setHttpAdapter($adapter);

				}

				if(sizeof($config)){

					$this->setConfig($config);

				}


			}

			public function setConfig(Array $config){

				$requiredKeys	=	Array("api_key","api_secret","endpoint","auth_endpoint");

				foreach($requiredKeys as $required){

					if(!array_key_exists($required,$config)){

						throw(new \Exception("Missing parameter $required for Flickr API"));

					}

				}

				$this->_config	=	$config;

			}

			public function setApiKey($key){

				$this->_config["api_key"]	=	$key;

			}

			public function getApiKey(){

				return $this->_config["api_key"];

			}

			public function setApiSecret($secret){

				$this->_config["api_secret"]	=	$secret;

			}

			public function getApiSecret(){

				return $this->_config["api_secret"];

			}


			public function setHttpAdapter(\apf\http\Adapter $adapter){

				$this->_adapter	=	$adapter;

			}

			public function setResponseFormat($format){

				if(!in_array($format,$this->_validResponseFormats)){

					throw(new \Exception("Invalid response format specified \"$format\""));

				}

				$this->_config["format"]	=	$format;

			}

			public function getResponseFormat(){

				return $this->_config["format"];

			}

			private function parseResponse($data){

				$data	=	trim($data);

				if(empty($data)){

					throw(new \Exception("Can't parse response data since it's empty!"));

				}

				$response	=	NULL;

				switch($this->_config["format"]){

					case 'json':
						$data			=	substr($data,strpos($data,'(')+1);
						$data			=	trim($data,')');
						return json_decode($data);
					break;

				}

				return $response;

			}

			public function __call($method,$args){

				$method		=	preg_replace("#_#",'.',$method);

				if(!preg_match("/^flickr.*/",$method)){

					$method	=	"flickr.".$method;

				}

				if(sizeof($args)){

					$args	=	$args[0];

				}

				$args['method']	=	$method;
				$args['api_key']	=	$this->_config['api_key'];
				$args["format"]	=	$this->_config["format"];

				if (isset($this->_config["api_secret"])){

					$args['api_sig'] = $this->signArgs($args);

				}

				$uri	=	new \apf\parser\Uri($this->_config["rest_endpoint"]);
				$uri->addRequestVariables($args);
				$this->_adapter->setUri($uri);
				$this->_adapter->setHttpMethod("GET");
				$data	=	trim($this->_adapter->connect());

				if(empty($data)){

					throw(new \Exception("Flickr webservice has returned no data!"));	

				}

				return $this->parseResponse($data);

			}


			public function getErrorCode(){

				return $this->_errorCode;

			}

			public function getErrorMessage(){

				return $this->_errorMessage;

			}

			public function getAuthUrl($perms="read", $frob=''){

				$args = array(
					'api_key'	=> $this->_config['api_key'],
					'perms'		=> $perms,
				);

				if (strlen($frob)){ $args['frob'] = $frob; }

				$args['api_sig'] = $this->signArgs($args);

				$uri	=	new \apf\parser\Uri($this->_config["auth_endpoint"]);
				$uri->addRequestVariables($args);
				return $uri;

			}

			public function signArgs($args){

				ksort($args);
				$a = '';
				foreach($args as $k => $v){
					$a .= $k . $v;
				}

				return md5($this->_config['api_secret'].$a);

			}

			public static function photoUri(Array $args,$size="m",$ext=".jpg"){

				$requiredKeys	=	Array("farm","server","secret","id","secret");	

				foreach($requiredKeys as $required){

					if(!isset($args[$required])){

						$msg	=	__CLASS__.'::'.__METHOD__." requires \"$required\" key to be set in the \$args array";
						throw(new \Exception($msg));

					}

				}

				$uri	=	"http://farm$args[farm].staticflickr.com/$args[server]/$args[id]_$args[secret]_$size".$ext;

				return new \apf\parser\Uri($uri);

			}

		}

	}

?>
