<?php
function smarty_modifier_slugify($string){
    return tipi_call_function("slugify", $string);
}

