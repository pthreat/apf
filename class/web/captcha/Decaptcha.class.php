<?php

		namespace apf\web\captcha{

			class Decaptcha{

				public static function decode($contents,$saveFile=FALSE){

					$file	=	md5(rand(0,time()));

					file_put_contents("/tmp/$file.gif",$contents);

					exec("convert /tmp/$file.gif /tmp/$file.jpg");

					$decoded	=	exec("djpeg -pnm -grayscale /tmp/$file.jpg | gocr - -l 30");

					if(!$saveFile){

						unlink("/tmp/$file.jpg");
						unlink("/tmp/$file.gif");
						$file	=	NULL;

					}else{

						$file	=	"/tmp/$file.gif";

					}

					$decoded	=	Array("decoded"=>$decoded,"file"=>$file);

					return $decoded;	

				}

			}

		}
	
?>
