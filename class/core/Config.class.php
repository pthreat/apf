<?php

	namespace apolloFramework\core{


		class Config{

			private	$_confFile	=	NULL;
			private	$_config		=	Array();

			public function __construct(\apolloFramework\core\File $file,$section=NULL){

				$this->setConfigFile($file,$section);

			}

			public function setConfigFile(\apolloFramework\core\File $file,$section){
	
				$this->_config	=	parse_ini_file($file,TRUE);

				foreach($this->_config as $k=>$v){

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

					throw(new \Exception("La seccion $section no esta definida en el archivo de configuracion"));

				}

				$param	=	trim($param);

				if(!isset($this->$section->$param)){
					return NULL;
				}

				return $this->$section->$param;

			}

			public function getSection($section){

				if(!isset($this->$section)){

					throw(new \Exception("La seccion $section no esta definida en el archivo de configuracion"));

				}

				return $this->$section;

			}

		}

	}

?>
