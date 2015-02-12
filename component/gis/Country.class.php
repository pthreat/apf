<?php

	namespace apf\component\gis{

		class Country extends Base{

			public static function getInstanceById($id=NULL){

				$id		=	\apf\Validator::intNum($id,Array("min"=>1));
				$country	=	(new \apf\db\mysql5\Select("countries"))->where("id=$id")->execute();

				if(empty($country)){

					throw(new \Exception("No such country with id: $id"));

				}

				$this->setId($id);
				$this->setNombre($country["name"]);

				$class	=	__CLASS__;
				$obj		=	new $class($id,$result["name"]);

				return $obj;

			}

			public static function getInstanceByName($name=NULL){

				$name		=	\apf\validate\String::mustBeNotEmpty($name);
				$result	=	(new \apf\db\mysql5\Select("countries"))
								->fields(['id','name'])
								->where("name=$name")
								->execute();

				if(empty($result)){

					throw(new \Exception("No such country with given name"));

				}

				$class	=	__CLASS__;
				$obj		=	new $class($result["id"],$result["name"]);
				return $obj;

			}


		}

	}

?>
