<?php

	namespace apolloFramework{

		abstract class Adapter implements \apolloFramework\AdapterInterface {

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