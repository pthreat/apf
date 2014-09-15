<?php

	namespace apf\web\core{

		class Boot extends \apf\core\Boot{

			public static function init($cfgFile=NULL,Array $classMap=Array(),$appClassDir=NULL){

				parent::init($cfgFile,$classMap,$appClassDir);

				$dispatcher		=	new \apf\web\core\Dispatcher();
				return $dispatcher;

			}

			public static function initAndDispatch($cfgFile,Array $paths=Array(),$appClassDir=NULL){

				return self::init($cfgFile,$paths,$appClassDir)->dispatch();

			}

		}

	}

?>
