<?php
include_once(dirname(__FILE__) . "/../companyImage.php");
function smarty_function_companyImage($params, &$smarty){
	$id=$params["id"];
	$x=$params["width"];
	$y=$params["height"];
	return  blsfe_helper_companyImage($id,$x,$y, $params);
};

