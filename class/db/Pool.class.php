<?php

	namespace apf\db{

		class Pool{

			use \apf\traits\pattern\Mingleton{

				\apf\traits\pattern\Mingleton::getInstanceNames as listConnections;
				\apf\traits\pattern\Mingleton::getInstance as getConnection;

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

			public static function addConnection(\apf\type\db\Connection $connection,Array $options=Array()){

				if(self::instanceExists(sprintf("%s",$connection))){

					throw(new \Exception("Connection ".$connection->getId()." already exists"));

				}

				switch($connection->getDriver()){

					case "mysql":
						$obj		=	new \apf\db\mysql\Adapter($connection,$options);
						self::addInstance($obj,$connection->getId());
					break;

					default:
						throw(new \Exception("No adapter available for ".$connection->getDriver." sorry"));
					break;

				}

			}


		}

	}

?>
