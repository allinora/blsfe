<?php
function smarty_function_tabactive($params, &$smarty){
	$name = $params["name"];
	if ($name == $smarty->getTemplateVars('action')){
      return "class = 'active'";
	}

}

