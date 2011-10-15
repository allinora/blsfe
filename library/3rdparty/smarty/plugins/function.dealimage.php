<?php

function smarty_function_dealimage($params, &$smarty){
	//print "<pre>" . print_r($params, true) . "</pre>";
	
    $id=$params["data"]["deal_id"];
    $image=$params["data"]["image_name"];
    $x=$params["width"];
    $y=$params["height"];
    $title=$params["title"];
    $css_class=$params["css_class"];

    $image_path="http://images.tipiness.com/deals/" . $id . "/";
    $image=preg_replace("/\.(jpg|png|gif)/", "_$1", $image);
    $image = $image_path . $image .  "_" . $x . "x" . $y . "_q85.jpg";

    $image_html = "<img src='$image' class='$css_class' width='$x' height='$y' title='$title'>";
    
    return $image_html;
	
}

/* vim: set expandtab: */

