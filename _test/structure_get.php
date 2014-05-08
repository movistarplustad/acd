<?php
/* Prueba de recuperar una de las estructuras */
require ('../conf.php');
require_once (DIR_BASE.'/class/structures_do.php');

$idSearch = isset($_GET['id']) ? $_GET['id'] : 'chat_tienda'; // programa_tv chat_tienda

$structures = new structures_do();
$structures->load();

$structure = $structures->get($idSearch);

print_r($structure);

