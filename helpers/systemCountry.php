<?php

function blsfe_helper_systemCountry($code, $field){
	$model = new BLModel("/sys/country", "id");
	$aData = $model->getByCode(array('code' => $code));
	if ($aData[$field]){
		return $aData[$field];
	}
};

