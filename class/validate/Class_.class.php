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
			*@throws \apf\exception\Validate Only if the class name is empty
			*@return boolean TRUE Class exists
			*@return boolean FALSE Class doesn't exists
			*/

			public static function exists($name){

				\apf\validate\String::mustBeNotEmpty($name,"Class name must not be empty",-1);

				return class_exists($name);

			}

			/**
			*Checks if a class exists, imperative mode.
			*If the class doesn't exists it will throw an Exception
			*@param String $name class name
			*@param String $msg Exception message if any
			*@param Int $exCode Exception code
			*@throws \apf\exception\Validate with code -1 if the class name is empty
			*@throws \apf\exception\Validate if the class doesn't exists
			*/

			public static function mustExist($name,$msg=NULL,$exCode=0){

				if(!self::exists($name)){

					$msg	=	empty($msg)	?	"Unexistent class $name"	:	$msg;

					throw new \apf\exception\Validate($msg,$exCode);

				}

			}

			/**
			*Checks if a class has a method named $method.
			*@param String $name class name
			*@param String $method method to be checked 
			*@throws \apf\exception\Validate with code -1 if the class name is empty
			*@throws \apf\exception\Validate with code -2 if the class doesn't exists
			*@throws \apf\exception\Validate with code -3 if the method name is empty
			*@return boolean TRUE if the class has a method named $method
			*@return boolean FALSE if the class doesn't has a method named $method
			*/

			public static function hasMethod($name,$method){

				self::mustExist($name,NULL,-2);

				\apf\validate\String::mustBeNotEmpty($name,"Method name must not be empty",-3);

				$rc	=	new \ReflectionClass($name);

				return $rc->hasMethod($method);

			}

			/**
			*Checks if a class has a method named $method (imperative mode).
			*@param String $name class name
			*@param String $method method to be checked 
			*@param String $msg Exception message if any
			*@param Int $exCode Exception code
			*@throws \apf\exception\Validate with code -1 if the class name is empty
			*@throws \apf\exception\Validate with code -2 if the class doesn't exists
			*@throws \apf\exception\Validate with code -3 if the method name is empty
			*@throws \apf\exception\Validate with code $exCode if method doesn't exists
			*/

			public static function mustHaveMethod($name,$method,$msg=NULL,$exCode=0){

				if(!self::hasMethod($name,$method)){

					$msg	=	empty($msg) ? "Class $name doesn't has a method named $method"	:	$msg;

					throw new \apf\exception\Validate($msg,$method,$exCode);

				}

				return $method;

			}

			/**
			*Checks if a class has a public method named $method.
			*@param String $name class name
			*@param String $method method to be checked 
			*@throws \apf\exception\Validate with code -1 if the class name is empty
			*@throws \apf\exception\Validate with code -2 if the class doesn't exists
			*@throws \apf\exception\Validate with code -3 if the method name is empty
			*@return boolean TRUE if the class has a public method named $method
			*@return boolean FALSE if the class doesn't has a public method named $method
			*/

			public static function hasPublicMethod($name,$method){

				if(!self::hasMethod($name,$method)){

					return NULL;

				}

				$rc		=	new \ReflectionClass($name);
				$methods	=	$rc->getMethods(\ReflectionMethod::IS_PUBLIC);

				return (boolean)$methods;

			}

			/**
			*Checks if a class has a public method named $method (imperative mode).
			*@param String $name class name
			*@param String $method method to be checked 
			*@param String $msg Exception message if any
			*@param Int $exCode Exception code
			*@throws \apf\exception\Validate with code -1 if the class name is empty
			*@throws \apf\exception\Validate with code -2 if the class doesn't exists
			*@throws \apf\exception\Validate with code -3 if the method name is empty
			*@throws \apf\exception\Validate with code $exCode if method doesn't exists
			*/

			public static function mustHavePublicMethod($name,$method,$msg=NULL,$exCode=0){

				if(!self::hasPublicMethod($name,$method)){

					$msg	=	empty($msg) ? "Class $name doesn't has a public method named \"$method\""	:	$msg;

					throw new \apf\exception\Validate($msg,$exCode);

				}

				return $method;

			}

			/**
			*Checks if a class has a constant named $constant
			*@param String $name class name
			*@param String $constant constant to be checked 
			*@throws \apf\exception\Validate with code -1 if the class name is empty
			*@throws \apf\exception\Validate with code -2 if the class doesn't exists
			*@throws \apf\exception\Validate with code -3 if the constant name is empty
			*@return boolean TRUE if the class has a constant named $constant
			*@return boolean FALSE if the class doesn't has a constant named $constant
			*/

			public static function hasConstant($name,$constant){

				self::mustExist($name,NULL,-2);

				\apf\validate\String::mustBeNotEmpty($name,"Constant name must not be empty",-3);

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

				if(!self::hasConstant($name,$constant)){

					$msg	=	empty($msg)	?	"Class $name doesn't has a constant named $constant"	:	$msg;

					throw new \apf\exception\Validate($msg,$exCode);	

				}

			}

			/**
			*Checks if a class has an attribute named $attribute
			*@param String $name class name
			*@param String $attribute attribute to be checked 
			*@throws \apf\exception\Validate with code -1 if the class name is empty
			*@throws \apf\exception\Validate with code -2 if the class doesn't exists
			*@throws \apf\exception\Validate with code -3 if the attribute name is empty
			*@return boolean TRUE if the class has a attribute named $attribute
			*@return boolean FALSE if the class doesn't has a attribute named $attribute
			*/

			public static function hasAttribute($name,$attribute){

				self::mustExist($name,NULL,-2);

				\apf\validate\String::mustNotBeEmpty($attribute,"Attribute name can't be empty",-3);

				$rc	=	new \ReflectionClass($name);
				return $rc->hasAttribute($attribute);

			}

			/**
			*Checks if a class has an attribute named $attribute (imperative mode)
			*@param String $name class name
			*@param String $attribute attribute to be checked 
			*@throws \apf\exception\Validate with code -1 if the class name is empty
			*@throws \apf\exception\Validate with code -2 if the class doesn't exists
			*@throws \apf\exception\Validate with code -3 if the attribute name is empty
			*@throws \apf\exception\Validate with code $exCode if the attribute doesn't exists
			*/

			public static function mustHaveAttribute($name,$attribute,$msg=NULL,$exCode=0){

				if(!self::hasAttribute($name,$attribute)){

					$msg	=	empty($msg)	?	"Class $name doesn't has an attribute named $attribute"	:	$msg;
					throw new \apf\exception\Validate($msg,$exCode);

				}

			}

		}

	}
