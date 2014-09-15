<?php

	namespace apf\type\db{

		class Connection extends \apf\type\net\Connection{

			private	$id			=	NULL;
			private	$database	=	NULL;
			private	$driver		=	NULL;
			private	$persistent	=	NULL;

			public function __construct($host=NULL,$port=NULL,\apf\type\User $user,$id=NULL,$database=NULL,$driver=NULL){

				parent::__construct($host,$port,$user);
				$this->setDatabase($database);
				$this->setDriver($driver);
				$this->setId($id);

			}

			public function setId($id){

				$this->id	=	\apf\Validator::emptyString($id,"Connection ID must be not empty");

			}

			public function getId(){

				return $this->id;

			}

			//Creates a instance of this class validating indexes 

			public static function create($data=NULL){

				if($data instanceof \stdClass){

					$data	=	(Array)$data;

				}

				if(!is_array($data)){

					throw(new \Exception("Creating a connection needs a data structure, array or stdClass"));
				}

				$missing	=	\apf\Validator::arrayKeys(["id","host","port","name","user","pass","driver"],$data,$throw=FALSE);

				if(is_string($missing)){

					throw(new \Exception("Missing $missing parameter, when attempting to create connection structure"));

				}

				if(empty($data["id"])){

					throw(new \Exception("Must provide a unique identifier (id) for this connection"));

				}

				$user	=	new \apf\type\User($data["user"],$data["pass"]);
				$host	=	new \apf\net\Host($data["host"]);

				$class	=	__CLASS__;
				return new $class($host,$data["port"],$user,$data["id"],$data["name"],$data["driver"]);

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

			public function setDatabase($name=NULL){

				$this->database	=	\apf\Validator::emptyString($name,"Database name can't be empty");

			}

			public function getDatabase(){

				return $this->database;

			}

			public function getPDOConnectionString($addUser=FALSE){

				$port	=	$this->getPort();

				if($port){
					$port	=	";port=$port";
				}

				$string	=	'';
				$string .=	$this->driver.':host='.$this->getHost()->get().
								$port.';dbname='.$this->database;

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
