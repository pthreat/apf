<?php

	namespace apf\core{

		abstract class Adapter implements \apf\iface\Adapter{

			public function getVersion(){

 	       	$constant   =  "static::ADAPTER_VERSION";

				if(defined($constant)){

					return constant($constant);

				}

				return "UNKNOWN";

			}

			public function getName(){

 		     	$constant   =  "static::ADAPTER_NAME";

				if(defined($constant)){

					return constant($constant);

				}

				return "UNKNOWN";

			}

		}

	}

?>
