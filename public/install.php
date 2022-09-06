<?php

use Acd\Controller\RolPermissionHttp;
use Acd\Controller\Install;
use Acd\View\BaseSkeleton;
use Acd\View\Tools;

require '../config/conf.php';

ini_set('session.gc_maxlifetime', $_ENV[ 'ACD_SESSION_GC_MAXLIFETIME']);
session_start();

if(!RolPermissionHttp::checkUserEditor([$_ENV['ACD_ROL_DEVELOPER']])) die();

$action = Install::VIEW_INFO;
@$result = $_GET['r'];

$installController = new Install();
$installController->setView($action);
$installController->setRequestUrl($_SERVER["REQUEST_URI"]); // For history back
$installController->load();
try {
	$sContent = $installController->render();
} catch (\Exception $e) {
	header("HTTP/1.0 404 Not Found");
    $sContent = "404 element not found.";
}

$skeletonOu = new BaseSkeleton();
$skeletonOu->setBodyClass('install');

$skeletonOu->setHeadTitle($installController->getTitle());
$skeletonOu->setHeaderMenu($installController->getHeaderMenuOu()->render());

$toolsOu = new Tools();
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
