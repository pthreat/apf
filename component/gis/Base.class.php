<?php

	namespace apf\component\gis{

		abstract class Base{

			private	$id	=	NULL;
			private	$name	=	NULL;

			public function __construct($id=NULL,$name=NULL){

				if(!is_null($id)){

					$this->setId($id);

				}

				if(!is_null($name)){

					$this->setName($name);

				}

			}

			public function setId($id=NULL){

				$this->id	=	\apf\Validator::intNum($id,Array("min"=>1));

			}

			public function getId(){

				return $this->id;

			}

			public function setName($name=NULL){

				$this->name	=	\apf\validate\String::mustBeNotEmpty($name);

			}

			public function getName(){

				return $this->name;

			}

			public function __toString(){

				return (string)$this->name;

			}

		}

	}

?>
