<?php

	try{

		require	"boot.php";

		\apf\core\Boot::init("sample_config.ini");

		class Test{

			public function __construct($args){
			}

			public static function factoryMethod($args){

			}

		}

		$entries	=	new \apf\dbc\main\test\entries();

		foreach($entries->select()->columns("t1.ALL")->fetchAs("test","factoryMethod")->run() as $entry){
			echo $entry."\n";
		}

	}catch(\Exception $e){

		echo $e->getMessage()."\n";
		echo "**********************************************\n";
		echo $e->getTraceAsString()."\n";

	}


?>
