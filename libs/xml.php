<?php
/**
 * XML basic recording and manipulation. Restrict XML schema for site content and control.
 * This XML must use a predefined root tag (see initXML method).
 * See framework "kernel methods" for differentail methods.
 * 
 * Prefere to initialise and do basic appendToRoot, etc. by (faster) string methods.
 * Any other complex manipulatin, by DOMDocument.
 */
class XML {
	public $xstr; 	// XML string representation (default).
	public $xdom;	// XML DOMDocument representation (alternative).
	var $rooTagName;
	var $rooTag;
	var $detectedType;
	var $detectedTypeXsl;
	var $pageStateTag;


	// // //  BEGIN KERNEL METHODS // // //

	/**
	 * Kernel method, CONSTRUCTOR. 
	 * Prefere a empty root initialization (use append to add content).
	 */
	function XML ($param=NULL) {
		$this->pageStateTag='pageState'; // defined by the FRAMEWORK
		$this->rooTagName='_ROOT_'; 	 // defined by the FRAMEWORK

		$this->xdom = NULL;
		$this->rooTag="<{$this->rooTagName}>";
		if (is_object($param))	 	// set by DOMDocument
			$this->setXMLfromDOM($param);	// param is xdom
		else				// set by XML string
			$this->initXML($param);		// param is xstr
		if ($param!==NULL) 
			if ( !preg_match("/\?>\\s*{$this->rooTag}/",$this->xstr) )
				die(" \n--- XML BUG1: BAD INITIALIZATION ---");
	}


	/**
	 * Kernel method, initialise xstr.
	 * Use a standard root tag, for framework's append and XSLT manipulations.
	 * Use append methods to add content.
	 */
	function initXML($xstr='') {
		if ($xstr || $xstr!==NULL) {
			$this->xstr = $xstr;
		} else
			$this->xstr = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<{$this->rooTagName}></{$this->rooTagName}>";
	}

	/**
	 * Kernel method for append, into the root of xstr, a XML file or a dynamically generated XML.
	 * @page object: converts DOMDocument or SimpleXML to xstr and append it.
	 * @page array: if path is 'database' then $page is a database query descriptor, else $page is a array representation od the XML content.
	 * @page string: if $path=='.' $page is a xml string, else $page is a file name. If $page file name starts with '_' it is a PHP include.
	 * @path string: a file path from the 'xml' directory, or '.' for xstr pages.
	 */
	function appendPage($page,$path='/pages'){
		if (is_object($page)) {   	   	   // DYNAMIC XML by DOMDocument or SimpleXML.
			$type = get_class($page);
			die("appendPage objects EM CONSTRUCAO: '$type' xmlDOM ou simpleXML"); 

		} elseif (is_array($page)) { 
			if ($path=='/pages') $path=''; // change default (ugly code!)

			if ($path=='database') { 	   // DYNAMIC XML by database
				die("appendPage database EM CONSTRUCAO");  // aproveita conexão, exige tipo XML (unico ou array) no retorno.

			} else				   // DYNAMIC XML by assoc. array
				$this->appendToRoot_string(  $this->xarray2xstr($page,$path)  );

		} else {
			// path
			if ($path=='.') {	   	   // DYNAMIC XML string.
				$this->appendToRoot_string(  $page  );

			} elseif (substr($page,0,1)=='_') { // DYNAMIC XML by PHP xml-page generation.
				set_error_handler('error_handler_sp'); //spectial error for NOT LOOP
				ob_start();
				require_once("xml$path/$page.tpl.php"); 		// runs the PHP that outputs XML
				$r = $this->appendToRoot_string( ob_get_contents() );  	// buffer procuces the XML content
				ob_end_clean();
				set_error_handler('error_handler'); //back to normal error
				return $r;

			} else{				   // STATIC XML file.
				return $this->appendToRoot_file("xml$path/$page.xml");
			}
		} // else
	}

	/**
	 * Kernel method for build the page-state array into the standard tag.
	 * @states array: all necessary state variables.
	 */
	function appendPageState(&$states) {
		$this->appendToRoot_string(  $this->xarray2xstr($states,$this->pageStateTag)  );
	}

	/**
	 * Kernel method, façade to appendPage().
	 */
	function appendSiteMap(){
		return $this->appendPage('sitemap', '');
	}

	// // //  END KERNEL METHODS // // //



	/**
	 * Set DOM once. Prepare to use DOMDocument methods with $this->xdom. Use initXML() first.
	 * @refresh boolean: force to sync dom with xml.
	 */
	function setDomFromXML($refresh=false) {
		if ($refresh || $this->xdom===NULL) {
			$this->xdom = new DOMDocument('1.0', 'UTF-8');
			if ($this->xstr) $this->xdom->loadXML( $this->xstr );
		}
		return true;
	}

	/**
	 * Set DOM once. Prepare to use DOMDocument methods with $this->xdom.
	 * @refresh boolean: force to sync dom with xml.
	 */
	function setXMLfromDOM(&$xdom) {
		$this->xdom = $xdom;
		$this->xstr = $this->xdom->saveXML();
		return true;
	}

	function appendToRoot_string($newXML){
		$this->xstr = str_replace($this->rooTag, "{$this->rooTag}\n$newXML\n", $this->xstr);
		$this->typeDetect();
		return true;
	}

	function appendToRoot_file($file){
		return $this->appendToRoot_string(
			preg_replace(
				'/^\s*((<\?xml )|(<!DOCTYPE ))[^>]+>/',
				'', 
				file_get_contents($file)
			)
		);
	}

