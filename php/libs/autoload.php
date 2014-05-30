<?

function __autoload($class_name) {
	$file_name = decamelize($class_name, '-').'.php';
	$path1 = DIR_LIBS.'/'.$file_name;
	$path2 = DIR_APP.'/'.$file_name;
	
	if (is_readable_file($path1)) {
		require_once($path1);
	}
	elseif (is_readable_file($path2)) {
		require_once($path2);
	}
	else {
		user_error("Could not autoload class {$class_name}, file not found: {$path}", E_USER_ERROR);
	}
}

function is_readable_file($path) {
	return is_file($path) && is_readable($path);
}

function decamelize($text, $separator='_') {
	return preg_match_all('/[A-Z][A-Z]*[a-z]*/u', $text, $m)?
		strtolower(join($separator, $m[0])):
		strtolower($camelized);
}

assert("decamelize('A') == 'a'");
assert("decamelize('Abc') == 'abc'");
assert("decamelize('ABC') == 'abc'");
assert("decamelize('AbcDef') == 'abc_def'");
assert("decamelize('AbcDEF') == 'abc_def'");
assert("decamelize('AbcDEFghiJKL_MnOP') == 'abc_defghi_jkl_mn_op'");

?>