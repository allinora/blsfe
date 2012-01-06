<?php
include_once(dirname(__FILE__) . "/../userData.php");
function smarty_function_userData($params, &$smarty){
	$user_id=$params["user_id"];
	$field=$params["field"];
	$data=blsfe_helper_userData($user_id, $field);
	return $data;
};

