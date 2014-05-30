<?

function output($msg, $color='#F88') {
	$style = <<<CSS
background-color:#FAFAF4;
color:#000;
padding:.3em 1em;
border:2px solid {$color};
margin-bottom:1em;
font-size:14px;
font-family:Consolas,monospace;
CSS;
	print "<pre style=\"{$style}\">{$msg}</pre>";
}

function debug($var) {
	$vars = func_get_args();
	foreach ($vars as $var) {
		output(str_replace(array('&', '<', '>'), array('&amp;', '&lt;', '&gt;'), var_export($var, true)));
	}
}

?>