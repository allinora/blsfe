<?php
include_once(dirname(__FILE__) . "/../systemCategory.php");
function smarty_function_systemCategory($params, &$smarty){
	$id=$params["id"];
	$field=$params["field"];
	if ($id>0){
		$data=blsfe_helper_systemCategory($id, $field);
	} else {
		// In case the sub_category_id is available and category is needed.
		if ($params["sub_category_id"]){
			include_once(dirname(__FILE__) . "/../systemSubCategory.php");
			$category_id=blsfe_helper_systemSubCategory($params["sub_category_id"], "category_id");
			if ($category_id>0){
				$params["id"]=$category_id;
				return smarty_function_systemCategory($params, &$smarty);
			}
		}
	}
	return $data;
};

