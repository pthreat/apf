<?php

	/**
	*Abstract class for all DML query types 
	*
	*SELECT
	*UPDATE
	*DELETE
	*INSERT
	*
	*This class is meant to be an intermmediary between all different
	*database types (mysql,pgsql,oracle,sql server, etc).
	*This class does not contain ANY SQL specific instructions.
	*/

	namespace apf\db{

		abstract class DMLQuery extends Query{

			private	$resultAs	=	"array";
			private	$rowMap		=	NULL;
			private	$columnMap	=	NULL;
			private	$columns		=	Array();
			private	$where		=	'';
			private	$having		=	'';
			private	$group		=	Array();
			private	$order		=	Array();

			/**
			*Specify list of columns to fetch
			*
			*@param Array $columns Columns Can also be an Array, this is the prefered and more performant form
			*@param String $columns Columns can be a list of comma separated values (less performant)
			*@return DMLQuery returns this very same DMLQuery Object
			*/

			public function columns($columns=NULL){

				if(is_string($columns)){

					\apf\validate\String::mustBeNotEmpty("Must specify columns");

					$columns	=	Array($this->parseColumns($columns));

				}

				$this->columns	=	$columns;

				return $this;

			}

			private function parseColumns($columns){

				$parsedColumns	=	Array();

				$pos	=	0;

				do{

					$columns	=	substr($columns,$pos);
					$column	=	substr($columns,0,strpos($columns,','));

					if(empty($column)){

						$column	=	$columns;

					}

					if(preg_match('/t[0-9]+\.ALL/i',$column)){

						$tableIndex	=	(int)substr($column,1,strpos($column,'.')-1);
						$table		=	$this->getTableByIndex($tableIndex);

						if(!$table){

							throw new \Exception("No such table $tableIndex");

						}

						$tablesAmount	=	sizeof($this->getTables());

						foreach($table->getColumns() as $column){

							var_dump($column);
							die();

							if($tablesAmount>1){

								$parsedColumns[]	=	sprintf("t%d.%s AS %s%s",$tableIndex,$column["name"],$table,"_$column[name]");
								continue;

							}

							$parsedColumns[]	=	sprintf("t%d.%s",$tableIndex,$column["name"]);

						}

					}


				}while($pos=strpos($columns,','));

				return implode($parsedColumns,',')."\n";

			}

			public function where($clause=NULL){

				$this->where	=	\apf\validate\String::mustBeNotEmpty($clause,"WHERE clause can't be empty");
				return $this;

			}

			public function having($clause){

				$this->having	=	\apf\validate\String::mustBeNotEmpty($clause,"HAVING clause can't be empty");
				return $this;

			}

			public function group($fields=NULL){

				$this->group	=	$fields;
				return $this->group;

			}

			public final function getHaving(){

				return $this->having;

			}

			public final function getGroup(){

				return $this->group;

			}

			public function order($field,$sort="ASC"){

				$this->order	=	Array("field"=>$field,"sort"=>$sort);
				return $this;

			}

			public final function getOrder(){

				return $this->order;

			}

			public final function getColumns(){

				if(sizeof($this->columns)){

					return $this->columns;

				}

				foreach($this->getTables() as $table){

					$alias	=	$table->getAlias();
					$columns	=	Array();

					foreach($table->getColumns() as $col){

						$columns[]	=	$alias	?	$alias.'.'.$col["name"]	:	$col["name"];

					}

					return $columns;

				}

			}

			protected function getWhere(){

				return $this->where;

			}

			public function run(Array $bind=Array()){

				if(sizeof($bind)){

					$this->bindParams($bind);

				}

				$sql		=	$this->getSQL();
				$this->parseQuery($sql);
				$conName	=	NULL;

				foreach($this->getTables() as $key=>$table){

					if(is_null($conName)){

						$conName	=	$table->getConnectionId();	
						continue;

					}

					//Different connections! Dump table into first table schema
					if($table->getConnectionId()!==$conName){

						//Make a copy of the table into the first connection schema
						$table->copyTo($this->getTableByIndex($key-1));

					}

				}

				$db	=	\apf\db\Pool::getConnection($conName);
				$stmt	=	$db->prepare($sql);

				foreach($this->getBoundParams() as $bindName=>$column){

					$stmt->bindParam($bindName,$column->getValue(),$column->getPDOType());	

				}

				$stmt->setFetchMode(\PDO::FETCH_ASSOC);

				if(!$stmt->execute()){

					throw new \Exception("Error preparing query: $sql");

				}

				return new DMLResult($this,$stmt);

			}

		}

	}

?>
