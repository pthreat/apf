<?php

	/**
	*MySQL query Builder class, this class is used to build SELECT SQL queries
	*/

	namespace apf\db\mysql{

		class Select extends Query{

			public function getSQL(){

				$sql	=	sprintf("SELECT %s",implode(',',$this->getColumns()));
				return $sql;

			}

		}

	}
?>
