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

				\apf\Validator::emptyString("Must specify columns");

				if(is_string($columns)){

					$columns	=	Array($this->parseColumns($columns));

				}

				$this->columns	=	$columns;

				return $this;

			}

			/**
			*Provides a way to map a single column of a row to a class.
			*This is VERY handy and avoids lots of extra operations.
			*
			*@param String $name The name of the column
			*@param String $class The class which the column should be mapped to
			*@param String $method Optional, provide a method which should be called, 
			*if no method is specified, the value of the column will be passed to the constructor of the class.
			*The method should NOT be static.
			*
			*/

			public function mapColumnToClass($name,$class,$method=NULL){

				$this->columnMap[$name]	=	Array(
															"type"	=>	"instance",
															"value"	=>	$class,
															"method"	=>	$method
				);

				return $this;

			}

			/**
			*Provides a way to map a single column of a row to a STATIC METHOD of class.
			*This is VERY handy and avoids lots of extra operations.
			*
			*@param String $name The name of the column
			*@param String $class The class which the column should be mapped to
			*@param String $method REQUIRED, the static method that should be called
			*if no method is specified, this method will assume that you have a STATIC method in this class named
			*columnMap.
			*The method HAS TO BE be static.
			*
			*/

			public function mapColumnToStaticClass($name,$class,$method=NULL){

				$this->columnMap[$name]	=	Array(
															"type"	=>	"static",
															"value"	=>	$class,
															"method"	=>	$method,
				);

				return $this;

			}

			/**
			*Maps a column to a callable function, anonymous or not.
			*This provides a way to do some "hacking" because it allows you
			*to map a column to a class and method, or to a static method or to whatever you want.
			*However this kind of hacking is discouraged, due to this making your code HARD to read.
			*If you plan using this for mapping a column to a class please notice that it is heavily discouraged.
			*@param Callable $function declared or anonymous function 
			*@param String $column Column name
			*
			*@return DMLQuery Instance of this object.
			*/

			public function mapColumnToFunction($name=NULL,callable $function){

				$this->columnMap[$column]	=	Array(
																"type"	=>	"callback",
																"value"	=>	$function
				);

				return $this;

			}

			/**
			*Maps an entire row to a class
			*@param String $class Class name
			*@param String $method Optional method to be called
			*/

			public function mapRowToClass($class,$method=NULL){

				$this->rowMap	=	Array(
													"type"	=>	"class",
													"value"	=>	$class,
													"method"	=>	$method
				);

				return $this;

			}



			/**
			*Fetch an entire row as a class
			*@param String $class Class name
			*@param String $method Optional method to be called
			*/

			public function fetchAs($class=NULL,$method=NULL){

				\apf\Validator::emptyString($class,"Must specify class to fetch rows as the specified class");

				if(!is_null($method)){

					$refMethod	=	new \ReflectionMethod("$class::$method");
					
					if(!$refMethod->isStatic()){

						throw new \Exception("Class \"$class\" needs to have a *STATIC* method named \"$method\" ");

					}

				}else{

					$reflectionClass = new \ReflectionClass($class);

				}

				$this->fetchAs	=	Array("class"=>$class,"method"=>$refMethod);

				return $this;

			}

			public function getFetchAs(){

				return $this->fetchAs;

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

				$this->where	=	\apf\Validator::emptyString($clause,"Where clause can't be empty");
				return $this;

			}

			public function having($clause){

				$this->having	=	\apf\Validator::emptyString($clause,"Where clause can't be empty");
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
