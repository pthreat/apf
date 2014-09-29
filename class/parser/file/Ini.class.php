<?php

	namespace apf\parser\file{

		class Ini{

			private	$iniFile	=	NULL;
			private	$data		=	Array();
			private	$lines	=	Array();

			public function __construct($file=NULL,$section=NULL){

				if(!is_null($file)){

					$this->setFile($file,$section);

				}

			}

			private function parseIniFile(){

				$this->iniFile->setReadFunction("fgets");
				$lineNo =	0;
					  
				$arrayConfig    =   array();
					  
				while($line = $this->iniFile->read()){

					$lineNo++;
					$line	=	trim($line);

					if(empty($line)){
						continue;
					}

					$isComment	=	strpos($line,';')===0;

					if($isComment){
						continue;
					}

					$comentarios = strpos($line,';');

					if($comentarios===0){
						continue;
					}

					$isSection	=	strpos($line,'[')===0;

					if($isSection){


						$section	=	substr($line,1,strpos($line,']')-1);
						$arrayConfig[$section]				=	Array();
						$this->lines[$section]["line"]	=	$lineNo;
						continue;

					}

					$param		=	trim(substr($line,0,strpos($line,'=')));
					$value		=	trim(substr($line,strpos($line,'=')+1));
					$hasComment	=	strpos($value,';');

					if($hasComment){
						$value	=	substr($value,0,$hasComment);
					}

					$value	=	trim($value,'"');

					if(isset($section)){

						$this->lines[$section]["params"][$param]	=	$lineNo;
						$arrayConfig[$section][$param]				=	$value;

						continue;
															
					}

					$this->lines["params"][$param]	=	$lineNo;
					$arrayConfig[$param]					=	$value;

				}

				return $arrayConfig;

			}

			public function getLine($section,$param=NULL){

				if(!isset($this->lines[$section])){

					throw new \Exception("Unknown section $secion");

				}

				if(is_null($param)){

					return $this->lines[$section]["line"];

				}

				if(!isset($this->lines[$section]["params"][$param])){

					throw new \Exception("Unknown parameter \"$param\" for section \"$section\"");

				}

				return $this->lines[$section]["params"][$param];

			}

			public function setFile($file=NULL,$section){

				if(empty($file)&&!is_string($file)&&!($file instanceof \apf\core\File)){
			
					$msg	=	"file argument should be an instance of ".
								"\\apf\\core\\File or a string containing ".
								"the path to the ini file";

					throw new \Exception($msg);

				}


				if($file instanceof \apf\core\File){

					$this->iniFile	=	$file;

				}elseif(is_string($file)){

					$this->iniFile	=	new \apf\core\File($file);

				}

				$this->data	=	$this->parseinifile($this->iniFile,TRUE);

				foreach($this->data as $k=>$v){

					if(!is_null($section)){

						if($k!==$section){
							continue;
						}

					}

					$this->$k	=	new \StdClass();

					foreach($v as $key=>$val){

						$this->$k->$key	=	$val;

					}

				}

			}

			public function getParamValue($section,$param){

				if(!isset($this->$section)){

					throw(new \Exception("La seccion $section no esta definida en el archivo ini"));

				}

				$param	=	trim($param);

				if(!isset($this->$section->$param)){

					return NULL;

				}

				return $this->$section->$param;

			}

			public function getSection($section){

				if(!isset($this->$section)){

					throw(new \Exception("La seccion $section no esta definida en el archivo ini"));

				}

				return $this->$section;

			}

			public function getSectionsLike($like=NULL){

				$like	=	trim($like,'/');

				\apf\Validator::emptyString($like,"Expecting regex");

				$like			=	preg_quote($like,'/');
				$like			=	"/$like/";
				$sections	=	get_object_vars($this);

				$return		=	Array();

				foreach($sections as $name=>$values){

					if(preg_match($like,$name)){

						$return[$name]	=	$values;

					}

				}

				return $return;

			}

		}

	}

?>
