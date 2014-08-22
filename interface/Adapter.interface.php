<?php

	namespace apolloFramework{

		interface AdapterInterface {

			public function connect();
			public function setUser($user);
			public function setPassword($password);
			public function setConnectTimeout($timeout);
			public function getConnectTimeout();
			public function setProxyServer($server);
			public function setProxyPort($port);
			public function setProxyAuth($auth);
			public function setProxyType($type);
			public function setLog(\apolloFramework\core\Logger &$log);
			public function getName();
			public function getType();
			public function getVersion();
			public function getRequestCount();
			public function setRequestInterval($interval);
			public function getRequestInterval();
			public function setUri(\apolloFramework\parser\Uri $uri);
			public function getUri();
			
		}

	}

?>
