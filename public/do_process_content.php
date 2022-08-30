<?php

namespace Acd;

use \Acd\Controller\RolPermissionHttp;
use \Acd\Model\ValueFormater;

require('../autoload.php');
ini_set('session.gc_maxlifetime', conf::$SESSION_GC_MAXLIFETIME);
session_start();

if(!RolPermissionHttp::checkUserEditor([\Acd\conf::$ROL_DEVELOPER, \Acd\conf::$ROL_EDITOR])) die();

const ERROR = 'ERROR';

$action = isset($_POST['a']) ? strtolower($_POST['a']) : ERROR;
$postId = isset($_POST['id']) ? $_POST['id'] : [];
if ($action == 'save' && $postId == '') {
	$action = 'new';
}
$returnUrl = null;
foreach ($postId as $id) {
	$idStructure = $_POST['ids'][$id];
	$title = isset($_POST['title'][$id]) ? $_POST['title'][$id] : null;
	$periodOfValidity = array(
		\Acd\Model\ContentDo::PERIOD_OF_VALIDITY_START => isset($_POST['validityPeriod'][$id]['start']) ? $_POST['validityPeriod'][$id]['start'] : null,
		\Acd\Model\ContentDo::PERIOD_OF_VALIDITY_END => isset($_POST['validityPeriod'][$id]['end']) ? $_POST['validityPeriod'][$id]['end'] : null
	);
	$periodOfValidity = ValueFormater::decode($periodOfValidity, ValueFormater::TYPE_DATE_TIME_RANGE, ValueFormater::FORMAT_EDITOR);

	$aliasId = isset($_POST['aliasId'][$id]) ? $_POST['aliasId'][$id] : null;
	$tags = isset($_POST['tags'][$id]) ? ValueFormater::decode($_POST['tags'][$id], ValueFormater::TYPE_TAGS, ValueFormater::FORMAT_EDITOR) : array();
	$profile = isset($_POST['profile'][$id]) ? ValueFormater::decode($_POST['profile'][$id], ValueFormater::TYPE_LIST_MULTIPLE, ValueFormater::FORMAT_EDITOR) : array();
	$fields = isset($_POST['field'][$id]) ? $_POST['field'][$id] : array();

	$contentLoader = new \ACD\Model\ContentLoader();
	$contentLoader->setId($idStructure);
	$content = $contentLoader->loadContent('id', ValueFormater::decode($id, ValueFormater::TYPE_ID, ValueFormater::FORMAT_EDITOR));

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
		} else {
			$structureFound = true;
			//$modified_content = $contents->one();
			$modified_content = $content;
		}
	} catch (\Exception $e) {
		$structureFound = null;
		$modified_content = new Model\ContentDo();
	}

	switch ($action) {
		case 'new':
		case 'save':
			$modified_content->setTitle($title);
			$modified_content->setPeriodOfValidity($periodOfValidity);
			$modified_content->setAliasId($aliasId);
			$modified_content->setTags($tags);
			$modified_content->setProfileValues($profile);
			$numFields = count($fields);
			$formater = new Model\ValueFormater();
			foreach ($fields as $key => $data) {
				//$n = 0; $n < $numFields; $n++) {
				$fieldId = $fields[$key]['id'];
				$fieldType = $modified_content->getFieldType($fieldId);

				if (!isset($fields[$key]['value'])) {
					$fields[$key]['value'] = '';
				};
				// @$fields[$key]['value'] = $fields[$key]['value'] ?: ''; // Forze set
				// If get array of values and types the field is collection, preparte normalized value
				if ($fieldType === 'collection' && is_array($fields[$key]['value']) && is_array($fields[$key]['type'])) {
					$normalizedvalue = [];
					foreach ($fields[$key]['value'] as $keyValue => $value) {
						$normalizedvalue[] = [
							'ref' => $fields[$key]['value'][$keyValue],
							'id_structure' => $fields[$key]['type'][$keyValue]
						];
					}
				} elseif ($fieldType === 'content' && isset($fields[$key]['value']) && isset($fields[$key]['type'])) {
					// Field type relation
					$normalizedvalue = [
						'ref' => $fields[$key]['value'],
						'id_structure' => $fields[$key]['type']
					];
				} elseif ($fieldType === 'file') {
					$normalizedvalue = [
						'origin' => conf::$DATA_CONTENT_BINARY_ORIGIN_FORM_UPLOAD,
						'value' => $fields[$key]['value'],
						'tmp_name' => '',
						'alt' => isset($fields[$key]['alt']) ? $fields[$key]['alt'] : '',
						'delete' => isset($fields[$key]['delete'])
					];
					// Optional info
					@$normalizedvalue['original_name'] = $fields[$key]['original_name'] ?: '';
					@$normalizedvalue['type'] = $fields[$key]['type'] ?: '';
					@$normalizedvalue['size'] = $fields[$key]['size'] ?: '';
					@$normalizedvalue['width'] = $fields[$key]['width'] ?: '';
					@$normalizedvalue['height'] = $fields[$key]['height'] ?: '';
					// If get a new upload file
					if (isset($_FILES['field']) && $_FILES['field']['error'][$id][$key]['file'] === UPLOAD_ERR_OK) {
						$normalizedvalue['original_name'] = $_FILES['field']['name'][$id][$key]['file'];
						$normalizedvalue['tmp_name'] = $_FILES['field']['tmp_name'][$id][$key]['file'];
						$normalizedvalue['size'] = $_FILES['field']['size'][$id][$key]['file'];
						$fileTools = new \Acd\Model\File();
						$fileType = $fileTools->getMimeFromFilename($normalizedvalue['original_name']);
						if (!$fileType) {
							$fileType = $fileTools->getMimeFromPath($normalizedvalue['tmp_name']);
						}
						$normalizedvalue['type'] = $fileType;
						// Add with and height info for images
						$normalizedvalue = array_merge($normalizedvalue, $fileTools->getImageGeometryFromPath($normalizedvalue['tmp_name']));
					}
				} else {
					$normalizedvalue = $formater->decode($fields[$key]['value'], $fieldType, $formater::FORMAT_EDITOR);
				}
				$modified_content->setFieldValue($fieldId, $normalizedvalue);
			}
			$modified_content = $contentLoader->saveContent($modified_content);

			$id = $modified_content->getId();

			// Return to de root content not to the last child
			if (!$returnUrl) {
				$returnUrl = 'content.php?a=edit&r=ok&id=' . urlencode($id) . '&idt=' . urlencode($idStructure);
			}
			/* TODO ERROR
				$returnUrl = 'content.php?a='.$action.'&r=ko&id='.urlencode($id).'&idt='.urlencode($idt).'&title='.urlencode($title);
			*/
			break;
		case 'clone':
			$returnUrl = 'content.php?a=clone&id=' . urlencode($id) . '&idt=' . urlencode($idStructure);
			break;
		case 'delete':
			try {
				$contentLoader->deleteContent($id);
				$returnUrl = 'content.php?a=' . $action . '&r=ok&id=' . urlencode($idStructure);
			} catch (\Exception $e) {
				$returnUrl = 'content.php?a=edit&r=ko_delete&id=' . urlencode($id) . '&idt=' . urlencode($idStructure);
			}

			break;
		case 'summary':
			$returnUrl = 'content.php?a=summary&id=' . urlencode($id) . '&idt=' . urlencode($idStructure);
			break;
		default:
			$returnUrl = ERROR;
			break;
	}
}
if (!$returnUrl || $returnUrl === ERROR) {
	header("HTTP/1.0 500 Internal Server Error");
	echo "Error, request can not be processed. Empty form.\n";
	die();
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Location:$returnUrl");
