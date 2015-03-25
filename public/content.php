<?php
namespace Acd;

require ('../autoload.php');
session_start();
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
		$skeletonOu->setHeadTitle('Manage content type');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());
		$skeletonOu->setTools($toolsOu->render());
		break;
	case 'delete':
	case 'list_contents':
		$id = $_GET['id'];
		$bResult = isset($_GET['r']) && $_GET['r'] === 'ko' ? false : true;
		$headerMenuOu = new View\HeaderMenu();
		$headerMenuOu->setType('backContent');

		$contentOu = new View\ContentEditListContent();
		$contentOu->setId($id);
		$contentOu->load();

		$contentLoader = new Model\ContentLoader();
		$contentLoader->setId($id);
		$contents = $contentLoader->loadContent('all');
		$contentOu->setContents($contents);

		$skeletonOu = new View\BaseSkeleton();
		$skeletonOu->setBodyClass('editContent');
		$skeletonOu->setHeadTitle('Manage elements');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());

		if ($action == 'delete' && $bResult) {
			$contentOu->setResultDesc('Done');
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
		$structure->loadFromFile();
		$contentOu->setStructure($structure);

		$content = new Model\ContentDo();
		$content->setIdStructure($idStructureType);
		$contentOu->setContent($content);
		$contentOu->newContent(true);

		$skeletonOu = new View\BaseSkeleton();
		$skeletonOu->setBodyClass('editContent');
		$skeletonOu->setHeadTitle('Manage content');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());

		break;
	case 'edit':
	case 'clone':
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
		$structure->loadFromFile();
		$contentOu->setStructure($structure);

		$contentLoader = new Model\ContentLoader();
		$contentLoader->setId($idStructureType);
		$content = $contentLoader->loadContent('id', $id);
		$content = $content->get($id); // TODO cambiar por next / first...
		//dd($contentLoader->getFields(),$structure, $content);

		// Modify relations or collection of relations
		//&idm=imagen alternativa&refm=yy54f5c82b6803fabb068b4567&reftm=enlace&posm=0
		if (isset($_GET['idm'])) {
			$modifiedFieldName = $_GET['idm']; //'imagen alternativa'; // elementos
			$modifiedRef = $_GET['refm']; //'xx54f5c82b6803fabb068b4567';
			$modifiedIdStructure = $_GET['reftm']; //''enlace';
			$modifiedFieldPosition = isset($_GET['posm']) ? $_GET['posm'] : null;
			try {
				$modifiedField = $content->getFields()->get($modifiedFieldName);
				//d($modifiedField->getType());
				switch ($modifiedField->getType()) {
					case Model\FieldDO::TYPE_CONTENT:
						$newRef = [
							'ref'=> $modifiedRef,
							'id_structure' => $modifiedIdStructure
						];
						break;
					case Model\FieldDO::TYPE_COLLECTION:
						$newRef = $modifiedField->getValue();

						/* Modify or delete item */
						if ($modifiedRef) {
							$newRef[$modifiedFieldPosition] = [
								'ref'=> $modifiedRef,
								'id_structure' => $modifiedIdStructure
							];
						}
						else {
							unset ($newRef[$modifiedFieldPosition]);
						}

						break;
					default:
				 		$newRef = $modifiedField;
						break;
				} 
				//d($content);
				//d($modifiedFieldName, $val);
				$content->setFieldValue($modifiedFieldName, $newRef);
				//d($content);

				$modifiedField->setDirty(true, $modifiedFieldPosition);
			}
			catch(\Exception $e) {
				$contentOu->setResultDesc("Error, field <em>$modifiedFieldName</em> not found in content");
				$bResult = false;
			}
		}
//dd($content->getFields()->get('Fotos')->getValue()); // Colección
//dd($content->getFields()->get('enlace')->getRef()); // Elemento simple 
		if ($action == 'clone') {
			$content->setId(null);
			$content->setTitle('[copy] '.$content->getTitle());
		}
		$contentOu->setContent($content);
		
		$skeletonOu = new View\BaseSkeleton();
		$skeletonOu->setBodyClass('editContent');
		$skeletonOu->setHeadTitle('Manage content');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());

		if ($bResult) {
			$contentOu->setResultDesc('Done');
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
