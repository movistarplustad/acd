<?php
namespace Acd;
use \Acd\Controller\RolPermissionHttp;

require ('../autoload.php');

ini_set('session.gc_maxlifetime', conf::$SESSION_GC_MAXLIFETIME);
session_start();

if(!RolPermissionHttp::checkUserEditor([\Acd\conf::$ROL_DEVELOPER, \Acd\conf::$ROL_EDITOR])) die();

//  A slightly modified version from  limalopex.eisfux.de. Fixes the missing Headers Content-Type and Content-Length and makes it Camel-Case.
if( !function_exists('apache_request_headers') ) {
	function apache_request_headers() {
		$arh = array();
		$rx_http = '/\AHTTP_/';
		foreach($_SERVER as $key => $val) {
			if( preg_match($rx_http, $key) ) {
				$arh_key = preg_replace($rx_http, '', $key);
				$rx_matches = array();
				// do some nasty string manipulations to restore the original letter case
				// this should work in most cases
				$rx_matches = explode('_', strtolower($arh_key));
				if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
					foreach($rx_matches as $ak_key => $ak_val)$rx_matches[$ak_key] = ucfirst($ak_val);
					$arh_key = implode('-', $rx_matches);
				}
				$arh[$arh_key] = $val;
			}
		}
		if(isset($_SERVER['CONTENT_TYPE'])) $arh['Content-Type'] =$_SERVER['CONTENT_TYPE'];
		if(isset($_SERVER['CONTENT_LENGTH'])) $arh['Content-Length'] =$_SERVER['CONTENT_LENGTH'];
		return( $arh );
	}
}

$idFile = $_GET['id'];
$fileName = $_GET['n'];
$path = \Acd\Model\File::getPath($idFile);
if (is_readable($path)){
	$fileTools = new \Acd\Model\File();
	$fileType = $fileTools->getMimeFromFilename($fileName);
	if (!$fileType) {
		$fileType = $fileTools->getMimeFromPath($path);
	}

	// Getting headers sent by the client.
	$headers = apache_request_headers();

	// Checking if the client is validating his cache and if it is current.
	if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($path))) {
		// Client's cache IS current, so we just respond '304 Not Modified'.
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT', true, 304);
	} else {
		// Image not cached or cache outdated, we respond '200 OK' and output the image.
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT', true, 200);
		header('Content-Length: '.filesize($path));
		header('Content-Type: '.$fileType);
		print file_get_contents($path);
	}
}
else {
	header("HTTP/1.0 404 Not Found");
	echo "404. File not found";
}
