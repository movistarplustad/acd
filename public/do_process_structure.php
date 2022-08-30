<?php
namespace Acd;
use \Acd\Controller\RolPermissionHttp;

require ('../autoload.php');

ini_set('session.gc_maxlifetime', conf::$SESSION_GC_MAXLIFETIME);
session_start();
if(!RolPermissionHttp::checkUserEditor([\Acd\conf::$ROL_DEVELOPER])) die();

$accion = strtolower($_POST['a']);
$id = $_POST['id'];
$name = isset($_POST['name']) ? $_POST['name'] : null;
$storage = isset($_POST['storage']) ? $_POST['storage'] : null;
$new_field_type = isset($_POST['new_field']) ? $_POST['new_field'] : null;
$fields = isset($_POST['field']) ? $_POST['field'] : array();

$structures = new Model\StructuresDo();
$structures->loadFromFile(\Acd\conf::$DATA_PATH);

try {
	$structureFound = $structures->get($id);
} catch (\Exception $e) {
	$structureFound = null;
}

switch ($accion) {
	case 'edit':
		$returnUrl = 'index.php?a=edit&id='.urlencode($id);
		$bNeedSave = false;
		break;
	case 'new':
	case 'save':
		$bIdValid = false;
		if ($accion === 'new') {
			$bIdValid = ($structureFound === null && $id !== '');
		}
		else {
			$bIdValid = ($structureFound !== null);
		}
		if($bIdValid) {
			// TODO: Set de la estructura, actualizar structures con los nuevos datos
			$modified_structure = new Model\StructureDo();
			$modified_structure->setId($id);
			$modified_structure->setName($name);
			$modified_structure->setStorage($storage);
			$numFields = count($fields);
			foreach ($fields as $idField => $data) {
				//$n = 0; $n < $numFields; $n++) {
				if (!isset($fields[$idField]['delete'])) {
					$field = new Model\FieldDo();
					$newId = ($fields[$idField]['id'] === ''  || $fields[$idField]['id'] === $field::EMPTY_ID )? $field->generateId($fields[$idField]['name']) : $fields[$idField]['id'];
					$field->setId($newId);
					$field->setType($fields[$idField]['type']);
					$field->setName($fields[$idField]['name']);

					if(isset($fields[$idField]['source'])) {
						$source = new Model\EnumeratedDo();
						$source->setId($fields[$idField]['source']);
						$field->setOptions($source);
					}

					if(isset($fields[$idField]['restrictedStructures'])) {
						$restrictedStructures = new Model\StructuresDo();
						foreach($fields[$idField]['restrictedStructures'] as $idRestrictedStructure) {
							$restrictedStructure = new Model\StructureDo();
							$restrictedStructure->setId($idRestrictedStructure);
							$restrictedStructures->add($restrictedStructure, $idRestrictedStructure);
						}
						$field->setRestrictedStructures($restrictedStructures);
					}

					$modified_structure->addField($field);
				}
			}
			if($new_field_type) {
				$field = new Model\FieldDo();
				$field->setType($new_field_type);
				$modified_structure->addField($field);
			}
			$structures->set($modified_structure, $id);
			$structures->save();

			$returnUrl = 'index.php?a=edit&r=ok&id='.urlencode($id);
		}
		else {
			$returnUrl = 'index.php?a='.$accion.'&r=ko&id='.urlencode($id).'&name='.urlencode($name).'&storage='.urlencode($storage);
		}
		break;
	case 'clone':
		$returnUrl = 'index.php?a=clone&id='.urlencode($id);
		break;
	case 'delete':
		try {
			$structures->remove($id);
			$structures->save();
			$result = 'ok';
		} catch (\Exception $e) {
			$result = 'ko';
		}

		$returnUrl = 'index.php?a='.$accion.'&r='.$result.'&id='.urlencode($id);
		break;
	default:
		$returnUrl = '404.html';
		break;
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Location:$returnUrl");
