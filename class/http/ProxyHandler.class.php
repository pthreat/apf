<?php

	namespace aidSQL\http{

		class ProxyHandler {

			private	$_logger				=	NULL;
			private	$_httpAdapter		=	NULL;
			private	$_ipUri				=	NULL;	//An ip that will just tell you your ip AKA echo $_SERVER["REMOTE_ADDR"]
			private	$_proxyList			=	array();
			private	$_revalidateOnGet	=	FALSE;

			public function __construct(\aidSQL\http\Adapter $adapter,\aidSQL\core\Logger &$log, $ipUri="http://cfaj.freeshell.org/ipaddr.cgi"){

				$this->setLog($log);

				if(!is_null($ipUri)){

					$this->setIpUri($ipUri);

				}

				$this->setHttpAdapter($adapter);

			}

			public function revalidateOnGet($revalidate=TRUE){

				$this->_revalidateOnGet	=	$revalidate;

			}

			public function setIpUri($ipUri){

				$this->_ipUri	=	new \aidSQL\parser\Uri($ipUri);

			}

			public function setHttpAdapter(\aidSQL\http\Adapter $adapter){

				$adapter	=	clone($adapter);
				$adapter->setUri($this->_ipUri);
				$adapter->setConnectRetry(1);
				$adapter->setConnectTimeout(10);
				$adapter->setTimeout(15);

				$this->_httpAdapter = $adapter;

			}

			public function setLog(\aidSQL\core\Logger &$log){
				$this->_logger = $log;
			}

			private function log($msg = NULL,$color="white",$type="0",$toFile=FALSE){

				if(!is_null($this->_logger)){

					$this->_logger->setPrepend("[".get_class($this)."]");
					$this->_logger->log($msg,$color,$type);
					return TRUE;

				}

				return FALSE;

			}

			public function checkProxyList($file=NULL,$shuffle=FALSE){

				$file		=	new \aidSQL\core\File($file);
				$proxies	=	$file->getContentsAsArray();

				if(!sizeof($proxies)){
					throw(new \Exception("Empty proxy file specified!"));
				}

				$this->log("Validating proxy list | (".count($proxies).") proxies found",0,"light_cyan");

				if($shuffle){
					shuffle($proxies);
				}

				foreach($proxies as $proxy){
	
					$proxy	=	trim($proxy);

					if(empty($proxy)){	//blank line
						continue;
					}

					$proxy	=	explode(':',$proxy);

					$port		=	(isset($proxy[1])&&is_int($proxy[1]))	?	$proxy[1]	:	80;		//port
					$tunnel	=	(isset($proxy[2])&&!empty($proxy[2]))	?	$proxy[2]	:	NULL;		//Proxy tunnel
					$user		=	(isset($proxy[3])&&!empty($proxy[3]))	?	$proxy[3]	:	NULL;		//Proxy user
					$pass		=	(isset($proxy[4])&&!empty($proxy[4]))	?	$proxy[4]	:	NULL;		//Proxy password
					$auth		=	(isset($proxy[5])&&!empty($proxy[5]))	?	$proxy[5]	:	NULL;		//Proxy auth type

					$proxy	=	$proxy[0];

					//Do host/ip validation  etc,etc
					if(!empty($proxy)){

						$isValid	=	$this->checkProxy($proxy,$port,$tunnel,$user,$pass,$auth);

						if($isValid){

							$this->log("Found valid proxy $proxy:$port!",0,"light_green");

						}else{

							$this->log("Invalid proxy $proxy:$port!",1,"red");

						}

					}else{

							$this->log("Syntax error on proxy list",2,"yellow");

					}

				}

			}

			public function checkProxy($proxy,$port=80,$tunnel=NULL,$user=NULL,$pass=NULL,$auth=NULL){

				
				$this->log("Checking proxy $proxy:$port",0,"light_cyan");
				$this->_httpAdapter->setProxyServer($proxy);
				$this->_httpAdapter->setProxyPort($port);

				if(!is_null($tunnel)){
					$this->_httpAdapter->setProxyTunnel($tunnel);
				}

				if(!is_null($user)){
					$this->_httpAdapter->setProxyUser($user);
				}

				if(!is_null($pass)){
					$this->_httpAdapter->setProxyPassword($pass);
				}

				if(!is_null($auth)){
					$this->_httpAdapter->setProxyAuth($auth);
				}

				try{	

					$contents	=	trim($this->_httpAdapter->connect());

					$isValidProxy	=	(ip2long($contents))	?	TRUE	:	FALSE;

					$tmpProxy	=	array(
													"server"		=>	$proxy,
													"port"		=>	$port,
													"tunnel"		=>	$tunnel,
													"user"		=>	$user,
													"password"	=>	$pass,
													"auth"		=>	$auth,
													"valid"		=>	$isValidProxy
					);

					if(sizeof($this->_proxyList)){

						foreach($this->_proxyList as &$proxy){

							if($proxy["proxy"] == $tmpProxy["proxy"]){
								$proxy	=	$tmpProxy;
							}

						}

					}else{

						$this->_proxyList[]	=	$tmpProxy;

					}

					return $tmpProxy["valid"];

				}catch(\Exception $e){

					$this->log($e->getMessage(),1,"red");
				}

			}

			public function getValidProxy(){

				$shuffledProxyList	=	$this->_proxyList;
				shuffle($shuffledProxyList);

				foreach($shuffledProxyList as $proxy){

					if($proxy["valid"]){

						if($this->_revalidateOnGet){

							if(!$this->checkProxy($proxy["proxy"],$proxy["port"],$proxy["tunnel"],$proxy["user"],$proxy["pass"],$proxy["auth"])){

								continue;

							}else{

								return $proxy;

							}

						}else{

							return $proxy;

						}

					}

				}

			}

			public function getAllProxies(){
				return $this->_proxyList;
			}

		}

	}
?>
