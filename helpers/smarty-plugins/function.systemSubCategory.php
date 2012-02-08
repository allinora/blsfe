<?php

include_once(dirname(__FILE__) . "/../systemSubCategory.php");
function smarty_function_systemSubCategory($params, &$smarty){
	$id=$params["id"];
	$field=$params["field"];
	$data=blsfe_helper_systemSubCategory($id, $field);
	return $data;
};

