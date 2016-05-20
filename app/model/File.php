<?php
namespace Acd\Model;

class File
{
	private $mimeTypes;
	public function __construct() {
		$this->mimeTypes = array(
			'txt' => 'text/plain',
			'htm' => 'text/html',
			'html' => 'text/html',
			'php' => 'text/html',
			'css' => 'text/css',
			'js' => 'application/javascript',
			'json' => 'application/json',
			'xml' => 'application/xml',
			'swf' => 'application/x-shockwave-flash',
			'flv' => 'video/x-flv',

			// images
			'png' => 'image/png',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'gif' => 'image/gif',
			'bmp' => 'image/bmp',
			'ico' => 'image/vnd.microsoft.icon',
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'svg' => 'image/svg+xml',
			'svgz' => 'image/svg+xml',

			// archives
			'zip' => 'application/zip',
			'rar' => 'application/x-rar-compressed',
			'exe' => 'application/x-msdownload',
			'msi' => 'application/x-msdownload',
			'cab' => 'application/vnd.ms-cab-compressed',

			// audio/video
			'mp3' => 'audio/mpeg',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',

			// adobe
			'pdf' => 'application/pdf',
			'psd' => 'image/vnd.adobe.photoshop',
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',

			// ms office
			'doc' => 'application/msword',
			'rtf' => 'application/rtf',
			'xls' => 'application/vnd.ms-excel',
			'ppt' => 'application/vnd.ms-powerpoint',

			// open office
			'odt' => 'application/vnd.oasis.opendocument.text',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		);
	}
	public function getMimeFromFilename($filename) {
		$ext = mb_strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		if (array_key_exists($ext, $this->mimeTypes)) {
			return $this->mimeTypes[$ext];
		}
		return null;
	}
	public function getMimeFromPath($filename) {
		$finfo = new \finfo(FILEINFO_MIME_TYPE);
		return $finfo->file($filename);
		// to uknown 'application/octet-stream';
	}
	public function getImageGeometryFromPath($filename) {
		try {
			$image = new \imagick($filename);
			return $image->getImageGeometry();
		}
		catch (\ImagickException $e) {
			return ['width' => '', 'height' => ''];
		}
	}
	public static function getPath($idFile) {
		return \Acd\conf::$DATA_CONTENT_PATH.'/'.substr($idFile, 0, 3).'/'.$idFile;
	}
}
