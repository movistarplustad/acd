<?php
namespace Acd;
use \Acd\Controller\RolPermissionHttp;

require ('../autoload.php');

/* Temporal hasta que ACD incorpore su propio sistema de modo mantenimiento */
require ('../offline.php');

ini_set('session.gc_maxlifetime', conf::$SESSION_GC_MAXLIFETIME);
session_start();

if(!RolPermissionHttp::checkUserEditor([\Acd\conf::$ROL_DEVELOPER, \Acd\conf::$ROL_EDITOR])) die();

$action =$_GET['a'];
@$id = $_GET['id'];
@$idStructureTypeSearch = $_GET['idt'];
@$titleSearch = $_GET['s'];
$idParent = $_GET['idp'];
$idStructureTypeParent = $_GET['idtp'];
$idField = $_GET['f'];
@$positionInField = $_GET['p'];
$numPage = isset($_GET['p']) ? (int) $_GET['p'] : 0;

$contentRelationController = new Controller\ContentRelation();
$contentRelationController->setIdContent($id);
$contentRelationController->setIdStructureTypeSearch($idStructureTypeSearch);
$contentRelationController->setTitleSearch($titleSearch);
$contentRelationController->setIdParent($idParent);
$contentRelationController->setIdStructureTypeParent($idStructureTypeParent);
$contentRelationController->setIdField($idField);
$contentRelationController->setPositionInField($positionInField);
$contentRelationController->setNumPage($numPage);
$contentRelationController->setRequestUrl($_SERVER["REQUEST_URI"]); // For history back
$contentRelationController->setAction($action);
$contentRelationController->load();

$skeletonOu = new View\BaseSkeleton();
$skeletonOu->setBodyClass('relation');

$skeletonOu->setHeadTitle($contentRelationController->getTitle());
$skeletonOu->setHeaderMenu($contentRelationController->getHeaderMenuOu()->render());

$toolsOu = new View\Tools();
$toolsOu->setLogin($_SESSION['login']);
$toolsOu->setRol($_SESSION['rol']);
$skeletonOu->setTools($toolsOu->render());
$skeletonOu->setContent($contentRelationController->render());

header("Content-Type: text/html");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
echo $skeletonOu->render();
