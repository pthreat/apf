<?php

	/**
	*MySQL query Builder class, this class is used to build SELECT SQL queries
	*/

	namespace apf\db\mysql5{

		class Update extends Query{

			public function __construct($table=NULL,$params=NULL){
				parent::__construct($table,$params);
			}

			//This is just an accesory method for you being able to wrap a certain value

			public function group(Array $group){

				$this->sqlArray["group"] = $group;

			}

			public function orderBy(Array $fields,$sort=NULL){

				if(empty($sort)){

					$sort	=	"ASC";

				}

				$this->sqlArray["order"]=Array("fields"=>$fields,"sort"=>$sort);

			}


			public function limit(Array $limit){

				$this->sqlArray["limit"]	=	$limit;

			}

			public function getSQL(){

				$s			=	$this->space;

				$fields	=	$this->getFields();

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


				if(!is_null($this->sqlArray["where"])){

					$where	=	$s."WHERE".$s.$this->sqlArray["where"];

				}else{

					$where	=	"";

				}

				$sql	=	"UPDATE".$s.$this->table->getName().$s."SET".$s.$fields.$s.$where.$s.$order.$s.$limit;

				return $sql;


			}

			public function getResult(){

				return $this->adapter->affected_rows;

			}

		}

	}
?>
