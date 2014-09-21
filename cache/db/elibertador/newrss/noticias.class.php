<?php

	namespace apf\dbc\elibertador\newrss{

		class noticias extends \apf\db\mysql\Table{

			protected $connectionId		=	"elibertador";
			protected $schema				=	"newrss";
			protected $name				=	"noticias";
			protected $alias				=	"";
			protected $columns			=	array('id'=>array('name'=>'id','type'=>'int','extra'=>'auto_increment','key'=>'PRI','charset'=>NULL,'maxlen'=>NULL,'octlen'=>NULL,'null'=>false,'pdo'=>'PDO::PARAM_INT','unsigned'=>true,),'titulo'=>array('name'=>'titulo','type'=>'varchar','extra'=>NULL,'key'=>NULL,'charset'=>'latin1','maxlen'=>'500','octlen'=>'500','null'=>false,'pdo'=>'PDO::PARAM_STR',),'copete'=>array('name'=>'copete','type'=>'varchar','extra'=>NULL,'key'=>NULL,'charset'=>'latin1','maxlen'=>'500','octlen'=>'500','null'=>false,'pdo'=>'PDO::PARAM_STR',),'cuerpo'=>array('name'=>'cuerpo','type'=>'text','extra'=>NULL,'key'=>NULL,'charset'=>'latin1','maxlen'=>'65535','octlen'=>'65535','null'=>true,'pdo'=>'PDO::PARAM_LOB',),'fecha'=>array('name'=>'fecha','type'=>'datetime','extra'=>NULL,'key'=>NULL,'charset'=>NULL,'maxlen'=>NULL,'octlen'=>NULL,'null'=>false,'pdo'=>'PDO::PARAM_STR',),'estado'=>array('name'=>'estado','type'=>'tinyint','extra'=>NULL,'key'=>NULL,'charset'=>NULL,'maxlen'=>NULL,'octlen'=>NULL,'null'=>true,'pdo'=>'PDO::PARAM_INT','unsigned'=>false,),);

			public function __construct($connectionId=NULL,$schema=NULL,$name=NULL){

				parent::setConnectionId(empty($connectionId)	?	$this->connectionId : $connectionId);
				parent::setSchema(empty($schema)	?	$this->schema : $schema);
				parent::setName(empty($name)	?	$this->name : $name);
				parent::setColumns($this->columns);

			}

		}

	}

?>
