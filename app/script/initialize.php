<?php
namespace Acd;

require ('../../autoload.php');
$mongo = new \MongoClient();
$db = $mongo->acd;
$mongoCollection = $db->selectCollection('relation');
$mongoCollection->createIndex (['parent' => 1, 'child' => 1]);

var_dump($mongoCollection->getIndexInfo());