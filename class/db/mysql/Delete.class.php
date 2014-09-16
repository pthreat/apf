<?php

	/**
	*MySQL query Builder class, this class is used to build SELECT SQL queries
	*/

	namespace apf\db\mysql5{

		class Delete extends Query{

			//This is just an accesory method for you being able to wrap a certain value

			public function group(Array $group){

				$this->sqlArray["group"] = $group;

			}

			public function join(Join $join){
				$this->sqlArray["join"][]	=	$join;
			}

			public function orderBy(Array $fields,$sort=NULL){

				if(empty($sort)){

					$sort	=	"ASC";

				}

				$this->sqlArray["order"]=Array("fields"=>$fields,"sort"=>$sort);

			}

			public function union(Select $select){

				$this->sqlArray["union"]	=	$select;

			}

			public function limit(Array $limit){

				$this->sqlArray["limit"]	=	$limit;

			}

			public function getSQL(){

				$s			=	$this->space;

				$from		=	$this->table->getName();

				$where	=	$this->sqlArray["where"];
				

				if(sizeof($this->sqlArray["limit"])){

					$limit	=	"LIMIT".$s.implode($this->sqlArray["limit"],',');

				}else{

					$limit	=	"";

				}

				if(sizeof($this->sqlArray["order"])){

					$order	=	"ORDER".$s."BY".implode($this->sqlArray["order"],',');

				}else{

					$order	=	"";

				}

				$from	=	"FROM".$s.$this->table->getName();


				if(!is_null($this->sqlArray["where"])){

					$where	=	$s."WHERE".$s.$this->sqlArray["where"];

				}else{

					$where	=	"";

				}

				$join		=	'';
				$tables  =  '';

				if(sizeof($this->sqlArray["join"])){

					$tables		=	Array();
					$tables[]	=	$this->getTable()->getName().".*";

					foreach($this->sqlArray["join"] as $objJoin){

						$tables[]=$objJoin->getTable()->getName().".*";

						$join .=	$objJoin->getSQL().$s;

					}

					$tables	=	implode(',',$tables);

				}


				$sql	=	"DELETE".$s.$tables.$s.$from.$s.$join.$where.$s.$order.$s.$limit;

				return $sql;


			}

			public function getResult(){

				return $this->adapter->affected_rows;

			}

		}

	}
?>
