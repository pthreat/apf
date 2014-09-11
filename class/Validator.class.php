<?php

	namespace apf{

		class Validator{

			public static function arrayKeys(Array $requiredKeys,Array $array){

				foreach($requiredKeys as &$k){

					if(!array_key_exists($k,$array)){

						if(empty($msg)){

							$msg	=	"Required array key $k, doesn't exists in given array";

						}

						throw(new \Exception($msg));

					}

				}
					
			}

			public static function emptyString($string=NULL,$msg=NULL){

				$string	=	trim($string);

				if(empty($string)){

					if(!$msg){
						$msg	=	"Empty String";
					}

					throw(new \Exception($msg));

				}

				return $string;

			}

			public static function length($value=NULL,Array $filter=Array()){

				if(!array_key_exists("minlen",$filter)){

					throw(new \Exception("Invalid filter array given, \"minlen\" key not found in array"));

				}

				$len	=	strlen($value);

				if($len<$filter["minlen"]){

					if(!isset($filter["minmsg"])){

						$msg	=	"String has to have a minimum of $filter[minlen] characteres. String ".
									"\"$value\" has only $len characteres";

					}else{

						$msg	=	$filter["minmsg"];

					}

					throw(new \Exception($msg));

				}

				if(array_key_exists("maxlen",$filter)&&$len>$filter["maxlen"]){

					if(!isset($filter["maxmsg"])){

						$msg	=	"String has to have a maximum of $filter[maxlen] characteres. String ".
									"\"$value\" has only $len characteres";

					}else{

						$msg	=	$filter["maxmsg"];

					}

					throw(new \Exception($msg));

				}

			}

			public static function intNum($num=NULL,Array $filter=Array()){

				if(!array_key_exists("cast",$filter)){

					$filter["cast"]	=	TRUE;

				}

				if($filter["cast"]){

					$num	=	(int)$num;

				}

				if(array_key_exists("min",$filter)){
				
					if($num<$filter["min"]){	

						if(array_key_exists("minmsg",$filter)){
							$msg	=	$filter["minmsg"];
						}else{
							$msg	=	"Invalid int value, $num is not greater than ".$filter["min"];
						}

						throw(new \Exception($msg));

					}

				}

				if(array_key_exists("max",$filter)){
				
					if($num>$filter["max"]){	

						if(array_key_exists("maxmsg",$filter)){

							$msg	=	$filter["maxmsg"];

						}else{

							$msg	=	"Invalid int value, $num is greater than ".$filter["max"];

						}

						throw(new \Exception($msg));

					}

				}

				return $num;

			}


			public static function realNum($num=NULL,Array $filter=Array()){

				if(!array_key_exists("cast",$filter)){

					$filter["cast"]	=	TRUE;

				}

				if($filter["cast"]){

					$num	=	(double)$num;

				}

				if(array_key_exists("min",$filter)){
				
					if($num<$filter["min"]){	

						if(array_key_exists("minmsg",$filter)){
							$msg	=	$filter["minmsg"];
						}else{
							$msg	=	"Invalid double value, $num is not greater than ".$filter["min"];
						}

						throw(new \Exception($msg));

					}

				}

				if(array_key_exists("max",$filter)){
				
					if($num>$filter["max"]){	

						if(array_key_exists("maxmsg",$filter)){

							$msg	=	$filter["maxmsg"];

						}else{

							$msg	=	"Invalid double value, $num is greater than ".$filter["max"];

						}

						throw(new \Exception($msg));

					}

				}

				return $num;

			}

  			public static function email($email=NULL) {

				$email	=	self::emptyString($email);

				if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE){

					throw(new \Exception("Invalid email"));

				}

				return $email;

			}

			public static function emptyArray($value,$filter){

				if(!array_key_exists("empty",$filter)){

					$filter["empty"]	=	FALSE;

				}

				if($filter["empty"]===FALSE){

					if(empty($value)){

						if(array_key_exists("emptymsg",$filter)){

							$msg	=	$filter["emptymsg"];

						}else{

							$msg	=	"Array value can't be empty";

						}

						throw(new \Exception($msg));

					}

				}

			}

			public static function arraySize(Array $value=Array(),Array $filter){

				if(!array_key_exists("size",$filter)&&!array_key_exists("minsize",$filter)&&!array_key_exists("maxsize",$filter)){

					throw(new \Exception("Uknown filters given in the filter array"));
					
				}

				$size	=	sizeof($value);

				if(array_key_exists("size",$filter)){

					if($size!==$filter["size"]){

						if(array_key_exists("sizemsg",$filter)){

							$msg	=	$filter["msg"];

						}else{

							$msg	=	"Array has $size elements, but array should be of size $filter[size]";

						}

						throw(new \Exception($msg));

					}

				}

				if(array_key_exists("minsize",$filter)){

					$filter["minsize"]	=	self::intNum($filter["minsize"],Array("min"=>1,"minmsg"=>"minsize has to be greater than 0"));

					if($size<$filter["minsize"]){

						if(array_key_exists("minmsg",$filter)){

							$msg	=	$filter["minmsg"];

						}else{

							$msg	=	"Minimum size required is  $filter[minsize], but array has $size elements";

						}

						throw(new \Exception($msg));

					}

				}

				if(array_key_exists("maxsize",$filter)){

					$filter["maxsize"]	=	self::intNum($filter["maxsize"]);

					if($size<$filter["maxsize"]){

						if(array_key_exists("maxmsg",$filter)){

							$msg	=	$filter["maxmsg"];

						}else{

							$msg	=	"Maximum size of $filter[maxsize] exceeded, has $size elements";

						}

						throw(new \Exception($msg));

					}

				}

			}

			public static function metaValidate($value,Array $filter=Array()){

				if(!sizeof($filter)){

					throw(new \Exception("Empty filter"));

				}

				if(empty($filter["type"])){

					throw(new \Exception("Must specify type"));

				}

				$type	=	strtolower($filter["type"]);
				unset($filter["type"]);	

				switch($type){

					case "array":

						if(!is_array($value)){

							throw(new \Exception("Given value is not an Array"));

						}

						if(array_key_exists("keys",$filter)){

							self::arrayKeys($value,$filter["keys"]);

						}

						self::emptyArray($value,$filter);

						if(array_key_exists("size",$filter)||array_key_exists("minsize",$filter)||array_key_exists("maxsize",$filter)){

							self::arraySize($value,$filter);
							
						}

					break;

					case "string":

						//trim string by default
						if(!array_key_exists("trim",$filter)){

							$value	=	trim($value);

						}

						if(array_key_exists("empty",$filter)&&$filter["empty"]==FALSE){

							self::emptyString($value);

						}

						if(array_key_exists("maxlen",$filter)||array_key_exists("minlen",$filter)){

							self::length($value,$filter);

						}

					break;

					case "int":
						self::intNum($value,$filter);
					break;

					case "float":
					case "double":
					case "real":
						self::realNum($value,$filter);
					break;

					case "email":
						return self::email($value);
					break;

					case "boolean":

						return (bool)$value;

					break;

					case "object":

						if(!array_key_exists("class",$filter)){
							throw(new \Exception("\"class\" key not found"));
						}

						if(!class_exists($filter["class"])){
							throw(new \Exception("Class $filter[class] doesn't exists, perhaps you forgot to require/include it?"));
						}

						if(!array_key_exists("args",$filter)){
							$filter["args"]=NULL;
						}

						if(array_key_exists("method",$filter)){

							$obj	=	new $filter["class"];
							$obj->$filter["method"]($value);

						}else{

							return new $filter["class"]($value);

						}

					break;

					case "date":

						$date	=	$value;

						if(isset($filter["format"])){

							switch($filter["format"]){

								case "dtime":
									$fmt	=	"Y-m-d H:i:s";
								break;

								case "date":
									$fmt	=	"Y-m-d";
								break;

								default:
									$fmt	=	$filter["format"];
								break;

							}

							$date	=	\DateTime::createFromFormat($fmt,$value);

							if(!$date){
								throw(new \Exception("Invalid format $filter[format] for date value \"$value\""));
							}

						}

						return $date;

					break;

				}

				return $value;

			}

		}

	}

?>
