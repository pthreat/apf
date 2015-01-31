<?php
	
	/**
	*This class is part of Apollo PHP Framework.
	*
	*Namespace	:	apf\validate
	*Class		:	Ip
	*Description:	A class used to validate ip addresses, version 6 and version 4 or both.
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

		class IP{

			public static function version4($ip,$msg=NULL,$exCode=0){

				$validIpv4	=	filter_var($ip,FILTER_VALIDATE_IP,FILTER_FLAG_IPV4);

				if($validIpv4){

					return TRUE;

				}

				$msg	=	empty($msg)	?	"Invalid IPV4 address" : $msg;

				throw new \Exception($msg,$exCode);

			}

			public static function version6($ip,$msg=NULL,$exCode=0){

				$validIpv6	=	filter_var($ip,FILTER_VALIDATE_IP,FILTER_FLAG_IPV6);

				if($validIpv6){

					return TRUE;

				}

				$msg	=	empty($msg)	?	"Invalid IPV6 address" : $msg;

				throw new \Exception($msg,$exCode);

			}

			public function address($ip,$msg=NULL,$exCode=0){

				if(self::version4($ip,$msg,$exCode){

					return TRUE;

				}

				if(self::version6($ip,$msg,$exCode)){

					return TRUE;

				}

				$msg	=	empty($msg)	?	"Invalid IP address" : $msg;

				throw new \Exception($msg,$exCode);

			}

		}

	}

