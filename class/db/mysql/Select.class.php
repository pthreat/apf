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


				if(sizeof($this->getWhere())){

					$where	=	$this->getWhere()["string"];
					$sql		=	sprintf("%s WHERE %s",$sql,$this->getWhere()["string"]);

				}

				return $sql;

			}

		}

	}
?>
