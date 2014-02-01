<?php


function blsfe_helper_modelList($model, $action=null, $idField, $searchField, $field_name, $id){
	
    $model=new BLModel($model, $idField, $searchField);
	if(!$action){
	    $rows=$model->getall(1);
	} else {
	    $rows=$model->$action();
	}
	$text="<select name='$field_name' id='$field_name'>";
	$text.="<option value=''>Please select</option>";
	if (is_array($rows)) {
		foreach($rows as $row){
			$text.="<option value='" . $row["id"] . "'";
			if ($id==$row["id"]){
				$text.=" selected ";
			}
			$text.=">" . $row["name"] . "</option>";
		}
	}
	$text.="</select>";
	return $text;
}

