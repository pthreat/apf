<?php

	namespace apf\type\net{

		class Connection{

			private	$user	=	NULL;
			private	$host	=	NULL;
			private	$port	=	NULL;

			public function __construct($host=NULL,$port=NULL,\apf\type\User $user=NULL){

				if (!($host instanceof \apf\net\Host)){

					$host	=	new \apf\net\Host($host);

				}

				$this->setHost($host);
				$this->setPort($port);

				if(!is_null($user)){

					$this->setUser($user);

				}

			}

			public function setPort($portNum=NULL){

				$portNum	=	(int)$portNum;

				if($portNum<0||$portNum>65535){
					throw(new \Exception("Port number must be within 0 and 65535"));
				}

				$this->port	=	$portNum;

			}

			public function getPort(){

				return $this->port;

			}

			public function setUser(\apf\type\User $user){

				$this->user=$user;

			}

			public function getUser(){


				return $this->user;

			}

			public function setHost(\apf\net\Host $host){

				$this->host=$host;

			}

			public function getHost(){
				return $this->host;

			}

			public function __toString(){

				$user	=	'';
				if($this->user){
					$user	=	sprintf("%s:",$this->user);
				}

				$port	=	'';

				if(!is_null($this->port)){

					$port	=	':'.$this->port;

				}

				return sprintf("%s%s%s",$user,$host,$port);

			}


		}

	}

?>
