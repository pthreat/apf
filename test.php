<?php

	require	"class/core/Boot.class.php";

	\apf\core\Boot::init();

	class test{
		public function blah(){
		}
	}

	\apf\validate\Class_::mustHavePublicMethod("test","bla");

