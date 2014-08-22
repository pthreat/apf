<?php

	namespace Mail{

		class Factory{

			public static function getInstance($email,$user=NULL,$password=NULL,$debug=TRUE){

				$pos	=	strpos($email,'@');

				if(!$pos){	

					throw(new \Exception('Invalid email provided for Mail\Factory'));

				}

				$host	=	strtolower(substr($email,$pos+1));

				$Host	=	$host;

				$host	=	substr($host,0,strpos($host,'.'));

				$mail	=	new \PHPMailer(TRUE);

				if(!is_null($user)){

					$mail->Username   =	$user;

				}

				if(!is_null($password)){

					$mail->Password   =	$password;

				}

				if($debug){

					$mail->SMTPDebug  =	2;

				}

				switch($host){

					case "gmail":

						$mail->IsSMTP();

						$mail->SMTPAuth   =	true;
						$mail->SMTPSecure =	"ssl";
						$mail->Host       =	"smtp.gmail.com";
						$mail->Port       =	465;

					break;

					case "live":

						$mail->IsSMTP();
						$mail->Mailer			=	"smtp";
						$mail->SMTPSecure		=	"tls";
						$mail->Host				=	"smtp.live.com";
						$mail->Port				=	25;
						$mail->SMTPAuth		=	TRUE;

					break;

					default:

						$mail->IsSMTP();
						$mail->Mailer			=	"smtp";
						$mail->Host				=	$Host;
						$mail->Port				=	25;
						$mail->SMTPAuth		=	TRUE;

					break;
						
				}

				return $mail;

			}

		}

	}

?>
