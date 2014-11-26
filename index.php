<?php
require ('conf.php');
require_once (DIR_BASE.'/class/structures_do.php');
require_once (DIR_BASE.'/class/auth.php');
$nick = '';
$hash = '';
//echo auth::isLoged($nick, $hash);

$structures = new structures_do();
$structures->loadFromFile(conf::$DATA_PATH);
$estructuras = $structures->getAllStructures();
//var_dump($estructuras);
//var_dump(conf::$STORAGE_TYPES);
/* Show action block */
$action = isset($_GET['a']) ? $_GET['a'] : 'list';
switch ($action) {
	case 'edit':
		$id = $_GET['id'];
		$estructura = $structures->get($id);
		if ($estructura === null) {
			/* Error, intentando editar una estructura que no existe */
			$titleName = '(error)';
			$tpl = DIR_BASE.'/tpl/error.tpl';
		}
		else {
			$name = $estructura->getName();
			$titleName = $name;
			$storage = $estructura->getStorage();
			$fields = $estructura->getFields();
			$tpl = DIR_BASE.'/tpl/edit.tpl';
		}
		break;
	case 'new':
		$bResult = isset($_GET['r']) && $_GET['r'] === 'ko' ? false : true;

		$estructura = new structure_do();
		$id = isset($_GET['id']) ? $_GET['id'] : '';
		$name = isset($_GET['name']) ? $_GET['name'] : '';
		$titleName = '(nuevo)';
		$storage = isset($_GET['storage']) ? $_GET['storage'] : '';
		$tpl = DIR_BASE.'/tpl/new.tpl';
		break;
	case 'clone':
		$id = $_GET['id'];
		$estructura = $structures->get($id);
		if ($estructura === null) {
			/* Error, intentando editar una estructura que no existe */
			$titleName = '(error)';
			$tpl = DIR_BASE.'/tpl/error.tpl';
		}
		else {
			$id = "cp_$id";
			$name = '[copia] '.$estructura->getName();
			$titleName = "(copia)";
			$storage = $estructura->getStorage();
			$fields = $estructura->getFields();
			$tpl = DIR_BASE.'/tpl/new.tpl';
		}
		break;
	case 'list':
	default:
		$tpl = DIR_BASE.'/tpl/index.tpl';
		break;
}

$result = isset($_GET['r']) ? $_GET['r'] : '';
switch ($result) {
	case 'ok':
		$resultDesc = 'Guardado';
		break;
	case 'ko':
		$resultDesc = '<em>Error</em>, no se ha podido procesar';
		break;
	default:
		$resultDesc = '';
		break;
}


header("Content-Type: text/html");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require($tpl);