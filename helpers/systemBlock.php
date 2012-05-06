<?php

function blsfe_helper_systemBlock($name){
	$model=new BLModel("/sys/block", "id");
	$data=$model->getBlockWithTranslationsByName(array("name" => $name));
	return $data;
};

