<?php

	namespace apf\net{

		class Host{

			private	$host			=	NULL;
			private	$resolved	=	NULL;
			private	$ipNotation	=	FALSE;

			public function __construct($host=NULL){

				$this->set($host);

			}

			public function resolve(){

				if($this->ipNotation){

					throw(new \Exception("Can't resolve an IP address through DNS :/"));

				}

				$this->resolved	=	 gethostbyname($this->host);

				return $this->resolved;

			}

			public function isIp(){

				return $this->ipNotation;

			}

			public function getIp(){

				if(!is_null($this->resolved)){
					return $this->resolved;
				}

				return $this->resolve();

			}

			public function set($host){

				try{

					\apf\Validator::ip($host);

					$this->host			=	$host;
					$this->ipNotation	=	TRUE;

				}catch(\Exception $e){

					$this->host			=	\apf\validate\String::mustBeNotEmpty($host,"Must enter host name or IP address");
					$this->ipNotation	=	FALSE;

				}

			}

			public function get(){

				return $this->host;

			}

			public function ping($timeout=1){

				$isWindows	=	preg_match("/win/i",PHP_OS);

				if(!$isWindows&&function_exists("posix_getuid")&&($pown=posix_getuid())!==0){

					if(!($pown===0)){

						throw(new \Exception("You must be root in order to ping a host within PHP"));

					}

				}

				$packet = "\x08\x00\x7d\x4b\x00\x00\x00\x00APF_PING";

				$socket  = socket_create(AF_INET, SOCK_RAW, 1);

				socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $timeout, 'usec' => 0));

				socket_connect($socket,$this->host,null);

				$ts = microtime(true);

				socket_send($socket,$packet,strlen($packet), 0);

				$result	=	socket_read($socket, 255)	?	microtime(TRUE) - $ts : FALSE;

				socket_close($socket);

				return $result;

			}

			public function __toString(){

				$ret	=	$this->host;
				return $ret;

			}

		}

	}

?>
