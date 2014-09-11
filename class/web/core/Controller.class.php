<?php

	namespace apf\web\core{

		abstract class Controller{

			protected	$get			=	NULL;
			protected	$post			=	NULL;
			protected	$session		=	NULL;
			protected	$request		=	NULL;
			protected	$files		=	NULL;
			protected	$env			=	NULL;
			protected	$cookie		=	NULL;
			protected	$name			=	NULL;
			protected	$controller	=	NULL;
			protected	$action		=	NULL;
			protected	$js			=	Array();
			protected	$css			=	Array();
			protected	$raw			=	Array();

			public function construct(){
				
				$controller				=	substr(getclass($this),strlen(NAMESPACE)+1);
				$controller				=	strtolower(substr($controller,0,strpos($controller,"Controller")));
				$this->controller		=	$controller;

			}

			//Deberiamos obtener los templates dentro del directorio que se llame como el controlador

			public function getViewInstance(Array $templates=Array()){

				$view			=	new View($templates);

				$controller	=	getclass($this);
				$controller	=	substr($controller,strrpos($controller,"\\")+1);
				$controller	=	substr($controller,0,strpos($controller,"Controller"));

				$view->setVar("controller",$controller);
				$view->setVar("action",$this->action);
				$view->setVar("get",$this->get);
				$view->setVar("post",$this->post);
				$view->setVar("session",$this->session);
				$view->setVar("files",$this->files);
				$view->setVar("env",$this->env);
				$view->setVar("cookie",$this->cookie);

				//Additional files
				$view->setVar("js",$this->js);
				$view->setVar("css",$this->css);
				$view->setVar("raw",$this->raw);

				return $view;

			}

			public function setName($name){

				$this->name	=	$name;

			}

			public function setAction($action){

				$this->action	=	$action;

			}

			public function setGet(\stdClass $get){

				$this->get	=	$get;

			}

			public function setCookie(\stdClass $cookie){

				$this->cookie	=	$cookie;

			}


			public function setPost(\stdClass $post){

				$this->post	=	$post;

			}

			public function setEnv(\stdClass $env){

				$this->env	=	$env;

			}

			public function setSession(\stdClass $session){

				$this->session	=	$session;

			}

			public function setRequest(\stdClass $request){

				$this->request	=	$request;

			}

			public function setFiles(\stdClass $files){

				$this->files	=	$files;

			}

			public function setServer(\stdClass $server){

				$this->server	=	$server;

			}

		}	

	}

?>
