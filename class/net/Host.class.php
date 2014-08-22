<?php

	namespace aidSQL\net{

		class Host{

			private	$_host	=	NULL;
			private	$_port	=	NULL;

			public function setHost($host){

				$this->_host	=	$host;

			}

			public function getHost(){

				return $this->_host;

			}

			public function setPort($port=0){

				$port	=	(int)$port;

				if($port>65535||$port<0){
	
					throw(new \Exception("Port range should be between 0 and 65535"));

				}

	
				$this->_port	=	$port;

			}

			public function __toString(){

				$ret	=	$this->_host;

				if(is_int($this->_port)){
					$ret.=':'.$this->_port;
				}

				return $ret;

			}

		}

	}

?>
