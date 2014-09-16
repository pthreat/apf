<?php

	namespace apf\iface{

		interface Log{

			public function error($message);
			public function warning($message);
			public function info($message);
			public function emergency($message);
			public function debug($message);
			public function success($message);

		}

	}

?>
