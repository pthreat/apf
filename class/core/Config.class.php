<?php

	namespace apf\core{

		class Config {

			use \apf\traits\pattern\Mingleton;

			private function __construct($name,$value){

				$this->addInstance($value,$name);

			}

			public static function fromIniFile($file){

				if(!($file instanceof \apf\core\File)){

					$file	=	new \apf\core\File($file);

				}

				$config		=	new \apf\parser\file\Ini($file);

				//This is in case there are two config files named the same way
				//since we use the basename of the file, this can happen 
				//When the user loads two config files with the same name.
				//So in this way, we provide the user for a way to name his config files
				//by adding a section named apf to his config file and by specifying 
				//it's config name through the config_name parameter
				//that is to say [apf] config_name = some_name

				try{

					$apfSection	=	$config->getParamValue("apf","config_name");
					$name			=	$apfSection;

				}catch(\Exception $e){

					$name	=	$file->getBaseName();

				}

				$class		=	__CLASS__;
				$instance	=	new $class("ini:$name",$config);
				return $instance;

			}

			//Since we will support a variety of config files such as JSON,YAML or other formats
			//The user must specify the type of configuration he wants. So in this fashion
			//we add methods such as getIniConfig, getJSONConfig .. etc

			public function getIniConfig($name=NULL){

				\apf\validate\String::mustBeNotEmpty($name,"Must provide config name");
				return self::getInstance("ini:$name");

			}

		}

	}

?>
