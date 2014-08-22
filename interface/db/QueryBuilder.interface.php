<?php

	namespace aidSQL\db {

		interface QueryBuilderInterface{
			
			public function setCommentOpen($commentOpen);
			public function setCommentClose($commentClose);
			public function select(Array $fields);
			public function from($table);
			public function where(Array $conditions);
			public function orderBy($field,$sort=NULL);
			public function union(Array $fields,$unionType="");
			public function limit(Array $limit);
			public function setFieldEqualityCharacter($equalityCharacter);
			public function setSpaceCharacter($_space);
			public function getSpaceCharacter();
			public function getSQL();
			public function setSQL($sql);
			public function __toString();
			public function join($joinType="INNER",$table,Array $condition);
			public function group(Array $group);
			public function toOutFile($file);
			public function reset();
			

		}

	}
