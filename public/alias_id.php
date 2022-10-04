<?php
// Show list of contents with the same alias-id
// Params:
//	id - alias-id

use Acd\Controller\RolPermissionHttp;
use Acd\Model\StorageKeyInvalidException;
use Acd\Controller\AliasId;
use Acd\View\BaseSkeleton;
use Acd\View\Tools;

require('../config/conf.php');
/* Temporal hasta que ACD incorpore su propio sistema de modo mantenimiento */
require ('../offline.php');

ini_set('session.gc_maxlifetime', $_ENV[ 'ACD_SESSION_GC_MAXLIFETIME']);
session_start();

if(!RolPermissionHttp::checkUserEditor([$_ENV['ACD_ROL_DEVELOPER'], $_ENV['ACD_ROL_EDITOR']])) die();

$action = 'ok';
@$aliasId = $_GET['id']; // alias-id
//$action = 'show';

$aliasIdController = new AliasId();
try {
	$aliasIdController->setAliasId($aliasId);
	$aliasIdController->setRequestUrl($_SERVER["REQUEST_URI"]); // For history back
	$aliasIdController->load();
}
catch( StorageKeyInvalidException $e) {
	$aliasIdController->setResultDesc("Error, zero results. ".$e->getMessage());
}

$skeletonOu = new BaseSkeleton();
$skeletonOu->setBodyClass('aliasId');

$skeletonOu->setHeadTitle($aliasIdController->getTitle());
$skeletonOu->setHeaderMenu($aliasIdController->getHeaderMenuOu()->render());

$toolsOu = new Tools();
$toolsOu->setLogin($_SESSION['login']);
$toolsOu->setRol($_SESSION['rol']);
$skeletonOu->setTools($toolsOu->render());
$skeletonOu->setContent($aliasIdController->render());

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
echo $skeletonOu->render();
