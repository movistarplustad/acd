<?php
//phpinfo();


// conectar
$m = new MongoClient();

// seleccionar una base de datos
$db = $m->comedy;

// seleccionar una colección (equivalente a una tabla en una base de datos relacional)
$collection = $db->cartoons;

// añadir un registro
$document = array( "title" => "Calvin and Hobbes", "author" => "Bill Watterson" );
$collection->insert($document);

// añadir un nuevo registro, con un distinto "perfil"
$document = array( "title" => "XKCD", "online" => true );
$collection->insert($document);

// encontrar todo lo que haya en la colección
$cursor = $collection->find();

// recorrer el resultado
foreach ($cursor as $document) {
    echo $document["title"] . "\n";
}