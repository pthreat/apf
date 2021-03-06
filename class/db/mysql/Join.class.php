<?php

	/**
	*MySQL query Builder class, this class is used to build SELECT SQL queries
	*/

	namespace apf\db\mysql5{

		class Join extends Query{

			private		$select		=	NULL;
			protected	$sqlArray	=	Array(
														"on"	=>NULL,
														"type"=>NULL,
			);

			public function setSelect(\apf\db\mysql5\Select $select){

				$this->select	=	$select;

			}

			public function type($type){

				$type	=	strtoupper($type);

				switch(strtoupper($type)){
					case "RIGHT":
					case "INNER":
					case "LEFT":
					case "OUTTER":
					case "CROSS":
					case "NATURAL":
						$this->sqlArray["type"]	=	$type;
					break;

					default:
						throw(new \Exception("Unknown JOIN type \"$type\""));
					break;

				}

			}

			public function on($conditions){

				if(is_string($conditions)){
			
					$this->sqlArray["on"].=	$this->adapter->real_escape_string($conditions);
					return $this->select;

				}

				foreach($conditions as $key=>$value){

					$on	=	'';

					if(!is_array($value)){

						throw(new \Exception("Query format error, given array subelement is not an array"));

					}
					
					if(!isset($value["field"])){

						$value["field"]='';

					}else{

						$value["field"]	=	$this->adapter->real_escape_string($value["field"]);

					}

					if(isset($value["operator"])){

						$value["operator"]	=	$this->adapter->real_escape_string($value["operator"]);

					}elseif(!isset($value["operator"])&&isset($value["value"])){
						
						$value["operator"]	=	'=';

					}else{

						$value["operator"]	=	$this->space;

					}


					$on	=	$value["field"].$this->space.$value["operator"].$this->space;

					if(!isset($value["value"])){

						$value["value"]='';

					}else{

						if(isset($value["quote"]) and $value["quote"] == TRUE  ){

							$on.="'".$this->adapter->real_escape_string($value["value"])."'";

						}else{

							$on.=$this->adapter->real_escape_string($value["value"]);

						}

					}


					$this->sqlArray["on"].=	$on;

				}


			}

			public function execute($smart=TRUE,$asObject=TRUE){

				throw(new \Exception("Can't call execute on a join object"));

			}

			public function getSQL(){

				$s			=	$this->space;
				$table	=	$this->table->getName();
				$on		=	$this->sqlArray["on"];

				$sql	=	$this->sqlArray["type"].$s."JOIN".$s.$table.$s."ON".$s.'('.$on.')';

				return $sql;

			}

			public function getResult(){}

		}

	}
?>
