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

	$httpAdapter	=	new \apf\http\adapter\Ecurl();
	$ustream			=	new \apf\web\service\ustream\Api($httpAdapter);
	$ustream->setFormat("json");
	$ustream->setKey("2040345AD71816CA54680529F492827D");
	$ustream->setSubjectId("phpancho");
	$user	=	$ustream->channel();
	var_dump($user->__getInfo()->getStatus());
	echo $user."\n";
	


?>
