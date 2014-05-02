<?php
/* Prueba de listado de estructuras */
require ('../conf.php');
require_once (DIR_BASE.'/class/structures_do.php');

$structures = new structures_do();
$structures->load(DIR_BASE.'/_test/structures_demo.json');
$estructuras = $structures->getAllStructures();

print_r($estructuras);


