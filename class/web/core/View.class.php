<?php

	namespace apf\web\core{

	   class View{

			private	static	$header	=	NULL;
			private	$_templates			=	Array();
			private	$_vars				=	Array();
			private	$_messages			=	NULL;
			private	$_singleTplPath	=	NULL;
			private	static	$footer	=	NULL;

			public function __construct($templates=NULL){

				if(is_null($templates)){

					return;

				}

				if(!is_array($templates)){

					$this->addTemplate($templates);
					return;

				} 

				if(sizeof($templates)){

					$this->addTemplates($templates);

				}

			}

			public function addTemplate($template=NULL,$first=NULL){

				\apf\Validator::emptyString($template,"Template to be added can't be empty");

				$config	=	\apf\core\Config::getSection("view");
				$this->validateConfig($config);

				$path			=	$config->template_path;
				$ext			=	trim($config->template_extension,'.');
				$template	=	strpos($template,".$ext")	?	$template	:	$template.".$ext";
				$this->_templates[]	=	new \apf\core\File($path.DIRECTORY_SEPARATOR.$template);

				return TRUE;

			}

			private function validateConfig($config){

				if(empty($config)){

					throw(new \Exception("Can't find view section in configuration"));

				}

				if(empty($config->template_path)){

					throw(new \Exception("Can't find template_path in configuration"));

				}

				if(empty($config->template_extension)){

					throw(new \Exception("Can't find template_extension in configuration"));

				}

			}

			public function addTemplates(Array $templates){

				foreach($templates as $template){

					$this->addTemplate($template);

				}


			}

			public static function setHeader($template){
				self::$header	=	$template;
			}

			public static function setFooter($template){
				self::$footer	=	$template;
			}

			public function addFirst($template){



			}

			public function getTemplates(){

				return $this->_templates;

			}

			public function setVar($name,$value) {

				 $this->$name=$value;

		   }

			private function load($base=NULL,$tpl=NULL){

				\apf\Validator::emptyString($base);

				try{

					$config	=	\apf\core\Config::getSection("view");
					$this->validateConfig($config);

					$ext		=	$config->template_extension;

					if(is_null($tpl)){

						$tpl	=	new \apf\core\File($this->_singleTplPath.DIRECTORY_SEPARATOR.$base.".$ext");
						require $tpl;
						return;

					}

					$tpl		=	preg_replace("/\W/",'',$tpl);
					$path		=	$config->template_path.DIRECTORY_SEPARATOR.$base;
					$tpl		=	$path.DIRECTORY_SEPARATOR."$tpl.$ext";
					$tpl		=	new \apf\core\File($tpl);

					require $tpl;

				}catch(\Exception $e){

					throw(new \Exception("Template $tpl not found in: ".$this->_singleTplPath));

				}

			}

			public function setVarArray(Array &$values){

				foreach($values as $name=>$value){

					$this->setVar($name,$value);

				}

		   }

			public function getVars(){

				return $this->_vars;

			}

			public function renderAsString(){

				if(!sizeof($this->_templates)){

					throw(new \Exception("Can't call render(), no templates have been added to this View object"));
					return;

				}

				ob_start();

				$this->render();

				$content	=	ob_get_contents();

				ob_end_clean();

				return $content;

			}

			public function render() {

				if(!sizeof($this->_templates)){

					throw(new \Exception("Can't call render(), no templates have been added to this View object"));
					return;

				}

				if(self::$header){

					$this->addTemplate(self::$header);

				}

				foreach($this->_templates as $template){

					$this->_singleTplPath	=	dirname($template);
					$this->load($template);

				}

				if(self::$footer){

					$this->addTemplate(self::$footer);
					$this->load(array_pop($this->_templates));

				}

				$this->_singleTplPath	=	NULL;


			}

			public function resetTemplates(){

				$this->_templates	=	Array();

			}

			public function __toString(){

				$str	=	NULL;

				if(!sizeof($this->_templates)){

					return "";

				}

				foreach($this->_templates as $template){

					$str	.= file_get_contents($template);

				}

				return $str;

			}

		}
 
	}

?>
