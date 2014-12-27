<?php
require ('conf.php');
require_once (DIR_BASE.'/class/structures_do.php');
require_once (DIR_BASE.'/class/auth.php');
require_once (DIR_BASE.'/tpl/BaseSkeleton.php');
require_once (DIR_BASE.'/tpl/Tools.php');
require_once (DIR_BASE.'/tpl/HeaderMenu.php');
require_once (DIR_BASE.'/tpl/ContentAdminIndex.php');

session_start();
if (!auth::isLoged()) {
	$action = 'login';
}
else {
	$structures = new structures_do();
	$structures->loadFromFile(conf::$DATA_PATH);
	$estructuras = $structures->getAllStructures();
	//var_dump($estructuras);
	//var_dump(conf::$STORAGE_TYPES);
	$action = isset($_GET['a']) ? $_GET['a'] : 'list';
}
/* Show action block */
$skeletonOu = new \Acd\Ou\BaseSkeleton();
$contentOu = new \Acd\Ou\ContentAdminIndex();
switch ($action) {
	case 'login':
		$skeletonOu ->setBodyClass('login');
		$contentOu->setActionType('login');
		$tplVar['login'] = isset($_GET['login']) ? $_GET['login'] : '';//BORRAR
		$tpl = DIR_BASE.'/tpl/login.tpl';//BORRAR
		$contentOu->setLogin(isset($_GET['login']) ? $_GET['login'] : '');
		break;
	case 'new':
		$bResult = isset($_GET['r']) && $_GET['r'] === 'ko' ? false : true;

		$estructura = new structure_do();
		$id = isset($_GET['id']) ? $_GET['id'] : '';//BORRAR
		$name = isset($_GET['name']) ? $_GET['name'] : '';//BORRAR
		$titleName = '(nuevo)';//BORRAR
		$storage = isset($_GET['storage']) ? $_GET['storage'] : '';//BORRAR
		$fields = new fields_do();//BORRAR
		$tpl = DIR_BASE.'/tpl/new.tpl';//BORRAR

		$contentOu->setActionType('new');
		$contentOu->setStorageTypes(conf::$STORAGE_TYPES);
		$contentOu->setStorage($estructura->getStorage());

		$headerMenuOu = new \Acd\Ou\HeaderMenu();
		$headerMenuOu->setType('back');

		$skeletonOu->setBodyClass('new');
		$skeletonOu->setHeadTitle('New structure');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());
		break;
	case 'edit':
		$id = $_GET['id'];
		$estructura = $structures->get($id);
		$contentOu->setStructureId($id);
		if ($estructura === null) {
			/* Error, intentando editar una estructura que no existe */
			$skeletonOu ->setBodyClass('error');
			$contentOu->setActionType('error');
			$titleName = '(error)';//BORRAR
			$tpl = DIR_BASE.'/tpl/error.tpl';//BORRAR
		}
		else {
			$skeletonOu->setBodyClass('edit');
			$contentOu->setActionType('edit');
			$name = $estructura->getName();//BORRAR
			$titleName = $name;//BORRAR
			$contentOu->setStructureName($estructura->getName());
			$storage = $estructura->getStorage();//BORRAR
			$contentOu->setStorageTypes(conf::$STORAGE_TYPES);
			$contentOu->setStorage($estructura->getStorage());
			$fields = $estructura->getFields();//BORRAR
			$contentOu->setFields($estructura->getFields());
			$tpl = DIR_BASE.'/tpl/edit.tpl';//BORRAR
		}
		$headerMenuOu = new \Acd\Ou\HeaderMenu();
		$headerMenuOu->setType('back');

		$skeletonOu->setHeadTitle('Edit structure');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());
		break;
	case 'clone':
		$id = $_GET['id'];
		$estructura = $structures->get($id);
		$contentOu->setStructureId("dup_$id");
		if ($estructura === null) {
			/* Error, intentando editar una estructura que no existe */
			$titleName = '(error)';//BORRAR
			$tpl = DIR_BASE.'/tpl/error.tpl';//BORRAR
			$skeletonOu ->setBodyClass('error');
			$contentOu->setActionType('error');
		}
		else {
			$id = "dup_$id";//BORRAR
			$name = '[copy] '.$estructura->getName();//BORRAR
			$titleName = "(copy)";//BORRAR
			$storage = $estructura->getStorage();//BORRAR
			$fields = $estructura->getFields();//BORRAR
			$tpl = DIR_BASE.'/tpl/new.tpl';//BORRAR
			$contentOu->setActionType('clone');
			$skeletonOu->setBodyClass('clone');
			$contentOu->setStructureName('[copy] '.$estructura->getName());
			$contentOu->setStorageTypes(conf::$STORAGE_TYPES);
			$contentOu->setStorage($estructura->getStorage());
			$contentOu->setFields($estructura->getFields());
		}

		$headerMenuOu = new \Acd\Ou\HeaderMenu();
		$headerMenuOu->setType('back');

		$skeletonOu->setHeadTitle('Clone structure');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());
		break;
	case 'list':
	default:
		$toolsOu = new \Acd\Ou\tools();
		$toolsOu->setLogin($_SESSION['login']);
		$toolsOu->setRol($_SESSION['rol']);
		$tplVar['tools'] = $toolsOu->render();//BORRAR

		$headerMenuOu = new \Acd\Ou\HeaderMenu();
		$headerMenuOu->setType('menu');

		$contentOu->setActionType('index');
		$contentOu->setStructures($structures);
		$contentOu->setTODO($estructuras);

		$skeletonOu->setBodyClass('index');
		$skeletonOu->setHeadTitle('Manage structures');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());
		$skeletonOu->setTools($toolsOu->render());
		$tpl = DIR_BASE.'/tpl/index.tpl';
		break;
}

$result = isset($_GET['r']) ? $_GET['r'] : '';
switch ($result) {
	case 'ok':
		$contentOu->setResultDesc('Done');
		break;
	case 'ko':
		$contentOu->setResultDesc('<em>Error</em>, has not been able to process');
		break;
	case 'kologin':
		$contentOu->setResultDesc('<em>Error</em>, incorrect login or password');
		break;
}
$skeletonOu->setContent($contentOu->render());

header("Content-Type: text/html");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

//require($tpl);
echo $skeletonOu->render();