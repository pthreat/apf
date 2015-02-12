<?php

	namespace apf\component\gis{

		class Location{

			private	$country	=	NULL;
			private	$region	=	NULL;
			private	$city		=	NULL;
			private	$lat		=	NULL;
			private	$lon		=	NULL;

			public function __construct($country,$region,$city,$lat,$lon){

				$this->setCountry($country);
				$this->setRegion($region);
				$this->setCity($city);

			}

			public static function getByIp($ip=NULL){

				\apf\validate\String::mustBeNotEmpty("IP address can't be empty");

				if(!ip2long($ip)){

					throw(new \Exception("Invalid IP address"));

				}

				if(!function_exists("geoip_record_by_name")){

					throw(new \Exception("Function geoip_record_by_name doesn't exists, missing geoip library?"));

				}


				$country	=	NULL;
				$region	=	NULL;
				$city		=	NULL;

				$geoip	=	geoip_record_by_name($ip);

				if(!empty($geoip["country_name"])){

					try{

						$country	=	Country::getInstanceByName($geoip["country_name"]);

					}catch(\Exception $e){


						$country	=	new Country();
						$country->setName("Desconocido");

					}

				}else{

					$country	=	new Country();
					$country->setName("Desconocido");

				}

				if(!empty($geoip["region"])&&$country->getId()){

					$region	=	geoip_region_name_by_code($geoip["country_code"],$geoip["region"]);

					if($country->getName()=="Argentina"&&$region="Distrito Federal"){

						$region	=	"Capital Federal";

					}

					try{

						$region	=	Region::getInstanceByName($country,$region);

					}catch(\Exception $e){

						$region	=	new Region();
						$region->setName("Desconocida");
						$region->setCountry($country);

					}

				}else{

					$region	=	new Region();
					$region->setName("Desconocida");

				}

				if(!empty($geoip["city"])&&$region->getId()){

					try{

						$city	=	City::getInstanceByName($region,$geoip["city"]);

					}catch(\Exception $e){

						$city	=	new City();
						$city->setName("Desconocida");
						$city->setRegion($region);

					}

				}else{

					$city	=	new City();
					$city->setName("Desconocida");

				}

				if(!empty($geoip["latitude"])){

					$data["latitude"]	=	$geoip["latitude"];

				}

				if(!empty($geoip["longitude"])){

					$data["longitude"]	=	$geoip["longitude"];

				}

				$class	=	__CLASS__;
				$obj		=	new $class($country,$region,$city,$geoip["latitude"],$geoip["longitude"]);

				return $obj;

			}

			public function setLatitude($lat=NULL){

				$this->lat	=	\apf\Validator::intNum($lat,Array("min"=>0));

			}

			public function getLatitude(){

				return $this->lat;

			}

			public function setLongitude($lon=NULL){

				$this->lon	=	\apf\Validator::intNum($lon,Array("min"=>0));

			}

			public function getLongitude(){

				return $this->lon;

			}

			public function setCountry(Country $country){

				$this->country	=	$country;

			}

			public function getCountry(){

				return $this->country;

			}

			public function setRegion(Region $region){

				$this->region	=	$region;

			}

			public function getRegion(){

				return $this->region;

			}

			public function setCity(City $city){

				$this->city	=	$city;

			}

			public function getCity(){

				return $this->city;

			}

			public function __toString(){

				return (string)$this->country->getName().','.$this->region->getName().','.$this->city->getName();

			}

		}

	}

?>
