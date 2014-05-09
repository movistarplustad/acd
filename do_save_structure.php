<?php
require ('conf.php');
require_once (DIR_BASE.'/class/structures_do.php');

$accion = $_POST['accion'];
$id = $_POST['id'];
$name = $_POST['name'];
$storage = $_POST['storage'];

$structures = new structures_do();
$structures->load();
// TODO: Set de la estructura, actualizar structures con los nuevos datos
$modified_structure = new structure_do();
$modified_structure->setId($id );
$modified_structure->setName($name);
$modified_structure->setStorage($storage);
$structures->set($id, $modified_structure);

$structures->save();

header('Location:index.php?a=edit&id='.$id);