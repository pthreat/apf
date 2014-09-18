<?php

	namespace apf\db{

		abstract class Adapter implements \apf\iface\Adapter{

			private	$isConnected	=	FALSE;
			private	$data				=	NULL;
			private	$pdoLink			=	NULL;
			private	$debug			=	FALSE;
			private	$options			=	Array();

			use \apf\traits\pattern\Mingleton;
			use \apf\traits\InnerLog;

			public final function __construct(\apf\type\db\Connection $connection, Array $options=Array()){

				$this->data		=	$connection;
				$this->setOptions($options);

			}

			public function setOptions(Array $options=Array()){

				if(empty($options)){

					$this->options["cache_method"]	=	"none";
					return;

				}

				if(!array_key_exists("cache_method",$options)){

					throw new \Exception("Must specify cache method");

				}

				switch(strtolower($options["cache_method"])){

					case "none":
					case "memory":
						$this->options	=	$options;
					return;

					case "disk":
					case "class":

						if(empty($options["cache_dir"])){

							throw new \Exception("Must specify cache directory if using disk or class cache");

						}

						if($options["cache_method"]=="disk"){

							if(empty($options["cache_format"])){

								$options["cache_format"]	=	"php";

							}

							if($options["cache_format"]!=="json"&&$options["cache_format"]!=="php"){

								throw new \Exception("Unknown disk caching method $options[cache_format], must specify one of: json, php");
							}

						}

						$dir	=	trim($options["cache_dir"],DIRECTORY_SEPARATOR);
						$dir	=	$dir.DIRECTORY_SEPARATOR.
									$this->data->getId().DIRECTORY_SEPARATOR.
									$this->data->getDatabase();

						try{

							$dir	=	new \apf\core\Directory($dir);

							if(!$dir->exists()){

								$dir->create();

							}

						}catch(\Exception $e){

							throw new \Exception("Unable to create database cache directory:$dir| ".$e->getMessage());

						}

						$options["cache_dir"]	=	$dir;

					break;

					default:
						throw new \Exception("Cache method must be one of disk, class, memory or none");
					break;

				}

				if(!empty($options["log"])){

					if(!isset($options["log_to"])){

						$options["log_to"]	=	"stdout";

					}

					$this->logObj	=	new \apf\core\Log();
					$this->logObj->setEcho(FALSE);

					if($options["log_to"]=="stdout"||$options["log_to"]=="both"){

						$this->logObj->setEcho(TRUE);

					}

					if($options["log_to"]=="file"||$options["log_to"]=="both"){

						$doesntHasLogFile	=	empty($options["log_file"]);

						if($doesntHasLogFile){

							if($options["cache_method"]!=="memory"){

								$options["log_file"]	=	$options["cache_dir"].DIRECTORY_SEPARATOR.
																$this->data->getId().".log";

							}else{

								$options["log_file"]	=	sys_get_temp_dir().DIRECTORY_SEPARATOR.
																$this->data->getId().".log";

							}

						}

						$this->logObj->toFile($options["log_file"]);

					}


				}

				$this->options	=	$options;

			}

			public function getOptions(){

				return $this->options;

			}

			public function createTableClassCache($name,$recache=FALSE){

				$this->info("Selected cache method: class");

				if(empty($this->options["cache_dir"])){

					$msg	=	"Seems that you tried to bring a table from cache,".
								"however there's no cache dir for this connection!". 
								"Try reviewing your configuration!";

					throw new \Exception($msg);

				}

				$class		=	"\\apf\\db\\".$this->data->getDriver()."\\Table";

				$cacheFile	=	$this->options["cache_dir"].DIRECTORY_SEPARATOR.
									strtolower($name).'.class.php';

				$this->info("Checking if cache file exists: $cacheFile");

				$file			=	new \apf\core\File($cacheFile,$check=FALSE);

				if($file->exists()&&!$recache){

					$this->info("Cache file exists and no recache has been specified, exiting");
					return $cacheFile;

				}

				$this->info("Creating cache file: $cacheFile");

				$table	=	new $class($this->data->getId(),$this->data->getDatabase(),$name);
				$fqdn		=	"apf\\dbc\\".$this->data->getId();
				$tplPath	=	__DIR__.DIRECTORY_SEPARATOR."TableTemplate.class.php";

				$file		=	new \apf\core\File($tplPath,$check=FALSE);

				$bTable	=	"\\apf\\db\\".$this->data->getDriver()."\\Table";
				$columns	=	var_export($table->export(),TRUE);
				$columns	=	preg_replace("/[\\r\\n\s]/","",$columns);

				$tpl		=	$file->getContents();

				$tpl		=	preg_replace("/\[fqdn\]/",$fqdn,$tpl);
				$tpl		=	preg_replace("/\[basetable\]/",$bTable,$tpl);
				$tpl		=	preg_replace("/\[connectionId\]/",$this->data->getId(),$tpl);
				$tpl		=	preg_replace("/\[schema\]/",$this->data->getDatabase(),$tpl);
				$tpl		=	preg_replace("/\[tablename\]/",$name,$tpl);
				$tpl		=	preg_replace("/\[name\]/",$name,$tpl);
				$tpl		=	preg_replace("/\[alias\]/","",$tpl);
				$tpl		=	preg_replace("/\[columns\]/",$columns,$tpl);

				$file	=	new \apf\core\File($cacheFile,$check=FALSE);
				$file->setContents($tpl);
				$file->putContents();

				$this->info("Wrote class cache file $cacheFile");

				return $cacheFile;

			}

			private function getCachedTable($name,$from,$recache=FALSE){

				$class	=	"\\apf\\db\\".$this->data->getDriver()."\\Table";

				switch(strtolower($from)){

					case "memory":

						$table	=	new $class($this->data->getId(),$this->data->getDatabase(),$name);

						$this->info("Selected cache method: memory");

						$memoryTable	=	$this->findTableInMemory($name);

						if(!$this->findTableInMemory($name)||$recache){

							$this->info("Didn't find table $name in memory, caching");

							self::addTable($this->data->getId(),$this->data->getDatabase(),$table);

							return $table;
							
						}

						$this->success("Found $name in memory");

						return $memoryTable;

					break;

					case "class":

						return $this->createTableClassCache($name);

					break;

					case "disk":
					default:

						$table	=	new $class($this->data->getId(),$this->data->getDatabase(),$name);

						$this->info("Selected cache method: disk");

						$cacheFile	=	$this->options["cache_dir"].DIRECTORY_SEPARATOR.
											$table->getName().'.'.$this->options["cache_format"];

						$this->info("Cache file: $cacheFile");

						if(!$table->exists()){

							if(file_exists($cacheFile)){

								unlink($cacheFile);

							}

							throw new \Exception("No such table $name, perhaps you might want to try recaching this table since it's obvious your database structure has changed!");

						}

						if(file_exists($cacheFile)&&!$recache){

							switch($this->options["cache_format"]){

								case "php":

									$data		=	@unserialize(file_get_contents($cacheFile));

									if(!$data){
										throw new \Exception("Detected corrupted cache file! $cacheFile");
									}

									$table->setColumns($data);

									return $table;

								break;

								case "json":

									$data		=	@json_decode(file_get_contents($cacheFile));

									if(!$data){
										throw new \Exception("Detected corrupted cache file! $cacheFile");
									}

									$table->setColumns($data);

									return $table;

								break;

							}

						}

						//Adding disk cache or recaching

						switch($this->options["cache_format"]){

							case "json":
								$cacheContents	=	json_encode($table->export());
							break;

							case "php":
								$cacheContents	=	serialize($table->export());
							break;


						}

						$file	=	new \apf\core\File($cacheFile,$check=FALSE);
						$file->setContents($cacheContents);
						$file->putContents();

						return $table;

					break;

				}

			}

			private function findTableInMemory($name){

				if(empty(self::$instances[$this->data->getId()][$this->data->getDatabase()]["tables"])){

					return FALSE;

				}

				$memoryCache	=	&self::$instances[$this->data->getId()][$this->data->getDatabase()]["tables"];

				foreach($memoryCache as $cname=>$cache){

					if($cname==$name){
						return $cache;
					}

				}

				return FALSE;

			}

			public function getCacheType(){

				return $this->cacheType;

			}

			private static function addTable($connectionId,$schema,\apf\db\Table $table){

				return self::$instances[$connectionId][$schema]["tables"][$table->getName()]	=	$table;

			}

			public final function getTable($name=NULL,$cacheMethod=NULL,$recache=FALSE){

				$cacheMethod	=	is_null($cacheMethod) ? $this->options["method"]	:	$cacheMethod;
				return $this->getCachedTable($name,$cacheMethod,$recache);

			}

			public final function getConnectionData(){

				return $this->data;

			}

			public final function disconnect(){

				$this->pdoLink	=	NULL;

			}

			public function getSelect(){

				switch($this->data->getDriver()){
				}

			}

			public final function query($sql){

				$this->connect();
				return $this->pdoLink->query($sql);

			}

			public final static function getDrivers(){

				if(!class_exists("\\PDO")){

					throw(new \Exception("Class PDO doesn't exists, perhaps you should install/enable it?"));
				}

				return \PDO::getAvailableDrivers();
				
			}

			public final static function isAvailableDriver($driver=NULL){

				\apf\Validator::emptyString($driver,"Driver name can't be empty");
				$drivers	=	self::getDrivers();

				return in_array($driver,$drivers);

			}

			public final function isConnected(){

				return (boolean)$this->isConnected;

			}

			public final function connect($reconnect=FALSE){

				if($this->isConnected&&!$reconnect){

					return $this->pdoLink;

				}

				$user					=	$this->data->getUser();
				$pdoString			=	$this->data->getPDOConnectionString();
				$this->pdoLink		=	new \PDO($pdoString,$user->getName(),$user->getPass());

				return $this->pdoLink;

			}

			public function setUser(\apf\type\User $user){}
			public function setConnectTimeout($timeout){}
			public function getConnectTimeout(){}
			public function setProxyServer($server){}
			public function setProxyPort($port){}
			public function setProxyAuth($auth){}
			public function setProxyType($type){}
			public function setLog(\apf\core\Logger &$log){}
			public function getVersion(){}

			//Every database has a different way of reversing tables
			//And thats one of the reasons why every database has it's own adapter
			abstract public function reverse($directory=NULL,$format="php");

			public final function __call($method,$args){

				$this->connect();
				return call_user_func_array(Array($this->pdoLink,$method),$args);

			}

		}

	}

?>
