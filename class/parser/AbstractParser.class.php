<?php

	namespace aidSQL\parser{

		abstract class AbstractParser implements ParserInterface {

			protected	$_logger			=	NULL;
			private		$_parserType	=	"undefined";
			protected	$_verbose		=	0;

			public function setLog(\aidSQL\core\Logger &$log){

				$this->_logger	=	$log;

			}

			public function setVerbosity($verbosity=0){

				$this->_verbose	=	(int)$verbosity;

			}

			protected function setParserType($type){

				switch(strtolower($type)){

					case "info":
					case "sqli":
						$this->_parserType	=	$type;
						break;

					default:

						$msg	=	__CLASS__. ' | ' . __FUNCTION__ . ' | ' . __LINE__."\n";				
						$msg	.= "Unknown parser type specified \"$type\" make sure parser type is one of \"info\"". 
						$msg	.= "or \"sqli\"\n";

						throw(new \Exception($msg));

					break;

				}

			}

			public function getParserType(){

				return $this->_parserType;

			}

			protected function getVerbosity(){

				return $this->_verbose;

			}

			protected function log($msg=NULL,$color="white",$level=0,$toFile=FALSE){

				if(is_null($this->_logger)){
					return FALSE;
				}

				$this->_logger->setPrepend('['.get_class($this).']');
				$this->_logger->log($msg,$color,$level,$toFile);

				return TRUE;

			}

		}

	}

?>
