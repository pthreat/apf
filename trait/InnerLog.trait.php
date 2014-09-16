<?php

	namespace apf\traits{

		trait innerLog{

			private	$logObj	=	NULL;

			private function setLog(\apf\core\Log $log){

				$this->logObj	=	$log;

			}

			private function debug($text=NULL){

				if(is_null($this->logObj)){
					return;
				}

				$this->logObj->log($text,3,"light_purple");

			}

			private function info($text=NULL){

				if(is_null($this->logObj)){
					return;
				}

				$this->logObj->log($text,0,"light_cyan");

			}

			private function warning($text=NULL){

				if(is_null($this->logObj)){
					return;
				}

				$this->logObj->log($text,2,"yellow");

			}

			private function error($text=NULL){

				if(is_null($this->logObj)){
					return;
				}

				$this->logObj->log($text,1,"light_red");

			}

			private function emergency($text=NULL){

				if(is_null($this->logObj)){
					return;
				}

				$this->logObj->log($text,1,"red");

			}

			private function success($text=NULL){

				if(is_null($this->logObj)){
					return;
				}

				$this->logObj->log($text,0,"light_green");
				
			}
	
		}

	}
