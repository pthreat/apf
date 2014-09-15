<?php

	namespace apf\traits{

		trait TypeRef{

			use Type;

			abstract public function __construct(&$value='',$cast=FALSE);

		}

	}

?>
