<?php

	namespace apolloFramework\core {

		class File implements \Iterator{

			private	$_dirname		=	NULL;
			private	$_file			=	NULL;
			private	$_contents		=	NULL;

			private	$_fp				=	NULL;
			private	$_line			=	NULL;
			private	$_fetchTimes	=	NULL;
			private	$_readFn			=	"fread";

			public function __construct($file=NULL){

				if(!is_null($file)){

					if(!file_exists($file)){

						throw (new \Exception ("File $file not found!"));

					}

					$this->setFilename($file);

				}

			}

			public function rewind(){

				if(!is_null($this->_fp)){
					fseek($this->_fp,0);
				}
				$this->fetch();

			}

			public function current(){

				return $this->_line;

			}

			public function key(){

				return $this->_fetchTimes;

			}

			public function read($bytes=2048){

				return $this->fetch($bytes);

			}

			public function close(){

				if(is_null($this->_fp)){

					throw(new \Exception("Theres no file handler open, the file can't be closed"));

				}

				fclose($this->_fp);
				$this->_fp	=	NULL;

			}

			public function &open($mode='r',$reOpen=FALSE){

				if($reOpen){

					$this->close();

				}

				if(!is_null($this->_fp)){

					return $this->_fp;

				}

				$this->_fp	=	fopen($this,$mode);

				if(!$this->_fp){

						throw(new \Exception("Can't open file $this file with mode \"$mode\""));

				}

				return $this->_fp;

			}

			public function setReadFunction($fName=NULL){

				$fName	=	\apolloFramework\Validator::emptyString($fName);

				if(!function_exists($fName)){

					throw(new \Exception("Function provided (\"$fName\") for reading file $this does not exists!"));

				}

				$this->_readFn	=	$fName;

			}

			public function getReadFunction(){

				return $this->readFn;

			}

			public function fetch($bytes=2048){

				$this->open();

				$this->_fetchTimes++;

				if(feof($this->_fp)){

					return $this->_line	=	FALSE;

				}

				//Add possibility to use fgets (read a line) instead of fread (read n bytes)
				$fragment		=	call_user_func($this->_readFn,$this->_fp,$bytes);
				$this->_line	=	$fragment;

				return $this->_line;

			}

			public function next(){

				$this->fetch();

			}

			public function valid(){

				return (!($this->_line===FALSE));

			}

			public function __destruct(){

				try{

					$this->close();

				}catch(\Exception $e){

				}

			}

			public function &getHandler(){

				return $this->_fp;

			}

			public function setContents($contents=NULL){

				$this->_contents	=	$contents;

			}


			public function setFileName($file){

				$this->_dirname = dirname($file);
				$this->_file    = basename($file);

			}

			public function delete(){

				return unlink($this->_dirname.DIRECTORY_SEPARATOR.$this->_file);

			}

			public function write(){

				file_put_contents($this->_dirname.DIRECTORY_SEPARATOR.$this->_file,$this->_contents);

			}

			public function isUsable(){

				$file	=	$this->getFile();

				if(!is_readable($file)){
					throw(new \Exception("File $file is not readable, please check your permissions!"));
				}

				if(!is_file($file)){
					throw(new \Exception("File $file is a directory!"));
				}

			}

			public function dirname(){
				return $this->_dirname;
			}

			public function basename(){

				return $this->_basename;

			}

			public function getFile(){

				return $this->_dirname.DIRECTORY_SEPARATOR.$this->_file;

			}

			public function getContents(){

				return file_get_contents($this->_dirname.DIRECTORY_SEPARATOR.$this->_file);

			}

			public function getContentsAsArray(){

				return file($this->_dirname.DIRECTORY_SEPARATOR.$this->_file);

			}

			public function __toString(){

				return $this->getFile();

			}

		}

	}

?>
