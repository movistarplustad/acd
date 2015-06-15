<?php
namespace Acd\Model;

class PersistentEnumeratedManagerMongoDB implements iPersistentEnumeratedManager
{
	public function initialize() {
		$mongo = new \MongoClient(\Acd\conf::$MONGODB_SERVER);
		$this->db = $mongo->acd;
		//$db->createCollection('content', false);
	}
	public function isInitialized() {
		return isset($this->db);
	}
	public function load($query) {
		if (!$this->isInitialized()) {
			$this->initialize();
		}
		$mongoCollection = $this->db->selectCollection('enumerated');
		try {
			$id = $query->getCondition('id');
			$id = null;
			if ($id) {
				$documentFound = $mongoCollection->findOne(array("_id" => $id));
				$documentFound = $this->normalizeDocument($documentFound);
				$enumeratedFound = new EnumeratedDo();
				$enumeratedFound->load($documentFound);
			}
			else {
				// All
				$cursor = $mongoCollection->find(array(), array('_id' => true));
				foreach ($cursor as $documentFound) {
					d($documentFound);
				}
				$documentFound = $this->normalizeDocument($documentFound);
				$enumeratedFound = new EnumeratedDo();

			}
			return $enumeratedFound;
		}
		catch( \Exception $e ) {
			return null;
		}
	}
	public function save($enumeratedDo) {
		if (!$this->isInitialized()) {
			$this->initialize();
		}
		$mongoCollection = $this->db->selectCollection('enumerated');
		$insert = $enumeratedDo->tokenizeData();

		$id = $enumeratedDo->getId();
		unset ($insert['id']);
		$insert['save_ts'] = time(); // Log, timestamp for last save / update operation
		$mongoCollection->update(array('_id' => $id), $insert, array('upsert' => true));

		return $enumeratedDo;
	}

	public function normalizeDocument($document) {
		$document['id'] = (string) $document['_id'];
		unset($document['_id']);
		return $document;
	}
}