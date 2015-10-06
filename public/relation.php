<?php
// Show  relationships (parents) of a content
// Params:
//	id - content id
//	idt - structure type id
namespace Acd;

require ('../autoload.php');

session_start();
if (!Model\Auth::isLoged()) {
	$action = 'login';
	header('Location: index.php');
	return;
}
else {
	if ($_SESSION['rol'] == 'editor') {
		header('HTTP/1.0 403 Forbidden');
		echo 'Unauthorized, only admin can show this section.';
		die();
	}
	else  {
		$action = 'ok';
		@$id = $_GET['id']; // id content
		@$idt = $_GET['idt']; // id structure
		//$action = 'show';
	}
}

$relationController = new Controller\Relation();
$relationController->setIdContent($id);
$relationController->setIdStructure($idt);
$relationController->setRequestUrl($_SERVER["REQUEST_URI"]); // For history back
$relationController->load();

$skeletonOu = new View\BaseSkeleton();
$skeletonOu->setBodyClass('relation');

$skeletonOu->setHeadTitle($relationController->getTitle());
$skeletonOu->setHeaderMenu($relationController->getHeaderMenuOu()->render());

$toolsOu = new View\Tools();
$toolsOu->setLogin($_SESSION['login']);
$toolsOu->setRol($_SESSION['rol']);
$skeletonOu->setTools($toolsOu->render());
$skeletonOu->setContent($relationController->render());

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
echo $skeletonOu->render();