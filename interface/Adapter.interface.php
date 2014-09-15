<?php

	namespace apf\iface{

		interface Adapter{

			public function connect();
			public function setUser(\apf\type\User $user);
			public function setConnectTimeout($timeout);
			public function getConnectTimeout();
			public function setProxyServer($server);
			public function setProxyPort($port);
			public function setProxyAuth($auth);
			public function setProxyType($type);
			public function setLog(\apf\core\Logger &$log);
			public function getVersion();

		}

	}

?>
