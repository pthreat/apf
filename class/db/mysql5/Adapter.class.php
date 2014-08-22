<?php

	namespace db\mysql5{

		class Adapter{

			private	static		$connections	=	Array();
			private				$queryColor		=	"light_cyan";
			private				$logger			=	NULL;
			private				$verbose		=	0;
			private				$connection		=	NULL;
			private				$params			=	Array();

			private function __construct(Array $params){

				$mysqli	=	new \Mysqli($params["host"],$params["user"],$params["pass"],$params["name"],$params["port"],$params["socket"]);

				if($mysqli->connect_errno){

					throw(new \Exception("Couldn't connect to database $params[host]:$params[port] - $params[user]"));

				}

				$mysqli->set_charset("utf8");

				$this->connection	=	$mysqli;
				$this->params		=	$params;

			}

			public function setQueryColor($color=NULL){

				$this->queryColor	=	\apolloFramework\Validator::emptyString($color);

			}

			public function __get($attr){

				return $this->connection->$attr;

			}

			public function getQueryColor(){

				return $this->queryColor;
			
			}

			public function __clone(){

				throw(new \Exception("Cloning is not possible"));

			}


			public static function getInstance($params=NULL){

				if(!$params&&sizeof(self::$connections)){

					return self::$connections[0]["connection"];

				}

				if(is_a($params,"\\stdClass")){

					$params	=	(Array)$params;

				}elseif(!is_array($params)){

					throw(new \Exception("Given parameters to adapter should be an array \"".var_export($params,TRUE).'" given'));

				}

				$requiredKeys	=	Array("user","pass","name","port");
				\apolloFramework\Validator::arrayKeys($requiredKeys,$params);

				if(!isset($params["host"])){

					$params["host"]	=	"localhost";

				}

				if(!array_key_exists("socket",$params)){
					$params["socket"]	=	'';
				}

				$msg	=	"Params for connecting to a MySQL Database should be ".implode(',',$requiredKeys);

				\apolloFramework\Validator::arrayKeys($requiredKeys,$params,$msg);

				if(sizeof(self::$connections)){

					foreach(self::$connections as $connection){

						if($connection["user"]==$params["user"]&&$connection["host"]==$params["host"]){

							return $connection["connection"];

						}	

					}

				}

				$class	=	__CLASS__;

				$obj		=	new $class($params);

				self::$connections[]	=	Array(
														"connection"	=>	$obj,
														"user"			=>	$params["user"],
														"host"			=>	$params["host"]
				);

				return self::$connections[sizeof(self::$connections)-1]["connection"];

			}

			public function getDatabaseName(){
				return $this->params["name"];
			}

			public function setLog(\apolloFramework\core\Logger &$log){

				$log->setPrepend('['.__CLASS__.']');
				$this->logger	=	$log;

			}

			public function getLog(){
				return $this->logger;
			}

			private function log($msg=NULL, $color="white", $level=0, $toFile=FALSE) {

				if (!is_null($this->logger)) {

					$this->logger->setPrepend('[' . __CLASS__ . ']');
					$this->logger->log($msg, $color, $level, $toFile);
					return TRUE;

				}

				return FALSE;

			}

			public function setVerbose($verbose=TRUE){

				$this->verbose	=	$verbose;

			}

			public function getVerbose(){

				return $this->verbose;

			}

			public static function getDate($asObject=FALSE){

				$adapter	=	self::getInstance();
				$res		=	$adapter->directQuery("SELECT NOW() AS date");
				$res		=	$res->fetch_assoc();
				$date		=	$res["date"];

				if($asObject){

					$date	=	\DateTime::createFromFormat("Y-m-d H:i:s",$date);

				}

				return $date;
			
			}

			public function directQuery($sql){

				if(is_null($this->logger)&&defined("LOG_SQL")){

					$log	=	new \apolloFramework\core\Logger(NULL,FALSE);
					$log->setEcho(TRUE);
					$this->setLog($log);

				}

				if($this->verbose||defined("LOG_SQL")){

					$this->log($sql,0,$this->queryColor);

				}

				$result	=	$this->connection->query($sql);

				if($result===FALSE){

					throw(new \Exception($this->connection->error));

				}

				return $result;
			
			}

			public function __call($method,$args){

				return call_user_func_array(Array($this->connection,$method),$args);

			}

		}

	}

?>
