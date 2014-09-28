<?php

	namespace apf\parser\file{

		class Ini{

			private	$_iniFile	=	NULL;
			private	$_data		=	Array();
			private	$_lines		=	Array();

			public function __construct($file=NULL,$section=NULL){

				if(!is_null($file)){

					$this->setFile($file,$section);

				}

			}

			private function parseIniFile(){

				$this->_iniFile->setReadFunction("fgets");
				$lineNo =	0;
					  
				$arrayConfig    =   array();
					  
				while($line = $this->_iniFile->read()){

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
						$arrayConfig[$section]	=	Array();
						continue;

					}

					$param	=	trim(substr($line,0,strpos($line,'=')));
					$value	=	trim(substr($line,strpos($line,'=')+1));
					$hasComment	=	strpos($value,';');

					if($hasComment){
						$value	=	substr($value,0,$hasComment);
					}

					$value	=	trim($value,'"');

					if(isset($section)){

						$arrayConfig[$section][]	=	Array(
																			"values"=>Array($param=>$value),"line"=>$lineNo
						);

						continue;
															
					}

					$arrayConfig[]	=	Array("values"=>Array($param=>$value),"line"=>$lineNo);

				}

				return $arrayConfig;

			}

			public function getLine($section,$param){


			}

			public function setFile($file=NULL,$section){

				if(empty($file)&&!is_string($file)&&!($file instanceof \apf\core\File)){
			
					$msg	=	"file argument should be an instance of ".
								"\\apf\\core\\File or a string containing ".
								"the path to the ini file";

					throw new \Exception($msg);

				}


				if($file instanceof \apf\core\File){

					$this->_iniFile	=	$file;

				}elseif(is_string($file)){

					$this->_iniFile	=	new \apf\core\File($file);

				}

	
				//$this->_data	=	parse_ini_file($this->_iniFile,TRUE);
				$this->_data	=	$this->parseinifile($this->_iniFile,TRUE);

				foreach($this->_data as $k=>$v){

					if(!is_null($section)){

						if($k!==$section){
							continue;
						}

					}

					$this->$k	=	new \StdClass();

					foreach($v as $key=>$val){

						foreach($val["values"] as $param=>$pValue){

							$this->$k->$param=$pValue;

						}

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
