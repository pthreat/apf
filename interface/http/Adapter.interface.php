<?php

	namespace apolloFramework\http {

		interface AdapterInterface extends \apolloFramework\AdapterInterface{

			public function setHttpMethod($method);
			public function getHttpCode();
			public function save(\apolloFramework\core\File $file);
			
		}

	}

?>
