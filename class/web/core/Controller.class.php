<?php

	namespace apolloFramework\web{

		class Controller{

			protected	$_get				=	NULL;
			protected	$_post			=	NULL;
			protected	$_session		=	NULL;
			protected	$_request		=	NULL;
			protected	$_files			=	NULL;
			protected	$_env				=	NULL;
			protected	$_cookie			=	NULL;
			protected	$_name			=	NULL;
			protected	$_controller	=	NULL;
			protected	$_action			=	NULL;
			protected	$_config			=	NULL;
			protected	$_js				=	Array();
			protected	$_css				=	Array();
			protected	$_raw				=	Array();

			public function __construct(){
				
				$controller				=	substr(get_class($this),strlen(__NAMESPACE__)+1);
				$controller				=	strtolower(substr($controller,0,strpos($controller,"Controller")));
				$this->_controller	=	$controller;

			}

			//Deberiamos obtener los templates dentro del directorio que se llame como el controlador

			public function getViewInstance(Array $templates=Array()){

				$view			=	new View($this->_config,$templates);

				$controller	=	get_class($this);
				$controller	=	substr($controller,strrpos($controller,"\\")+1);
				$controller	=	substr($controller,0,strpos($controller,"Controller"));

				$view->setVar("_controller",$controller);
				$view->setVar("_action",$this->_action);
				$view->setVar("_get",$this->_get);
				$view->setVar("_post",$this->_post);
				$view->setVar("_session",$this->_session);
				$view->setVar("_files",$this->_files);
				$view->setVar("_env",$this->_env);
				$view->setVar("_cookie",$this->_cookie);

				//Additional files
				$view->setVar("_js",$this->_js);
				$view->setVar("_css",$this->_css);
				$view->setVar("_raw",$this->_raw);

				foreach($this->_config as $section=>$values){

					if($section=="database"){
						continue;
					}

					$view->setVar($section,$values);

				}

				return $view;

			}

			public function setName($name){

				$this->_name	=	$name;

			}

			public function setAction($action){

				$this->_action	=	$action;

			}

			public function setGet(\stdClass $get){

				$this->_get	=	$get;

			}

			public function setCookie(\stdClass $cookie){

				$this->_cookie	=	$cookie;

			}


			public function setPost(\stdClass $post){

				$this->_post	=	$post;

			}

			public function setEnv(\stdClass $env){

				$this->_env	=	$env;

			}

			public function setSession(\stdClass $session){

				$this->_session	=	$session;

			}

			public function setRequest(\stdClass $request){

				$this->_request	=	$request;

			}

			public function setFiles(\stdClass $files){

				$this->_files	=	$files;

			}

			public function setServer(\stdClass $server){

				$this->_server	=	$server;

			}

			public function setConfig(\apolloFramework\core\Config $config){

				$this->_config	=	$config;

			}

		}	

	}

?>
