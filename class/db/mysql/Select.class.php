<?php

	/**
	*MySQL query Builder class, this class is used to build SELECT SQL queries
	*/

	namespace apf\db\mysql{

		class Select extends Query{

			public function getSQL(){

				foreach($this->getTables() as $table){
					var_dump($table->getColumnsAsString());
				}

			}

		}

	}
?>
