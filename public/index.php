<?php
// namespace Acd;
require __DIR__ . '/../vendor/autoload.php';

use Acd\Model\SessionNavigation;
use Acd\Model\Auth;
// use Acd\Conf\conf;

require ('conf2.php');

/* Temporal hasta que ACD incorpore su propio sistema de modo mantenimiento */
require ('../offline.php');

//$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
//$dotenv->safeLoad();
// var_dump('assd', $_ENV['ACD_SESSION_GC_MAXLIFETIME']);die;
ini_set('session.gc_maxlifetime', $_ENV['ACD_SESSION_GC_MAXLIFETIME']);
session_start();

if (!Auth::isLoged()) {
	$action = 'login';
}
else {
	// Temporal patch
	if ($_SESSION['rol'] == 'editor') {
		header('Location: content.php');
		die();
	}
	$structures = new Acd\Model\StructuresDo();
	$structures->loadFromFile($_ENV['ACD_DATA_PATH']);
	$action = isset($_GET['a']) ? $_GET['a'] : 'list';
}
/* Show action block */
$skeletonOu = new Acd\View\BaseSkeleton();
$contentOu = new Acd\View\ContentAdmin();
switch ($action) {
	case 'login':
		$skeletonOu->setBodyClass('login');
		$contentOu->setActionType('login');
		$contentOu->setLogin(isset($_GET['login']) ? $_GET['login'] : '');
		$contentOu->setRemember(isset($_GET['remember']) && $_GET['remember'] === '1');
		// Referer
		if(isset($_GET['re'])) {
			$contentOu->setPostLogin($_GET['re']);
		}
		break;
	case 'new':
		$bResult = isset($_GET['r']) && $_GET['r'] === 'ko' ? false : true;

		$structure = new Model\StructureDo();

		$skeletonOu->setBodyClass('new');
		$contentOu->setActionType('new');
		$contentOu->setStorageTypes($_ENV['ACD_STORAGE_TYPES']);
		$contentOu->setStorage($structure->getStorage());

		// back button
		$navigation = new SessionNavigation();
		$navigation->load();
		$back = !$navigation->isEmpty();
		$navigation->push([
			'hash' => "new_structure - *new*",
			'url' => $_SERVER["REQUEST_URI"],
			'title' => 'New structure'
		]);
		$navigation->save();

		$headerMenuOu = new View\HeaderMenu();
		$headerMenuOu->setBack($back);

		$toolsOu = new View\Tools();
		$toolsOu->setLogin($_SESSION['login']);
		$toolsOu->setRol($_SESSION['rol']);

		$skeletonOu->setHeadTitle('New structure');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());
		$skeletonOu->setTools($toolsOu->render());
		break;
	case 'edit':
		try {
			$id = $_GET['id'];
			$structure = $structures->get($id);
			$contentOu->setStructureId($id);
			$contentOu->setStructures($structures);

			$skeletonOu->setBodyClass('edit');
			$contentOu->setActionType('edit');
			$contentOu->setStructureName($structure->getName());
			$contentOu->setStorageTypes($_ENV['ACD_STORAGE_TYPES']);
			$contentOu->setStorage($structure->getStorage());
			$contentOu->setFieldTypes(Model\FieldDo::getAvailableTypes());
			$contentOu->setFields($structure->getFields());

			$enumeratedLoader = new Model\EnumeratedLoader();
			$query = new Model\Query();
			$query->setType('all');
			$enumerated = $enumeratedLoader->load($query);
			$contentOu->setEnumeratedList($enumerated);
		} catch (\Exception $e) {
			/* Error, intentando editar una estructura que no existe */
			$skeletonOu->setBodyClass('error');
			$contentOu->setActionType('error');
		}

		// back button
		$navigation = new SessionNavigation();
		$navigation->load();
		$back = !$navigation->isEmpty();
		$navigation->push([
			'hash' => "edit_structure - $id",
			'url' => $_SERVER["REQUEST_URI"],
			'title' => 'Edit structure '.$structure->getName()
		]);
		$navigation->save();

		$headerMenuOu = new View\HeaderMenu();
		$headerMenuOu->setBack($back);

		$toolsOu = new View\Tools();
		$toolsOu->setLogin($_SESSION['login']);
		$toolsOu->setRol($_SESSION['rol']);

		$skeletonOu->setHeadTitle('Edit structure '.$structure->getName());
		$skeletonOu->setHeaderMenu($headerMenuOu->render());
		$skeletonOu->setTools($toolsOu->render());
		break;
	case 'clone':
		$id = $_GET['id'];
		$structure = $structures->get($id);
		$contentOu->setStructureId("dup_$id");
		if ($structure === null) {
			/* Error, intentando editar una estructura que no existe */
			$skeletonOu->setBodyClass('error');
			$contentOu->setActionType('error');
		}
		else {
			$skeletonOu->setBodyClass('clone');
			$contentOu->setActionType('clone');
			$contentOu->setStructureName('[copy] '.$structure->getName());
			$contentOu->setStorageTypes($_ENV['ACD_STORAGE_TYPES']);
			$contentOu->setStorage($structure->getStorage());
			$contentOu->setFieldTypes(Model\FieldDo::getAvailableTypes());
			$contentOu->setFields($structure->getFields());

			$enumeratedLoader = new Model\EnumeratedLoader();
			$query = new Model\Query();
			$query->setType('all');
			$enumerated = $enumeratedLoader->load($query);
			$contentOu->setEnumeratedList($enumerated);
		}

		// back button
		$navigation = new SessionNavigation();
		$navigation->load();
		$back = !$navigation->isEmpty();
		$navigation->push([
			'hash' => "clone_structure - $id",
			'url' => $_SERVER["REQUEST_URI"],
			'title' => 'Clone structure '.$structure->getName()
		]);
		$navigation->save();

		$headerMenuOu = new View\HeaderMenu();
		$headerMenuOu->setBack($back);

		$toolsOu = new View\Tools();
		$toolsOu->setLogin($_SESSION['login']);
		$toolsOu->setRol($_SESSION['rol']);

		$skeletonOu->setHeadTitle('Clone structure '.$structure->getName());
		$skeletonOu->setHeaderMenu($headerMenuOu->render());
		$skeletonOu->setTools($toolsOu->render());
		break;
	case 'list':
	default:
		$toolsOu = new View\Tools();
		$toolsOu->setLogin($_SESSION['login']);
		$toolsOu->setRol($_SESSION['rol']);

		// back button
		$navigation = new SessionNavigation();
		$navigation->load();
		$back = !$navigation->isEmpty();
		$navigation->push([
			'hash' => "list_structures",
			'url' => $_SERVER["REQUEST_URI"],
			'title' => 'Manage structures'
		]);
		$navigation->save();

		$headerMenuOu = new View\HeaderMenu();
		$headerMenuOu->setBack($back);

		$contentOu->setActionType('index');
		$contentOu->setStructures($structures);

		$skeletonOu->setBodyClass('index');
		$skeletonOu->setHeadTitle('Manage structures');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());
		$skeletonOu->setTools($toolsOu->render());
		break;
}

$result = isset($_GET['r']) ? $_GET['r'] : '';
switch ($result) {
	case 'ok':
		$contentOu->setResultDesc('Done', 'ok');
		break;
	case 'ko':
		$contentOu->setResultDesc('<em>Error</em>, has not been able to process', 'fail');
		break;
	case 'kologin':
		$contentOu->setResultDesc('<em>Error</em>, incorrect login or password', 'fail');
		break;
	case 'kologinzerouser':
		$contentOu->setResultDesc('<em>Error</em>, user database empty or not available', 'fail');
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
