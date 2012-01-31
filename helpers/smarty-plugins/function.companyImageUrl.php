<?php
include_once(dirname(__FILE__) . "/../companyImageUrl.php");
function smarty_function_companyImageUrl($params, &$smarty){
	$id=$params["id"];
	$x=$params["width"];
	$y=$params["height"];
	return  blsfe_helper_companyImageUrl($id,$x,$y);
};

