<?php
/****************************************/
error_reporting(E_ALL && ~E_NOTICE);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Asia/Hong_Kong');
set_time_limit(60);

define('TAB', "\t");
define('CR', "\r");
define('LF', "\n");
define('CRLF', CR . LF);
define('BR', '<br/>');
define('NL', BR . CRLF);

define('SEC_MS', 1000);
define('SEC_S', 1);
define('SEC_I', 60);
define('SEC_H', 60*60);
define('SEC_D', 60*60*24);
define('SEC_W', 60*60*24*7);
define('SEC_M', 60*60*24*30);
define('SEC_Y', 60*60*24*365);
/****************************************/
// auto expire if file not modified for ??? days
function check_expiry($days) {
	if ( (time() - filemtime(__FILE__)) > ($days * 24 * 60 * 60) )
		die('Script expired.');
}
check_expiry(1);
/****************************************/

// screen outputs

function m($s = '') {
	echo $s . NL;
}

function h1($s) {
	echo $m("<h1>$s</h1>") . CRLF;
}

function hr() {
	echo '<hr/>' . CRLF;
}

function pre($s) {
	echo "<pre>\r\n$s\r\n</pre>\r\n";
}

function vd($vars) {
	echo '<pre>' . CRLF;
	var_dump($vars);
	echo '</pre>' . CRLF;
}

// files

function safe_dir($path) {
	$dir = dirname($path);	
	if (! file_exists($dir))
		mkdir($dir, 0755, TRUE);
	else if (! is_dir($dir))
		throw new Exception("File with same name exists: $dir");
}

function load($path) {
	return file_get_contents($path);
}

function save($path, $content) {
	return file_put_contents($path, $content);
}

// tsv

function parse_tsv($content, $headers = FALSE) {
	$items = $row = array();
	$content = trim($content);
	$lines = explode(CRLF, $content);
	// default mode, unassoc array
	if ($headers === FALSE) {
		for($i = 0; $i < count($lines); $i++)
			$items[] = explode(TAB, $lines[ $i ]);
	}
	// first row as header
	else if ($headers === TRUE) {
		$headers = explode(TAB, $lines[0]);
		for($i = 1; $i < count($lines); $i++) {
			$cells = explode(TAB, $lines[ $i ]);
			foreach ($headers as $h_num => $h_key)
				$row[ $h_key ] = $cells[ $h_num ];
			$items[] = $row;
		}
	}
	// header provided
	else if (is_array($headers)) {
		for($i = 0; $i < count($lines); $i++) {
			$cells = explode(TAB, $lines[ $i ]);
			foreach ($headers as $h_num => $h_key)
				$row[ $h_key ] = $cells[ $h_num ];
			$items[] = $row;
		}
	}
	return $items;
}

function make_tsv($tsv, $headers = FALSE) {
	// default mode, unassoc array
	if ($headers === FALSE) {
		for($i = 0; $i < count($tsv); $i++)
			$content .= implode(TAB, array_values($tsv[ $i ])) . CRLF;
	} 
	// assoc array, keys as header
	else if ($headers === TRUE) {
		$content .= implode(TAB, array_keys($tsv[0])) . CRLF;
		for($i = 0; $i < count($tsv); $i++)
			$content .= implode(TAB, array_values($tsv[ $i ])) . CRLF;
	}
	// assoc array, keys as header
	else if (is_array($headers)) {
		$content .= implode(TAB, array_values($headers)) . CRLF;
		for($i = 0; $i < count($tsv); $i++)
			$content .= implode(TAB, array_values($tsv[ $i ])) . CRLF;
	}
	return $content;
}












