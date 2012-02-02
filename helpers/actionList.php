<?php

include_once(dirname(__FILE__) . "/modelList.php");

function blsfe_helper_actionList($id){
	$x=blsfe_helper_modelList("zolomo/action", 'getAllActions' , "id", null, 'action_id', $id);
	return $x;
}

