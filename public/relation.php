<?php
// Show  relationships (parents) of a content
// Params:
//	id - content id
//	idt - structure type id

use Acd\Controller\RolPermissionHttp;
use Acd\Controller\Relation;
use Acd\View\BaseSkeleton;
use Acd\View\Tools;

require '../config/conf.php';

ini_set('session.gc_maxlifetime', $_ENV[ 'ACD_SESSION_GC_MAXLIFETIME']);
session_start();

if(!RolPermissionHttp::checkUserEditor([$_ENV['ACD_ROL_DEVELOPER'], $_ENV['ACD_ROL_EDITOR']])) die();

$action = 'ok';
@$id = $_GET['id']; // id content
@$idt = $_GET['idt']; // id structure
//$action = 'show';

$relationController = new Relation();
$relationController->setIdContent($id);
$relationController->setIdStructure($idt);
$relationController->setRequestUrl($_SERVER["REQUEST_URI"]); // For history back
$relationController->load();

$skeletonOu = new BaseSkeleton();
$skeletonOu->setBodyClass('relation');

$skeletonOu->setHeadTitle($relationController->getTitle());
$skeletonOu->setHeaderMenu($relationController->getHeaderMenuOu()->render());

$toolsOu = new Tools();
$toolsOu->setLogin($_SESSION['login']);
$toolsOu->setRol($_SESSION['rol']);
$skeletonOu->setTools($toolsOu->render());
$skeletonOu->setContent($relationController->render());

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
echo $skeletonOu->render();
