<?php
include_once(dirname(__FILE__) . "/../companyData.php");
function smarty_function_companyData($params, &$smarty){
	$id=$params["id"];
	$field=$params["field"];
	$data=blsfe_helper_companyData($id, $field);
	return $data;
};

