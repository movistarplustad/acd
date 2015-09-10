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
// back button
$navigation = new Controller\SessionNavigation();
$navigation->load();
$back = !$navigation->isEmpty(); // Check empty before insert new navigation
$navigation->push([
	'hash' => "enumerated_$action - $id", // Page hash, consecutive same hash no add navigation
	'url' => $_SERVER["REQUEST_URI"]
]);
$navigation->save();

$relationController = new Controller\Relation();
$relationController->setIdContent($id);
$relationController->setIdStructure($idt);
$relationController->setBack($back);
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

echo $skeletonOu->render();