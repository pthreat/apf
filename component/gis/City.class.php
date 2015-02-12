<?php

	namespace apf\component\gis{

		class City extends Base{

			private $region	=	NULL;

			public static function getInstanceById($id=NULL){

				$id		=	\apf\Validator::intNum($id,Array("min"=>1));
				$result	=	(new \apf\db\mysql5\Select("cities"))
								->fields(['id','name'])
								->where("id=$id");

				if(empty($result)){

					throw(new \Exception("No such city with id: $id"));

				}

				$class	=	__CLASS__;
				$obj		=	new $class($id,$result["name"]);

				return $obj;

			}

			public function setRegion(Region $region){

				$this->region	=	$region;

			}

			public function getRegion(){

				return $this->region;

			}

			public static function getInstanceByName(Region $region,$name=NULL){

				$id		=	\apf\validate\String::mustBeNotEmpty($name);

				$where	=	Array(	
											["field"=>"name","value"=>$name],
											["operator"=>"AND"],
											["field"=>"id_region","value"=>$region->getId()]
				);

				$result	=	(new \apf\db\mysql5\Select("cities"))
								->fields(['id','name'])
								->where($where)
								->execute();

				if(empty($result)){

					throw(new \Exception("No such region with given name"));

				}

				$class	=	__CLASS__;
				$obj		=	new $class($result["id"],$result["name"]);

				return $obj;

			}

		}

	}

?>
