<?php

	namespace apf\core {

		class File implements \Iterator{

			private	$dirname		=	NULL;
			private	$file			=	NULL;
			private	$contents	=	NULL;
			private	$fp			=	NULL;
			private	$line			=	NULL;
			private	$fetchTimes	=	NULL;
			private	$readFn		=	"fread";

			public function __construct($file=NULL){

				if(!is_null($file)){

					if(!file_exists($file)){

						throw (new \Exception ("File $file not found!"));

					}

					$this->setFilename($file);

				}

			}

			public function rewind(){

				if(!is_null($this->fp)){
					fseek($this->fp,0);
				}

				$this->fetch();

			}

			public function current(){

				return $this->line;

			}

			public function key(){

				return $this->fetchTimes;

			}

			public function read($bytes=2048){

				return $this->fetch($bytes);

			}

			public function close(){

				if(is_null($this->fp)){

					throw(new \Exception("Theres no file handler open, the file can't be closed"));

				}

				fclose($this->fp);
				$this->fp	=	NULL;

			}

			public function &open($mode='r',$reOpen=FALSE){

				if($reOpen){

					$this->close();

				}

				if(!is_null($this->fp)){

					return $this->fp;

				}

				$this->fp	=	fopen($this,$mode);

				if(!$this->fp){

						throw(new \Exception("Can't open file $this file with mode \"$mode\""));

				}

				return $this->fp;

			}

			public function setReadFunction($fName=NULL){

				$fName	=	\apf\Validator::emptyString($fName);

				if(!function_exists($fName)){

					throw(new \Exception("Function provided (\"$fName\") for reading file $this does not exists!"));

				}

				$this->readFn		=	$fName;

			}

			public function getReadFunction(){

				return $this->readFn;

			}

			public function fetch($bytes=2048){

				$this->open();

				$this->fetchTimes++;

				if(feof($this->fp)){

					return $this->line	=	FALSE;

				}

				$this->line		=	call_user_func($this->readFn,$this->fp,$bytes);

				return $this->line;

			}

			public function next(){

				$this->fetch();

			}

			public function valid(){

				return (!($this->line===FALSE));

			}

			public function __destruct(){

				try{

					$this->close();

				}catch(\Exception $e){

				}

			}

			public function &getHandler(){

				return $this->fp;

			}

			public function setContents($contents=NULL){

				$this->contents	=	$contents;

			}


			public function setFileName($file){

				$this->dirname = dirname($file);
				$this->file    = basename($file);

			}

			public function delete(){

				return unlink($this->dirname.DIRECTORY_SEPARATOR.$this->file);

			}

			public function write(){

				file_put_contents($this->dirname.DIRECTORY_SEPARATOR.$this->file,$this->contents);

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
				return $this->dirname;
			}

			public function basename(){

				return $this->basename;

			}

			public function getFile(){

				return $this->dirname.DIRECTORY_SEPARATOR.$this->file;

			}

			public function getContents(){

				return file_get_contents($this->dirname.DIRECTORY_SEPARATOR.$this->file);

			}

			public function getContentsAsArray(){

				return file($this->dirname.DIRECTORY_SEPARATOR.$this->file);

			}

			public function __toString(){

				return $this->getFile();

			}

		}

	}

?>
