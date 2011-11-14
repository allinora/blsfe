<?php

$routing=array();
$routing['@^admin/*(.*)@']='core/admin/\1';

// Override the defaults
$default['controller'] = 'products';
$default['action'] = 'list';



