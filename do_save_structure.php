<?php
require ('conf.php');
require_once (DIR_BASE.'/class/structures_do.php');

$accion = $_POST['a'];
$id = $_POST['id'];
$name = $_POST['name'];
$storage = $_POST['storage'];

$structures = new structures_do();
$structures->load();

$bIdValid = false;
$structureFound = $structures->get($id);
switch ($accion) {
	case 'new':
		$bIdValid = ($structureFound === null && $id !== '');
		break;
	case 'edit':
		$bIdValid = ($structureFound !== null);
		break;
}

if($bIdValid) {
	// TODO: Set de la estructura, actualizar structures con los nuevos datos
	$modified_structure = new structure_do();
	//var_dump($modified_structure->generateId($name));die();
	$modified_structure->setId($id );
	$modified_structure->setName($name);
	$modified_structure->setStorage($storage);
	$structures->set($id, $modified_structure);

	$structures->save();

	$returnUrl = 'index.php?a=edit&r=ok&id='.urlencode($id);
}
else {
	$returnUrl = 'index.php?a='.$accion.'&r=ko&id='.urlencode($id).'&name='.urlencode($name).'&storage='.urlencode($storage);
}

header("Location:$returnUrl");