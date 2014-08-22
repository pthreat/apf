<?php

	/**
	*MySQL query Builder class, this class is used to build INSERT SQL queries
	*/

	namespace db\mysql5{

		class Replace extends \db\mysql5\Query {

			public function getSQL(){

				$sql		=	"REPLACE INTO ".$this->table." SET ".$this->getFields();
				return $sql;
				
			}

			public function getResult(){

				return $this->adapter->affected_rows;

			}

		}

	}

?>
