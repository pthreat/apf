<?php

	namespace apf\net{

		class Connection{

			private	$user	=	NULL;
			private	$host	=	NULL;
			private	$port	=	NULL;

			public function __construct(\apf\type\net\Connection $connection){

				$this->setUser($connection["user"]);
				$this->setPass($connection["pass"]);
				$this->setHost($connection["host"]);
				$this->setPort($connection["port"]);

			}

			public function setUser($user=NULL){

				$this->user	=	$user;

			}

			public function getUser(){

				return $this->user;

			}

			public function setPass($pass=NULL){

				$this->pass	=	$pass;

			}

			public function getPass(){

				return $this->pass;

			}

			public function setPort($port=0){

				$port	=	(int)$port;

				if($port>65535||$port<0){
	
					throw(new \Exception("Port range should be between 0 and 65535"));

				}

				$this->port	=	$port;

			}

			public function getPort(){

				return $this->port;

			}

			public function setHost($host=NULL){

				if($host instanceof "\\apf\\net\\Host")){

					return $this->host	=	$host;

				}

				return $this->host	=	new \apf\net\Host($host);

			}

			public function getHost(){

				return $this->host;

			}

		}

	}

?>
