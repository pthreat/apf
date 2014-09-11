<?php

	namespace apf\core{

		class Config extends \apf\pattern\Singleton{

			public static function fromIniFile(\apf\core\File $file,$reload=FALSE){

				parent::$data	=	new \apf\parser\file\Ini($file);
				return parent::$data;

			}

			public static function __callStatic($method,$data){

				return call_user_func_array(array(parent::$data,$method),$data);

			}

		}

	}

?>
