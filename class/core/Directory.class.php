<?php

	namespace apf\core{

		class Directory extends \SPLFileInfo{

			public function __construct($directory=NULL){

				parent::__construct($directory);

			}

			public function exists(){

				return is_dir($this->getPathName());

			}

			public function create($mode=0755){

				if($this->isFile()){

					throw new \Exception("File exists but it's not a directory");

				}

				if($this->exists()){

					throw new \Exception("Directory already exists");

				}


				if(!@mkdir($this->getPathName(),$mode,TRUE)){

					throw new \Exception("Can't create directory ".$this->getPathName());

				}

				return TRUE;

			}

			public function ls(){

				$files	=	Array();

				$directoryIterator	=	new \DirectoryIterator($this->getFilename());

				foreach ($directoryIterator as $fileInfo){

					if($fileInfo->isDot()){
						continue;
					}

					$files[]	=	$fileInfo->getFileName();

				}

				return $files;

			}

		}

	}
?>
