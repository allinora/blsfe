<?php
include_once(dirname(__FILE__) . "/../cmsImageUrl.php");
function smarty_function_cmsImageUrl($params, &$smarty){
	$id=$params["id"];
	$x=$params["width"];
	$y=$params["height"];
	return  blsfe_helper_cmsImageUrl($id,$x,$y);
};

