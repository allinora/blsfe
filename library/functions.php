<?php

/** shared global functions for the framework **/

function htmldump($x){
	print "<pre>" . print_r($x, true) . "</pre>"; 
}

function dumprequest(){
	print "<pre>" . print_r($_REQEST, true) . "</pre>"; 
	
}

