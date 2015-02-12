<?php

	namespace apf\web\core{

	   class View{

			private	$templates		=	Array();
			private	$vars				=	Array();
			private	$messages		=	NULL;

			public function __construct($templates=NULL){

				$config	=	\apf\core\Config::getSection("view");
				$this->validateConfig($config);

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

			public function renderPack($pathToPackFile){


				$config	=	\apf\core\Config::getSection("view");
				$this->validateConfig($config);

				$path			=	$config->template_path;
				$packFile	=	$path.DIRECTORY_SEPARATOR.$pathToPackFile;

				$packFile	=	new \apf\core\File($packFile);
				$packFile->setReadFunction("fgets");

				foreach($packFile as $line){
					$line	=	trim($line,"\r\n");
					$this->load($line);
				}

				$packFile->close();

				die();

			}

			public function addTemplate($template=NULL,$first=NULL){

				\apf\validate\String::mustBeNotEmpty($template,"Template to be added can't be empty");

				$config		=	\apf\core\Config::getSection("view");
				$path			=	$config->template_path;
				$this->templates[]	=	new \apf\core\File($path.DIRECTORY_SEPARATOR.$template);

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

			public function getTemplates(){

				return $this->templates;

			}

			public function setVar($name,$value) {

				 $this->$name=$value;

		   }

			private function load($tpl=NULL){

				static $template	=	NULL;

				$template	=	$tpl;

				\apf\validate\String::mustBeNotEmpty($tpl,"Must provide template name to load");

				$config	=	\apf\core\Config::getSection("view");
				$this->validateConfig($config);

				$ext		=	trim($config->template_extension,'.');
				$tpl		=	strpos($tpl,".$ext")	?	$tpl	:	$tpl.".$ext";
				$tpl		=	$config->template_path.DIRECTORY_SEPARATOR.$tpl;

				try{

					$tpl		=	new \apf\core\File($tpl);
					require $tpl;

				}catch(\Exception $e){

					throw new \Exception("Template $template not found in ".$config->template_path);

				}

			}


			public function setVarArray(Array &$values){

				foreach($values as $name=>$value){

					$this->setVar($name,$value);

				}

		   }

			public function getVars(){

				return $this->vars;

			}

			public function renderAsString(){

				if(!sizeof($this->templates)){

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

				if(!sizeof($this->templates)){

					throw(new \Exception("Can't call render(), no templates have been added to this View object"));
					return;

				}

				foreach($this->templates as $template){

					$this->load($template);

				}

			}

			public function resetTemplates(){

				$this->templates	=	Array();

			}

			public function __toString(){

				$str	=	NULL;

				if(!sizeof($this->templates)){

					return "";

				}

				foreach($this->templates as $template){

					$str	.= file_get_contents($template);

				}

				return $str;

			}

		}
 
	}

?>
