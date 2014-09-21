<?php

	date_default_timezone_set('America/Toronto');

	//Http Adapter related
	//////////////////////////////////////////////////

	$httpAdapter	=	new \apf\http\adapter\Ecurl();
	$ustream			=	new \apf\web\service\livestream\Api($httpAdapter);
	$channel			=	$ustream->channel("yourchannel")->info();
	var_dump($channel->isLive());
	


?>
