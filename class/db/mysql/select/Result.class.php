<?php

	namespace apf\db\mysql5\select{

		class Result implements \Iterator,\ArrayAccess{

			private	$select		=	NULL;
			private	$result		=	NULL;
			private	$currentRow	=	NULL;
			private	$position	=	NULL;
			private	$map			=	NULL;

			public function __construct(\apf\db\mysql5\Select $select,$map=NULL){

				$this->select	=	$select;
				$this->result	=	$select->getQueryResult();
				$this->map		=	$map;

			}

			private function map(Row $data=NULL){

				$map	=	$this->select->getMap();

				if(is_null($this->map)){

					return $data;

				}

				//execute(some\class);
				/////////////////////////////////////////

				if(is_string($this->map)){

					if(!class_exists($this->map)){

						$msg	=	"Class \"$this->map\" doesn't exists. Maybe you forgot to ".
									"require/include the class?";

						throw(new \Exception($msg));

					}

					return new $this->map($data);

				}

				//execute(Array("class"=>"some\\class","merge"=>Array(.....
				///////////////////////////////////////////////////////////////

				if(is_array($this->map)){

					if(isset($this->map["class"])){

						if(!class_exists($this->map["class"])){
							$msg	=	"Class \"$this->map\" doesn't exists. Maybe you forgot to ".
										"require/include the class?";
						}

						if(array_key_exists("merge",$this->map)){

							if(!is_array($this->map["merge"])){

								throw(new \Exception("\"merge\" argument expected to be an array ".gettype($this->map["merge"])." given"));

							}

							foreach($this->map["merge"] as $key=>$val){

								$data[$key]=$val;

							}

						}

						return new $this->map["class"]($data);

					}

				}

			}

			public function rewind(){

				$this->result->data_seek(0);
				$this->fetch();

			}

			public function current(){

				return $this->currentRow;

			}

			public function key(){

				return $this->position;

			}

			public function fetch(){

				$this->currentRow	=	$this->result->fetch_assoc();

				if(!$this->currentRow){
				
					return;

				}

				$result	=	new Row($this->currentRow,$this->select->getTable());

				return $this->currentRow=$this->map($result);

			}

			public function next(){

				$this->fetch();
				++$this->position;

			}

			public function valid(){

				return $this->position < $this->result->num_rows;

			}

			public function offsetGet($offset){

				if(is_null($this->currentRow)){
					$this->fetch();
				}

				return $this->currentRow[$offset];

			}


			public function offsetSet($offset,$value){

				if(is_null($this->currentRow)){

					$this->fetch();

				}

				return $this->currentRow[$offset]	=	$value;

			}

			public function offsetUnset($offset){

				if(is_null($this->currentRow)){

					$this->fetch();

				}

				unset($this->currentRow[$offset]);

			}

			public function offsetExists($offset){

				if(is_null($this->currentRow)){

					$this->fetch();

				}

				return array_key_exists($offset,$this->currentRow);
				
			}

			public function __get($var){

				return $this->offsetGet($var);

			}

			public function __set($var,$value){

				return $this->offsetSet($var,$value);

			}

			public function __destruct(){

				$this->result->free();

			}
			
			public function __toString(){

				return(sprintf("%s",$this->currentRow));

			}

		}

	}

?>
