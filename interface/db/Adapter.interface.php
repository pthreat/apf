<?php

	namespace apolloFramework\db {

		interface AdapterInterface extends \apolloFramework\AdapterInterface {
			public function setDatabaseName($name);
			public function setPort($port);
		}

	}
?>
