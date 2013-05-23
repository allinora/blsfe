<?php
function smarty_function_subtab($params, &$smarty){
	$name=$params["name"];
	$label=$params["label"];
	$controller=$params["controller"];
	$action=$params["action"];
	$current=$smarty->tpl_vars["current"]->value;
	
	if (!$label){
		$label=ucFirst($name);
	}
	if (!$action){
		$action="index";
	}
	
	$lang=LANG;
	$str="<a class='subtab ";
	if ($name==$current){
		$str.=" active ";
	}
	$str .= "' href='/$lang/$controller/$action'>$label</a>";
	return $str;
};

