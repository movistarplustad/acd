<?php
namespace Acd;

use \Acd\Controller\RolPermissionHttp;

require('../autoload.php');

ini_set('session.gc_maxlifetime', conf::$SESSION_GC_MAXLIFETIME);
session_start();

if(!RolPermissionHttp::checkUserEditor([\Acd\conf::$ROL_DEVELOPER])) die();

@$action = $_GET['a'];
switch ($action) {
	case 'edit':
		$action = Controller\User::VIEW_DETAIL;
		break;
	case 'new':
		$action = Controller\User::VIEW_DETAIL_NEW;
		break;
	default:
		$action = Controller\User::VIEW_LIST;
		break;
}
@$id = $_GET['id'];
@$result = $_GET['r'];


/* TODO: Revisar */
$userController = new Controller\User();
$userController->setView($action);
$userController->setRequestUrl($_SERVER["REQUEST_URI"]); // For history back
$userController->setId($id);
$userController->load();
try {
	$sContent = $userController->render();
} catch (\Exception $e) {
	header("HTTP/1.0 404 Not Found");
	$sContent  = "404 element not found.";
}
/* FIN TODO: Pendiente */

$skeletonOu = new View\BaseSkeleton();
$skeletonOu->setBodyClass('user');

$skeletonOu->setHeadTitle($userController->getTitle());
$skeletonOu->setHeaderMenu($userController->getHeaderMenuOu()->render());

$toolsOu = new View\Tools();
$toolsOu->setLogin($_SESSION['login']);
$toolsOu->setRol($_SESSION['rol']);

$skeletonOu->setTools($toolsOu->render());

switch ($result) {
    case 'ok':
        $skeletonOu->setResultDesc('Done', 'ok');
        break;
    case 'ko':
        $skeletonOu->setResultDesc('Fail', 'ko');
        break;
}

$skeletonOu->setContent($sContent);

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
echo $skeletonOu->render();
