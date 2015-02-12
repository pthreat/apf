<?php

	/**
	*This class is part of Apollo PHP Framework.
	*
	*Namespace	:	\apf\util
	*Class		:	Map
	*Description:	Maps array keys to a class, a class method, an instance, etc.
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

	namespace db{

		class Map{

			private	$class		=	NULL;
			private	$method		=	NULL;

			private	$instance	=	Array(
													"instance"	=>	NULL,
													"method"		=>	NULL
			);

			private	$callable	=	NULL;
			private	$value		=	NULL;

			public function __construct($value){

				$this->value	=	$value;

			}

			public function toInstance($obj,$method){

				\apf\validate\Instance::mustHaveMethod($obj,$method);
				$this->instance	=	Array("instance"=>$obj,"method"=>$method);

			}

			public function getInstance(){

				return $this->instance;

			}

			public function toClass($name=NULL){

				$this->class	=	\apf\validate\Class_::mustExist($name);

			}

			public function getClass(){

				return $this->class;

			}

			public function toClassMethod($class,$method)

				$this->method	=	\apf\validate\Class_::mustHaveMethod($class,$method);

			}

			public function getClassMethod(){

				return $this->method;
				
			}

			public function toCallable(callable $callable){

				$this->callable	=	$callable;

			}

			public function getCallable(){

				return $this->callable;

			}

		}

	}

?>
