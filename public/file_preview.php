<?php

use Acd\Controller\RolPermissionHttp;
use Acd\Model\File;

require '../config/conf.php';

ini_set('session.gc_maxlifetime', $_ENV[ 'ACD_SESSION_GC_MAXLIFETIME']);
session_start();

if(!RolPermissionHttp::checkUserEditor([$_ENV['ACD_ROL_DEVELOPER'], $_ENV['ACD_ROL_EDITOR']])) die();

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
try {
	$filesystem = File::getFileSystemFromEnvConfiguration();
    $response = $filesystem->read($idFile);
    $lastModified = $filesystem->lastModified($idFile);
	$fileSize = $filesystem->fileSize($idFile);
    try {
        $mimeType = $filesystem->mimeType($idFile);
    } catch (FilesystemException | UnableToRetrieveMetadata $exception) {
		// to uknown 'application/octet-stream';
        $mimeType = 'application/octet-stream';
    }
	// Getting headers sent by the client.
	$headers = apache_request_headers();

	// Checking if the client is validating his cache and if it is current.
	if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == $lastModified)) {
		// Client's cache IS current, so we just respond '304 Not Modified'.
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', $lastModified).' GMT', true, 304);
	} else {
		// Image not cached or cache outdated, we respond '200 OK' and output the image.
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', $lastModified).' GMT', true, 200);
		header('Content-Length: '.$fileSize);
		header('Content-Type: '.$mimeType);
		print $response;
	}
} catch (FilesystemException | UnableToReadFile $exception) {
    // handle the error
	header("HTTP/1.0 404 Not Found");
	echo "404. File not found";
    echo $exception->getMessage();
}
