<?php

function blsfe_helper_systemCategory($id, $field){
	$model=new BLModel("/sys/category", "id");
	$category=$model->get($id);
	return $category[$field];
};

