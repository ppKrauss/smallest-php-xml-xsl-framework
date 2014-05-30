<?php
/*
 * MAIN CONFIGS AND INITIALIZATIONS
 * version 1.0
 */

// --- --- -- -- 
// BEGIN GENERAL CONFIGS:
	define('_DEBUG_', true);
	date_default_timezone_set('Brazil/East'); 
	session_start();
	$PRJ= (preg_match('/((127\.0\.[0-9]+\.[0-9]+)|(localhost))/i',$_SERVER['HTTP_HOST']))? 'teste': 'production'; 
	$output_types = array(
		'html' => 'text/html',
		'text' => 'text/plain',
		'xml' => 'text/xml',
	);
	$default_output_type = 'html';
// END GENERAL CONFIGS
// --- --- -- -- 

define('DIR_ROOT', str_replace('\\', '/', dirname(dirname(__FILE__))));
define('DIR_SYS', str_replace('\\', '/', dirname(__FILE__)));
define('DIR_LOGS', DIR_SYS.'/logs');	// used by error-handdler (see error_log function)

?>
