<?php

	/**
	*This class is part of Apollo PHP Framework.
	*
	*Namespace	:	\apf\validate
	*Class		:	Vector
	*Description:	A class for validating arrays
	*
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

		class Vector{

			/**
			*Validates that an Array has EXACTLY a set of keys.
			*@param Array $requiredKeys these is an array containing the keys an array should have
			*@param Array $array Array to validate
			*@param String $msg A message used to throw an exception if the condition is not met.
			*@param Int $exCode integer code to throw an exception if the condition is not met.
			*@throws \apf\exception\Validate In case the array lacks a given key (CODE: -1)
			*@throws \apf\exception\Validate In case the array contains EXTRA elements (CODE:$exCode)
			*/

			public static function mustHaveExactlyTheseKeys(Array $requiredKeys,Array $array,$msg=NULL,$exCode=0){

				self::mustHaveKeys($requiredKeys,$array,$msg,-1);

				$keys	=	array_keys($array);
				$diff	=	array_diff($keys,$requiredKeys);

				if($diff){

					$msg	=	empty($msg)	?	sprintf('Array contains extra keys: %s',implode(',',$diff))	:	$msg;

					throw new \apf\exception\Validate($msg,$exCode);
					
				}

			}

			/**
			*Validates that an Array has a set of keys.
			*WARNING: This method does NOT validate if the array contains EXTRA keys
			*If you'd like a stricter validation please use: mustHaveExactlyTheseKeys
			*@param Array $requiredKeys this is an array containing the keys an array should have
			*@param Array $array Array to validate
			*@param String $msg A message used to throw an exception if the condition is not met.
			*@param Int $exCode integer code to throw an exception if the condition is not met.
			*@throws \apf\exception\Validate In case the array lacks a given key (CODE: -1)
			*@throws \apf\exception\Validate In case the array contains EXTRA elements (CODE:$exCode)
			*@see self::mustHaveExactlyTheseKeys
			*/

			public static function mustHaveKeys(Array $requiredKeys,Array &$array,$msg=NULL,$exCode=0){

				$msg	=	empty($msg)	?	'Required array key "%s", doesn\'t exists in given array' : $msg;

				array_walk($requiredKeys,function($key,&$val) use (&$array,$msg,$exCode){

					self::mustHaveKey($key,$array,sprintf($msg,$key),$exCode);

				});

			}

			/**
			*Validates that an array has a given key (boolean way)
			*This is just an alias of array_key_exists, but it adds a bit of code clarity
			*and is provided for completion.
			*
			*@param String $key Key to be checked if it exists in the array
			*@param Array $array arra to be checked if it contains a key specified in $key
			*@return boolean TRUE The key $key exists
			*@return boolean FALSE The key $key doesn't exists
			*/

			public static function hasKey($key,Array $array){

				return array_key_exists($key,$array);

			}

			/**
			*Validates that an array has a key in an imperative way
			*@param String $key Key to check if it exists in the array
			*@param Array $array Array to be checked
			*@param String $msg A message used to throw an exception if the condition is not met.
			*@param Int $exCode integer code to throw an exception if the condition is not met.
			*@throws \apf\exception\Validate In case the array lacks of the specified key $key
			*/

			public static function mustHaveKey($key,Array $array,$msg=NULL,$exCode=0){

				if(!self::hasKey($key,$array)){

					$msg	=	empty($msg)	?	"Array doesn't has a key named $key"	:	$msg;
					throw new \apf\exception\Validate($msg,$exCode);

				}

			}

			/**
			*Validates that an array has a given key (boolean way)
			*This is just an alias of the "empty" function, but it adds a bit of code clarity
			*and is provided for completion.
			*@param Array $array array to be checked if is empty.
			*@return boolean TRUE The array is empty.
			*@return boolean FALSE The array is not empty.
			*/

			public static function isEmpty(Array $array){

				return empty($array);

			}

			/**
			*Validates that an Array MUST be empty (imperative way)
			*@param Array $array Array to validate
			*@param String $msg A message used to throw an exception if the condition is not met.
			*@param Int $exCode integer code to throw an exception if the condition is not met.
			*@throws \apf\exception\Validate In case the array is not empty.
			*/

			public static function mustBeEmpty(Array $array,$msg=NULL,$exCode=0){

				if(empty($array)){

					return TRUE;

				}

				$msg	=	empty($msg)	?	"Array is not empty"	:	$msg;

				throw new \apf\exception\Validate($msg,$exCode);

			}

			/**
			*Validates that an Array MUST NOT be empty (imperative way)
			*@param Array $array Array to validate
			*@param String $msg A message used to throw an exception if the condition is not met.
			*@param Int $exCode integer code to throw an exception if the condition is not met.
			*@throws \apf\exception\Validate In case the array IS EMPTY.
			*/

			public static function mustBeNotEmpty(Array $array,$msg=NULL,$exCode=0){

				if(empty($array)){

					$msg	=	empty($msg) ? "Array must be not empty"	:	$msg;
					throw new \apf\exception\Validate($msg,$exCode);

				}

				return TRUE;

			}

		}

	}

?>
