<?php
require '../config/conf.php';
use \Acd\Model\ContentLoader;

$ID_STRUCTURE = 'test_borrar';
$ALIAS_ID = 'uno';
//$PROFILE = 'ANDORRA';
//$PROFILE = 'DTHTITULAR';
$PROFILE = 'LITE';

$contentLoader = new ContentLoader();
$contentLoader->setId($ID_STRUCTURE);

header('Content-Type: text/plain');
echo "\nid\n";
$ID_CONTENT = '67b85597d6de7f8f2604e5c2';
$content = $contentLoader->loadContent('id', $ID_CONTENT);
echo "Cargando de estructura '$ID_STRUCTURE' id '$ID_CONTENT'\n";
if($content === null) {
    echo "No se ha encontrado el contenido\n";
}
else {
    var_dump($content->getTitle());
}

echo "\nalias-id-deep profile\n";
$content = $contentLoader->loadContent('alias-id-deep', ['id' => $ALIAS_ID, 'depth' => 5, 'profile'=> $PROFILE, 'validity-date' => time()]);
echo "Cargando de estructura '$ID_STRUCTURE' contenido '$ALIAS_ID' perfil '$PROFILE'\n";
if($content === null) {
    echo "No se ha encontrado el contenido\n";
}
else {
    var_dump($content->getTitle());
}
