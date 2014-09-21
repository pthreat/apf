<?php

	namespace apf\parser {

		class Dom extends AbstractParser{

			private	$_content	=	NULL;
			private	$_domType	=	"html";

			public function __construct($content=NULL,$domType="html"){

				$this->setContent($content);
				$this->setDomType($domType);
				
			}

			public function setDomType($type="html"){

				$this->_domType	=	$type;

			}

			private function getDomObject(){

				if($this->_verbose>2){

					$this->log('IN:['.__FUNCTION__.']',0,"purple");

				}

				if($this->_verbose>1){

					$this->log("Content length: ".strlen($this->_content),0,"light_purple");

				}

				$DOM	=	new \DomDocument();

				switch($this->_domType){

					case "xml":

						if($this->_verbose>2){

							$this->log('<'.__LINE__.'> DOMDocument->loadXML',0,"purple");

						}

						@$DOM->loadXML($this->_content);

					break;

					case "html":

						if($this->_verbose>2){

							$this->log('<'.__LINE__.'> DOMDocument->loadXML');

						}

						@$DOM->loadHtml($this->_content);

					break;

					default:

						throw(new \Exception("Unknwon DOM type used to load content \"".$this->_domType."\""));

					break;

				}

				return $DOM;

			}
			
			//Fetches all A elements from a given content

			public function fetchLinks(){

				$links	=	$this->fetchTag('a',"href");

				$javascripts	=	Array();
				$anchors			=	Array();
				$pureLinks		=	Array();
				$emails			=	Array();

				foreach($links as $link){
				
					if(preg_match("/javascript\:/i",$link)){

						if(!in_array($link,$javascripts)){

							$javascripts[]=$link;

						}

					}elseif(preg_match("/^#.*/",$link)){

							if(!in_array($link,$anchors)){

								$anchors[]=$link;

							}

					}elseif(preg_match("/^mailto\:/i",$link)){

						if(!in_array($link,$emails)){

							$emails[]=$link;

						}
						
					}else{

						if(!in_array($link,$pureLinks)){

							$pureLinks[]=$link;

						}

					}

				}

				return array(
					"javascript"	=>	$javascripts,
					"anchors"		=>	$anchors,
					"links"			=>	$pureLinks,
					"emails"			=>	$emails
				);

			}

			//Fetches all img elements from a given content

			public function fetchImages(){

				return $this->fetchTag("img","src");

			}

			public function fetchForms(){

				$return	=	array();
				$dom		=	$this->getDomObject();

				$forms	=	$dom->getElementsByTagName("form");

				$return = array();

				$length	=	$forms->length;

				if($length > 0){

					for($i=0;$i<$length;$i++){

						$form			=	$forms->item($i);
						$formName	=	$form->getAttribute("name");
						$formName	=	(!empty($formNname)) ? $formName : "form_$i";
						$curForm					=	array();
						$curForm[$formName]	=	array();

						$curForm[$formName]["attributes"]	=	$this->getNodeAttributes($form);

						$childTypes	=	array( "input",
							"select",
							"textarea"
						);

						//Get all the childs from the current <form>

						$childs		=	$this->getChildNodes($form,$childTypes);	

						//for every child of the <form>

						if($childs){

							foreach($childs as $childKey=>$childNode){

								$nodeName				=	$childNode->nodeName;

								$tmpChild				=	array();
								$tmpChild[$nodeName]	=	array();

								if ($childNode->hasChildNodes()){	//should be a <select>

									$childNodeChilds	=	$this->getChildNodes($childNode);
									$attributes			=	$this->getNodeAttributes($childNode);

									if(!isset($attributes["name"])){
										continue;
									}

									$elementName		=	$attributes["name"];

									$tmpChild[$nodeName]["attributes"]["name"]	=	$elementName;

									$elementValues		=	array();

									foreach($childNodeChilds as $childNodeChild){	//get all the <option> values

										$attributes	=	$this->getNodeAttributes($childNodeChild);

										if(sizeof($attributes)){
											if(isset($attributes["value"])){
												$tmpChild[$nodeName]["attributes"]["values"][]	=	$attributes["value"];
											}
										}

									}

								}else{	//Should be anything else, like an <input>

									$attributes	=	$this->getNodeAttributes($childNode);

									if(sizeof($attributes)){

										if(!isset($attributes["name"])){
											continue;
										}

										$tmpChild[$nodeName]["attributes"]	=	$attributes;

										if(isset($tmpChild[$nodeName]["attributes"]["value"])){

											if(isset($curForm[$formName]["elements"])){

												foreach($curForm[$formName]["elements"] as $key=>$formElement){

													$formElementName	=	key($formElement);
													$formElement		=	$formElement[$formElementName];

													if($formElement["attributes"]["name"]	==	$tmpChild[$nodeName]["attributes"]["name"]){
	
														$tmpChildValue		=	$tmpChild[$nodeName]["attributes"]["value"];

														if(!isset($curForm[$formName]["elements"][$key][$formElementName]["attributes"]["values"])){
															unset($curForm[$formName]["elements"][$key][$formElementName]["attributes"]["value"]);

															$formElementValue	=	$formElement["attributes"]["value"];

															$curForm[$formName]["elements"]
															[$key][$formElementName]["attributes"]
															["values"][]	=	$formElementValue;

															$curForm[$formName]["elements"]
															[$key][$formElementName]["attributes"]
															["values"][]	=	$tmpChildValue;

														}else{

															if(!in_array($tmpChildValue,$curForm[$formName]["elements"][$key][$formElementName]["attributes"]["values"])){
																$curForm[$formName]["elements"]
																[$key][$formElementName]["attributes"]
																["values"][]	=	$tmpChildValue;

															}

														}
													
													}else{
													
														foreach($curForm[$formName]["elements"] as $key=>$formElement){

															$names[]	=	$formElement[key($formElement)]["attributes"]["name"];

														}

														if(!in_array($tmpChild[$nodeName]["attributes"]["name"],$names)){
															$curForm[$formName]["elements"][]	=	$tmpChild;
														}

													}

												}

											}else{

												$curForm[$formName]["elements"][]	=	$tmpChild;

											}

										}else{

											$curForm[$formName]["elements"][]	=	$tmpChild;

										}

									}

								}

							}

							$return[]	=	$curForm;

						}


					}

				}

				return $return;

			}

			public function getChildNodes(\DomNode $node,Array $types=array()){

				if(!$node->hasChildNodes()){
					return FALSE;
				}

				$childs	=	$node->childNodes;

				$return	=	array();

				foreach($childs as $child){

					$nodeName	=	$child->nodeName;

					if(is_a($child,"DomElement")){

						if($hasChilds	=	$this->getChildNodes($child,$types)){

							foreach($hasChilds as $childChild){
								$return[]	=	$childChild;
								continue;
							}
	
						}

						if(sizeof($types)){

							if(in_array($nodeName,$types)){
								$return[]=$child;
							}

						}else{

								$return[]=$child;

						}

					}

				}

				return $return;

			}

			private function getPostFieldsFromInputs($form){

			}

			public function fetchTag($tagName,$attrName=NULL,$limit=NULL){

				if($this->_verbose>2){

					$this->log('IN:['.__FUNCTION__.']');

				}

				$return	=	Array();
				$dom		=	$this->getDomObject();

				if($this->_verbose>2){

					$this->log('<'.__LINE__.">getElementsByTagName($tagName)",0,"purple");

				}

				$tags		=	$dom->getElementsByTagName($tagName);
				$return	=	Array();

				if($this->_verbose>1){

					$this->log("Elements matching $tagName: ".$tags->length,0,"light_purple");

				}


				if($tags->length > 0){


					if(is_null($limit)){

						$limit	=	$tags->length;

					}else{

						if($this->_verbose>1){

							if($limit<$tags->length){

								$this->log("Warning, I will only fetch $limit \"$tagName\" elements out of ".$tags->length,0,"yellow");

							}

						}	

					}

					for($i=0;$i<$limit;$i++){

						$tag		=	$tags->item($i);	
						$value	=	(!is_null($attrName))	?	$tag->getAttribute($attrName)	:	$tag->textContent;

						if($this->_verbose>1){

							$this->log("Found $value",0,"light_purple");

						}

						$return[]	=	$value;

					}

				}

				return $return;

			}

			public function getNodeAttributes(\DomNode $node){

				$attr			=	$node->attributes;
				$attributes	=	array();

				foreach($attr as $attribute=>$domAttr){

					$attributes[$attribute]	=	$domAttr->value;

				}

				return $attributes;

			}

			public function setContent($content=NULL){

				if(empty($content)){

					throw (new \Exception(__CLASS__.": Content is empty!"));

				}

				$this->_content	=	$content;
				
			}

			public function getContent(){

				return $this->_content;

			}

	
			public function getInnerHTML($node){

				$doc	=	$this->getDomObject();

				foreach ($node->childNodes as $child){

					$doc->appendChild($doc->importNode($child, true));

				}

				return $doc->saveHTML();

			}

			public function analyze($content){
			}

			public function getConfig(){
			}

		}

	}
	
?>
