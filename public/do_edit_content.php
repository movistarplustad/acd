<?php
namespace Acd;
use Acd\Controller\RolPermissionHttp;
use Acd\Model\ContentLoader;


require '../config/conf.php';

ini_set('session.gc_maxlifetime', $_ENV[ 'ACD_SESSION_GC_MAXLIFETIME']);
session_start();

if(!RolPermissionHttp::checkUserEditor([$_ENV['ACD_ROL_DEVELOPER'], $_ENV['ACD_ROL_EDITOR']])) die();

$action =$_GET['a'];
@$id = $_GET['id'];

$contentLoader = new ContentLoader();
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
