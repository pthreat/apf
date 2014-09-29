<?php

	namespace apf\dbc\main\test{

		class entries extends \apf\db\mysql\Table{

			protected $connectionId		=	"main";
			protected $schema				=	"test";
			protected $name				=	"entries";
			protected $alias				=	"";
			protected $columns			=	array('id'=>array('name'=>'id','type'=>'int','extra'=>'auto_increment','key'=>'PRI','charset'=>NULL,'maxlen'=>NULL,'octlen'=>NULL,'null'=>false,'unsigned'=>true,'php_type'=>'int','pdo_type'=>'PDO::PARAM_INT',),'value'=>array('name'=>'value','type'=>'varchar','extra'=>NULL,'key'=>NULL,'charset'=>'latin1','maxlen'=>'20','octlen'=>'20','null'=>false,'pdo_type'=>'PDO::PARAM_STR','php_type'=>'string',),);

			public function __construct($connectionId=NULL,$schema=NULL,$name=NULL){

				parent::setConnectionId(empty($connectionId)	?	$this->connectionId : $connectionId);
				parent::setSchema(empty($schema)	?	$this->schema : $schema);
				parent::setName(empty($name)	?	$this->name : $name);
				parent::setColumns($this->columns);

			}

		}

	}

?>
