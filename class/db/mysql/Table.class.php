<?php

	namespace apf\db\mysql{

		class Table extends \apf\db\Table{

			public function __construct($connectionId=NULL,$schema=NULL,$name=NULL){
				parent::setConnectionId($connectionId);
				parent::setSchema($schema);
				parent::setName($name);
			}

			public function select(Array $columns=Array()){

				return new Select($this,$columns=Array());

			}

			protected function _find($values,$columns=NULL){
				var_dump($columns);
				die();
			}


			public function getColumnsFromDbSchema(){

				$sql	=	"SELECT COLUMN_NAME,DATA_TYPE,COLLATION_NAME,COLUMN_KEY,EXTRA, ".
							"CHARACTER_SET_NAME,CHARACTER_MAXIMUM_LENGTH,CHARACTER_OCTET_LENGTH, ".
							"IS_NULLABLE,COLUMN_TYPE ".
							"FROM information_schema.COLUMNS ".
							"WHERE TABLE_SCHEMA=:schema AND TABLE_NAME=:table";

				$db		=	\apf\db\Pool::getConnection($this->getConnectionId());
				$stmt		=	$db->prepare($sql);
				$input	=	Array(":schema"=>$this->getSchema(),":table"=>$this->getName());
				$stmt->execute($input);

				$columns	=	Array();

				foreach($stmt as $column){

					$name	=	$column["COLUMN_NAME"];
					$type	=	strtolower($column["DATA_TYPE"]);

					$columns[$name]=Array(
													"name"	=>	$column["COLUMN_NAME"],
													"type"	=>	$type,
													"extra"	=>	empty($column["EXTRA"])	?	NULL	:	$column["EXTRA"],
													"key"		=>	empty($column["COLUMN_KEY"])	?	NULL	:	$column["COLUMN_KEY"],
													"charset"=>	$column["CHARACTER_SET_NAME"],
													"maxlen"	=>	$column["CHARACTER_MAXIMUM_LENGTH"],
													"octlen"	=>	$column["CHARACTER_OCTET_LENGTH"],
													"null"	=>	$column["IS_NULLABLE"]=="NO" ? FALSE : TRUE
					);

					switch($type){

						case "tinyint":
						case "smallint":
						case "mediumint":
						case "bigint":
						case "int":
						case "integer":
						case "serial":
						case "bit":
							$columns[$name]["unsigned"]	=	(boolean)preg_match("/unsigned/",$column["COLUMN_TYPE"]);
							$columns[$name]["php_type"]	=	"int";
							$columns[$name]["pdo_type"]	=	"PDO::PARAM_INT";
						break;

						case "boolean":
							$columns[$name]["php_type"]	=	"boolean";
							$columns[$name]["pdo_type"]	=	"PDO::PARAM_BOOL";
						break;

						case "tinytext":
						case "mediumtext":
						case "text":
						case "longblob":
						case "blob":
							$columns[$name]["php_type"]	=	"string";
							$columns[$name]["pdo_type"]	=	"PDO::PARAM_LOB";
						break;

						case "dec":
						case "float":
						case "double":
						case "decimal":
							$columns[$name]["php_type"]	=	"double";
							$columns[$name]["pdo_type"]	=	"PDO::PARAM_STR";
						break;

						case "date":
						case "datetime":
						case "year":
						case "set":
						case "varchar":
						case "char":
						case "enum":
						default:
							$columns[$name]["pdo_type"]	=	"PDO::PARAM_STR";
							$columns[$name]["php_type"]	=	"string";
						break;

					}

				}

				return $columns;

			}

			public function exists(){

				$sql		=	"SELECT TABLE_NAME FROM information_schema.TABLES ".
								"WHERE TABLE_SCHEMA=:schema AND TABLE_NAME=:table ";
				$db		=	\apf\db\Pool::getConnection($this->getConnectionId());
				$stmt		=	$db->prepare($sql);
				$stmt->execute(Array(":schema"=>$this->getSchema(),":table"=>$this->getName()));

				return (boolean)$stmt->rowCount();
				

			}

		}

	}

	
?>
