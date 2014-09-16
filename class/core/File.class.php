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

			public function __construct($file=NULL,$checkExistence=TRUE){

				if(is_null($file)){

					return;

				}

				if($checkExistence&&!file_exists($file)){

					throw (new \Exception ("File $file not found!"));

				}

				$this->setFilename($file);

			}

			public function getMd5Sum(){

				return md5_file($this->getFile());

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

			public function write($content,$mode="a+"){

				if(is_null($this->fp)){

					$this->open($mode);

				}

				return fwrite($this->fp,$content,strlen($content));

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

			public function getBaseName(){
				return $this->file;
			}

			public function delete(){

				return unlink($this->dirname.DIRECTORY_SEPARATOR.$this->file);

			}

			public function dirname(){
				return $this->dirname;
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

			public function getOwner(){

				if(!function_exists("posix_getpwuid")){

					throw(new \Exception("Can't get file owner, function posix_getpwuid doesn't exists"));

				}

				return posix_getpwuid(fileowner($this->getFile()));

			}

			public function getGroup(){

				if(!function_exists("posix_getgrgid")){

					throw(new \Exception("Can't get file group, function posix_getgrgid doesn't exists"));

				}

				return posix_getgrgid(filegroup($filename));

			}

			public function getPerms(){

				//Taken from the PHP manual
				$perms	=	fileperms($this->getFile());

				$return	=	Array();

				if (($perms & 0xC000) == 0xC000){
					 // Socket
					 $type = 's';
				} elseif (($perms & 0xA000) == 0xA000) {
					 // Symbolic Link
					 $type = 'l';
				} elseif (($perms & 0x8000) == 0x8000) {
					 // Regular
					 $type = '-';
				} elseif (($perms & 0x6000) == 0x6000) {
					 // Block special
					 $type = 'b';
				} elseif (($perms & 0x4000) == 0x4000) {
					 // Directory
					 $type = 'd';
				} elseif (($perms & 0x2000) == 0x2000) {
					 // Character special
					 $type = 'c';
				} elseif (($perms & 0x1000) == 0x1000) {
					 // FIFO pipe
					 $type = 'p';
				} else {
					 // Unknown
					 $type = 'u';
				}

				// Owner
				$read = ($perms & 0x0100) ? TRUE : FALSE;
				$return["owner"]["read"]	=	$read;

				$write = ($perms & 0x0080) ? TRUE : FALSE;

				$return["owner"]["write"]	=	$write;

				$exec = (($perms & 0x0040) ?
							(($perms & 0x0800) ? 's' : 'x' ) :
							(($perms & 0x0800) ? 'S' : '-'));

				switch($exec){

					case 'x':
						$return["owner"]["exec"]	=	TRUE;
					break;

					case 's':
						$return["owner"]["exec"]	=	TRUE;
						$return["owner"]["suid"]	=	TRUE;
					break;

					case 'S':
						$return["owner"]["exec"]	=	TRUE;
						$return["owner"]["sgid"]	=	TRUE;
					break;

					default:
						$return["owner"]["exec"]	=	FALSE;
					break;

				}

				// Group
				$read	=	($perms & 0x0020) ? TRUE	:	FALSE;

				$return["group"]["read"]	=	$read;

				$write = ($perms & 0x0010) ? TRUE : FALSE;
				$return["group"]["write"]	=	$write;

				$info .= (($perms & 0x0008) ?
								(($perms & 0x0400) ? 's' : 'x' ) :
								(($perms & 0x0400) ? 'S' : '-'));

				switch($exec){

					case 'x':
						$return["group"]["exec"]	=	TRUE;
					break;

					case 's':
						$return["group"]["exec"]	=	TRUE;
						$return["group"]["suid"]	=	TRUE;
					break;

					case 'S':
						$return["group"]["exec"]	=	TRUE;
						$return["group"]["sgid"]	=	TRUE;
					break;

					default:
						$return["group"]["exec"]	=	FALSE;
					break;

				}

				// World
				$read	=	($perms & 0x0004) ? TRUE	:	FALSE;

				$return["world"]["read"]	=	$read;

				$write	=	($perms & 0x0002) ?	TRUE	:	FALSE;

				$return["world"]["write"]	=	$write;

				$exec	= (($perms & 0x0001) ?
								(($perms & 0x0200) ? 't' : 'x' ) :
								(($perms & 0x0200) ? 'T' : '-'));

				switch($exec){

					case 'x':
						$return["world"]["exec"]	=	TRUE;
					break;

					case 't':
						$return["world"]["exec"]	=	FALSE;
						$return["world"]["sticky"]	=	TRUE;
					break;

					case 'T':
						$return["world"]["exec"]	=	FALSE;
						$return["world"]["sticky"]	=	TRUE;
					break;

					default:
						$return["world"]["exec"]	=	FALSE;
					break;

				}

				return $return;

			}

		}

	}

?>
