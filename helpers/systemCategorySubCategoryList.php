<?php

function blsfe_helper_systemCategorySubCategoryList($id=0){
    $model=new BLModel("/sys/category", "id");
	$categories=$model->getFullTree();
	// print "<pre>" . print_r($categories, true) . "</pre>";
	
	
	$text="<select name='sub_category_id' id='sub_category_id'>";
	foreach($categories as $category){
		if ($category["children"]){
			foreach($category["children"] as $subcat){
				$text.="<option value='" . $category["id"] . "'";
				if ($id==$subcat["id"]){
					$text.=" selected ";
				}
				$text.=">" . $category["name"] . "::"  . $subcat["name"] .  "</option>";
			}
		}
	}
	$text.="</select>";
	return $text;
}

