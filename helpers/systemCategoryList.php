<?php

function blsfe_helper_systemCategoryList($category_id=0){
    $model=new BLModel("/sys/category", "id");
    $categories=$model->getall(1);
	$text="<select name='category_id' id='category_id'>";
	foreach($categories as $category){
		$text.="<option value='" . $category["id"] . "'";
		if ($category_id==$category["id"]){
			$text.=" selected ";
		}
		$text.=">" . $category["name"] . "</option>";
	}
	$text.="</select>";
	return $text;
}

