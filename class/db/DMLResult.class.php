<?php

	namespace apf\db{

		class DMLResult implements \Iterator{

			private	$stmt			=	NULL;
			private	$query		=	NULL;
			private	$position	=	NULL;
			private	$curRow		=	NULL;

			public function __construct(\apf\db\DMLQuery $query,\PDOStatement $stmt){

				$this->query	=	$query;
				$this->stmt		=	$stmt;

			}

			/**
			*Provides a way to map a SQL Array result *entirely* to a class
			*an object, a callable, etc OR a way to map EACH array (column) index
			*to different objects, classes, or callables.
			*@param Array $map a map of \apf\util\Map objects
			*@param \apf\util\Map $map a single map 
 			*/


			public function map($map,$type,$config=NULL){

				if(is_array($map)){

					foreach($map as $m){
					}

				}

				$this->mappings	=	$map;

			}

			private function fetch(){

				$result	=	$this->stmt->fetch(\PDO::FETCH_ASSOC);
				$as		=	$this->query->getFetchAs();

				if(!isset($as["class"])){

					return $result;

				}

				if(!is_null($as["method"])){

					return $as["method"]->invokeArgs(NULL,Array($result));

				}

				return new $as["class"]($result);

			}

			public function next(){

				++$this->position;
				return $this->fetch();

			}

			public function valid(){

				return $this->position < $this->stmt->rowCount();

			}

			public function key(){

				return $this->position;

			}

			public function current(){

				return $this->fetch();

			}

			public function rewind(){

				$this->position	=	0;

			}

			public function __toString(){

				return implode(',',$this->curRow);

			}

		}

	}

?>
