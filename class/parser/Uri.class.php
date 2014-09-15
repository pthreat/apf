<?php

	namespace apf\parser {

		class Uri {

			private	$_uri						=	array();
			private	$_variables				=	array();
			private	$_varDelimiter			=	'&';
			private	$_equalityOperator	=	'=';
			private	$_queryIndicator		=	'?';
			private	$_pathSeparator		=	'/';
			private	$_restorePath			=	array();
			private	$_modRewrite			=	FALSE;

			public function __construct ($uri=NULL){

				$this->parse($uri);

			}

			public function getVariableDelimiter(){

				return $this->_varDelimiter;

			}

			public function changePath($matchPath=NULL,$newPath=NULL){

				if(empty($matchPath)||empty($newPath)){
					throw(new \Exception("Must enter a path and a new value to assign to the old path when using changePath!"));
				}

				$uriPaths	=	$this->getPathAsArray();

				if(!in_array($matchPath,$uriPaths)){
					throw(new \Exception("Path $matchPath wasnt found in this uri"));
				}

				$this->_restorePath	=	$uriPaths;

				foreach($uriPaths as $index=>$path){

					if($path == $matchPath){
						$uriPaths[$index]	=	urlencode($newPath);
					}

				}

				$this->setPathArray($uriPaths);

			}

			public function restorePath(){
	
				$this->setPathArray($this->_restorePath);

			}

			public function setPathArray(Array $pathArray){

				$this->_uri["path"]	=	implode($this->_pathSeparator,$pathArray);

			}

			public function parse($uri=NULL){

				$dirtyUri	=	$uri;
	
				if(is_array($uri)){
					throw(new \Exception("Array given when String was required!"));
				}

				if(empty($uri)){
					throw(new \Exception("Uri cant be empty!"));
				}

				$uri	= trim($uri);
				$uri	= rtrim($uri,'/');


				$parsedUri	=	array(

					"fullUri"		=>	$uri,
					"scheme"			=>	"http",
					"host"			=>	NULL,
					"path"			=>	'/',
					"page"			=>	NULL,
					"is_relative"	=>	NULL
					
				);

				//SCHEME PARSING
				/////////////////////////////////////////////////


				if(preg_match("#://#",$uri)){

					$parsedUri["scheme"]	=	substr($uri,0,strpos($uri,":"));

				}else{

					$uri	=	$parsedUri["scheme"]."://".$uri;

				}

				$uri	=	substr($uri,strlen($parsedUri["scheme"])+3);

				//HOST PARSING
				/////////////////////////////////////////////////

				if(($pos=strpos($uri,$this->_pathSeparator))!==FALSE){	// '/'

					$parsedUri["host"]	=	substr($uri,0,$pos);

				}elseif($pos = strpos($uri,$this->_queryIndicator)){		// '?'

					$parsedUri["host"]	=	substr($uri,0,$pos);

				}else{

					$parsedUri["host"]	=	trim($uri);
					return $this->_uri	=	$parsedUri;
				}

				//PATH PARSING
				/////////////////////////////////////////////////

				$length					=	strlen($parsedUri["host"]);
				$parsedUri["path"]	=	substr($uri,$length);
				$dirtyPath				=	$parsedUri["path"];
				$tmpPath					=	substr($parsedUri["path"],0,strrpos($parsedUri["path"],$this->_pathSeparator));

				$tmpPath					=	trim($tmpPath,$this->_pathSeparator);

				$parsedUri["path"]	=	(empty($tmpPath)) ? $this->_pathSeparator	:	$tmpPath;

				//PAGE PARSING
				/////////////////////////////////////////////////

				$lastPathPiece				=	substr($dirtyPath,strrpos($dirtyPath,$this->_pathSeparator)+1);
				$tmpLastPathPieceCheck	=	substr($lastPathPiece,0,strpos($lastPathPiece,$this->_queryIndicator));

				if(!empty($tmpLastPathPieceCheck)){
					$lastPathPiece	=	$tmpLastPathPieceCheck;
				}

				if($pos = strrpos($lastPathPiece,'.')){

					$pageExtension	=	substr($lastPathPiece,$pos+1);
					
					if(strlen($pageExtension)>=1 && $pageExtension!='.'){

						$parsedUri["page"]	=	$lastPathPiece;

					}
					
				}else{

					$parsedUri["page"]	=	$lastPathPiece;

				}

				//QUERY PARSING
				/////////////////////////////////////////////////

				if(strpos($uri,$this->_queryIndicator)==FALSE){

					$parsedUri["query"]	=	"";

				}else{

					$parsedUri["query"]	=	substr($uri,strpos($uri,$this->_queryIndicator)+1);
					$this->addRequestVariables($this->queryStringToArray($parsedUri["query"]));

				}

				//Checkout if its a relative path
				
				if(preg_match("/\.\./",$parsedUri["path"])){

					$parsedUri["is_relative"]	=	TRUE;

				}else{

					$parsedUri["is_relative"]	=	FALSE;

				}

				$tmpPath						=	explode($this->_pathSeparator,$parsedUri["path"]);
				$parsedUri["path"]		=	$this->parseRelativePath($tmpPath);

				if($parsedUri["path"]!==$this->_pathSeparator){
					$parsedUri["path"]		=	trim($parsedUri["path"],$this->_pathSeparator);
				}

				if(preg_match("#:\/\/.*:[0-9]+#",$dirtyUri)){

					$scheme					=	$parsedUri["scheme"];
					$host						=	$parsedUri["host"];
					$path						=	$parsedUri["path"];
					$port						=	(int)substr($host,strpos($host,':')+1);
					$parsedUri["port"]	=	$port;
					$parsedUri["host"]	=	substr($parsedUri["host"],0,(strlen($port)+1)*-1);

				}

				$this->_uri	=	$parsedUri;

			}

			public function getPort(){

				if(isset($this->_uri["port"])){
					return $this->_uri["port"];
				}

				return NULL;

			}

			public function setPath($path=NULL){
	
				$this->_uri["path"]=$path;

			}

			private function queryStringToArray($queryString=NULL){

				$variables	=	array();

				if(empty($queryString)){
					return $variables;
				}

				$tmpQuery	=	explode($this->_varDelimiter,$queryString);

				foreach($tmpQuery as $tmpString){

					$tmpVarValue	=	explode($this->_equalityOperator,$tmpString);
					$variables[$tmpVarValue[0]]	=	(isset($tmpVarValue[1])) ? $tmpVarValue[1] : NULL;

				}

				return $variables;

			}

			public function isRelative(){
				return $this->_uri["is_relative"];
			}

			public function addRequestVariable($var,$value=NULL,$uriEncode=TRUE){

				if($uriEncode){

					$this->_variables[]=Array("var"=>$var,"value"=>urlencode($value));

				}else{

					$this->_variables[]=Array("var"=>$var,"value"=>urlencode($value));

				}

			}

			public function replaceRequestVariable($var,$value,$uriEncode=TRUE){

				foreach($this->_variables as &$uriVar){

					if($uriVar["var"]==$var){

						if($uriEncode){

							$uriVar["value"]	=	urlencode($value);

						}else{

							$uriVar["value"]	=	$value;

						}

					}

				}

			}

			public function getRequestVariable($var=NULL){

				foreach($this->_variables as $vars){

					if($vars["var"]==$var){

						return $vars["value"];

					}

				}

				return NULL;

			}

			function addRequestVariables(Array $array){

				foreach($array as $k=>$v){
					$this->addRequestVariable($k,$v);
				}

			}

			public function deleteRequestVariable($var){

				if(isset($this->requestVariables[$var])){
					unset($this->requestVariables[$var]);
					return TRUE;
				}

				return FALSE;

			}

			private function parseVariables(){

				$vars = "";

				foreach ($this->_variables as $k=>$v){

					if (is_null($v["value"])){
						$vars .= $v["var"] . $this->_varDelimiter;
						continue;
					}

					$vars .= $v["var"] . $this->_equalityOperator . $v["value"] . $this->_varDelimiter;

				}

				return substr($vars,0,-1);

			}

			public function getQueryAsArray(){
				return	$this->_variables;
			}

			public function setVariableDelimiter($delimiter=NULL){

				$this->_varDelimiter = $delimiter;

			}

			public function setEqualityOperator($char=NULL){

				$this->_equalityOperator = $char;

			}

			public function getEqualityOperator(){

				return $this->_equalityOperator;

			}

			public function setPathSeparator($char=NULL){

				$this->_pathSeparator = $char;

			}


			public function getPathSeparator($char=NULL){

				return $this->_pathSeparator;

			}


			public function setQueryIndicator($char=NULL){

				$this->_queryIndicator = $char;

			}

			public function getQueryIndicator(){

				return $this->_queryIndicator;

			}

			public function getScheme(){

				return $this->_uri["scheme"];

			}

			public function getHost(){

				return $this->_uri["host"];

			}

			public function getPath(){

				return $this->_uri["path"];

			}

			public function setPage($page=NULL){

				$this->_uri["page"]	=	$page;

			}

			public function getPathAsArray(){

				$paths		=	explode($this->_pathSeparator,$this->_uri["path"]);
				$cleanPath	=	array();

				foreach($paths as $key=>$value){

					if($value=='*'){	//Fix for /* query escaping injection
						continue;
					}

					if(!empty($value)){
						$cleanPath[]	=	$value;
					}

				}

				return $cleanPath;

			}

			public function getPage(){
				return $this->_uri["page"];
			}

			public function getQueryAsString(){

				return $this->parseVariables();

			}

			public function getUriAsString($parameters=TRUE){

				$full	=	$this->_uri["scheme"]."://".$this->_uri["host"];
				$path	=	(isset($this->_uri["path"]))	?	'/'.trim($this->_uri["path"],'/') : '/';
				$page	=	(isset($this->_uri["page"]))	?	'/'.trim($this->_uri["page"],'/') : NULL;

				if($path==$this->_pathSeparator){

					$path=NULL;

				}

				$full	.=	$path.$page;

				if(sizeof($this->_variables)&&$parameters){

					$full	.=	$this->_queryIndicator.$this->parseVariables();

				}

				return $full;
				
			}

			public function getUriAsArray(){
				return $this->_uri;
			}

			public function getVariables(){
				return $this->_variables;
			}

			public function getVariable($variableName){

				if(!isset($this->_variables[$variableName])){
					throw(new \Exception("Unknown variable $variableName"));
				}

				return $this->_variables[$variableName];

			}

			public function parseRelativePath(Array &$path) {

				$r = array();

				foreach ($path as $piece){

					if ($piece === '..'){

						array_pop($r);

					}else{

						if ($piece !== '' && $piece !== '.'){
							array_push($r, $piece);
						}

					} 

				}

				return $this->_pathSeparator.implode($r,$this->_pathSeparator);

			}


			public function __toString(){

				return $this->getUriAsString(TRUE);

			}

			function hasModRewrite(){

				return (sizeof($this->_variables)) ? FALSE	:	TRUE;

			}	

			function unsetRequestVariables(){

				$this->_variables	=	Array();

			}

		}

	}

?>
