<?php
// Version
define('VERSION', '3.0.3.2');

// Configuration
if (is_file('config.php')) {
	require_once('config.php');
}

ini_set('error_reporting',0);
// Startup
// require_once('http://localhost/baqala/system/startup.php'); 
require_once(DIR_SYSTEM.'startup.php'); 

start('catalog');