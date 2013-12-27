<?php


function blsfe_helper_yesno($field_name, $id=0){
	$text = "Yes <input type='radio' name='$field_name' value='1' ";
		if ($id == 1) {
			$text .= " checked ";
		}
	$text .= ">\n";
	$text .= "No <input type='radio' name='$field_name' value='0' ";
		if ($id == 0) {
			$text .= " checked ";
		}
	$text .= ">\n";
	return $text;
}

