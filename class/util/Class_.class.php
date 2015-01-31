<?php

		namespace apf\util{


			class Class_{
				
				public static function removeNamespace($class){

					$separator	=	'\\';
					return substr($class,strrpos($class,$separator)+1);

				}

			}

		}

?>
