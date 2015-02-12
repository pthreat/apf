<?php

	namespace apf\type{

		class User{

			private $name	=	NULL;
			private $pass	=	NULL;

			public function __construct($name=NULL,$pass=NULL){

				if(!is_null($name)){
					$this->setName($name);
				}

				if(!is_null($pass)){
					$this->pass	=	$pass;
				}

			}

			public function setName($name=NULL){

				$this->name	=	\apf\validate\String::mustBeNotEmpty($name);

			}

			public function getName(){

				return $this->name;

			}

			public function setPass($pass=NULL){

				$this->pass	=	$pass;

			}

			public function getPass(){

				return $this->pass;

			}

			public function __toString(){

				return (string)$this->name;

			}

		}

	}

?>
