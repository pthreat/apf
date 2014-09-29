<?php

	namespace apf\db{

		abstract class DMLQuery extends Query{

			private	$columns	=	Array();
			private	$where	=	'';
			private	$having	=	'';
			private	$group	=	'';
			private	$order	=	Array();

			public function where($clause=NULL){

				$this->where	=	\apf\Validator::emptyString($clause,"Where clause can't be empty");

				//$this->bindParams($bindParams);

				return $this;

			}

			public function having($clause){

				$this->having	=	\apf\Validator::emptyString($clause,"Where clause can't be empty");

			}

			public final function getHaving(){

				return $this->having;

			}

			public final function getGroup(){

				return $this->group;

			}

			public function order($field,$sort="ASC"){

				$this->order	=	Array("field"=>$field,"sort"=>$sort);

			}

			public final function getOrder(){

				return $this->order;

			}

			public final function getColumns(){

				if(sizeof($this->columns)){

					return $this->columns;

				}

				foreach($this->getTables() as $table){

					$alias	=	$table->getAlias();
					$columns	=	Array();

					foreach($table->getColumns() as $col){

						$columns[]	=	$alias	?	$alias.'.'.$col["name"]	:	$col["name"];

					}

					return $columns;

				}

			}

			protected function getWhere(){

				return $this->where;

			}

		}

	}

?>
