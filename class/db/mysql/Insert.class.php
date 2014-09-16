<?php

	/**
	*MySQL query Builder class, this class is used to build INSERT SQL queries
	*/

	namespace apf\db\mysql5{

		class Insert extends Query {

			public function getSQL(){

				$sql		=	"INSERT INTO ".$this->table." SET ".$this->getFields();
				return $sql;
				
			}

			public function getResult(){

				return $this->adapter->insert_id;

			}

		}

	}

?>
