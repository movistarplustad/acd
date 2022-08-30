<?php
namespace Acd;

use \Acd\Controller\RolPermissionHttp;

require('../autoload.php');

ini_set('session.gc_maxlifetime', conf::$SESSION_GC_MAXLIFETIME);
session_start();

if(!RolPermissionHttp::checkUserEditor([\Acd\conf::$ROL_DEVELOPER])) die();

$action = Controller\Install::VIEW_INFO;
@$result = $_GET['r'];

$installController = new Controller\Install();
$installController->setView($action);
$installController->setRequestUrl($_SERVER["REQUEST_URI"]); // For history back
$installController->load();
try {
	$sContent = $installController->render();
} catch (\Exception $e) {
	header("HTTP/1.0 404 Not Found");
    $sContent = "404 element not found.";
}

$skeletonOu = new View\BaseSkeleton();
$skeletonOu->setBodyClass('install');

$skeletonOu->setHeadTitle($installController->getTitle());
$skeletonOu->setHeaderMenu($installController->getHeaderMenuOu()->render());

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
