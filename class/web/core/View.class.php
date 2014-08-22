<?php

	namespace apolloFramework\web{

	   class View{

			private	$_templates	=	Array();
			private	$_vars		=	Array();
			private	$_config		=	NULL;
			private	$_messages	=	NULL;

			public function __construct(\apolloFramework\core\Config $config,$templates=NULL){

				$this->setConfig($config);

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

			public function setConfig(\apolloFramework\core\Config $config){

				$this->_config	=	$config;

			}

			public function addTemplate($template){

				if(empty($template)){

					return FALSE;

				}

				$path						=	$this->_config->getSection("templates");
				$path						=	$path->path;
				$this->_templates[]	=	new \apolloFramework\core\File($path.DIRECTORY_SEPARATOR.$template);

				return TRUE;

			}

			public function addTemplates(Array $templates){

				foreach($templates as $template){

					$this->addTemplate($template);

				}

			}

			public function getTemplates(){

				return $this->_templates;

			}

			public function setVar($name,$value) {

				 $this->$name=$value;

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

				foreach($this->_templates as $template){

					require $template;

				}

				$content	=	ob_get_contents();

				ob_end_clean();

				return $content;
			}

			public function setMessages(Array $messages=Array()){

				if(!sizeof($messages)){

					return;

				}

				$requiredKeys	=	Array("msg","status");

				foreach($messages as &$message){

					\apolloFramework\Validator::arrayKeys($requiredKeys,$message);

					$this->addMessage($message["msg"],$message["status"]);

				}

			}

			public function addMessage($message,$status="error"){

				switch($status){

					case "success":
					case "error":
					break;

					default:
						throw(new \Exception("Unknown message status \"$status\""));
					break;

				}

				$this->_messages[]	=	Array("msg"=>$message,"status"=>$status);

			}

			public function getMessages(){

				return $this->_messages;

			}

			public function getMessagesAsHtml($template=NULL){

				if(!sizeof($this->_messages)){
					return;
				}

				if(!is_null($template)){

					$view=new View($this->_config);
					$view->addTemplate($template);
					$view->addVar("messages",$messages);
					return $view->renderAsString();

				}

				$str	=	'<div class="apfw msglist">';

				foreach($this->_messages as $msg){
					$str.='<div class="'.$msg["status"].'">'.$msg["msg"].'</div>';		
				}

				$str	.=	'</div>';

				return $str;

			}

			public function render() {

				if(!sizeof($this->_templates)){

					throw(new \Exception("Can't call render(), no templates have been added to this View object"));
					return;

				}


				foreach($this->_templates as $template){

					require $template;

				}


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
