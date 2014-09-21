<?php

	namespace apf\type\db{

		class Connection extends \apf\type\net\Connection{

			private	$id			=	NULL;
			private	$schemas		=	Array();
			private	$driver		=	NULL;
			private	$options		=	NULL;

			public function __construct($host=NULL,$port=NULL,\apf\type\User $user,$id=NULL,$schemas=NULL,$driver=NULL,Array $options=Array()){

				parent::__construct($host,$port,$user);


				$schemas	=	explode(',',$schemas);

				$this->setSchemas($schemas);
				$this->setDriver($driver);
				$this->setId($id);

				if(sizeof($options)){

					$this->setOptions($options);

				}

			}

			public function getAmountOfSchemas(){

				return sizeof($this->schemas);

			}

			public function setSchemas(Array $schemas){

				foreach($schemas as $schema){

					$this->addSchema($schema);

				}

			}

			public function setId($id){

				$this->id	=	\apf\Validator::emptyString($id,"Connection ID must be not empty");

			}

			public function getId(){

				return $this->id;

			}

			public function setOptions(Array $options){

				$this->options	=	$options;

			}

			public function getOptions(){

				return $this->options;

			}

			//Creates a instance of this class validating indexes 

			public static function create($data=NULL){

				if($data instanceof \stdClass){

					$data	=	(Array)$data;

				}

				if(!is_array($data)){

					throw(new \Exception("Creating a connection needs a data structure, array or stdClass"));

				}

				$missing	=	\apf\Validator::arrayKeys(["id","host","port","schemas","user","pass","driver"],$data,$throw=FALSE);

				if(is_string($missing)){

					throw(new \Exception("Missing $missing parameter, when attempting to create connection structure"));

				}

				if(empty($data["id"])){

					throw(new \Exception("Must provide a unique identifier (id) for this connection"));

				}

				$user	=	new \apf\type\User($data["user"],$data["pass"]);
				$host	=	new \apf\net\Host($data["host"]);

				$class	=	__CLASS__;

				return new $class($host,$data["port"],$user,$data["id"],$data["schemas"],$data["driver"]);

			}

			public function isValidSchema($schema=NULL){
				return in_array($schema,$this->schemas);	
			}

			public function setDriver($driver=NULL){

				$driver	=	strtolower(\apf\Validator::emptyString($driver,"Driver name can't be empty"));
				if(!\apf\db\Adapter::isAvailableDriver($driver)){

					$msg	=	"Given database driver \"$driver\" doesn't seems to be available,". 
								"perhaps you need to install it?";

					throw(new \Exception($msg));

				}

				$this->driver	=	$driver;

			}

			public function getDriver(){

				return $this->driver;

			}

			public function addSchema($name=NULL){

				if(in_array($name,$this->schemas)){

					throw new \Exception("Duplicated schema name \"$name\"");

				}

				$this->schemas[]	=	\apf\Validator::emptyString($name,"Schema name can't be empty");

			}

			public function getSchemas(){

				return $this->schemas;

			}

			public function getPDOConnectionString($addUser=FALSE){

				$port	=	$this->getPort();

				if($port){

					$port	=	";port=$port";

				}

				$string	=	'';
				$string .=	$this->driver.':host='.$this->getHost()->get().
								$port.';dbname=';

				if(sizeof($this->schemas)){

					$string.=$this->schemas[0];

				}

				if($addUser){
					$string.='|user:'.$this->getUser()->getName();
				}

				return $string;

			}

			public function __toString(){

				return $this->getPDOConnectionString(TRUE);

			}

		}

	}

?>
