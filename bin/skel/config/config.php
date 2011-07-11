<?php

/** Configuration Variables **/

define ('DEVELOPMENT_ENVIRONMENT',true);

define('BASE_URL','/%%appname%%');

define('BLSERVER_URL','http://localhost/tipibls');
define('BLSERVER_SHARED_KEY','s0m3p433');

define('CACHE_BACKEND', 'FILE');
define('CACHE_PATH', ROOT . DS . "tmp" . DS . "cache");

define('SESSION_BACKEND', 'FILE');
define('SESSION_PATH', ROOT . DS . "tmp" . DS . "sessions");
