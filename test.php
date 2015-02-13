<?php

	require	"class/core/Boot.class.php";

	\apf\core\Boot::init();

	abstract class test{
		public $prop=NULL;

		public function blah(){
		}

		abstract public static function bleh();
		private function a(){
		}
	}

	var_dump(\apf\validate\Class_::hasMethod("test","bleh",'abstract'));

