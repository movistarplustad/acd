<?php
namespace Acd\Model;

class PersistentStructureManagerMongoDBException extends \exception {} // TODO Unificar
class PersistentStructureManagerMongoDB implements iPersistentStructureManager
{
	private $mongo;
	private $db;
	public function initialize() {
		try {
			$this->mongo = new \MongoClient();
			$this->db = $this->mongo->acd;
			return true;
		}
		catch (MongoConnectionException $e) {
			throw new PersistentStructureManagerMongoDBException("Failed to connect to MongoDB", self::NO_CONNECTION);
		}
	}
	public function isInitialized() {
		try {
			$this->initialize();
			return true;
		}
		catch ( PersistentStructureManagerMongoDBException $e ) {
			return false;
		}
	}
	public function loadAll() {
		if (!$this->isInitialized()) {
			$this->initialize();
		}
		$mongoCollection = $this->db->selectCollection('structure');
		$byStructureQuery = array();

		$cursor = $mongoCollection->find($byStructureQuery);
		$result = [];
		foreach ($cursor as $documentFound) {
			$id = $documentFound['_id'];
			unset($documentFound['_id']);
			$result[$id] = array($documentFound);
		}
		//+d($result);
		return $result;
	}
	public function save($structuresDo) {
		if (!$this->isInitialized()) {
			$this->initialize();
		}
		//db.structure.update({'_id' : 'chat_tienda'},{'_id' : 'chat_tienda',       "name": "Chat de tienda online Mongo", "storage" : "mongodb", 'fieds' : []}, {upsert :true})
		dd("TODO");
		$path = \ACD\conf::$DATA_PATH;
		/* Construct the json */
		$data = $structuresDo->tokenizeData();
		$tempPath = DIR_DATA.'/temp.json';
		$somecontent = json_encode($data);

		if (!$handle = fopen($tempPath, 'a')) {
			 echo "Cannot open file ($tempPath)";
			 exit;
		}

		// Write $somecontent to our opened file.
		if (fwrite($handle, $somecontent) === FALSE) {
			throw new PersistentStructureManagerTextPlainException("Cannot write to file ($tempPath)", self::SAVE_FAILED);
			exit;
		}
		fclose($handle);
		rename($tempPath, $path);
	}
}
/*
db.structure.update({'_id' : 'chat_tienda'},{'_id' : 'chat_tienda',       "name": "Chat de tienda online Mongo", "storage" : "mongodb", 
	'fields' : 
	[
        {
          "Títulofer": {
            "type": "text_simple",
            "name": "Títulofer",
            "value": null,
            "ref": null,
            "id_structure": null
          }
        },
        {
          "Otros": {
            "type": "content",
            "name": "Otros",
            "value": null,
            "ref": {
              "ref": "",
              "id_structure": ""
            },
            "id_structure": null
          }
        },
        {
          "Lista de otros": {
            "type": "collection",
            "name": "Lista de otros",
            "value": null,
            "ref": [],
            "id_structure": null
          }
        }
      ]
    }, {upsert :true})
    */