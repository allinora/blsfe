<?php

class BLHelper{

	function __construct(){
	}
	
	function __call($name, $params){
		if (!defined("ROOT")){
			return;
		}
		$helpers_directory=ROOT . DS . "application" . DS . "helpers";
		$helper_file=$helpers_directory . DS . $name . ".php";
		$functionName=$name;

		//print "Searching for helper $name<br>";

		if(substr($name, 0,12)=="blsfe_helper"){
			//print "System helper " .  substr($name, 13);
			$helpers_directory=BLSFE_ROOT . DS .  "helpers";
			$helper_file=$helpers_directory . DS . substr($name, 13) . ".php";
		};
		//print "Loading $helper_file<br>";
		if (file_exists ($helper_file)){
			include_once($helper_file);
		}
		if (function_exists($name)){
			return call_user_func_array($functionName,$params);
		}
	}

}
