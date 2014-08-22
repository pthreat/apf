<?php

	date_default_timezone_set('America/Toronto');

	set_include_path("/home/aidsql/apollo_framework/");

	//Http Adapter related
	//////////////////////////////////////////////////
	require "interface/Parser.interface.php";
	require "interface/Adapter.interface.php";
	require "interface/http/Adapter.interface.php";
	require "class/core/Adapter.class.php";
	require "class/parser/Uri.class.php";
	require "class/http/Adapter.class.php";
	require "class/http/adapter/Ecurl.class.php";
	require "class/web/Service.class.php";

	require "Api.class.php";
	require "Response.class.php";

	$httpAdapter	=	new \apolloFramework\http\adapter\Ecurl();
	$ustream			=	new \apolloFramework\web\service\livestream\Api($httpAdapter);
	$channel			=	$ustream->channel("phpancho")->info();
	var_dump($channel->isLive());
	


?>
