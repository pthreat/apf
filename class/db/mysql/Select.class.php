<?php

	/**
	*MySQL query Builder class, this class is used to build SELECT SQL queries
	*/

	namespace apf\db\mysql{

		class Select extends \apf\db\DMLQuery{

			public function getSQL(){

				$columns	=	implode(',',$this->getColumns());
				$sql		=	sprintf("SELECT %s",$columns);

				$tables	=	Array();

				foreach($this->getTables() as $table){

					if($table->getAlias()){
						$tables[]=sprintf("%s AS %s",$table,$table->getAlias());
					}else{
						$tables[]=sprintf("%s",$table);
					}

				}

				if(!empty($tables)){

					$sql	=	sprintf("%s FROM %s",$sql,implode(',',$tables));

				}

				$where	=	$this->getWhere();

				if($where){

					$sql	=	sprintf("%s WHERE %s",$sql,$where);

				}

				$group	=	$this->getGroup();

				if($group){

					$sql	=	sprintf("%s GROUP BY %s",$sql,$group);

				}

				$having	=	$this->getHaving();

				if($having){

					$sql	=	sprintf("%s HAVING %s",$sql,$having);

				}

				$order	=	$this->getOrder();

				if($order){

					$sql	=	sprintf("%s ORDER BY %s %s",$sql,$order["fields"],$order["sort"]);

				}

				return $sql;

			}

		}

	}
?>
