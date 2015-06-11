<?php
namespace Acd;

require ('../autoload.php');

session_start();
if (!Model\Auth::isLoged()) {
	$action = 'login';
}
else {
	if ($_SESSION['rol'] == 'editor') {
		$action = 'unauthorized';
	}
	else  {$action = 'ok';}
}

$skeletonOu = new View\BaseSkeleton();
$skeletonOu->setBodyClass('enumerated');
$headerMenuOu = new View\HeaderMenu();
$headerMenuOu->setType('menu');

$skeletonOu->setHeadTitle('Collection of enumerated values');
$skeletonOu->setHeaderMenu($headerMenuOu->render());

$toolsOu = new View\Tools();
$toolsOu->setLogin($_SESSION['login']);
$toolsOu->setRol($_SESSION['rol']);
$skeletonOu->setTools($toolsOu->render());
//$skeletonOu->setContent($contentOu->render());
$skeletonOu->setContent($action);

echo $skeletonOu->render();