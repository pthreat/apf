<?php 

	/**
	*This class is part of Apollo PHP Framework.
	*
	*Namespace	:	apf\validate
	*Class		:	Class_
	*Description:	A class for validating vertain class properties, the lower dash is due 
	*					PHP lacking the ability to be able to distinguish between the class keyword
	*					as a class name. Since there is not a significant synonym in the English language
	*					to be able to describe "class", I've used a lower dash instead.
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

		class Class_{

			/**
			*Checks if a class exists.
			*@param String $name Class name
			*@return Int -1 class name is empty
			*@return boolean TRUE Class exists
			*@return boolean FALSE Class doesn't exists
			*/

			public static function exists($name){

				if(\apf\validate\String::isEmpty($name)){

					return -1;

				}

				return class_exists($name);

			}

			/**
			*Checks if a class exists, imperative mode.
			*If the class doesn't exists it will throw an Exception
			*@param String $name class name
			*@param String $msg Exception message if any
			*@param Int $exCode Exception code
			*@throws \apf\exception\Validate if the class doesn't exists
			*/

			public static function mustExist($name,$msg=NULL,$exCode=0){

				if($exCode<0){

					throw new \InvalidArgumentException($exCode);

				}

				$exists	=	self::exists($name);

				if(!($exists===TRUE)){

					if(\apf\validate\String::isEmpty($msg)){

						$msg	=	"Unexistent class $name";

					}

					$exCode	=	$exCode	?	$exCode	:	$exists;

					throw new \apf\exception\Validate($msg,$exCode);

				}

			}

			/**
			*Checks if a class has a method named $method.
			*@param String $name class name
			*@param String $method method to be checked 
			*@return Int -1 if the class name is empty
			*@return Int -2 if the method name is empty
			*@return Int -3 if the class doesn't exists
			*@return Int -4 ONLY if specified filters are empty
			*@return Int -5 ONLY if an invalid filter was specified
			*@return Int -6 Method doesn't exists
			*@return boolean TRUE if the class has a method named $method
			*@return boolean FALSE if the class doesn't has a method named $method
			*/

			public static function hasMethod($name,$method,$filters=NULL,$strict=TRUE){

				if(empty($name)){

					return -1;

				}

				if(empty($method)){

					return -2;

				}

				if(!class_exists($name)){

					return -3;

				}

				$rc	=	new \ReflectionClass($name);

				if(is_null($filters)){

					return $rc->hasMethod($method);

				}

				if(is_string($filters)){

					$filters	=	Array($filters);

				}

				//Check if entered filters are not empty

				if(!sizeof($filters)){

					return -4;

				}

				//Check if entered filters are valid

				foreach($filters as $filter){

					$filter	=	sprintf('IS_%s',strtoupper($filter));

					//If an invalid filter is detected, then return -5

					if(!self::hasConstant('\ReflectionMethod',$filter)){

						return -5;

					}

				}

				//Fetch all class methods
				$methods	=	$rc->getMethods();

				foreach($methods as $m){

					//If the current method is not equal to the selected one then continue
					//with the next element in the array

					if($m->name!==$method){

						continue;

					}

					//Get all method modifiers (public, abstract, private, final, etc)

					$modifiers	=	\Reflection::getModifierNames($m->getModifiers());

					//if non strict mode was specified, only one modifier is necessary to be true in order 
					//to the consulted method to be valid

					if($strict==FALSE){

						foreach($filters as $f){

							if(in_array($f,$modifiers)){

								return TRUE;

							}

						}

					}

					foreach($modifiers as $mod){

						if(!in_array($mod,$filters)){

							return FALSE;

						}

					}

					return $flag;

				}

				//Method doesn't exists in this class (return -6)
				return -6;

			}

			/**
			*Checks if a class has a method named $method (imperative mode).
			*
			*@param String $name class name
			*@param String $method method to be checked 
			*@param Mixed  $filters, a set of filters to check for class attributes such as public,
			*					private, abstract, final, etc. You can specify an array like structure,
			*					e.g: Array('abstract','public') or just a string like 'public'
			*
			*@param bool	$strict if set to TRUE (which is the default value) 
			*					the method must contain ALL and ONLY the parameters that you specify in the 
			*					$filters argument. If set to FALSE, only ONE attribute from $filters 
			*					is necessary to exist in the given method for it to be TRUE.
			*					
			*@param String $msg Exception message if any
			*@param Int $exCode Exception code
			*@throws \apf\exception\Validate with code -1 if the class name is empty
			*@throws \apf\exception\Validate with code -2 if the class doesn't exists
			*@throws \apf\exception\Validate with code -3 if the method name is empty
			*@throws \apf\exception\Validate with code -4 ONLY if specified filters are empty
			*@throws \apf\exception\Validate with code -5 if an invalid attribute was specified in the
			*			$filters argument.
			*@throws \apf\exception\Validate with code -6 if the method doesn't exists be aware that
			*			the existence of the method depends on the filters that you have specified
			*			(if any).
			*@throws \apf\exception\Validate with code $exCode if method doesn't exists
			*/

			public static function mustHaveMethod($name,$method,$filters=NULL,$strict=TRUE,$msg=NULL,$exCode=0){

				if($exCode<0){

					throw new \Exception("Exception code must be greater than 0");	

				}

				$hasMethod	=	self::hasMethod($name,$method,$filters,$strict);

				if(!($hasMethod===TRUE)){

					$msg		=	empty($msg) ? "Class $name doesn't has a method named $method"	:	$msg;
					$exCode	=	$exCode	?	$exCode	:	$hasMethod;

					throw new \apf\exception\Validate($msg,$exCode);

				}

			}

			/**
			*Checks if a class has a public method named $method.
			*@param String $name class name
			*@param String $method method to be checked 
			*@return Int -1 class doesn't exists
			*@return Int -2 class name is empty
			*@return Int -3 method name is empty
			*@return boolean TRUE if the class has a public method named $method
			*@return boolean FALSE if the class doesn't has a public method named $method
			*/

			public static function hasPublicMethod($name,$method){

				if(!class_exists($name)){

					return -1;

				}

				if(\apf\validate\String::isEmpty($name)){

					return -2;

				}

				if(\apf\validate\String::isEmpty($name)){

					return -3;

				}

				$rc		=	new \ReflectionClass($name);
				$methods	=	$rc->getMethods(\ReflectionMethod::IS_PUBLIC);

				return (boolean)$methods;

			}

			/**
			*Checks if a class has a private method named $method.
			*@param String $name class name
			*@param String $method method to be checked 
			*@return Int -1 class doesn't exists
			*@return Int -2 class name is empty
			*@return Int -3 method name is empty
			*@return boolean TRUE if the class has a private method named $method
			*@return boolean FALSE if the class doesn't has a private method named $method
			*/

			public static function hasPrivateMethod($name,$method){

				if(!class_exists($name)){

					return -1;

				}

				if(\apf\validate\String::isEmpty($name)){

					return -2;

				}

				if(\apf\validate\String::isEmpty($name)){

					return -3;

				}

				$rc		=	new \ReflectionClass($name);
				$methods	=	$rc->getMethods(\ReflectionMethod::IS_PRIVATE);

				return (boolean)$methods;

			}

			/**
			*Checks if a class has a public method named $method (imperative mode).
			*@param String $name class name
			*@param String $method method to be checked 
			*@param String $msg Exception message if any
			*@param Int $exCode Exception code
			*@throws \InvalidArgumentException In case $exCode is lower than 0
			*@throws \apf\exception\Validate with code -1 if the class name is empty
			*@throws \apf\exception\Validate with code -2 if the class doesn't exists
			*@throws \apf\exception\Validate with code -3 if the method name is empty
			*@throws \apf\exception\Validate with code $exCode if method doesn't exists
			*/

			public static function mustHavePublicMethod($name,$method,$msg=NULL,$exCode=0){

				if($exCode<0){

					throw new \InvalidArgumentException("Exception code must be greater than 0");	

				}

				$hasPublicMethod	=	self::hasPublicMethod($name,$method);

				if(!($hasPublicMethod===TRUE)){

					$msg		=	empty($msg) ? "Class $name doesn't has a public method named \"$method\""	:	$msg;
					$exCode	=	$exCode	?	$exCode	:	$hasPublicMethod;

					throw new \apf\exception\Validate($msg,$exCode);

				}

			}

			/**
			*Checks if a class has a constant named $constant
			*@param String $name class name
			*@param String $constant constant to be checked 
			*@return Int -1 if the class name is empty
			*@return Int -2 if the constant name is empty
			*@return Int -3 if the class doesn't exists
			*@return boolean TRUE if the class has a constant named $constant
			*@return boolean FALSE if the class doesn't has a constant named $constant
			*/

			public static function hasConstant($name,$constant){

				if(\apf\validate\String::isEmpty($name)){

					return -1;

				}

				if(\apf\validate\String::isEmpty($constant)){

					return -2;

				}

				if(!class_exists($name)){

					return -3;

				}

				$rc	=	new \ReflectionClass($name);

				return $rc->hasConstant($constant);

			}

			/**
			*Checks if a class has a constant named $constant
			*@param String $name class name
			*@param String $constant constant to be checked 
			*@throws \apf\exception\Validate with code -1 if the class name is empty
			*@throws \apf\exception\Validate with code -2 if the class doesn't exists
			*@throws \apf\exception\Validate with code $exCode if constant $constant doesn't exists
			*/

			public static function mustHaveConstant($name,$constant,$msg=NULL,$exCode=0){

				if($exCode<0){

					throw new \InvalidArgumentException("Exception code must be greater than 0");	

				}

				$hasConstant	=	self::hasConstant($name,$constant);

				if(!($hasConstant===TRUE)){

					if(\apf\validate\String::isEmpty($msg)){

						$msg	=	"Class $name doesn't has a constant named $property";

					}

					$exCode	=	$exCode	?	$exCode	:	$hasConstant;

					throw new \apf\exception\Validate($msg,$exCode);

				}

			}

			/**
			*Checks if a class has an property named $property
			*@param String $name class name
			*@param String $property property to be checked 
			*@return Int -1 class name is empty
			*@return Int -2 property name is empty
			*@return Int -3 Provided class doesn't exists
			*@return Int -4 ONLY If a filter was specified and such filter is invalid
			*@return boolean TRUE if the class has a property named $property
			*@return boolean FALSE if the class doesn't has a property named $property
			*/

			public static function hasProperty($name,$property,$filter=NULL){

				if(\apf\validate\String::isEmpty($name)){

					return -1;

				}

				if(\apf\validate\String::isEmpty($property)){

					return -2;

				}

				if(!class_exists($name)){

					return -3;

				}

				$rc	=	new \ReflectionClass($name);

				if(!is_null($filter)){

					$_filter	=	(int)$filter;

					$constant	=	sprintf('IS_%s',strtoupper($filter));

					if(!self::hasConstant('\ReflectionProperty',$constant)){

						return -4;

					}

					$_filter	=	constant(sprintf('\ReflectionProperty::%s',$constant));

					return (boolean)$rc->getProperties($_filter);

				}

				return $rc->hasProperty($property);

			}

			/**
			*Checks if a class has an property named $property (imperative mode)
			*@param String $name class name
			*@param String $property property to be checked 
			*@throws \apf\exception\Validate with code -1 if the class name is empty
			*@throws \apf\exception\Validate with code -2 if the class doesn't exists
			*@throws \apf\exception\Validate with code -3 if the property name is empty
			*@throws \apf\exception\Validate with code $exCode if the property doesn't exists
			*/

			public static function mustHaveProperty($name,$property,$filter=NULL,$msg=NULL,$exCode=0){

				if($exCode<0){

					throw new \InvalidArgumentException("Exception code must be greater or equal than 0");

				}

				$hasProperty	=	self::hasProperty($name,$property,$filter);

				if(!($hasProperty===TRUE)){

					if(\apf\validate\String::isEmpty($msg)){

						$msg	=	"Class $name doesn't has an property named $property";

					}

					$exCode	=	$exCode	?	$exCode	:	$hasProperty;

					throw new \apf\exception\Validate($msg,$exCode);

				}

			}

		}

	}
