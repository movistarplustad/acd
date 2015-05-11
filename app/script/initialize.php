<?php
namespace Acd;

require ('../../autoload.php');

$bMongoDB = false;
$bMySql = true;

if($bMongoDB){
	$mongo = new \MongoClient();
	$db = $mongo->acd;

	$aCollections = [
		'content',
		'relation',
		'structure'
		];
	$aCollectionsInDB = [];
	$aCollectionsInDB = $db->getCollectionNames();
	foreach ($aCollections as $collectionName) {
		if (in_array($collectionName, $aCollectionsInDB)) {
			echo "$collectionName is ok\n";
		}
		else {
			echo "Creating $collectionName\n";
			$db->createCollection($collectionName, false);
		}
	}

	// Relation
	echo "Creating indexed in relation collection\n";
	$mongoCollection = $db->selectCollection('relation');
	$mongoCollection->createIndex (['parent' => 1, 'child' => 1]);
	var_dump($mongoCollection->getIndexInfo());

	// Content and tags
	echo "Creating indexed in content collection\n";
	$mongoCollection = $db->selectCollection('content');
	$mongoCollection->createIndex (['id_structure' => 1, 'tags' => 1]);
	// Content and alias_id
	$mongoCollection->createIndex (['id_structure' => 1, 'alias_id' => 1]);
	var_dump($mongoCollection->getIndexInfo());


}

if($bMySql) {
	$dbHost = \Acd\conf::$MYSQL_SERVER;
	$dbUser = 'root'; //\Acd\conf::$MYSQL_USER;
	$dbPassword = \Acd\conf::$MYSQL_PASSWORD;
	$db = \Acd\conf::$MYSQL_SCHEMA;

	$mysqli = new \mysqli($dbHost, $dbUser, $dbPassword, $db);
	if ($mysqli->connect_errno) {
		throw new PersistentManagerMySqlException("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error, self::NO_CONNECTION);
	}

	$select = "CREATE TABLE `acd`.`content_tag` (
		`id` INT NOT NULL,
		`tag` VARCHAR(128) NOT NULL,
		PRIMARY KEY (`id`, `tag`))";
	$dbResult = $mysqli->query($select);
	if (!$dbResult) {
		echo "Fail: ".$mysqli->error;
	}

	$mysqli->close();
}