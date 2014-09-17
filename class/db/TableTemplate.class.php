<?php

	namespace [fqdn]{

		class [tablename] extends [basetable]{

			protected $connectionId		=	"[connectionId]";
			protected $schema				=	"[schema]";
			protected $name				=	"[name]";
			protected $alias				=	"[alias]";
			protected $columns			=	[columns];

			public function __construct($connectionId=NULL,$schema=NULL,$name=NULL){

				parent::setConnectionId(empty($connectionId)	?	$this->connectionId : $connectionId);
				parent::setSchema(empty($schema)	?	$this->schema : $schema);
				parent::setName(empty($name)	?	$this->name : $name);
				parent::setColumns($this->columns);

			}

		}

	}

?>
