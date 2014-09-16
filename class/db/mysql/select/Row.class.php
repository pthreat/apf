<?php

	namespace apf\db\mysql5\select{

		class Row implements \ArrayAccess{

			private	$data		=	NULL;
			private	$table	=	NULL;
	
			public function __construct(Array &$data,\apf\db\mysql5\Table $table=NULL){

				$this->table	=	$table;
				$this->data		=	$data;

			}

			public function offsetGet($offset){

				$row	=	&$this->data;

				if(!array_key_exists($offset,$this->data)){

					$msg	=	"\"$offset\" offset doesn't exists in given result set.".
								"valid offsets are: ".implode(array_keys($this->data));

					throw(new \Exception($msg));

				}

				return $this->data[$offset];

			}

			public function update(Array $where=Array()){

				$update	=	new \apf\db\mysql5\Update($this->table);

				foreach($this->data as $key=>$value){

					$update->$key	=	$value;

				}

				return $update;

			}

			public function replace(){

				$replace	=	new \apf\db\mysql5\Replace($this->table);

				foreach($this->data as $key=>$value){

					$replace->$key	=	$value;

				}

				return $replace;

			}
			
			public function insert(){

				$insert	=	new \apf\db\mysql5\Insert($this->table);

				foreach($this->data as $key=>$value){

					$insert->$key	=	$value;

				}

				return $insert;

			}

			public function __get($var){

				return $this->offsetGet($var);

			}

			public function __set($var,$value){

				return $this->offsetSet($var,$value);

			}

			public function offsetSet($offset,$value){

				$this->data[$offset]	=	$value;

			}

			public function offsetUnset($offset){

				if(!array_key_exists($offset,$this->data)){

					$msg	=	"\"$offset\" offset doesn't exists in given result set.".
								"valid offsets are: ".implode(array_keys($this->data));

					throw(new \Exception($msg));

				}

				unset($this->data[$offset]);

			}

			public function offsetExists($offset){

				return array_key_exists($offset,$this->data);
				
			}

			public function toArray(){
				return $this->data;	
			}

			public function __toString(){

				$str	=	'';

				foreach($this->data as $k=>$v){
					$str.="$k\t\t=>\t$v\n";
				}

				return $str;

			}

		}

	}

?>
