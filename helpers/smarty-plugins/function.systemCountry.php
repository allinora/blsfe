<?php
include_once(dirname(__FILE__) . "/../systemCountry.php");
function smarty_function_systemCountry($params, &$smarty){
	$code = $params['code'];
	$field = $params['field'];
	$data = blsfe_helper_systemCountry($code, $field);
	return $data;
};

