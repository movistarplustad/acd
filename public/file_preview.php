<?php
namespace Acd;

require ('../autoload.php');

$idFile = $_GET['id'];
$path = \Acd\conf::$DATA_CONTENT_PATH.'/'.$idFile;
if (is_readable($path)){
	$finfo = new \finfo(FILEINFO_MIME_TYPE);
	$type = $finfo->file($path);

	//d($path, $type);
	// Getting headers sent by the client.
	$headers = \apache_request_headers();

	// Checking if the client is validating his cache and if it is current.
	if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($path))) {
		// Client's cache IS current, so we just respond '304 Not Modified'.
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT', true, 304);
	} else {
		// Image not cached or cache outdated, we respond '200 OK' and output the image.
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT', true, 200);
		header('Content-Length: '.filesize($path));
		header('Content-Type: '.$type);
		print file_get_contents($path);
	}
}
else {
	header("HTTP/1.0 404 Not Found");
	echo "404. File not found";
}