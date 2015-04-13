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
$tags = isset($_POST['tags']) ? \Acd\Model\ValueFormater::decode($_POST['tags'] , \Acd\Model\ValueFormater::TYPE_TAGS, \Acd\Model\ValueFormater::FORMAT_EDITOR): array();
$fields = isset($_POST['field']) ? $_POST['field'] : array();

$contentLoader = new \ACD\Model\ContentLoader();
$contentLoader->setId($idStructure);
//$contents = $contentLoader->loadContents('id', $id);
$content = $contentLoader->loadContents('id', $id);
//TODO Resolver mejor

try {
	//if (is_null($contents) || $contents->length() === 0) {
	if (is_null($content)) {
		$structureFound = false;
		$structure = new Model\StructureDo();
		$structure->setId($idStructure);
		$structure->loadFromFile();
		$modified_content = new Model\ContentDo();
		$modified_content->buildSkeleton($structure);
	}
	else {
		$structureFound = true;
		//$modified_content = $contents->one();
		$modified_content = $content;
	}
} catch (\Exception $e) {
	$structureFound = null;
	$modified_content = new Model\ContentDo();
}

switch ($accion) {
	case 'new':
	case 'save':
			$modified_content->setTitle($title);
			$modified_content->setTags($tags);
			$numFields = count($fields);
			$formater = new Model\ValueFormater();
			foreach ($fields as $key => $data) {
				//$n = 0; $n < $numFields; $n++) {
				$fieldId = $fields[$key]['id'];
				$fieldType = $modified_content->getFieldType($fieldId);
				@$fields[$key]['value'] = $fields[$key]['value'] ?: ''; // Forze set
				// If get array of values and types the field is collection, preparte normalized value
				if ($fieldType === 'collection' && is_array($fields[$key]['value']) && is_array($fields[$key]['type'])) {
					$normalizedvalue = [];
					foreach ($fields[$key]['value'] as $keyValue => $value) {
						$normalizedvalue[] = [
							'ref'=> $fields[$key]['value'][$keyValue],
							'id_structure' => $fields[$key]['type'][$keyValue]
							];
					}
				}
				elseif ($fieldType === 'content' && isset($fields[$key]['value']) && isset($fields[$key]['type'])) {
					// Field type relation
					$normalizedvalue = [
						'ref'=> $fields[$key]['value'],
						'id_structure' => $fields[$key]['type']
					];
				}
				else {
					$normalizedvalue = $formater->decode($fields[$key]['value'], $fieldType, $formater::FORMAT_EDITOR);
				}
				$modified_content->setFieldValue($fieldId, $normalizedvalue);
			}
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