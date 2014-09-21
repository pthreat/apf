#!/usr/bin/php
<?php

	error_reporting(E_ALL);

	require "/home/phpescas/apf/boot.php";

	try{

		$configs	=	Array("/home/phpescas/newrss/front/config.ini");

		\apf\core\Boot::init($configs);
		$noti	=	new \apf\dbc\elibertador\newrss\noticias();
		//$test	=	new \apf\db\mysql\Select("elibertador:newrss:noticias");
		var_dump($noti);
		die();
		echo $test->getSQL();
		die();

		echo $noti->getSQL();
		var_dump($noti->select());
		die();
		$db	=	\apf\db\Pool::getConnection("elibertador")->getTable("noticias","memory");
		$db	=	\apf\db\Pool::getConnection("elibertador")->getTable("noticias","class");

	}catch(\Exception $e){

		echo $e->getMessage();
		echo $e->getTraceAsString()."\n";

	}

?>
