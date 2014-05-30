<?

function assert_handler($file, $line, $code) {
	$pos = str_replace(DIR_ROOT, '', $file).':'.$line;
	output("<small>Assertion failed at {$pos}</small><br/><code>{$code}</code>");
}

function init_assert($active=true) {
	assert_options(ASSERT_ACTIVE, $active);
	if (!$active) return false;

	assert_options(ASSERT_WARNING, 0);
	assert_options(ASSERT_QUIET_EVAL, 1);
	assert_options(ASSERT_CALLBACK, 'assert_handler');
}

?>