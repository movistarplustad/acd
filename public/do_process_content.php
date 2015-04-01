<?php
namespace Acd;

require ('../autoload.php');

$accion = strtolower($_POST['a']);
$id = $_POST['id'];
if ($accion == 'save' && $id == '') {
	$accion = 'new';
}
$idStructure = $_POST['ids'];
$title = isset($_POST['title']) ? $_POST['title'] : null;
$fields = isset($_POST['field']) ? $_POST['field'] : array();

$contentLoader = new \ACD\Model\ContentLoader();
$contentLoader->setId($idStructure);
$contents = $contentLoader->loadContents('id', $id);
//TODO Resolver mejor

try {
	if (is_null($contents) || $contents->length() === 0) {
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
				@$fields[$idField]['value'] = $fields[$idField]['value'] ?: ''; // Forze set
				// If get array of values and types the field is collection, preparte normalized value
				if (is_array($fields[$idField]['value']) && is_array($fields[$idField]['type'])) {
					$normalizedvalue = [];
					foreach ($fields[$idField]['value'] as $key => $value) {
						$normalizedvalue[] = [
							'ref'=> $fields[$idField]['value'][$key],
							'id_structure' => $fields[$idField]['type'][$key]
							];
					}
				}
				elseif (isset($fields[$idField]['value']) && isset($fields[$idField]['type'])) {
					// Field type relation
					$normalizedvalue = [
						'ref'=> $fields[$idField]['value'],
						'id_structure' => $fields[$idField]['type']
					];
				}
				else {
					$normalizedvalue = $fields[$idField]['value'];
				}
				//d($normalizedvalue);
				$modified_content->setFieldValue($fields[$idField]['name'], $normalizedvalue);
			}
			//dd($fields, $modified_content);
			$modified_content = $contentLoader->saveContent($modified_content);
			$id = $modified_content->getId();

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
			$returnUrl = 'content.php?a='.$accion.'&r=ok&id='.urlencode($idStructure);
		} catch (\Exception $e) {
			$returnUrl = 'content.php?a=edit&r=ko_delete&id='.urlencode($id).'&idt='.urlencode($idStructure);
		}

		break;
	default:
		$returnUrl = '404.html';
		break;
}
header("Location:$returnUrl");