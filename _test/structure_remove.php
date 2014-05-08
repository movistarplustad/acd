<?php
/* Prueba de añadir nuevas estructuras */
require ('../conf.php');
require_once (DIR_BASE.'/class/structures_do.php');
require_once (DIR_BASE.'/class/structure_do.php');

$structures_do = new structures_do();
for ($n = 0; $n < 5; $n++) {
$new_structure = new structure_do();
	$new_structure->setId("foo_$n");
	$new_structure->setName("Name foo $n");
	$new_structure->setStorage('text/plain');
	$structures_do->add($new_structure);
}
$structures_do->remove('foo_2');

$estructuras = $structures_do->getAllStructures();

print_r($estructuras);