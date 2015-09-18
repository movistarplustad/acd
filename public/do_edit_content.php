<?php
namespace Acd;
require ('../autoload.php');

$action =$_GET['a'];
@$id = $_GET['id'];

$contentLoader = new \Acd\Model\ContentLoader();
$matchContent = $contentLoader->loadContent('meta-information', ['id' => $id]);
if($matchContent) {
	$returnUrl = "content.php?a=edit&id=$id&idt=".$matchContent->getIdStructure();
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