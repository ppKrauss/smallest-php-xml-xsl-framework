<?

assert("defined('DIR_XSL')");
assert("defined('DIR_XML')");

class XML_XSL {

	public static function Exists($xml_file, $xsl_file=false) {
		if (!$xsl_file) $xsl_file = $xml_file;
		$xml_file = DIR_XML.'/'.$xml_file.'.xml';
		$xsl_file = DIR_XSL.'/'.$xsl_file.'.xsl';

		return 
			file_exists($xml_file) && 
			is_readable($xml_file) && 
			file_exists($xsl_file) && 
			is_readable($xsl_file);
	}

	public static function Render($xml_file, $xsl_file=false) {

		if (!$xsl_file) $xsl_file = $xml_file;
		$xml_file = DIR_XML.'/'.$xml_file.'.xml';
		$xsl_file = DIR_XSL.'/'.$xsl_file.'.xsl';

		$xml = new DOMDocument;
		$xml->substituteEntities = true;
		if ($xml->load($xml_file) == false) {
			user_error("Could not load XML file: {$xml_file}", E_USER_ERROR);
		}

		$xsl = new DOMDocument;
		$xsl->substituteEntities = true;
		if ($xsl->load($xsl_file) == false) {
			user_error("Could not load XSLT file: {$xsl_file}", E_USER_ERROR);
		}

		$proc = new XSLTProcessor();
		$proc->importStyleSheet($xsl);
		return $proc->transformToXML($xml);
	}

	public static function RenderString($xml_string, $xsl_file) {
		$xml = new DOMDocument;
		$xml->substituteEntities = true;

		$xsl_file = DIR_XSL.'/'.$xsl_file.'.xsl';

		if ($xml->loadXML($xml_string) == false) {
			user_error("Could not load XML file: {$xml_file}", E_USER_ERROR);
		}

		$xsl = new DOMDocument;
		$xsl->substituteEntities = true;
		if ($xsl->load($xsl_file) == false) {
			user_error("Could not load XSLT file: {$xsl_file}", E_USER_ERROR);
		}

		$proc = new XSLTProcessor();
		$proc->importStyleSheet($xsl);
		return $proc->transformToXML($xml);
	}
}

?>