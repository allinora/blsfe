<?php	

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));
define('BLSFEROOT', '/Users/aghaffar/www/blsfe');

$url = $_GET['url'];

require_once (BLSFEROOT . DS . 'library' . DS . 'bootstrap.php');