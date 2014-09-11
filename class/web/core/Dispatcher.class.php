<?php

	namespace apf\web\core{

		class Dispatcher{

			private	$_controllerPath	=	NULL;
			private	$_controllerFile	=	NULL;
			private	$_action				=	NULL;
			private	$_debug				=	FALSE;
			private	$_log					=	NULL;
			private	$_request			=	NULL;
			private	$_map					=	NULL;

			public function __construct(){

				$msgError	=	"Error obteniendo peticion HTTP para interpretar modelo MVC, error en el .htaccess?".
				"El archivo .htaccess en el raiz del directorio tiene que tener el siguiente formato:".
				"8<-----------------------------------------------------cortar\n".
				"RewriteEngine On\n".
				"RewriteBase /\n".
				'RewriteRule ^(css|javascript|img|fonts)($|/) - [L]'."\n".
				"RewriteCond %{REQUEST_FILENAME} !-f\n".
				"RewriteCond %{REQUEST_FILENAME} !-d\n".
				'RewriteRule ^(.*)$ index.php?request=$1 [L,QSA]'."\n";

				if(empty($_GET["request"])||!isset($_GET["request"])){

					$this->setController("index");
					$this->setAction("index");

					return;

				}

				$httpRequest		=	$_GET["request"];

				$this->_request	=	$httpRequest;

				$request				=	explode('/',$httpRequest);

				if(!$request||empty($request)){

					$controlador	=	"index";
					$accion			=	"index";
					$this->setController($controlador);
					$this->setAction($accion);
					return;

				}

				$accion	=	isset($request[1])	?	$request[1]	:	"index";
				$this->setController($controlador);
				$this->setAction($accion);

			}

			public function setDebug($bool=TRUE){

				$this->_debug	=	$bool;

			}
	
			public function getDebug(){

				return $this->_debug;

			}

			public function setController($controlador){

				$this->_controller	=	ucwords(preg_replace("/\W/",'',$controlador));

			}

			public function getController(){

				return $this->_controller;

			}

			public function setAction($action=NULL){

				$this->_action	=	$action;
	
			}

			public function getAction(){

				return $this->_action;

			}

			public function setControllerPath($path){

				$this->_controllerPath	=	$path;

			}

			public function getControllerPath(){

				return $this->_controllerPath;

			}

			private function _parsePathRequest(Array $request){

				if(!array_key_exists("request",$request)){

					return new \stdClass();

				}

				$request	=	explode('/',$request["request"]);

				$controller	=	$request[0];

				if(isset($request[1])){

					$action		=	$request[1];

				}

				unset($request[0]);
				unset($request[1]);

				$obj	=	new \stdClass();

				for($i=2;isset($request[$i]);$i++){

					if(empty($request[$i])){

						continue;

					}

					if(isset($request[$i+1])){

						$obj->$request[$i]	=	$request[$i+1];
						$i++;

					}else{


						$obj->$request[$i]	=	NULL;

					}

				}

				return $obj;

			}

			private function _parseRequest($request){

				if(!sizeof($request)){
					return new \stdClass();
				}

				if(is_array($request)){

					return (object)array_map(Array($this,__FUNCTION__),$request);

				}

				return $request;

			}


			public function map(\apf\web\Controller $controller,Array $map=Array()){

				if(!sizeof($map)){

					throw(new \Exception("Must provide dispatcher configuration"));

				}

				if(!empty($map["before"])){

					$before	=	$map["before"];

					if(!array_key_exists("action",$before)){

						throw(new \Exception("\"action\" key needed on map for controller $map[controller]"));
					}

					if(!method_exists($controller,$before["action"])){

						throw(new \Exception("before-method $map[before] does not exists"));

					}

					$ret	=	$controller->$before["action"]();

					if($ret){

						$action	=	$this->_action;
						$controller->$action();

						if(array_key_exists("success",$before)){

							if(!method_exists($controller,$before["success"])){

								throw(new \Exception("success-method \"$before[success]\" does not exists"));

							}

							$controller->$before["success"]();

						}

					}elseif($ret===FALSE){

						if(array_key_exists("failure",$before)){

							if(!method_exists($controller,$before["failure"])){

								throw(new \Exception("failure-method \"$before[failure]\" does not exists"));

							}

							$controller->$before["failure"]();

						}

					}

				}

			}

			private function getControllerMap($controller){

				foreach($this->_map as $controllerMap){

					if(!array_key_exists("controller",$controllerMap)){

						throw(new \Exception("Malformed controller map found"));

					}

					if(strtolower($controller)==$controllerMap["controller"]){

						return $controllerMap;

					}

				}

				return FALSE;

			}

			public function dispatch(Array $map=Array()){

				$this->_map		=	$map;

				$config			=	\apf\core\Config::getSection("controller");

				if(!$config){

					throw(new \Exception("Controller section not found in configuration"));

				}

				if(!isset($config->path)){

					throw(new \Exception("Controller path not found in configuration"));

				}

				$controller			=	$this->_controller."Controller";
				$controllerFile	=	"$controller.class.php";

				if(!file_exists($config->path.DIRECTORY_SEPARATOR.$controllerFile)){

					throw(new \Exception("Controller not found ".$this->_controller));

				}

				require $config->path.'/'.$controllerFile;

				$loadedClases			=	get_declared_classes();
				$controllerClass		=	"\\".array_pop($loadedClases);
				$controllerInstance	=	new $controllerClass();

				if(!is_a($controllerInstance,"\\apf\\web\\core\\Controller")){

					throw(new \Exception("Controllers must extend to \\apf\\web\\core\\Controller"));

				}

				$classMethods	=	get_class_methods($controllerInstance);
	
				if(!in_array("__call",$classMethods)){

					if(!in_array($this->_action,$classMethods)){

						throw(new \Exception("Undefined action in controller ".$this->_controller));

					}

				}

				$get		=	$this->_parsePathRequest($_GET);
				$controllerInstance->setGet($get);

				$post		=	$this->_parseRequest($_POST);
				$controllerInstance->setPost($post);

				$env	=	$this->_parseRequest($_ENV);
				$controllerInstance->setEnv($env);

				$cookie	=	$this->_parseRequest($_COOKIE);
				$controllerInstance->setCookie($cookie);

				if(isset($_SESSION)){

					$session	=	$this->_parseRequest($_SESSION);
					$controllerInstance->setSession($session);

				}

				if(isset($_REQUEST)){

					$request	=	$this->_parsePathRequest($_REQUEST);
					$controllerInstance->setRequest($request);

				}

				$server	=	$this->_parseRequest($_SERVER);
				$controllerInstance->setServer($server);

				$files	=	$this->_parseRequest($_FILES);
				$controllerInstance->setFiles($files);

				$controllerInstance->setName($this->_controller);
				$controllerInstance->setAction($this->_action);

				if(sizeof($this->_map)&&isAdmin()){

					$controllerMap	=	$this->getControllerMap($this->_controller);

					if($controllerMap){

						$this->map($controllerInstance,$controllerMap);
						return;

					}

				}

				$action	=	$this->_action;
				$controllerInstance->$action();

			}

		}

	}

?>
