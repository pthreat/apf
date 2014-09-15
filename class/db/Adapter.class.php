<?php

	namespace apf\db{

		class Adapter extends \apf\core\Adapter{

			use \apf\traits\pattern\Mingleton{

				\apf\traits\pattern\Mingleton::getInstanceNames as listConnections;
				\apf\traits\pattern\Mingleton::getInstance as getConnection;

			}

			private	$pdoLink			=	NULL;
			private	$isConnected	=	FALSE;
			private	$data				=	NULL;

			private function __construct(\apf\type\db\Connection $connection){

				$this->data	=	$connection;	

				try{

					$this->addInstance($this,$connection->getId());

				}catch(\Exception $e){

					throw new \Exception("A connection named \"".$connection->getId()."\" already exists!");

				}

			}

			public function disconnect(){

				$this->pdoLink	=	NULL;

			}

			public static function connectAll(){

				foreach(self::listConnections as $name){
					self::getConnection($name)->connect();
				}

			}

			public static function testConnections(){

				$test	=	Array();

				foreach(self::listConnections() as $name){

					try{

						$connection	=	self::getConnection($name);
						$connection->connect();
						$test[$name]	=	Array("status"=>"OK","result"=>TRUE);
						$connection->disconnect();
					
					}catch(\Exception $e){

						$test[$name]	=	Array("status"=>"ERROR","result"=>$e->getMessage());

					}

				}

				return $test;

			}

			
			public function isConnected(){

				return (boolean)$this->isConnected;

			}

			public static function addConnection(\apf\type\db\Connection $connection){

				if(self::instanceExists(sprintf("%s",$connection))){

					throw(new \Exception("Connection ".$connection->getId()." already exists"));

				}

				$class	=	__CLASS__;
				$obj		=	new $class($connection);

			}

			public function getConnectionData(){

				return $this->data;

			}


			public static function getDrivers(){

				if(!class_exists("\\PDO")){

					throw(new \Exception("Class PDO doesn't exists, perhaps you should install/enable it?"));
				}

				return \PDO::getAvailableDrivers();
				
			}

			public static function isAvailableDriver($driver=NULL){

				\apf\Validator::emptyString($driver,"Driver name can't be empty");
				$drivers	=	self::getDrivers();

				return in_array($driver,$drivers);

			}

			public function connect($reconnect=FALSE){

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
			
		}

	}

?>
