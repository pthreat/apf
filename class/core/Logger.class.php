<?php

	namespace apf\core {

		class Logger {

			/**
			 * @var $colors Array Different colors for console output
			 */

			private $colors = array(
				"black"			=>"\33[0;30m",
				"blue"			=>"\33[0;34m",
				"light_blue"	=>"\33[1;34m",
				"green"			=>"\33[0;32m",
				"light_green"	=>"\33[1;32m",
				"cyan"			=>"\33[0;36m",
				"light_cyan"	=>"\33[1;36m",
				"red"				=>"\33[0;31m",
				"light_red"		=>"\33[0;31m",
				"purple"			=>"\33[0;35m",
				"light_purple"	=>"\33[1;35m",
				"brown"			=>"\33[0;33m",
				"gray"			=>"\33[1;30m",
				"light_gray"	=>"\33[0;37m",
				"yellow"			=>"\33[1;33m",
				"white"			=>"\33[1;37m"
			);


			private $_colors = TRUE;

	
			/**
			 * @var resource file pointer
			 */

			private $_fp = NULL;

			/**
			 * @var $_uselogDate
			 * @see Log::useLogDate($filename)
			 */
		
			private $_useLogDate = FALSE;
	
			/**
			 * @var $_filename String name of log file
			 * @see Log::setFilename($filename)
			 */
	
			private  $_filename = NULL;
	
			/**
			*
			* @var $_echo print to stdout or not
			* @see self::setEcho()
			*
			*/
	
			private $_echo = NULL;
	
			/**
			*
			* @var $_x11Info 
			* @see self::setX11Info()
			*
			*/
	
			private $_x11Info = TRUE;
	
			/**
			*
			* @var $_write wether to write to a file or not
			* @see self::setWrite()
			*
			*/
	
			private $_write = NULL;
	
	
			/**
			* @var $_prepend Adds a static string to every message *before* the message
			* @see Log::setPrepend()
			*/
	
			private $_prepend = NULL;
	
			/**
			* @var $_append Adds a static string to every message *after* the message
			* @see Log::setAppend()
			*/
	
			private $_append = NULL;

			/**
			* @var $_lineCharacter it's the character that outputs at the end of a message, by default it's \n
			* @see Log::setCarriageReturnChar
			*/

			private	$_lineCharacter	=	"\n";	
	
			public function setFilename($filename=NULL){
	
			  if(!empty($this->_filename)){
					$this->endLog();
				}
	
				$this->_filename = (empty($filename)) ? "Log_".date("d-M-Y_H:i:s") : $filename;
	
				if(!$this->openFile()){
					throw(new \Exception("Unable to log to $filename, please check file permissions!"));
				}
	
				return TRUE;
	
			}
	
			public function setX11Info($boolean=TRUE){
				$this->_x11Info = (bool)$boolean;
			}
	
			public function getX11Info(){
				return $this->_x11Info;
			}
	
			private function openFile() {
	
				return $this->_fp = @fopen($this->_filename.".log","a+");
	
			}
	
			/**
			*Specifies if date should be prepended in the log file
			*@param boolean $boolean TRUE prepend date
			*@param boolean $boolean FALSE do NOT prepend date
			*/
			public function useLogDate($boolean=TRUE){
					$this->_useLogDate = $boolean;
			}

			public function setCarriageReturnChar($char="\n"){
				$this->_lineCharacter	=	$char;
			}
	
			/**
			*
			* @method registraLog() registro los eventos en el archivo log creado por el constructor
			*/
	
			public function log($msg=NULL,$type=0,$color=NULL){
	
				if(empty($msg)){

					throw(new \Exception("Message to be logged cant be empty"));

				}

				if(is_array($msg)){
					$msg	=	var_export($msg,TRUE);
				}
	
				$date = ($this->_useLogDate) ? date("[d-M-Y / H:i:s]") : NULL;
			
				$code = NULL;
	
				$type = ($this->_x11Info) ? $this->_infoType($type) : NULL;

				$origMsg	=	$msg;	
				$msg		=	$this->_prepend.$type." ".$date.$msg.$this->_append;
	
				if ($this->_echo){

					if($color && $this->_colors) {
	
						if(!in_array(strtolower($color),array_keys($this->colors))) {
	
							throw(new \Exception("Invalid color specified when trying to log $code $msg"));
	
						} else {
	
							if($this->_colors) {
	
								echo $this->colors[$color].$code.$msg."\033[37m\r\n";
	
							} else {	//Log without coloring
	
								echo $code.$msg.$this->_lineCharacter;
	
							}
	
						}
	
					} else {
	
						echo $msg.$this->_lineCharacter;
	
					}
	
				}
	
				if(!empty($this->_filename)){
	
					$write	= TRUE;
					$write	&= $this->_fwrite($msg."\n");
	
					return $write;
	
				}
			
			}

			public function reset(){
				echo $this->colors["light_gray"]."\r";
			}
	
			private function _fwrite($msg){

				$return = fwrite($this->_fp,$msg);
	
				if($return === FALSE){
	
					$msg = "Error writing to log file, youre trying to write this log file in ".$this->_filename.".log".
					"check for permission problems and disk space";
	
					throw (new \Exception($msg));
	
				}
	
				return strlen($msg);
	
			}
	
			/**
			*Returns an X11 debug like tag according to the given number
			*/
	
			private function _infoType($type=NULL) {
	
				switch($type) {
					case 1:
						return "[EE]";
					case 2:
						return "[WW]";
					case 3:
						return "[DD]";
					case 4:
						return "[SS]";
					case 0:
					default:
						return "[II]";
				}
	
			}

			public function debug($text=NULL){

				$this->log($text,3,"light_purple");

			}

			public function info($text=NULL){

				$this->log($text,0,"light_cyan");

			}

			public function warning($text=NULL){

				$this->log($text,2,"yellow");

			}

			public function error($text=NULL){

				$this->log($text,1,"red");

			}

			public function success($text=NULL){

				$this->log($text,0,"light_green");
				
			}
	
	
			/**
			* @method endLog() closes pointer to created file
			*/

			public function endLog() {
	
				return fclose($this->_fp);
	
			}
	
			/**
			*@method setEcho() 
			*@param $echo bool TRUE output to stdout
			*@param $echo bool FALSE Do NOT output to stdout
			*/
	
			public function setEcho($echo=TRUE) {
	
				$this->_echo = $echo;
	
			}
	
			/**
			 *@method setPrepend() Prepends a string to every log message
			 *@param String The string to be prepend
			 */
	
			public function setPrepend($prepend=NULL) {
	
				$this->_prepend = $prepend;
	
			}
	
			/**
			*@method setAppend() Adds 
			*@param string El string a posponer en el mensaje log
			*
			*/
	
			public function setAppend($append=NULL) {
	
				$this->_append = $append;
	
			}
	
			public function getAppend(){
	
				return $this->_append;
		
			}
	
			public function getPrepend(){
	
				return $this->_prepend;
	
			}
	
	
			/**
			* @method setColors() Color output (Console only)
			* @param bool $bool TRUE ACTIVADO FALSE DESACTIVADO
			*/
	
			public function setColors($bool=TRUE) {
				$this->_colors=$bool;
			}

			public function getEcho(){
				return $this->_echo;
			}
	
		}

	}