	/**
	 * Converts XML assoc array representation. 
	 * @xarray array: the XML representation, with reserved keys '_localName' (root) and '_text'.
	 * @root string: optional local-name for root (ignore '_localName').
	 * @return XML string representation (xstr).
	 */
	function xarray2xstr(&$xarray, $root='') {
		if (!is_array($xarray) || count($xarray)==0)
			return $root? "<$root/>": '';
		if (!$root) $root=$xarray['_localName'];
		$xstr = "<$root";
		$text = '';
		$next = array();
		foreach ($xarray as $k=>$v) {
			if (is_array($v)) {
				if (!array_key_exists('_localName',$v)) 
					$v['_localName'] = $k;
				$next[]= $v;
			} elseif ($k=='_text') {
				if ($v) $text = $v;	// add not-empty text
			} elseif ($k!='_localName')
				$xstr .= " $k=\"$v\""; 	// add attribute
		}
		if (count($next))
			foreach ($next as $x)
				$text .= "\n".$this->xarray2xstr($x)."\n"; // check if loop-bugs!
		return $text? "$xstr>$text</$root>": "$xstr/>";
	}

	/**
	 * Detect main content type of the $this->xstr.
	 */
	function typeDetect(){
		if (preg_match('/<article (?:(?:type)|(?:dtd))="([^"]+)(?: xsl="([^"]+))?"/i',$this->xstr,$m) ) {
			$this->detectedType    = $m[1];
			$this->detectedTypeXsl = empty($m[2])? '': $m[2];
		} else
			$this->detectedType = '';
	}

	function RenderFile($xml_file, $xsl_file=false) {
		// facade to RenderString()
		return $this->RenderString($xml_file, $xsl_file, 'file');
		}

	function RenderString($xml_string, $xsl_file, $xmlOrigin='string', $refresh=false, $MAX_FILE_LEN=120) {
		//$this->xstr->substituteEntities = true;
		if ($xml_string=='' ||  $xmlOrigin=='here') {
			$xmlOrigin='here';
			$this->setDomFromXML($refresh);
			$xDom = $this->xdom; // usar ponteiro?  $xDom &= $this->xstr;
		} else {
			$xDom = new DOMDocument;
			if ($xmlOrigin=='string') {
				if ($xDom->loadXML($xml_string) == false)
					user_error("Could not load XML file: {$xml_string}", E_USER_ERROR);
			} else { // suppose it file name
				if (strlen($xml_string)>$MAX_FILE_LEN)
					user_error("File too big in RenderString", E_USER_ERROR);
				if ($xsl->load($xml_string) == false)
					user_error("Could not load XSLT file: {$xml_string}", E_USER_ERROR);
			}
		}
		$xsl = new DOMDocument;
		$xsl->substituteEntities = true;
		if (strlen($xsl_file)>$MAX_FILE_LEN) { // STR MODE
			if ($xsl->loadXML($xsl_file) == false) {
				user_error("Could not load XSLT string: {$xsl_file}", E_USER_ERROR);
			}
		} else {				// FILE MODE
			$xsl_file = "xsl/$xsl_file.xsl";
			if ($xsl->load($xsl_file) == false) {
				user_error("Could not load XSLT file: {$xsl_file}", E_USER_ERROR);
			}
		}
		$proc = new XSLTProcessor();
		$proc->importStyleSheet($xsl);
		return $proc->transformToXML($xDom);
	}

	function Render($xslList=NULL, $refresh=false) {
	   $XSL = '<?xml version="1.0" encoding="UTF-8"?>
			<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	   ';
	   switch ($this->detectedType) {
	    case 'sputnik': 	// SPUTNIK ARTICLE DTD (non-standard)
		$XSL .='<xsl:include href="xsl/SKIN1.xsl" />
			<xsl:include href="xsl/sputnik_article.xsl" />
			<xsl:include href="xsl/sputnik_flash.xsl" />
		';
		break;

	    case 'nlm': 	// NLM ARTICLE DTD (JAST standard)
		$XSL .='<xsl:include href="xsl/SKIN1.xsl" />
			<xsl:include href="xsl/ViewNLM-v2.3b.xsl" />
		';
		break;

	    default:		// direct XSLT 
		$XSL .='<xsl:include href="xsl/SKIN1.xsl" />';
		if ($xslList) 
			foreach ($xslList as $file) {
				$XSL .="\n<xsl:include href=\"xsl/$file\" />";
			}
		elseif ($this->detectedTypeXsl)	// $this->detectedType=='sp'
			$XSL .="\n<xsl:include href=\"xsl/{$this->detectedTypeXsl}.xsl\" />"; // vide antiga função
		else
			die("\n<h2>ERRO212: SEM  detectedTypeXsl nem lista de XSLs</h2>");
		break;
	   }
	   $XSL .='</xsl:stylesheet>';
	   return $this->RenderString('', $XSL);	
	}

	// UTIL

	/**
	 * @param string $element
	 * @param array $attribs Name=>value pairs. Values will be escaped.
	 * @param string $contents opcional, pode ser outro xml
	 * @return string
	 */
	function xmlTag( $element, $attribs, $contents = NULL, $putLines=0) {
		$L = $putLines? "\n": '';
		$out = $L.'<' . $element;
		foreach( $attribs as $name => $val ) {
		$out .= ' ' . trim($name) . '="' . xmlsafe( $val ) . '"';  // xmlsafe function?
		}
		if ( $contents == '' || is_null($contents) ) {
		    $out .= '/>';
		} else {
		    $out .= '>';
		    $out .= $L.xmlsafe( $contents );
		    $out .= "$L</$element>";
		}
		return $out;
	}


} // class

?>
