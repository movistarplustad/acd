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
		$headerMenuOu = new View\HeaderMenu();
		$headerMenuOu->setType('backListContent');
		$headerMenuOu->setUrl('content.php?a=list_contents&amp;id='.urlencode($idStructureType));

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
		$bResult = isset($_GET['r']) && $_GET['r'] == 'ok' ? true : false;
		$id = $_GET['id'];
		$idStructureType = $_GET['idt'];
		$headerMenuOu = new View\HeaderMenu();
		$headerMenuOu->setType('backListContent');
		$headerMenuOu->setUrl('content.php?a=list_contents&amp;id='.urlencode($idStructureType));

		$contentOu = new View\ContentEditContent();
		$structure = new Model\StructureDo();
		$structure->setId($idStructureType);
		$structure->loadFromFile();
		$contentOu->setStructure($structure);

		$contentLoader = new Model\ContentLoader();
		$contentLoader->setId($idStructureType);
		$content = $contentLoader->loadContent('id', $id);
		$content = $content->get($id); // TODO cambiar por next / first...
		$contentOu->setContent($content);
		
		$skeletonOu = new View\BaseSkeleton();
		$skeletonOu->setBodyClass('editContent');
		$skeletonOu->setHeadTitle('Manage content');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());

		if ($bResult) {
			$contentOu->setResultDesc('Done');
		}
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
