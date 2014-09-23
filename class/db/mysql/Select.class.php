<?php

	/**
	*MySQL query Builder class, this class is used to build SELECT SQL queries
	*/

	namespace apf\db\mysql{

		class Select extends Query{

			public function getSQL(){

				$columns	=	implode(',',$this->getColumns());
				$sql		=	sprintf("SELECT %s",$columns);

				$tables	=	Array();

				foreach($this->getTables() as $table){
					$tables[]=sprintf("%s",$table);
				}

				if(!empty($tables)){

					$sql	=	sprintf("%s FROM %s",$sql,implode(',',$tables));

				}

				$where	=	$this->getWhere();

				if($where){

					$sql	=	sprintf("%s WHERE %s",$sql,$where);

				}

				return $sql;

			}

		}

	}
?>
