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
		@$action = $_GET['a'];
		switch ($action) {
			case 'edit':
				$action = Controller\Enumerated::VIEW_DETAIL;
				break;
			case 'new':
				$action = Controller\Enumerated::VIEW_DETAIL_NEW;
				break;
			default:
				$action = Controller\Enumerated::VIEW_LIST;
				break;
		}
		@$id = $_GET['id']; //'PROFILE';
	}
}
$enumeratedController = new Controller\Enumerated();
$enumeratedController->setView($action);
$enumeratedController->setId($id);
$enumeratedController->load();

$skeletonOu = new View\BaseSkeleton();
$skeletonOu->setBodyClass('enumerated');

$skeletonOu->setHeadTitle($enumeratedController->getTitle());
$skeletonOu->setHeaderMenu($enumeratedController->getHeaderMenuOu()->render());
try {
	$sContent = $enumeratedController->render();
} catch (\Exception $e) {
	header("HTTP/1.0 404 Not Found");
	$sContent  = "404 element not found.";
}
$toolsOu = new View\Tools();
$toolsOu->setLogin($_SESSION['login']);
$toolsOu->setRol($_SESSION['rol']);

$skeletonOu->setTools($toolsOu->render());
$skeletonOu->setContent($sContent);
echo $skeletonOu->render();