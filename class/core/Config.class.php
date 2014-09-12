<?php

	namespace apf\core{

		class Config {

			use \apf\pattern\Singleton;

			public static function fromIniFile(\apf\core\File $file,$reload=FALSE){

				self::$instance	=	new \apf\parser\file\Ini($file);
				return self::$instance;

			}

		}

	}

?>
