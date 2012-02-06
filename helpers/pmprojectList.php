<?php

include_once(dirname(__FILE__) . "/companyData.php");

function blsfe_helper_pmprojectList($id){
	$field_name="project_id";
	
	$model=new BLModel("sys/pm/project", "id");
	$rows=$model->getAllProjects();
	$text="<select name='$field_name' id='$field_name'>";
	foreach($rows as $row){
		$text.="<option value='" . $row["id"] . "'";
		if ($id==$row["id"]){
			$text.=" selected ";
		}
		$text.=">" . blsfe_helper_companyData($row["company_id"], "name") . " :: "  . $row["name"] . "</option>";
	}
	$text.="</select>";
	return $text;
    
	return $x;
	print "<pre>" . print_r($x, true) . "</pre>";
}

