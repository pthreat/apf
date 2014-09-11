<?php

	namespace apf\iface\http {

		interface Adapter extends \apf\iface\Adapter{

			public function setHttpMethod($method);
			public function getHttpCode();
			public function save(\apf\core\File $file);
			
		}

	}

?>
