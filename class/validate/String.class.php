<?php

	namespace apf\validate{

		class String{

			/**
			*Check if a string is empty.
			*@param String $string The string to be checked.
			*@param boolean $useTrim wether to trim the string or not.
			*@param String $msg Exception message.
			*@param Int $exCode Exception code.
			*@throws \Exception in case the given string is effectively empty.
			*@return String The string (trimmed or not, this is specified with the $useTrim parameter).
			*/
			
			public static function isEmpty($string,$useTrim=TRUE,$msg=NULL,$exCode=0){

				if($useTrim){

					$string	=	trim($string);

				}

				if(empty($string)){

					$msg	=	empty($msg) ?	"Empty string"	:	$msg;

					throw new \Exception($msg);

				}

				return $string;

			}

			/**
			*Check if the length of a string is between specified limits.
			*@param Int $min Minimum limit
			*@param Int $maximum Maximum limit
			*@param String $string The string to be checked
			*@param String $msg Exception message.
			*@param Int $exCode Exception code.
			*@throws \Exception in case the given string is not between specified limits
			*@return Int The string length 
			*/

			public static function lengthBetween($min,$max,$string,$msg=NULL,$exCode=0){

				$min	=	(int)$min;
				$max	=	(int)$max;
				$len	=	strlen($string);

				$msg	=	empty($msg) ? sprintf('String length has to be between %d and %d characteres. String "%s" has a length of %d characters',$min,$max,$string,$len) : $msg;


				return Int::isBetween($min,$max,$len,$msg,$exCode);

			}

			/**
			*Check if the length of a string has a minimum of $min characters
			*@param Int $min Amount of minimum characters
			*@param String $string The string to be checked
			*@param String $msg Exception message.
			*@param Int $exCode Exception code.
			*@throws \Exception in case the given string has not the amount of minimum characters.
			*@return Int The string length 
			*/

			public static function minLength($min=NULL,$string,$msg=NULL,$exCode=0){

				$min	=	(int)$min;
				$len	=	strlen($string);

				$msg	=	empty($msg) ? sprintf('String has to have a minimum of %d characteres. String "%s" has only %d characters',$min,$string,$len) : $msg;

				return Int::isGreaterOrEqualThan($min,$len,$msg,$exCode);

			}

			/**
			*Check if the length of a string exceeds a maximum amount of characters
			*@param Int $max Maximum amount of characters
			*@param String $string The string to be checked
			*@param String $msg Exception message.
			*@param Int $exCode Exception code.
			*@throws \Exception in case the given string has exceeded the maximum amount of characters.
			*@return Int The string length 
			*/

			public static function maxLength($max,$string,$msg=NULL,$exCode=0){

				$max	=	(int)$max;
				$len	=	strlen($string);

				$msg	=	empty($msg) ? sprintf('String has exceeded the amount of %d characteres. String "%s" has %d characters',$max,$string,$len) : $msg;

				return Int::isLowerOrEqualThan($max,$len,$msg,$exCode);

			}

		}

	}

