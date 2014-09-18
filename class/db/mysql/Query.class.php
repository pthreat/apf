<?php

	namespace apf\db\mysql{

			abstract class Query extends \apd\db\Query{

				public function where(){
				}

				public function join($join,$type=NULL){

					$this->sqlArray["join"][]	=	$join;

				}

				public function addField($field,$value){
				}

				public function __set($var,$value){

					return $this->addField($var,$value);
					
				}

				public function __toString(){

					try{

						$sql	=	$this->getSQL();
						$this->error	=	FALSE;
						return $sql;

					}catch(\Exception $e){

						$this->error	=	$e->getMessage();
						return '';

					}

				}

			}

		}
?>
