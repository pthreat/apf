<?php
	/**
	*Enforced Array Structure 
	*/
	namespace apf{

		abstract class Vvector extends \apf\type\Vector{

			public function __construct($value='',$cast=FALSE){

				parent::__construct($value,$cast);

				$this->validate();

			}

			protected final function validate(){

				if(!isset($this->enforce)){

					throw(new \Exception("enforce property must be set"));

				}

				if(!is_array($this->enforce)){

					throw(new \Exception("enforce property must be an Array"));

				}

				if(!sizeof($this->enforce)){

					throw(new \Exception("enforce property must have content in order to validate the structure"));

				}

				foreach($this->enforce as $keyName=>$enforce){

					$value	=	&$this->value;

					if(!isset($enforce["types"])){

						throw(new \Exception("Must specify types for key $keyName"));

					}

					//Assume this key is required
					if(!isset($enforce["required"])){

						$enforce["required"]	=	TRUE;

					}

					$keyExists		=	array_key_exists($keyName,$value);

					//If the key is not set in the value just fill it up with NULL
					if(!$keyExists){
						$value[$keyName]	=	NULL;
					}

					$typeOfValue	=	($keyExists) ? gettype($value[$keyName]) : NULL;

					if($enforce["required"]&&!$keyExists){

						throw(new \Exception("Missing key $keyName"));

					}

					if(isset($enforce["empty"])&&$enforce["empty"]===FALSE&&$keyExists&&empty($value[$keyName])){
						throw(new \Exception("Value for key $keyName can not be empty"));
						
					}


					if($keyExists&&isset($enforce["types"])&&is_array($enforce["types"])){

						if(!sizeof($enforce["types"])){

							throw(new \Exception("If the types key is an array you should put something inside"));
						}

						if(!in_array($typeOfValue,$enforce["types"])){

							throw new \Exception("Key $keyName should be of one of these types: ".implode(',',$enforce["types"]));

						}

					}

				}
				
			}
			
		}

	}

?>
