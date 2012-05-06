<?php
include_once(dirname(__FILE__) . "/../systemBlock.php");
function smarty_function_systemBlock($params, &$smarty){
	$name=$params["name"];
	if (defined("LANG")){
		$data=blsfe_helper_systemBlock($name);
		if (isset($data["translations"][LANG]["data"])){
			return $data["translations"][LANG]["data"];
		}
	}
};

