<?php

	/**
	*This class is part of Apollo PHP Framework.
	*
	*Namespace	:	apf\validate
	*Class		:	Number
	*Description:	This is an abstract class which validates numbers
	*					Derived numeric classes can extend to this class.
	*					Derived numeric classes must have a static method named cast.
	*
	*Author		:	Federico Stange <jpfstange@gmail.com>
	*License		:	3 clause BSD
	*
	*Copyright (c) 2015, Federico Stange
	*
	*All rights reserved.
	*
	*Redistribution and use in source and binary forms, with or without modification, 
	*are permitted provided that the following conditions are met:
	*
	*1. Redistributions of source code must retain the above copyright notice, 
	*this list of conditions and the following disclaimer.
	*
	*2. Redistributions in binary form must reproduce the above copyright notice, 
	*this list of conditions and the following disclaimer in the documentation and/or other 
	*materials provided with the distribution.
	*
	*3. Neither the name of the copyright holder nor the names of its contributors may be used to 
	*endorse or promote products derived from this software without specific prior written permission.
	*
	*THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS 
	*OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY 
	*AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER 
	*OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
	*CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	*LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY 
	*OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
	*ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY 
	*OF SUCH DAMAGE.
	*
	*/

	namespace apf\validate{

		abstract class Number{

			public static function cast($num){

				$type	=	sprintf('\apf\type\%s',\apf\util\Class_::removeNamespace(get_called_class()));
				return $type::cast($num);

			}

			/**
			*Check if a number is odd
			*@param mixed $val Number/String to check
			*@return boolean TRUE the number is odd
			*@return boolean FALSE the number is even
			*/

			public static function isOdd($num){

				$num	=	self::cast($num);
				return !(boolean)($num%2);

			}

			/**
			*Check if a number is even
			*@param mixed $val Number/String to check
			*@return boolean TRUE the number is even
			*@return boolean FALSE the number is odd
			*/

			public static function isEven($num){

				$num	=	self::cast($num);
				return (boolean)($num%2);

			}

			/**
			*Check if a number is positive
			*@param mixed $num Could be a numeric string or an integer
			*@return boolean TRUE is positive
			*@return boolean FALSE is not positive
			*/

			public static function isPositive($num){

				return !(self::cast($num) <= 0);

			}

			/**
			*Check if a number is negative
			*@param mixed $num Could be a numeric string or an integer
			*@return boolean TRUE is negative
			*@return boolean FALSE is not negative
			*/

			public static function isNegative($num){

				return !self::isPositive($num);

			}

			/**
			*Check if a number is positive (imperative mode).
			*@param mixed $num Number/String to check
			*@param String $msg A message to be shown in case an exception is thrown
			*@param int $exCode A code to be added in case an exception is thrown
			*@throws \apf\exception\Validate If the number is not positive
			*@return number Entered number casted to the given type
			*/

			public static function mustBePositive($num,$msg=NULL,$exCode=0){

				$num	=	self::cast($num);

				if($num<=0){

					$msg	=	empty($msg)	?	"Given number is not positive" : $msg;

					throw new \apf\exception\Validate($msg,$exCode);

				}

				return $num;

			}

			/**
			*Check if a number is negative (imperative mode).
			*@param mixed $val Number/String to check
			*@param String $msg A message to be shown in case an exception is thrown
			*@param int $exCode A code to be added in case an exception is thrown
			*@throws \apf\exception\Validate If the number is not negative
			*@return number Entered number casted to the given type
			*/

			public static function mustBeNegative($num,$msg=NULL,$exCode=0){

				$num	=	self::cast($num);

				if($num>=0){

					$msg	=	empty($msg)	?	"Given number is not negative" : $msg;

					throw new \apf\exception\Validate($msg,$exCode);

				}

			}

			/**
			*Check if a number is greater than another number (imperative mode)
			*@param mixed $cmp Base number
			*@param mixed $num Number to be compared
			*@param String $msg A message to be shown in case an exception is thrown
			*@param int $exCode A code to be added in case an exception is thrown
			*@throws \apf\exception\Validate If $num is not greater than $cmp
			*@return number Entered number casted to the given type
			*/

			public static function isGreatherThan($num,$cmp){

				return $num>$cmp;

			}

			/**
			*Check if a number is greater than another number (imperative mode)
			*@param mixed $cmp Base number
			*@param mixed $num Number to be compared
			*@param String $msg A message to be shown in case an exception is thrown
			*@param int $exCode A code to be added in case an exception is thrown
			*@throws \apf\exception\Validate If $num is not greater than $cmp
			*@return number Entered number casted to the given type
			*/

			public static function mustBeGreaterThan($num,$cmp,$msg=NULL,$exCode=0){

				$num	=	static::cast($num);
				$cmp	=	static::cast($cmp);

				if($num>$cmp){

					return $num;

				}

				$msg	=	empty($msg)	?	"Number $num is not greater than $cmp"	:	$msg;

				throw new \apf\exception\Validate($msg);

			}

			/**
			*Check if a number is greater OR EQUAL than another number
			*@param mixed $cmp Base number
			*@param mixed $num Number to be compared
			*@param String $msg A message to be shown in case an exception is thrown
			*@param int $exCode A code to be added in case an exception is thrown
			*@throws \apf\exception\Validate If $num is not greater than $cmp
			*@return number Entered number casted to the given type
			*/

			public static function mustBeGreaterOrEqualThan($num,$cmp,$msg=NULL,$exCode=0){

				$num	=	static::cast($num);
				$cmp	=	static::cast($cmp);

				if($num>=$cmp){

					return $num;

				}

				$msg	=	empty($msg)	?	"Number $num is not greater or equal to $cmp"	:	$msg;

				throw new \apf\exception\Validate($msg);

			}

			/**
			*Check if a number is lower than another number
			*
			*@param mixed $cmp Number specified as the lower number
			*@param mixed $num Number to compare
			*@param String $msg A message to be shown in case an exception is thrown
			*@param int $exCode A code to be added in case an exception is thrown
			*@return mixed Number casted to the proper type
			*@throws \apf\exception\Validate $num is not lower than $cmp
			*/

			public function mustBeLowerThan($num,$cmp,$msg=NULL,$exCode=0){

				$num	=	static::cast($num);
				$cmp	=	static::cast($cmp);

				if($num<$cmp){

					return $num;

				}

				$msg	=	empty($msg)	?	"Number $num is not lower than $cmp"	:	$msg;

				throw new \apf\exception\Validate($msg);

			}

			/**
			*Check if a number is lower OR EQUAL than another number
			*
			*@param mixed $cmp Number specified as the lower number
			*@param mixed $num Number to compare
			*@param String $msg A message to be shown in case an exception is thrown
			*@param int $exCode A code to be added in case an exception is thrown
			*@return mixed Number casted to the proper type
			*@throws \apf\exception\Validate $num is not lower than $cmp
			*/

			public function mustBeLowerOrEqualThan($num,$cmp,$msg=NULL,$exCode=0){

				$num	=	static::cast($num);
				$cmp	=	static::cast($cmp);

				if($num<=$cmp){

					return $num;

				}

				$msg	=	empty($msg)	?	"Number $num is not lower or equal to $cmp"	:	$msg;

				throw new \apf\exception\Validate($msg);

			}

			/**
			*Check if a number is between two numbers
			*
			*@param mixed $min Lower range
			*@param mixed $max Higher range
			*@param mixed $len String length to be compared
			*@param String $msg A message to be shown in case an exception is thrown
			*@param int $exCode A code to be added in case an exception is thrown
			*@return mixed Number casted to the proper type
			*@throws \apf\exception\Validate $num is not between $min and $max
			*/

			public static function isBetween($min,$max,$num,$msg,$exCode){

				$num	=	static::cast($num);
				$min	=	static::cast($min);
				$max	=	static::cast($max);

				if($num >= $min && $num <= $max){

					return $num;

				}

				$msg	=	empty($msg)	?	"Number $num is not between $min and $max"	:	$msg;

			}


			/**
			*Check if a number is between two numbers (imperative mode)
			*
			*@param mixed $min Lower range
			*@param mixed $max Higher range
			*@param mixed $num Number to be compared
			*@param String $msg A message to be shown in case an exception is thrown
			*@param int $exCode A code to be added in case an exception is thrown
			*@return mixed Number casted to the proper type
			*@throws \apf\exception\Validate $num is not between $min and $max
			*/

			public static function mustBeBetween($num,$min,$max,$msg=NULL,$exCode=0){

				$num	=	static::cast($num);
				$min	=	static::cast($min);
				$max	=	static::cast($max);

				if($num >= $min && $num <= $max){

					return $num;

				}

				$msg	=	empty($msg)	?	"Number $num is not between $min and $max"	:	$msg;

				throw new \apf\exception\Validate($msg);

			}

			/**
			*Check if a number is a power of another number.
			*
			*@param mixed $pow Power number
			*@param mixed $num Number to be checked
			*@param String $msg A message to be shown in case an exception is thrown
			*@param int $exCode A code to be added in case an exception is thrown
			*@return boolean TRUE $num is a power of $pow
			*@throws \apf\exception\Validate \apf\exception\Validate with code -1 if $pow is not greater than 0
			*@throws \apf\exception\Validate \apf\exception\Validate with code -2 if $num is not greater than 0
			*@throws \apf\exception\Validate \apf\exception\Validate with code $exCode and message $msg
			*/

			public static function mustBePowerOf($num,$pow,$msg=NULL,$exCode=0){

				$num	=	static::cast($num);
				$pow	=	static::cast($pow);

				$msg	=	empty($msg)	?	"Power must be a number greater than 0"	: 	$msg;
				self::mustBeGreaterThan(0,$num,$msg,-1);
				
				$msg	=	empty($msg)	?	"Number to check if is a power of $pow must be greater than 0"	: 	$msg;
				self::mustBeGreaterThan(0,$pow,$msg,-2);

				while (($num%$pow) == 0){

					$num/=$pow;

				}

				if(!$num){

					$msg	=	empty($msg)	?	"Number $num is not a power of $pow"	:	$msg;

					throw new \apf\exception\Validate($msg,$exCode);

				}

				return TRUE;

			}

		}

	}

?>
