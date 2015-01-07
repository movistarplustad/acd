<?php
namespace Acd;

require ('../autoload.php');
session_start();
$action = isset($_GET['a']) ? $_GET['a'] : 'list';
if (!Model\Auth::isLoged()) {
	$action = 'login';
}

switch ($action) {
	case 'login':
		header('Location: index.php');
		return;
		break;
	case 'list': 
	$structures = new Model\StructureIterator();
	$structures->loadFromFile(conf::$DATA_PATH);
		$headerMenuOu = new View\HeaderMenu();
		$headerMenuOu->setType('menu');

		$toolsOu = new View\Tools();
		$toolsOu->setLogin($_SESSION['login']);
		$toolsOu->setRol($_SESSION['rol']);

		$contentOu = new View\ContentEditIndex();
		//$contentOu->setActionType('index');
		$contentOu->setStructures($structures);
		//$contentOu->setTODO($estructuras);

		$skeletonOu = new View\BaseSkeleton();
		$skeletonOu->setBodyClass('indexContent');
		$skeletonOu->setHeadTitle('Manage structures');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());
		$skeletonOu->setTools($toolsOu->render());
		break;
	case 'edit':
		$id = $_GET['id'];
		$headerMenuOu = new View\HeaderMenu();
		$headerMenuOu->setType('backContent');

		$contentOu = new View\ContentEditIndex();
$structures = array();
		$contentOu->setStructures($structures);

		$skeletonOu = new View\BaseSkeleton();
		$skeletonOu->setBodyClass('editContent');
		$skeletonOu->setHeadTitle('Manage structures');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());
		break;
}
$skeletonOu->setContent($contentOu->render());

header("Content-Type: text/html");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

echo $skeletonOu->render();
