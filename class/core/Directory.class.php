<?php

	namespace apolloFramework\core{

		class Directory{

			private	$_directory	=	NULL;

			public function __construct($directory){

				$this->setDirectory($directory);

			}

			public function setDirectory($directory){
		
				if(!is_dir($directory)){

					throw(new \Exception("Invalid directory \"$directory\". Directory doesn't exists"));

				}

				$this->_directory	=	$directory;

			}

			public function getDirectory(){

				return $this->_directory;

			}

			public function getFilesAsArray(){

				$directoryIterator	=	new \DirectoryIterator($this->_directory);

				$files	=	Array();

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
