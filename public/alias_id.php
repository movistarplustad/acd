<?php
// Show list of contents with the same alias-id
// Params:
//	id - alias-id
namespace Acd;
use \Acd\Controller\RolPermissionHttp;

require ('../autoload.php');

/* Temporal hasta que ACD incorpore su propio sistema de modo mantenimiento */
require ('../offline.php');

ini_set('session.gc_maxlifetime', conf::$SESSION_GC_MAXLIFETIME);
session_start();

if(!RolPermissionHttp::checkUserEditor([\Acd\conf::$ROL_DEVELOPER, \Acd\conf::$ROL_EDITOR])) die();

$action = 'ok';
@$aliasId = $_GET['id']; // alias-id
//$action = 'show';

$aliasIdController = new Controller\AliasId();
try {
	$aliasIdController->setAliasId($aliasId);
	$aliasIdController->setRequestUrl($_SERVER["REQUEST_URI"]); // For history back
	$aliasIdController->load();
}
catch( \Acd\Model\StorageKeyInvalidException $e) {
	$aliasIdController->setResultDesc("Error, zero results. ".$e->getMessage());
}

$skeletonOu = new View\BaseSkeleton();
$skeletonOu->setBodyClass('aliasId');

$skeletonOu->setHeadTitle($aliasIdController->getTitle());
$skeletonOu->setHeaderMenu($aliasIdController->getHeaderMenuOu()->render());

$toolsOu = new View\Tools();
$toolsOu->setLogin($_SESSION['login']);
$toolsOu->setRol($_SESSION['rol']);
$skeletonOu->setTools($toolsOu->render());
$skeletonOu->setContent($aliasIdController->render());

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
echo $skeletonOu->render();
