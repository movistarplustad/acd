<?php
namespace Acd;

require ('../autoload.php');
session_start();
$action =$_GET['a'];
@$id = $_GET['id'];
@$idStructureTypeSearch = $_GET['idt'];
@$titleSearch = $_GET['s'];
$idParent = $_GET['idp'];
$idStructureTypeParent = $_GET['idtp'];
$idField = $_GET['f'];
@$positionInField = $_GET['p'];
$numPage = isset($_GET['p']) ? (int) $_GET['p'] : 0;
if (!Model\Auth::isLoged()) {
	$action = 'login';
}

switch ($action) {
	case 'login':
		header('Location: index.php');
		return;
		break;
	case 'select_type': 
	case 'search': 
		$structures = new Model\StructuresDo();
		$structures->loadFromFile(conf::$DATA_PATH);

		// back button
		$navigation = new Controller\SessionNavigation();
		$navigation->load();
		$back = !$navigation->isEmpty();
		$navigation->push([
			'hash' => "edit_content _relation - $idParent - $idStructureTypeParent -  $idField",
			'url' => $_SERVER["REQUEST_URI"]
		]);
		$navigation->save();

		$headerMenuOu = new View\HeaderMenu();
		$headerMenuOu->setBack($back);

		$toolsOu = new View\Tools();
		$toolsOu->setLogin($_SESSION['login']);
		$toolsOu->setRol($_SESSION['rol']);

		$contentOu = new View\ContentEditSearch();
		//$contentOu->setActionType('index');
		$contentOu->setId($idParent);
		$contentOu->setType($idStructureTypeParent);
		$contentOu->setIdField($idField);
		$contentOu->setPositionInField($positionInField);
		$contentOu->setStructures($structures);
		$contentOu->setTitleSeach($titleSearch);
		$contentOu->setStructureTypeSeach($idStructureTypeSearch);

		if ($action === 'search') {
			$contentLoader = new Model\ContentLoader();
			$contentLoader->setId($idStructureTypeSearch);
			$whereCondition = [];
			if($titleSearch) {
				$whereCondition['title'] = $titleSearch;
			}
			if($idStructureTypeSearch) {
				$whereCondition['idStructure'] = $idStructureTypeSearch;
			}
			$limits = $contentLoader->getLimits();
			$limits->setPage($numPage);
			$matchContents = $contentLoader->loadContents('editor-search', $whereCondition);
			//d($matchContents);
			$contentOu->setResultSearch($matchContents);
		}


		$skeletonOu = new View\BaseSkeleton();
		$skeletonOu->setBodyClass('indexContent');
		$skeletonOu->setHeadTitle('Manage content type');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());
		$skeletonOu->setTools($toolsOu->render());
		break;

	default:
		dd("Error 404");
}

$skeletonOu->setContent($contentOu->render());

header("Content-Type: text/html");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

echo $skeletonOu->render();
