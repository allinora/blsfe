<?php

function blsfe_helper_systemSubCategory($id, $field){
	$model=new BLModel("/sys/category/sub", "id");
	$category=$model->get($id);
	return $category[$field];
};

