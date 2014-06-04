<?php
require ('conf.php');
require_once (DIR_BASE.'/class/structures_do.php');

$accion = $_POST['a'];
$id = $_POST['id'];
$name = isset($_POST['name']) ? $_POST['name'] : null;
$storage = isset($_POST['storage']) ? $_POST['storage'] : null;
$new_field_type = isset($_POST['new_field']) ? $_POST['new_field'] : null;

$structures = new structures_do();
$structures->load();

try {
	$structureFound = $structures->get($id);	
} catch (Exception $e) {
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
			$modified_structure = new structure_do();
			//var_dump($modified_structure->generateId($name));die();
			$modified_structure->setId($id );
			$modified_structure->setName($name);
			$modified_structure->setStorage($storage);
			if($new_field_type) {
				$field = new field_do();
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
			$result = $structures->remove($id) ? 'ok' : 'ko';
			$structures->save();
		} catch (Exception $e) {
			$result = 'ko';
		}

		$returnUrl = 'index.php?a='.$accion.'&r='.$result.'&id='.urlencode($id);
		break;
	default:
		$returnUrl = '404.html';
		break;
}

header("Location:$returnUrl");