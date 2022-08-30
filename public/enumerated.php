<?php
namespace Acd;

use \Acd\Controller\RolPermissionHttp;

require('../autoload.php');

/* Temporal hasta que ACD incorpore su propio sistema de modo mantenimiento */
require('../offline.php');

ini_set('session.gc_maxlifetime', conf::$SESSION_GC_MAXLIFETIME);
session_start();
if(!RolPermissionHttp::checkUserEditor([\Acd\conf::$ROL_DEVELOPER])) die();

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
@$result = $_GET['r'];

$enumeratedController = new Controller\Enumerated();
$enumeratedController->setView($action);
$enumeratedController->setRequestUrl($_SERVER["REQUEST_URI"]); // For history back
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
