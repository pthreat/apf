<?php

	namespace apf\db\mysql{

		class Adapter extends \apf\db\Adapter{

			public function reverse($directory=NULL,$format="php"){

				if(is_null($directory)){

					$directory	=	$this->getCacheDir();

				}

				$db	=	$this->connect();
				$data	=	$this->getConnectionData();

				$sql	=	"SELECT TABLE_NAME FROM information_schema.TABLES ".
							"WHERE TABLE_SCHEMA=?";

				$stmt	=	$db->prepare($sql);
				$stmt->setFetchMode(\PDO::FETCH_ASSOC);
				$stmt->execute(Array($data->getDatabase()));

				foreach($stmt as $result){

					$table	=	new \apf\db\mysql\Table($data->getId(),$data->getDatabase(),$result["TABLE_NAME"]);
					$export	=	$table->export();

					switch($format){

						case "json":
							$cacheData	=	json_encode($export);
						break;

						case "php":
						default:
							$cacheData	=	serialize($export);
						break;

					}

					file_put_contents($directory.DIRECTORY_SEPARATOR.$result["TABLE_NAME"].".$format",$cacheData);

				}

			}

		}

	}

?>
