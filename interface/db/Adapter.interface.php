<?php

	namespace apf\iface\db {

		interface Adapter extends \apf\iface\Adapter {
			public function setDatabaseName($name);
			public function setPort($port);
		}

	}
?>
