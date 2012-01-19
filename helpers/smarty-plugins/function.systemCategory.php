<?php
include_once(dirname(__FILE__) . "/../systemCategory.php");
function smarty_function_systemCategory($params, &$smarty){
	$id=$params["id"];
	$field=$params["field"];
	$data=blsfe_helper_systemCategory($id, $field);
	return $data;
};

