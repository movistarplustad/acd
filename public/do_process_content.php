<?php
namespace Acd;

require ('../autoload.php');

$accion = strtolower($_POST['a']);
$id = $_POST['id'];
$idStructure = $_POST['ids'];
$title = isset($_POST['title']) ? $_POST['title'] : null;
$fields = isset($_POST['field']) ? $_POST['field'] : array();

$contentLoader = new \ACD\Model\ContentLoader();
$contentLoader->setId($idStructure);
$contents = $contentLoader->loadContent('id', $id);
//TODO Resolver mejor

try {
	if (is_null($contents)) {
		$structureFound = false;
		$modified_content = new Model\ContentDo();
		$modified_content->setIdStructure($idStructure);
	}
	else {
		$structureFound = true;	
		$modified_content = $contents->getFirst();
	}
} catch (\Exception $e) {
	$structureFound = null;
	$modified_content = new Model\ContentDo();
}

switch ($accion) {
	case 'new':
	case 'save':
			$modified_content->setTitle($title);
			$numFields = count($fields);
			foreach ($fields as $idField => $data) {
				//$n = 0; $n < $numFields; $n++) {
				$modified_content->setFieldValue($fields[$idField]['name'], $fields[$idField]['value']);
			}

			$contentLoader->saveContent($modified_content);

			$returnUrl = 'content.php?a=edit&r=ok&id='.urlencode($id).'&idt='.urlencode($idStructure);
		/* TODO ERROR
			$returnUrl = 'content.php?a='.$accion.'&r=ko&id='.urlencode($id).'&idt='.urlencode($idt).'&title='.urlencode($title);
		*/
		break;
	case 'clone':
		$returnUrl = 'content.php?a=clone&id='.urlencode($id).'&idt='.urlencode($idStructure);
		break;
	case 'delete':
		try {
			$contentLoader->deleteContent($id);
			$result = 'ok';
		} catch (\Exception $e) {
			d($e);
			$result = 'ko';
		}

		$returnUrl = 'content.php?a='.$accion.'&r='.$result.'&id='.urlencode($idStructure);
		break;
	default:
		$returnUrl = '404.html';
		break;
}

header("Location:$returnUrl");