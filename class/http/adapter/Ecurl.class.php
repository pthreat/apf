<?php

	namespace apolloFramework\http\Adapter {

		class Ecurl extends \apolloFramework\http\Adapter {

			const			ADAPTER_NAME			=	"ECurl";
			const			ADAPTER_VERSION		=	"1.0";

			private		$_cookie					=	NULL;
			private		$_curlOptions			=	Array();
			private		$_handler				=	NULL;
			private		$_content				=	NULL;
			private		$_requestInterval		=	0;
			private		$_transferInfo			=	NULL;
			private		$_connectRetry			=	20;	//Put in config and interfaces!!!!!
			private		$_ignoreHttpErrors	=	FALSE;
			private		$_httpRequestCount	=	0;
			private		$_headers				=	Array();
			private		$_requestHeaders		=	Array();

			private		$_proxy				=	array(
				"server"		=>NULL,
				"port"		=>80,
				"user"		=>"",
				"password"	=>"",
				"auth"		=>"BASIC",
				"type"		=>"HTTP",
				"tunnel"		=>0
			);

			private		$_proxyHandler		=	NULL;		//Proxy object validator

			public function __construct(\apolloFramework\parser\Uri $uri=NULL){

				if(!is_null($uri)){
					$this->setUri($uri);
				}

				$this->setHandler(curl_init());
				$this->setCurlDefaults();

			}

			public function addRequestHeader($name=NULL,$value=NULL){

				\apolloFramework\Validator::emptyString($name,"Header name must be set in order to add a header");

				$this->_requestHeaders[]	=	"$name: $value";

			}

			public function getRequestHeaders(){

				return $this->_requestHeaders;

			}

			public function setConfig(Array $config){

				if(isset($config["http-method"])){

					$this->setHttpMethod($config["http-method"]);

				}

				if(isset($config["follow-redirects"])){

					$this->setFollowRedirects($config["follow-redirects"]);

				}
				if(isset($config["connect-timeout"])){

					$this->setConnectTimeout($config["connect-timeout"]);

				}

				if(array_key_exists("http-ignore-errors",$config)){

					$this->_ignoreHttpErrors	=	TRUE;

				}

				if(isset($config["request-interval"])&&$config["request-interval"]>0){

					$this->setRequestInterval($config["request-interval"]);

				}

				parent::setConfig($config);

			}

			public function setUser($user){
			}

			public function setPassword($password){
			}

			public function getRequestCount(){

				return $this->_httpRequestCount;

			}

			public function setConnectRetry($int=20){

				if(!is_int($int)){
					throw(new \Exception("Connect retry should be an integer"));
				}

				$this->_connectRetry	=	$int;	

			}

			public function getConnectRetry($int=20){
				return $this->_connectRetry;
			}

			public function setConnectTimeout($timeout=0){

				$this->setCurlOption('CONNECTTIMEOUT',(int)$timeout);

			}

			public function setTimeOut($seconds=0){

				$this->setCurlOption('TIMEOUT',(int)$seconds);

			}

			public function getConnectTimeout(){

				return $this->getCurlOption('CONNECTTIMEOUT');

			}

			public function setCookieFile($cookie="/tmp/cookie"){

				$this->_cookie=$cookie;
				$this->setCurlOption("CURLOPT_COOKIEJAR",$this->_cookie);
				$this->setCurlOption("CURLOPT_COOKIEFILE",$this->_cookie);

			}

			public function getCookieFile(){

				return $this->_cookie;

			}

			public function setProxyHandler(\apolloFramework\http\ProxyHandler $proxyHandler){

				$this->_proxyHandler	=	$proxyHandler;

			}


			public function setProxyServer($server){

				$this->log("Setting proxy server to $server",0,"light_cyan");
				$this->_proxy["server"] = $server;

			}

			public function setProxyTunnel($boolean){
				$this->_proxy["tunnel"] = $boolean;
			}

			public function setProxyPort($port){

				$port = (int)$port;

				if(!$port){
					throw(new \Exception("Invalid proxy port specified"));
				}

				$this->_proxy["port"] = $port;

			}

			public function setPort($port=80){

				if(!is_int($port)||$port <=0){
					throw (new \Exception("Invalid HTTP port specified! Port must be an integer (1-65535)"));
				}

				if(sizeof($this->_config) && $this->_config["verbose"]==2){
					$this->log("Set Port: ".$port,0,"white");
				}

				return $this->setCurlOption("PORT",$port);

			}

			public function setProxyUser($user){
				$this->_proxy["user"]=$user;
			}

			public function setProxyPassword($password){
				$this->_proxy["password"] = $password;
			}

			public function getProxyPort(){

				return (int)$this->_proxy["port"];

			}

			public function getProxyServer(){

				return $this->_proxy["server"];

			}

			public function setSeparator($separator=NULL){

				$this->separator = $separator;

			}

			public function setPreSeparator($char=NULL){

				$this->preSeparator=$char;

			}

			public function setEqualityOperator($char=NULL){

				$this->equalityOperator = $char;

			}

			public function setProxyAuth($auth="BASIC"){

				$auth = strtoupper($auth);

				switch($auth){
					case "NTLM":
					case "BASIC":
						if($this->_config["verbose"]==2){
							$this->log("Set proxy AUTH type to $auth");
						}
						$this->_proxy["auth"] = $auth;
					break;
					default:
							throw(new \Exception("Invalid authentication method ->$auth<-"));
						break;
				}

			}

			public function setProxyType($type="HTTP"){

				$type = strtoupper($type);

				switch($type){

					case "HTTP":
					case "SOCKS5":
						$this->_proxy["type"] = $type;
					break;

					default:
							throw(new \Exception("Invalid proxy type specified ->$type<- should be http or socks5"));
					break;

				}

			}

			public function setCurlDefaults(){

				$default = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
				$this->setBrowser($default);
				$this->setHttpMethod("POST");
				$this->setCurlOption("FOLLOWLOCATION",TRUE);
				$this->setCurlOption("RETURNTRANSFER",TRUE);

				//For avoiding https verification
				$this->setCurlOption("SSL_VERIFYPEER",FALSE);
				$this->setCurlOption("SSL_VERIFYHOST",FALSE);

				//Avoid URI caching mechanisms
				$this->setCurlOption("FORBID_REUSE",TRUE);
				$this->setCurlOption("FRESH_CONNECT",TRUE);

			}

			public function getHttpHeaders(){

				return $this->_headers;

			}

			public function setFollowRedirects($boolean=TRUE){

				$this->setCurlOption("FOLLOWLOCATION",$boolean);

			}

			public function getFollowRedirects(){

				return $this->getCurlOptions("FOLLOWLOCATION");	

			}	



			public function setBrowser($browser=NULL){

				if(is_null($browser)){

					$browser = "eCurl 0.1";

				}

				$this->setCurlOption('USERAGENT',$browser);

			}

			public function setHost($host){
			}

			public function getHost(){
			}


			public function setCurlOption ($option=NULL,$value=NULL){

				if(is_null($this->_handler)){

					throw (new \Exception("Could not set option $option, there's no cURL handler set!"));

				}

				$option = strtoupper($option);
				$option = preg_replace ('#\s#','_',$option);
				$option = $this->parseCurlOption($option);

				$this->_curlOptions[$option]=$value;

				if(!is_array($value)&&!is_object($value)&&!is_resource($value)){

					if(!is_bool($value)){

						$value = trim($value);

					}

					return curl_setopt($this->_handler,constant($option),$value);

				}

				//var_dump($value);
				return curl_setopt($this->_handler,constant($option),$value);

			}

			private function parseCurlOption($option){

				$option = (!preg_match('#^CURLOPT_.*#',$option)) ? 'CURLOPT_'.$option : $option;

				return strtoupper($option);

			}

			public function getCurlOptions(){

				return $this->_curlOptions;

			}

			public function getCurlOptionsAsString(){

				$options = $this->getCurlOptions();

				$strOptions = NULL;
				$strOptions.= "Curl Options:\n";
				$strOptions.= "-------------\n";

				foreach ($options as $opt=>$val){

					$strOptions.= $opt . " => ". $val."\n";

				}

				return $strOptions;

			}

			public function getProxyOptions(){
				return $this->_proxy;
			}

			public function setRequestInterval($interval=0){
					  $this->_requestInterval = (int)$interval;
			}

			public function getRequestInterval(){
					  return $this->requestInterval;
			}

			private function setHandler($handler){

				$this->_handler=$handler;

			}


			public function getHandler(){

				return $this->_handler;

			}

			private function setContent($content){

				$this->_content = $content;

			}

			public function getContent(){

				return $this->_content;

			}

			public function getProxyAuth(){

				if(is_null($this->_proxy["auth"])){
					$this->_proxy["auth"]="BASIC";
				}

				return $this->_proxy["auth"];
			}

			private function configureProxy(){

				if(empty($this->_proxy["server"])&&is_null($this->_proxyHandler)){
					return FALSE;
				}

				if($this->_proxyHandler){

					$proxy	=	$this->_proxyHandler->getValidProxy();

					if($this->_config["verbose"]==2){
						$this->log("Got proxy $proxy[server]:$proxy[port]",0,"light_cyan");
					}

					if(is_null($proxy)){
						throw(new \Exception("Couldnt get a valid proxy from the proxy handler"));
					}

					unset($proxy["valid"]);

					$this->_proxy	=	$proxy;

				}

				$this->setCurlOption("PROXY",$this->_proxy["server"]);
				$this->setCurlOption("PROXYPORT",$this->_proxy["port"]);

				if(!empty($this->_proxy["user"])){

					$userPassword = $this->_proxy["user"].":".$this->_proxy["password"];
					$this->setCurlOption("PROXYUSERPWD",$userPassword);

					$authType = $this->getProxyAuth();
					$authValue = constant("CURLAUTH_".$authType);

				}

				if($this->_proxy["tunnel"]){
					$this->setCurlOption("HTTPPROXYTUNNEL",TRUE);
				}

			}

			private function parseHttpHeaders($content){

				$headers	=	explode("\r\n",$content);

				foreach($headers as $index=>$header){

					//This indicates that we are done parsing the HTTP headers

					if(empty($header)){

						break;

					}

					$instruction	=	substr($header,0,strpos($header,':'));

					//This is the HTTP response header HTTP/1.1 404 Not Found, etc

					if(empty($instruction)&&preg_match("#HTTP\/[0-9]+#",$header)){

						$this->_headers["header_response"]	=	$header;	
						continue;

					}

					$value			=	substr($header,strpos($header,':')+1);

					$this->_headers[trim(strtolower($instruction))]	=	trim($value);

				}

				$headers	=	array_slice($headers,$index+1);
				$headers	=	implode($headers,"\r\n");

				return $headers;

			}

			public function setHttpMethod($method){

				$this->_httpMethod	=	strtoupper($method);

			}

			public function getHttpMethod(){

				return $this->_httpMethod;

			}

			public function connect(){

				$this->configureProxy();

				if(!is_null($this->getCookieFile())){

					$this->setCurlOption("COOKIE",$this->getCookieFile());
					$this->setCurlOption("COOKIEJAR",$this->getCookieFile());

				}

				if(sizeof($this->_requestHeaders)){

					$this->setCurlOption("HTTPHEADER",$this->_requestHeaders);

				}

				if($this->_httpMethod=="POST"){

					$requestVariables =  $this->_uri->getQueryAsArray();
					$post             =  array();

					foreach($requestVariables as $var=>$value){
						$post[]  =  "$var".$this->_uri->getEqualityOperator()."$value";
					}

					$post =  implode($this->_uri->getVariableDelimiter(),$post);

					$this->setCurlOption("POSTFIELDS",$post);
					$this->setCurlOption("URL",$this->_uri->getUriAsString(FALSE));

				}else{

					$this->setCurlOption('URL',$this->_uri->getURIAsString());

				}

				if((int)$this->_requestInterval > 0){
					usleep($this->_requestInterval);
				}

				$connect	=	0;

				do{

					$content					= curl_exec($this->_handler);
					$this->_transferInfo	= curl_getinfo($this->_handler);
			
					if($this->getCurlOption("HEADER")){

						$content	=	$this->parseHttpHeaders($content);

					}

					$this->setContent($content);

					$errno	=	curl_errno($this->_handler);
					$error	=	curl_error($this->_handler);

					if($connect>0&&$errno){

						$this->log("$error, attempting reconnect ... $connect",1,"red");

					}else{

						$this->_httpRequestCount++;

					}

					$connect++;

				} while($connect < $this->_connectRetry && $errno > 0);

				if(($this->_transferInfo["http_code"]!=200&&isset($this->_config["verbose"]))&&$this->_config["verbose"]==2){
					$this->log("WARNING: GOT ".$this->_transferInfo["http_code"],2,"yellow");

				}

				if(!$this->_ignoreHttpErrors){

					if($errno){
						throw (new \Exception($error));
					}

				}

				return $this->getContent();

			}

			public function getCurlOption($getCurlOption){

				$getCurlOption = $this->parseCurlOption($getCurlOption);

				$options = $this->getCurlOptions();

				foreach($options as $opt=>$value){

					if($opt == $getCurlOption){

						return $value;

					}

				}

				return NULL;

			}

			public function getTransferInfo(){

				return $this->_transferInfo;

			}

			public function getHttpCode(){

				return $this->_transferInfo["http_code"];

			}

			public function save(\apolloFramework\core\File $file){

				try{

					$returnOpt	=	$this->getCurlOption("RETURNTRANSFER");

					$this->setCurlOption("RETURNTRANSFER",FALSE);
					$this->setCurlOption("FILE",$file->open("w"));

					$this->connect();

					$file->close();

					$this->setCurlOption("RETURNTRANSFER",$returnOpt);

					return TRUE;

				}catch(\Exception $e){

					$this->setCurlOption("RETURNTRANSFER",$returnOpt);

					throw($e);

					return FALSE;

				}

			}

		}

	}
?>
