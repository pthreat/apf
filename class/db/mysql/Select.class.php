<?php

	/**
	*MySQL query Builder class, this class is used to build SELECT SQL queries
	*/

	namespace apf\db\mysql{

		private	$fields	=	Array();

		class Select extends Query implements \ArrayAccess{

			protected	$map	=	NULL;

			public function __construct($tables=NULL){

				parent::__construct($tables,$params);

			}

			public function __get($var){

				return $this->offsetGet($var);

			}

			public function __set($var,$value){

				return $this->offsetSet($var,$value);

			}

			public function getCalcFoundRows(){

				return $this->calcFoundRows;

			}

			public function map($map){
				$this->map=$map;
			}
			
			public function getMap(){
				return $this->map;
			}
			
			/*Array access interface*/
			public function offsetGet($offset){

				$this->validateResult();
				return $this->result[$offset];

			}

			/*Array access interface*/
			public function offsetSet($offset,$value){

				$this->validateResult();

				$this->result[$offset]	=	$value;

			}
			
			/*Array access interface*/
			public function offsetUnset($offset){

				$this->validateResult();
				unset($this->result[$offset]);

			}
			
			/*Array access interface*/
			public function offsetExists($offset){

				$this->validateResult();
				return isset($this->result[$offset]);
				
			}

			public function group(Array $group){

				$this->sqlArray["group"] = $group;

			}

			public function toOutFile($file){

				$this->sqlArray["outfile"]	=	$file;

			}

			public function fields(Array $fields=Array()){

				return $this;

			}

			public function join($join,$type="INNER"){

				$join		=	new Join($join);
				$join->type($type);
				$join->setSelect($this);
				$this->sqlArray["join"][]	=	$join;

				return $join;

			}

			public function offset($offset){

				if((int)$offset){
					$this->sqlArray["offset"]	=	(int)$offset;
				}
				
			}

			public function getOffset(){

				return $this->sqlArray["offset"];

			}

			public function orderBy(Array $fields){

				foreach($fields as $field=>&$sort){

					$sort	=	strtoupper($sort);

					if($sort!=="ASC"&&$sort!=="DESC"){

						throw(new \Exception("Unknown sorting order specified \"$sort\""));

					}

					$this->sqlArray["order"][$field]=$sort;

				}

				return;

			}

			public function getOrder(){
			}

			public function union(Select $select){

				$this->sqlArray["union"]	=	$select;

			}

			public function limit(Array $limit){

				$this->sqlArray["limit"]	=	$limit;

			}

			public function getSQL(){

				$s			=	$this->space;

				$where	=	$this->sqlArray["where"];

				if(sizeof($this->sqlArray["fields"])){

					$fields	=	implode($this->fieldDelimiter,$this->sqlArray["fields"]);

					if($this->calcFoundRows){

						$fields	=	"SQL_CALC_FOUND_ROWS".$s.$fields;

					}

				}else{

					$fields		=	$this->table->getFields();

					$tmpFields	=	Array();

					foreach($fields as $f){

						$tmpFields[]	=	$f["name"];	

					}

					$fields	=	implode($this->fieldDelimiter,$tmpFields);

					if($this->calcFoundRows){

						$fields	.=	"SQL_CALC_FOUND_ROWS ".$s.$fields;

					}

					unset($tmpFields);

				}

				if(!is_null($this->sqlArray["union"])){

					$union	=	$this->sqlArray["union"]->getSQL();

				}else{

					$union	=	"";

				}

				if(sizeof($this->sqlArray["limit"])){

					$limit	=	"LIMIT".$s.implode($this->sqlArray["limit"],',');

				}else{

					$limit	=	"";

				}

				$from		=	NULL;

				if(!is_null($this->table)){
					$from	=	"FROM".$s.sprintf("%s",$this->table);
				}


				if(!is_null($this->sqlArray["where"])){

					$where	=	$s."WHERE".$s.$this->sqlArray["where"];

				}else{

					$where	=	"";

				}

				if(sizeof($this->sqlArray["group"])){

					$group	=	"GROUP BY ".implode($this->sqlArray["group"],',');

				}else{

					$group	=	'';

				}

				if(!is_null($this->sqlArray["offset"])){

					$offset	=	"OFFSET ".$this->sqlArray["offset"];

				}else{

					$offset	=	'';

				}

				$join	=	'';

				if(sizeof($this->sqlArray["join"])){

					foreach($this->sqlArray["join"] as $objJoin){

						$join .=	$objJoin->getSQL().$s;

					}

				}

				$having	=	'';
				$order	=	$this->getOrder();

				$sql	=	"SELECT".$s.$fields.$s.$from.$s.$join.$s.$where.$s.$group.$s.$having.$s.$order.$s.$limit.$s.$offset.$s.$union;
				return $sql;


			}

			public function getResult($smart=TRUE){
			
				if(!$this->result->num_rows){

					return NULL;

				}
				

				if($this->result->num_rows==1&&$smart){

					$result	=	new \apf\db\mysql5\select\Result($this,$this->map);
					$this->result	=	$result;
					return $this->result->fetch();

				}

				$this->result	=	new \apf\db\mysql5\select\Result($this,$this->map);
				
				return $this->result;

			}

			public function __call($method,$args){

				if(is_null($this->result)){
					throw(new \Exception("No valid result set found"));
				}

				call_user_func(Array($this->result,$method),$args);

			}

		}

	}
?>
