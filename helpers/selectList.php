<?php


function blsfe_helper_selectList($field_name, $items=array(), $id=''){
	
	$text="<select name='$field_name' id='$field_name'>";
	$text.="<option value=''>Please select</option>";
	if (is_array($items)) {
		foreach($items as $item){
			$text.="<option value='" . $item . "'";
			if ($id == $item){
				$text.=" selected ";
			}
			$text.=">" . $item . "</option>";
		}
	}
	$text.="</select>";
	return $text;
}

