<?php
namespace Acd;

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
if (!Model\Auth::isLoged()) {
	$action = 'login';
}

switch ($action) {
	case 'login':
		header('Location: index.php');
		return;
		break;
	case 'list_structures': 
	$structures = new Model\StructuresDo();
	$structures->loadFromFile();
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
		$skeletonOu->setHeadTitle('Manage content type');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());
		$skeletonOu->setTools($toolsOu->render());
		break;
	case 'delete':
	case 'list_contents':
		$id = $_GET['id'];
		@$titleSearch = $_GET['s'];
		$numPage = isset($_GET['p']) ? (int) $_GET['p'] : 0;
		$bResult = isset($_GET['r']) && $_GET['r'] === 'ko' ? false : true;
		$headerMenuOu = new View\HeaderMenu();
		$headerMenuOu->setType('backContent');

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

		$skeletonOu = new View\BaseSkeleton();
		$skeletonOu->setBodyClass('editContent');
		$skeletonOu->setHeadTitle('Manage elements');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());

		if ($action == 'delete' && $bResult) {
			$contentOu->setResultDesc('Done', 'ok');
		}
		break;
	case 'new':
		$idStructureType = $_GET['idt'];
		// Posible parent
		@$idParent = $_GET['idp'] ?: null; // TODO duplicado en edit y clone
		@$idTypeParent = $_GET['idtp'] ?: null;

		$headerMenuOu = new View\HeaderMenu();
		$headerMenuOu->setType('backListContent');
		if ($idParent) {
			$headerMenuOu->setUrl('content.php?a=edit&amp;id='.urlencode($idParent).'&amp;idt='.urlencode($idTypeParent));
		}
		else {
			$headerMenuOu->setUrl('content.php?a=list_contents&amp;id='.urlencode($idStructureType));
		}

		$contentOu = new View\ContentEditContent();
		$structure = new Model\StructureDo();
		$structure->setId($idStructureType);
		$structure->loadFromFile(['loadEnumerated' => true]);

		$contentOu->setStructure($structure);

		$enumeratedLoader = new Model\EnumeratedLoader();
		$profiles = $enumeratedLoader->load('PROFILE');

		$content = new Model\ContentDo();
		$content->setIdStructure($idStructureType);
//dd($content->getProfile()->getOptions());
		$contentOu->setContent($content, $profiles);
		$contentOu->newContent(true);
		$contentOu->setUserRol($_SESSION['rol']);

		$skeletonOu = new View\BaseSkeleton();
		$skeletonOu->setBodyClass('editContent');
		$skeletonOu->setHeadTitle('Manage content');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());

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

		$headerMenuOu = new View\HeaderMenu();
		$headerMenuOu->setType('backListContent');
		if ($idParent) {
			$headerMenuOu->setUrl('content.php?a=edit&amp;id='.urlencode($idParent).'&amp;idt='.urlencode($idTypeParent));
		}
		else {
			$headerMenuOu->setUrl('content.php?a=list_contents&amp;id='.urlencode($idStructureType));
		}

		$contentOu = new View\ContentEditContent();
		$structure = new Model\StructureDo();
		$structure->setId($idStructureType);
		$structure->loadFromFile(['loadEnumerated' => true]);
		$contentOu->setStructure($structure);


		$enumeratedLoader = new Model\EnumeratedLoader();
		$profiles = $enumeratedLoader->load('PERFIL');

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
		$contentOu->setContent($content, $profiles);
		$contentOu->setUserRol($_SESSION['rol']);
		
		$skeletonOu = new View\BaseSkeleton();
		$skeletonOu->setBodyClass('editContent');
		$skeletonOu->setHeadTitle('Manage content');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());

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

header("Content-Type: text/html");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

echo $skeletonOu->render();
