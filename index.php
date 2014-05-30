<?

require 'php/init.php';

uses('xml', 'xml-xsl');

$path = array_pop((array_keys($_GET)));
if (!$path) $path = 'index';

if (XML_XSL::Exists('pages/'.$path, 'default')) {

	$site = xml('site');
	$page = xml('pages/'.$path);

	$page->append($site->extra);

	print XML_XSL::RenderString($page->asXML(), 'default');
}
else {
	die('404 Not found');
}

?>