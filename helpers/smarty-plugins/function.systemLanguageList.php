<?php
include_once(dirname(__FILE__) . "/../systemLanguageList.php");
function smarty_function_systemLanguageList($params, &$smarty){
	$selected = $params["selected"];
	$data = blsfe_helper_systemLanguageList($selected);
	return $data;
};