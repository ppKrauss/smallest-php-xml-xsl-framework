<?
/*
 * MAIN PAGE (HUB)
 * Do appends and Render.
 *
 * index.php?pageName&otherParam=Val
 *
 */
require 'conf.php';
require_once('libs/error-handler.php');
require_once('libs/xml.php');
require_once('libs/page-state.php');

$xInput = new XML();
$st = new PageStates();

$xInput->appendSiteMap();  		// to build skin
$xInput->appendPageState($st->ALL); 	// supplay page-state with state array
$xInput->appendPage($st->pageName);	// to build the main content

header("Content-Type: {$st->output_mime}");
print $st->output_xinput? $xInput->xstr: $xInput->Render();  // Render or debug input
?>
