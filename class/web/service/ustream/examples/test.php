<?php

	date_default_timezone_set('America/Toronto');

	$httpAdapter	=	new \apf\http\adapter\Ecurl();
	$ustream			=	new \apf\web\service\ustream\Api($httpAdapter);
	$ustream->setFormat("json");
	$ustream->setKey("yourkey");
	$ustream->setSubjectId("yournickname");
	$user	=	$ustream->channel();
	var_dump($user->__getInfo()->getStatus());
	echo $user."\n";
	


?>
