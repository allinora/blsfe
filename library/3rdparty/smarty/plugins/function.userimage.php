<?php

function smarty_function_userimage($params, &$smarty){
    $avatar=$params["data"]["avatar"];

    $x=$params["width"];
    $y=$params["height"];

    $css_class=$params["class"];
    $stle=$params["style"];
    $title=$params["title"];

    if (!$avatar) {
		$pic=($params["data"]["gender"]=="m")?"avatar_m.png":"avatar_f.png";
		return "<img src='/staticmedia/images/icons/$pic' class='$css_class'  width='$x' height='$height' title='$title'>";
    }
}

/* vim: set expandtab: */

