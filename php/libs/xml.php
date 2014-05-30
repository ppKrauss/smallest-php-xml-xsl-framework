<?

assert("defined('DIR_XML')");
assert("is_dir(DIR_XML)");

function xml ($name) {
	return simplexml_load_file(DIR_XML.'/'.$name.'.xml', 'XML');
}

class XML extends SimpleXMLElement implements ArrayAccess {

	function attr($name, $value=false) {
		return (string) $this->attributes()->$name;
	}

	function offsetExists($offset) {
		print 'offsetExists<br>';
		return isset($this->attributes()->$offset);
	}

	function offsetGet($offset) {
		print 'offsetGet<br>';
		return $this->attributes()->$offset;
	}

	function offsetSet($offset, $value) {
		print 'offsetSet<br>';
		return $this->attributes()->$offset = $value;
	}

	function offsetUnset ($offset) {
		print 'offsetUnset<br>';
		unset($this->attributes()->$offset);
	}

	function asXML($filename=false) {
		$doc = new DOMDocument('1.0', 'UTF-8');
		$doc->formatOutput = true;
		$domnode = dom_import_simplexml($this);
		$domnode = $doc->importNode($domnode, true);
		$domnode = $doc->appendChild($domnode);
		$xml = $doc->saveXML();

		return $filename? file_put_contents($filename, $xml): $xml;
	}

	function append(SimpleXMLElement $new_child){
	   $node1 = dom_import_simplexml($this);
	   $dom_sxe = dom_import_simplexml($new_child);
	   $node2 = $node1->ownerDocument->importNode($dom_sxe, true);
	   $node1->appendChild($node2);
	}

	function asHTML() {
		return nl2br(str_replace(array(' ','<','>'), array('&nbsp;','&lt;','&gt;'), $this->asXML()));
	}
}

?>