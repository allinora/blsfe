<?php

include_once(dirname(__FILE__) . "/../modelData.php");
function smarty_function_modelData($params, &$smarty){
	$model = $params["model"];
	$idField = 'id';
	$field_name = $params["field"];
	$id = $params["id"];

	$data = blsfe_helper_modelData($model, $idField, $field_name, $id);
	return $data;
};

