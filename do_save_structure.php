<?php
$accion = $_POST['accio'];
$id = $_POST['id'];
$name = $_POST['name'];
$storage = $_POST['storage'];

$structures = new structures_do();
$structures->load();
// TODO: Set de la estructura, actualizar structures con los nuevos datos
// $structures->save();

header('Location:index.php?a=edit&id='.$id);