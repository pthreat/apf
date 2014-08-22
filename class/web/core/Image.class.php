<?php

	namespace \core{

		class Image{

			private	$_image	=	NULL;
			private	$_imgRes	=	NULL;
			private	$_types	=	Array("jpg","gif","png");
			private	$_type	=	NULL;

			public function __construct($file=NULL){

				if(!is_null($file)){

					$this->setFile($file);

				}

			}

			public function setImage(\core\File $file){

				if(!getimagesize($file)){

					throw(new \Exception("Archivo de imagen invalido"));

				}

				switch($extension){

					case "jpg":
					case "jpeg":
						$type	=	"jpg";
						$img	=	imagecreatefromjpeg($file);
					break;

					case "gif":
						$img	=	imagecreatefromjpeg($file);
					break;

					case "png":
						$img	=	imagecreatefromjpeg($file);
					break;

					default:

						throw(new \Exception("Extension de imagen desconocida"));

					break;

				}

				$this->_type		=	$extension;
				$this->_image		=	$file;
				$this->_imageRes	=	$img;

			}


			public function getImage(){

				return $this->_image;

			}

			public function resize($width,$height){

				$maxHeight		=	$height;
				$maxWidth		=	$width;

				$width			=	imagesx($this->_imgRes);
				$height			=	imagesy($this->_imgRes);

				if($height>$width){

					$ratio		=	$maxHeight	/	$height;
					$newHeight	=	$maxHeight;
					$newWidth	=	$width * $ratio;
					$writeX		=	round(($maxWidth-$newWidth)/2);
					$writeY		=	0;

				}else{

					$ratio		=	$maxWidth	/	$width;
					$newWidth	=	$maxWidth;
					$newHeight	=	$height * $ratio;
					$writeX		=	0;
					$writeY		=	round(($maxHeight-$newHeight)/2);

				}

				$newImg			=	imagecreatetruecolor($maxWidth,$maxHeight);
				$paletteSize	=	imagecolorstotal($this->_imgRes);

				for($i=0;$i<$paletteSize;$i++){

					$colors	=	imagecolorsforindex($img,$i);
					imagecolorallocate($newImg,$colors["red"],$colors["green"],$colors["blue"]);

				}

				imagecopyresized($newImg,$this->_imgRes,$writeX,$writeY,0,0,$newWidth,$newHeight,$width,$height);
				$this->_imgRes	=	$newImg;

			}

			public function save($type="jpg"){

				imagejpeg($newImg,$imagenPerfil);

			}

		}

	}

?>
