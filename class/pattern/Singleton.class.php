<?php
	namespace apf\pattern{

		abstract class Singleton{

			protected static $data	=	NULL;

			public function __construct(){

				throw(new \Exception("Can't make instance of singleton class"));

			}

			public function __clone(){

				throw(new \Exception("Can't clone singleton class"));

			}

			//To be redefined by child classes but added by default
			public static function getInstance(){

				if(self::$data){

					return self::$data;

				}

			}

		}

	}

?>
