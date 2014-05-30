<?

define('_DEBUG_', true);

date_default_timezone_set('Europe/Warsaw'); 
error_reporting(_DEBUG_? E_ALL ^ E_STRICT: 0);
ini_set('display_errors', _DEBUG_);

define('DIR_ROOT', str_replace('\\', '/', dirname(dirname(__FILE__))));
define('DIR_SYS', str_replace('\\', '/', dirname(__FILE__)));
define('DIR_APP', DIR_SYS.'/app');
define('DIR_LIBS', DIR_SYS.'/libs');
define('DIR_LOGS', DIR_SYS.'/logs');
define('DIR_XML', DIR_SYS.'/xml');
define('DIR_XSL', DIR_SYS.'/xsl');

function uses() {
	$args = func_get_args();
	foreach ($args as $arg) {
		require_once(DIR_LIBS.'/'.$arg.'.php');
	}
}

uses('debug', 'assert', 'autoload', 'error-handler');

set_error_handler('error_handler');

init_assert(_DEBUG_);

$default_output_type = 'html';
$output_types = array(
	'html' => 'text/html',
	'text' => 'text/plain',
	'xml' => 'text/xml',
);

$output = empty($_GET['output'])? $default_output_type: $_GET['output'];

header('Content-Type: '.$output_types[$output]);

?>