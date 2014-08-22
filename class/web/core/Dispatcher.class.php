<?php

	namespace apolloFramework\web\core{

		class Dispatcher{

			private	$_controllerPath	=	NULL;
			private	$_controllerFile	=	NULL;
			private	$_action				=	NULL;
			private	$_debug				=	FALSE;
			private	$_log					=	NULL;
			private	$_config				=	NULL;
			private	$_request			=	NULL;
			private	$_map					=	NULL;

			public function __construct(\apolloFramework\core\Config $config){

				$msgError	=	"Error obteniendo peticion HTTP para interpretar modelo MVC, error en el .htaccess?".
				"El archivo .htaccess en el raiz del directorio tiene que tener el siguiente formato:".
				"8<-----------------------------------------------------cortar\n".
				"RewriteEngine On\n".
				"RewriteBase /\n".
				'RewriteRule ^(css|javascript|img|fonts)($|/) - [L]'."\n".
				"RewriteCond %{REQUEST_FILENAME} !-f\n".
				"RewriteCond %{REQUEST_FILENAME} !-d\n".
				'RewriteRule ^(.*)$ index.php?request=$1 [L,QSA]'."\n";

				$this->_config	=	$config;


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

				}else{

					$controlador	=	preg_replace("/\W/",'',$request[0]);

					if(isset($request[1])){

						$accion	=	$request[1];

					}else{

						$accion	=	"index";

					}

				}

				$this->setController($controlador);
				$this->setAction($accion);
				$this->setConfig($config);

			}

			public function setLog(){
			}

			public function setDebug($bool=TRUE){

				$this->_debug	=	$bool;

			}
	
			public function getDebug(){

				return $this->_debug;

			}

			public function setControllerDir($directory=NULL){

				if(!is_dir($directory)){

					throw(new \Exception("Controller directory doesn't exists"));

				}

			}

			public function setController($controlador){

				$this->_controller			=	ucwords($controlador);

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

			public function setConfig(\apolloFramework\core\Config $config){

				$this->_config	=	$config;

			}

			public function setControllerPath($path){

				$this->_controllerPath	=	$path;

			}

			public function getControllerPath(){

				return $this->_controllerPath;

			}

			private function _parsePathRequest($request){

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

			private function _parseRequest(Array $request){

				return arrayToObject($request);

			}


			public function map(\apolloFramework\web\Controller $controller,Array $map=Array()){

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

				$controllers	=	$this->_config->getSection("controllers");
				$this->setControllerPath($controllers->path);

				$controller						=	$this->_controller."Controller";
				$archivoClaseControlador	=	"$controller.class.php";

				if(!file_exists($this->_controllerPath.DIRECTORY_SEPARATOR.$archivoClaseControlador)){

					throw(new \Exception("El controlador $this->_controller  NO existe"));

				}

				require $this->_controllerPath.'/'.$archivoClaseControlador;

				$claseControlador				=	'\\apolloFramework\\web\\'.$controller;
				$objControlador				=	new $claseControlador;
				$classMethods					=	get_class_methods($objControlador);
	
				if(!in_array("__call",$classMethods)){

					if(!in_array($this->_action,$classMethods)){

						$this->_action	=	"index";

					}

				}

				$get		=	$this->_parsePathRequest($_GET);
				$objControlador->setGet($get);

				$post		=	$this->_parseRequest($_POST);
				$objControlador->setPost($post);

				$env	=	$this->_parseRequest($_ENV);
				$objControlador->setEnv($env);

				$cookie	=	$this->_parseRequest($_COOKIE);
				$objControlador->setCookie($cookie);

				if(isset($_SESSION)){

					$session	=	$this->_parseRequest($_SESSION);
					$objControlador->setSession($session);

				}

				if(isset($_REQUEST)){

					$request	=	$this->_parsePathRequest($_REQUEST);
					$objControlador->setRequest($request);

				}

				$server	=	$this->_parseRequest($_SERVER);
				$objControlador->setServer($server);

				$files	=	$this->_parseRequest($_FILES);
				$objControlador->setFiles($files);
				//unset($_FILES);

				$objControlador->setName($this->_controller);
				$objControlador->setAction($this->_action);
				$objControlador->setConfig($this->_config);

				if(sizeof($this->_map)&&isAdmin()){

					$controllerMap	=	$this->getControllerMap($this->_controller);

					if($controllerMap){

						$this->map($objControlador,$controllerMap);
						return;

					}

				}

				$action	=	$this->_action;
				$objControlador->$action();

			}

		}

	}

?>
