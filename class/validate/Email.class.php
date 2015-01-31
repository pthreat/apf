<?php

	/**
	*This class is part of Apollo PHP Framework.
	*
	*Namespace	:	\apf\validate
	*Class		:	Email
	*Description:	Validates email addresses
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

		class Email{

			/**
			*Validates an email address
			*@param String $email the email address to be validated
			*@param String $msg Message if an exception ocurrs
			*@param Int    $exCode Exception code
			*@throws \Exception if the given email String is empty (with exception code -1)
			*@throws \Exception if the given email String is not a valid email (with code $exCode)
			*/

			public static function address($email,$msg=0,$exCode=0){

				$email	=	String::isEmpty($email,$useTrim=TRUE,$msg,-1);

				if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE){

					$msg  =	empty($msg)	?	"Invalid Email"	:	$msg;

					throw new \Exception($msg);

				}

				return $email;

			}

			public static function domain($address,$domain,$msg=NULL,$exCode=0){

				self::address($address,$msg,-2);

				$addressDomain	=	substr($address,strpos($address,'@')+1);
				$addressDomain	=	substr($addressDomain,strpos($addressDomain,'.'));

				if($addressDomain!==$domain){

					$msg	=	empty($msg)	?	"Domain $addressDomain doesn't matches with domain $domain" : $msg;
					throw new \Exception($msg);

				}

				return $domain;

			}

			public static function domains($address,Array $domains,$msg=NULL,$exCode=0){

				self::address($address,$msg,-2);

				$domainsMsg	=	empty($msg)	?	"You must especify a non empty list of domains to match the email address domain against"	:	$msg;

				Array_::mustBeNotEmpty($domains,$msg,-3);

				$addressDomain	=	substr($address,strpos($address,'@')+1);

				foreach($domains as $domain){

					if(strtolower($addressDomain) == strtolower($domain)){

						return TRUE;

					}

				}

				$msg	=	empty($msg)	?	sprintf('Email domain doesn\'t matches with any of the domains listed: %s',implode(',',$domains);

				throw new \Exception($msg,$exCode);

			}

		}

	}
