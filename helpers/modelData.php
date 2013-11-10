<?php
function blsfe_helper_modelData($model, $idField, $field_name, $id){
	
    $model = new BLModel($model, $idField);
	if ($id) {
		$aData = $model->get($id);
		if (is_array($aData)){
			if (isset($aData[$field_name])){
				return $aData[$field_name];
			}
		}
	}
}

