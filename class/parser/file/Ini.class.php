<?php

	namespace apf\parser\file{

		class Ini{

			private	$_iniFile	=	NULL;
			private	$_data		=	Array();

			public function __construct(\apf\core\File $file,$section=NULL){

				$this->setFile($file,$section);

			}

			public function setFile(\apf\core\File $file,$section){
	
				$this->_data	=	parse_ini_file($file,TRUE);

				foreach($this->_data as $k=>$v){

					if(!is_null($section)){

						if($k!==$section){
							continue;
						}

						foreach($v as $key=>$val){

							$this->$key=$val;

						}

					}

					$this->$k	=	new \StdClass();

					foreach($v as $key=>$val){

						$this->$k->$key=$val;

					}

				}

			}

			public function getParamValue($section,$param){

				if(!isset($this->$section)){

					throw(new \Exception("La seccion $section no esta definida en el archivo ini"));

				}

				$param	=	trim($param);

				if(!isset($this->$section->$param)){

					return NULL;

				}

				return $this->$section->$param;

			}

			public function getSection($section){

				if(!isset($this->$section)){

					throw(new \Exception("La seccion $section no esta definida en el archivo ini"));

				}

				return $this->$section;

			}

			public function getSectionsLike($like=NULL){

				$like	=	trim($like,'/');

				\apf\Validator::emptyString($like,"Expecting regex");

				$like			=	preg_quote($like,'/');
				$like			=	"/$like/";
				$sections	=	get_object_vars($this);

				$return		=	Array();

				foreach($sections as $name=>$values){

					if(preg_match($like,$name)){

						$return[$name]	=	$values;

					}

				}

				return $return;

			}

		}

	}

?>
