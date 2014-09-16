<?php

	namespace apf\core{

		class Boot{

			private static $paths			=	Array();
			private static $frameworkDir	=	NULL;
			private static	$namespaceMap	=	Array();
			private static $appDir			=	NULL;
			private static $appClassDir	=	NULL;

			private static function addNamespaceMap(Array $map){

				\apf\Validator::arrayKeys(Array("dir","namespace"),$map);
				$map["dir"]	=	\apf\Validator::emptyString($map["dir"]);
				self::$namespaceMap[]	=	$map;

			}

			private static function isMapped($namespace){

				foreach(self::$namespaceMap as $map){

					if($map["namespace"]==$namespace){

						return $map;

					}

				}

				return FALSE;

			}

			public static function autoLoad($class){

				$isAPFClass	=	substr($class,0,strpos($class,"\\"))=="apf";

				if(!$isAPFClass){
				
					$map	=	self::isMapped(substr($class,0,strrpos($class,'\\')));

					if(!$map){

						$msg	=	"Don't know how to autoload class $class, since no mapping has been ".
									"provided for such namespace";

						throw(new \Exception($msg));

					}

					$class	=	implode('/',explode("\\",$class));

					$file		=	self::$appClassDir.DIRECTORY_SEPARATOR.
									$map["dir"].DIRECTORY_SEPARATOR.
									basename($class).".class.php";

					if(!file_exists($file)){

						throw(new \Exception("Class file not found $file, incorrect mapping?"));

					}

					require $file;

					return;

				}


				$class	=	trim(substr($class,strpos($class,"\\")+1));
				$path		=	explode("\\",$class);
				$class	=	implode('/',$path);

				switch($path[0]){

					case "component":
						$file		=	self::$frameworkDir.DIRECTORY_SEPARATOR.
						$class.".class.php";
					break;

					case "iface":

						unset($path[0]);

						$class	=	implode('/',$path);

						$file		=	self::$frameworkDir.DIRECTORY_SEPARATOR.
										"interface".DIRECTORY_SEPARATOR.
										$class.".interface.php";
					break;

					case "traits":

						$path[0]	=	"trait";


						$class	=	implode('/',$path);

						$file		=	self::$frameworkDir.DIRECTORY_SEPARATOR.
										$class.".trait.php";
						
					break;


					case "class":
					default:
						$file		=	self::$frameworkDir.DIRECTORY_SEPARATOR.
										"class".DIRECTORY_SEPARATOR.
										$class.".class.php";
					break;

				}

				if(!file_exists($file)){

					throw(new \Exception("File $file not found"));

				}

				return require	$file;

			}

			public static function init($cfgFile=NULL,Array $classMap=Array(),$appClassDir=NULL){

				spl_autoload_register(__CLASS__."::autoLoad");

				self::$appDir			=	dirname($_SERVER["SCRIPT_FILENAME"]);
				self::$frameworkDir	=	substr(dirname(__FILE__),0,strrpos(dirname(__FILE__),'class'));
				self::$frameworkDir	=	rtrim(self::$frameworkDir,DIRECTORY_SEPARATOR);

				//Assume that it's just one directory up the appDir

				if(is_null($appClassDir)){

					$appClassDir	=	substr(self::$appDir,0,strrpos(self::$appDir,DIRECTORY_SEPARATOR));

				}

				self::$appClassDir	=	$appClassDir;

				if(sizeof($classMap)){

					foreach($classMap as $data){

						self::addNamespaceMap($data);

					}

				}

				if(!is_null($cfgFile)){

					if(!is_array($cfgFile)){

						$cfgFile	=	Array($cfgFile);

					}

					foreach($cfgFile as $config){


						//Initialize configuration class
						\apf\core\Config::fromIniFile(new \apf\core\File($config));

						$dbConfig		=	\apf\core\Config::getSectionsLike("database");

						if($dbConfig){

							foreach($dbConfig as $dbName=>$options){

								$options->id	=	(isset($options->id))	?	$options->id	:	$dbName;

								//Do NOT connect unless it's required
								//In this fashion we just add a connection
								//but we do NOT connect unless we are required to do so, we just 
								//add the connection options.

								try{

									$connectionData	=	\apf\type\db\Connection::create($options);

								}catch(\Exception $e){

									throw new \Exception("Error in config file $config, section $dbName: ".$e->getMessage());

								}

								if(!isset($options->cache_method)){

									$options->cache_method	=	"disk";

								}

								if(!isset($options->cache_dir)){

									$options->cache_dir	=	self::$appDir.DIRECTORY_SEPARATOR.
																	"cache".DIRECTORY_SEPARATOR.
																	"db".DIRECTORY_SEPARATOR;

								}

								try{

									$db	=	\apf\db\Pool::addConnection($connectionData,(Array)$options);

								}catch(\Exception $e){

									throw new \Exception("Error in config file $config, section $dbName: ".$e->getMessage());
								}

							}

						}

					}

				}

			}

			public function getAppDir(){

				return self::$appDir;

			}

		}

	}

?>
