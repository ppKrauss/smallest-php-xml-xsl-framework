<?

assert("defined('_DEBUG_')");
assert("defined('DIR_ROOT')");
assert("defined('DIR_LOGS')");

function get_trace() {
	
	$trace = debug_backtrace();
	array_shift($trace);
	$parsed = array();
	$prev = null;
	
	foreach ($trace as $step) {

		$step['place'] = empty($step['file'])?
			'':
			str_replace(DIR_ROOT.'/', '', str_replace('\\', '/', $step['file'])).':'.$step['line'];

		$call_prefix = empty($step['type'])? '': $step['class'].$step['type'];
		
		$step['call'] = $call_prefix.$step['function'].'('.(empty($step['args'])? '': '...').')';

		if ($prev) {
			$snippet = isset($prev['file'])? debug_code_snippet($prev['file'], $prev['line'], 3): '';
			$parsed[] = array(
				'call' => $step['call'],
				'place' => $prev['place'],
				'file' => @$prev['file'],
				'line' => @$prev['line'],
				'snippet' => $snippet,
			); 
		}

		$prev = $step;
	}

	return array_reverse($parsed, true);
}

function debug_code_snippet($file, $line, $before=3, $after=false) {
	if (!$after) $after = $before;
	$code = ' '.highlight_file($file, 1);
	$lines = array_map('trim', explode('<br />', $code));
	array_unshift($lines, '');
	return array_slice($lines, max(0, $line-$before), $before+$after+1, true);
}

function error_handler ($no, $str, $file, $line) {

	$stop = false;

	switch ($no) {
		
		case E_ERROR:
		case E_USER_ERROR: 
			$type = 'error'; $stop = true; break;

		case E_WARNING:      
		case E_USER_WARNING: 
			$type = 'warning'; break;

		case E_NOTICE:       
		case E_USER_NOTICE: 
			$type = 'notice';  break;

		case E_STRICT:       
		case E_USER_STRICT: 
			$type = 'strict';  break;

		default:
			$type = "unknown_{$no}"; break;
	}
	
	$date = date('ymd');
	$time = date('His.').substr(microtime(), 2, 4);
	$sid = session_id()? substr(session_id(), 0, 8): '-'.getmypid();

	$file = str_replace('\\', '/', $file);
	$place = str_replace(DIR_ROOT, '', $file).':'.$line;

	$msg = "{$time}\t{$sid}\t{$place}\t{$str}";
	
	error_log("{$msg}\n", 3, DIR_LOGS."/{$date}_{$type}.log");

	//if ($stop) {
		$name = ucfirst($type);

		print <<<JS
<script type="text/javascript">
var get  = function(elem) { return document.getElementById('debug_s_'+elem.id.substring(8)) }
var show = function(elem) { get(elem).style.display = 'block' }
var hide = function(elem) { get(elem).style.display = 'none' }
</script>
<style type="text/css">
.Snippet { position:absolute; display:none; background-color:#FFF; margin:.2em .4em; padding:.4em .6em; border:1px solid #DDD; border-color:#DDD #CCC #CCC #CCC; border-width:1px 2px 2px 1px; font-family:Consolas; font-size:12px; }
.Snippet TH { text-align:right; font-weight:normal; color:#BBB; padding-right:.5em; font-size:90%; }
.Snippet .Current { background-color:#FFA; }
.Toggle { cursor:pointer; border-bottom:1px dotted #888; }
</style>
JS;

		$trace = get_trace();
		$first = $trace[0];

		print "<div style=\"background-color:#FAFAFA;color:#000;padding:.2em .4em;font-family:Calibri,sans-serif;border:1px solid #EEE\">\n";
		print "<strong>{$name}:</strong>&nbsp;\n";
		print "<span style=\"background-color:#FF8\">{$str}</span>&nbsp;<span style=\"color:#888\">in</span> {$first['call']} <span style=\"font-size:smaller;color:#888\">{$first['place']}</span>\n";
		$indent = 15;
		$id = 1;
		foreach ($trace as $step) {
			print "<div>";
			print "<span style=\"color:#888;font-size:smaller\">#{$id}</span> <span>{$step['call']}</span> <span onmouseover=\"show(this)\" onmouseout=\"hide(this)\" id=\"debug_a_{$id}\" style=\"font-size:smaller;color:#888\" class=\"Toggle\">{$step['place']}</span>\n";

			if (is_array($step['snippet'])) {
				print '<table class="Snippet" id="debug_s_'.$id.'">';
				foreach ($step['snippet'] as $kk=>$vv) {
					$style = $kk == $step['line']? ' class="Current"': '';
					print "<tr{$style}>\n\t<td>{$vv}</td>\n</tr>\n";
				}
				print '</table>';
			}

			print "</div>\n";
			$id++;

			$indent += 15;
		}
		if ($stop) exit(1);
	//}

    /* Don't execute PHP internal error handler */
    return true;
}

?>