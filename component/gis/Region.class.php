<?php

	namespace apf\component\gis{

		class Region extends Base{

			private $country	=	NULL;

			public static function getInstanceById($id=NULL){

				$id		=	\apf\Validator::intNum($id,Array("min"=>1));

				$result	=	(new \apf\db\mysql5\Select("regions"))
								->fields(["id","name"])
								->where("id=$id")
								->execute();

				if(empty($result)){

					throw(new \Exception("No such region with id: $id"));

				}

				$class	=	__CLASS__;
				$obj		=	new $class($id,$result["name"]);

				return $obj;

			}

			public function setCountry(Country $country){

				$this->country	=	$country;

			}

			public function getCountry(){

				return $this->country;

			}

			public static function getInstanceByName(Country $country,$name=NULL){

				$name		=	\apf\Validator::emptyString($name);

				$where	=	Array(	
											["field"=>"name","value"=>$name],
											["operator"=>"AND"],
											["field"=>"id_country","value"=>$country->getId()]
				);

				$result	=	(new \apf\db\mysql5\Select("regions"))
								->fields(["id","name"])
								->where($where)
								->execute();

				if(empty($result)){

					throw(new \Exception("No such region with given name"));

				}

				$class	=	__CLASS__;
				$obj		=	new $class($result["id"],$result["name"]);
				$obj->setCountry($country);

				return $obj;

			}

		}

	}

?>
