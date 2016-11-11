<?php
namespace Acd;
use \Acd\Model\SessionNavigation;

require ('../autoload.php');
session_start();
function loadNewRef($idRef, $idStructure) {
	if (!$idRef || !$idStructure) {
		return null;
	}
	$contentLoader = new Model\ContentLoader();
	$contentLoader->setId($idStructure);
	$content = $contentLoader->loadContent('id', $idRef);
	//$contents = new Model\ContentsDo();
	//$contents->add($content);

	return $content;
}
$action = isset($_GET['a']) ? $_GET['a'] : 'list_structures';
$view = isset($_GET['v']) ? $_GET['v'] : 'page';

if (!Model\Auth::isLoged()) {
	$action = 'login';
}

switch ($action) {
	case 'login':
		header('Location: index.php?re='.urlencode($_SERVER["REQUEST_URI"]));
		return;
		break;
	case 'list_structures':
		$structures = new Model\StructuresDo();
		$structures->loadFromFile();

		// back button
		$navigation = new SessionNavigation();
		$navigation->load();
		$back = !$navigation->isEmpty();
		$navigation->push([
			'hash' => "content_list_structures",
			'url' => $_SERVER["REQUEST_URI"],
			'title' => 'Content, list of contents type'
		]);
		$navigation->save();

		$headerMenuOu = new View\HeaderMenu();
		$headerMenuOu->setBack($back);

		$toolsOu = new View\Tools();
		$toolsOu->setLogin($_SESSION['login']);
		$toolsOu->setRol($_SESSION['rol']);

		$contentOu = new View\ContentEditIndex();
		//$contentOu->setActionType('index');
		$contentOu->setStructures($structures);
		//$contentOu->setTODO($estructuras);

		$skeletonOu = new View\BaseSkeleton();
		$skeletonOu->setBodyClass('indexContent');
		$skeletonOu->setHeadTitle('Content, list of contents type');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());
		$skeletonOu->setTools($toolsOu->render());
		break;
	case 'delete':
	case 'list_contents':
		$id = $_GET['id'];
		@$titleSearch = $_GET['s'];
		$numPage = isset($_GET['p']) ? (int) $_GET['p'] : 0;
		$bResult = isset($_GET['r']) && $_GET['r'] === 'ko' ? false : true;

		// back button
		$navigation = new SessionNavigation();
		$navigation->load();
		$back = !$navigation->isEmpty();
		$navigation->push([
			'hash' => "list_contents - $id - $titleSearch - $numPage",
			'url' => $_SERVER["REQUEST_URI"],
			'title' => 'Manage elements of '.$id
		]);
		$navigation->save();

		$headerMenuOu = new View\HeaderMenu();
		$headerMenuOu->setBack($back);

		$contentOu = new View\ContentEditListContent();
		$contentOu->setId($id);
		$contentOu->setTitleSeach($titleSearch);
		$contentOu->load();

		$contentLoader = new Model\ContentLoader();
		$contentLoader->setId($id);
		$limits = $contentLoader->getLimits();
		$limits->setPage($numPage);

		if ($titleSearch) {
			$whereCondition = [];
			$whereCondition['title'] = $titleSearch;
			$whereCondition['idStructure'] = $id;
			$contents = $contentLoader->loadContents('editor-search', $whereCondition);
		}
		else {
			$contents = $contentLoader->loadContents('all');
		}
		$contentOu->setContents($contents);

		$toolsOu = new View\Tools();
		$toolsOu->setLogin($_SESSION['login']);
		$toolsOu->setRol($_SESSION['rol']);

		$skeletonOu = new View\BaseSkeleton();
		$skeletonOu->setBodyClass('editContent');
		$skeletonOu->setHeadTitle('Manage elements of '.$id);
		$skeletonOu->setHeaderMenu($headerMenuOu->render());
		$skeletonOu->setTools($toolsOu->render());

		if ($action == 'delete' && $bResult) {
			$contentOu->setResultDesc('Done', 'ok');
		}
		break;
	case 'new':
		$idStructureType = $_GET['idt'];
		// Posible parent
		@$idParent = $_GET['idp'] ?: null; // TODO duplicado en edit y clone
		@$idTypeParent = $_GET['idtp'] ?: null;

		// back button
		$navigation = new SessionNavigation();
		$navigation->load();
		$back = !$navigation->isEmpty();
		$navigation->push([
			'hash' => "edit_content - $idStructureType - *new*",
			'url' => $_SERVER["REQUEST_URI"],
			'title' => 'New content ('.$idStructureType.')'
		]);
		$navigation->save();

		$headerMenuOu = new View\HeaderMenu();
		$headerMenuOu->setBack($back);

		$contentOu = new View\ContentEditContent();
		$structure = new Model\StructureDo();
		$structure->setId($idStructureType);
		$structure->loadFromFile(['loadEnumerated' => true]);

		$contentOu->setStructure($structure);

		$content = new Model\ContentDo();
		$content->setIdStructure($idStructureType);
		$contentOu->setContent($content);
		$contentOu->newContent(true);
		$contentOu->setUserRol($_SESSION['rol']);

		$toolsOu = new View\Tools();
		$toolsOu->setLogin($_SESSION['login']);
		$toolsOu->setRol($_SESSION['rol']);

		$skeletonOu = new View\BaseSkeleton();
		$skeletonOu->setBodyClass('editContent');
		$skeletonOu->setHeadTitle('New content ('.$structure->getName().')');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());
		$skeletonOu->setTools($toolsOu->render());
		break;
	case 'edit':
	case 'clone':
	case 'summary':
		$bResult = isset($_GET['r']) && $_GET['r'] == 'ok' ? true : false;
		$id = $_GET['id'];
		$idStructureType = $_GET['idt'];
		// Posible parent
		@$idParent = $_GET['idp'] ?: null;
		@$idTypeParent = $_GET['idtp'] ?: null;

		// back button
		$navigation = new SessionNavigation();
		$navigation->load();
		$back = !$navigation->isEmpty();
		$navigation->push([
			'hash' => "edit_content - $idStructureType - $id",
			'url' => $_SERVER["REQUEST_URI"],
			'title' => 'Manage content ('.$idStructureType.')'
		]);
		$navigation->save();

		$headerMenuOu = new View\HeaderMenu();
		$headerMenuOu->setBack($back);

		$contentOu = new View\ContentEditContent();
		$structure = new Model\StructureDo();
		$structure->setId($idStructureType);
		$structure->loadFromFile(['loadEnumerated' => true]);
		$contentOu->setStructure($structure);

		$contentLoader = new Model\ContentLoader();
		$contentLoader->setId($idStructureType);
		$content = $contentLoader->loadContent('id+countParents', $id);
		//$content = $contents->get($id); // TODO cambiar por next / first...
		//dd($contentLoader->getFields(),$structure, $content);

		// Modify relations or collection of relations
		//&idm=imagen alternativa&refm=yy54f5c82b6803fabb068b4567&reftm=enlace&posm=0
		if (isset($_GET['idm'])) {
			$modifiedFieldName = $_GET['idm']; //'imagen alternativa'; // elementos
			$modifiedRef = $_GET['refm']; //'xx54f5c82b6803fabb068b4567';
			$modifiedIdStructure = $_GET['reftm']; //''enlace';
			$modifiedFieldPosition = isset($_GET['posm']) ? $_GET['posm'] : null;
			try {
				//+d($content->getFields());
				$modifiedField = $content->getFields()->get($modifiedFieldName);
				switch ($modifiedField->getType()) {
					case Model\FieldDo::TYPE_CONTENT:
						$newRef = loadNewRef($modifiedRef, $modifiedIdStructure);
						break;
					case Model\FieldDo::TYPE_COLLECTION:

						/* Modify or delete item */
						if ($modifiedRef) {
							$newRef = $modifiedField->getValue();
							$newRef->add(loadNewRef($modifiedRef, $modifiedIdStructure));
						}
						else {
							$newRef = $modifiedField->getValue();
							$newRef->remove($modifiedFieldPosition);
						}

						break;
					default:
				 		$newRef = $modifiedField;
						break;
				}
				//d($content);
				//d($modifiedFieldName, $newRef);
				$content->setFieldValue($modifiedFieldName, $newRef);
				//d($content);

				$modifiedField->setDirty(true, $modifiedFieldPosition);
			}
			catch(\Exception $e) {
				$contentOu->setResultDesc("Error, field <em>$modifiedFieldName</em> not found in content", "fail");
				$bResult = false;
			}
		}

		if ($action == 'clone') {
			$content->setId(null);
			$content->setTitle('[copy] '.$content->getTitle());
		}
		$contentOu->setContent($content);
		$contentOu->setUserRol($_SESSION['rol']);

		$toolsOu = new View\Tools();
		$toolsOu->setLogin($_SESSION['login']);
		$toolsOu->setRol($_SESSION['rol']);

		$skeletonOu = new View\BaseSkeleton();
		$skeletonOu->setBodyClass('editContent');
		$skeletonOu->setHeadTitle('Manage content ('.$structure->getName().')');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());
		$skeletonOu->setTools($toolsOu->render());

		if ($action == 'summary') {
			$summaryController = new Controller\Summary();
			$summaryController->setIdContent($id);
			$summaryController->setIdStructure($idStructureType);
			$summaryController->load();
			$contentOu->setSummary($summaryController->render());
		}

		if ($bResult) {
			$contentOu->setResultDesc('Done', 'ok');
		}
		break;
	default:
		dd("Error 404");
}

$skeletonOu->setContent($contentOu->render());
$skeletonOu->setView($view);

header("Content-Type: text/html");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

echo $skeletonOu->render();
