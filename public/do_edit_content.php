<?php
namespace Acd;
use \Acd\Controller\RolPermissionHttp;
require ('../autoload.php');

ini_set('session.gc_maxlifetime', conf::$SESSION_GC_MAXLIFETIME);
session_start();

if(!RolPermissionHttp::checkUserEditor([\Acd\conf::$ROL_DEVELOPER, \Acd\conf::$ROL_EDITOR])) die();

$action =$_GET['a'];
@$id = $_GET['id'];

$contentLoader = new \Acd\Model\ContentLoader();
$matchContent = $contentLoader->loadContent('meta-information', ['id' => $id]);
if($matchContent) {
	$returnUrl = "content.php?a=edit&id=$id&idt=".$matchContent->getIdStructure();
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	header("Location:$returnUrl");
}
else {
	$skeletonOu = new View\BaseSkeleton();
	$skeletonOu->setBodyClass('content');
	$skeletonOu->setHeadTitle('Element not found');

	$skeletonOu->setContent('404 element not found.');
	header('HTTP/1.0 404 Not Found');
	echo $skeletonOu->render();
}
