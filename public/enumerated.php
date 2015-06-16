<?php
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
		@$id = $_GET['id']; //'PROFILE';
	}
}
$enumeratedController = new Controller\Enumerated();
$enumeratedController->setId($id);
$enumeratedController->load();

$skeletonOu = new View\BaseSkeleton();
$skeletonOu->setBodyClass('enumerated');

$skeletonOu->setHeadTitle($enumeratedController->getTitle());
$skeletonOu->setHeaderMenu($enumeratedController->getHeaderMenuOu()->render());

$toolsOu = new View\Tools();
$toolsOu->setLogin($_SESSION['login']);
$toolsOu->setRol($_SESSION['rol']);
$skeletonOu->setTools($toolsOu->render());
$skeletonOu->setContent($enumeratedController->render());

echo $skeletonOu->render();