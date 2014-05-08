<?php
require ('conf.php');
require_once (DIR_BASE.'/class/structures_do.php');

$structures = new structures_do();
$structures->load();
$estructuras = $structures->getAllStructures();
//var_dump($estructuras);
//var_dump(conf::$STORAGE_TYPES);
/* Show action block */
$action = isset($_GET['a']) ? $_GET['a'] : 'list';
switch ($action) {
	case 'edit':
		$id = $_GET['id'];
		$estructura = $structures->get($id);
		$name = $estructura->getName();
		$storage = $estructura->getStorage();
		$tpl = DIR_BASE.'/tpl/edit.tpl';
		break;
	
	case 'list':
	default:
		$tpl = DIR_BASE.'/tpl/index.tpl';
		break;
}


require($tpl);