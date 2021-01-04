<?php
namespace Acd\Model;

class PersistentEnumeratedManagerMongoDB implements iPersistentEnumeratedManager
{
	private $db;
	public function initialize() {
//		$mongo = new \MongoClient(\Acd\conf::$MONGODB_SERVER);
//		$this->db = $mongo->selectDB(\Acd\conf::$MONGODB_DB);
    //TODO: Ver cóm pasarle el servidor, porque si es '' no funciona.
    $mongo = new \MongoDB\Client(\Acd\conf::$MONGODB_SERVER);
    $this->db = $mongo->selectDatabase(\Acd\conf::$MONGODB_DB);
	}
	public function isInitialized() {
		return isset($this->db);
	}
	public function load($query) {
		if (!$this->isInitialized()) {
			$this->initialize();
		}
		if($query->getCondition('id')) {
			return $this->loadById($query);
		}
		else {
			return $this->loadAll($query);
		}
	}
	private function loadById($query) {
		$mongoCollection = $this->db->enumerated;
		try {
			$id = $query->getCondition('id');
			$documentFound = $mongoCollection->findOne(["_id" => $id]);
			$documentFound = $this->normalizeDocument($documentFound);
			$enumeratedFound = new EnumeratedDo();
			$enumeratedFound->load($documentFound);
			return $enumeratedFound;
		}
		catch( \Exception $e ) {
			return null;
		}
	}
	private function loadAll($query) {
		$mongoCollection = $this->db->enumerated;
		try {
			$cursor = $mongoCollection->find([], ['projection' => ['_id' => true], 'sort' =>['_id' => 1]]);
//			$cursor->sort(array( '_id' => 1));
			$enumeratedCollectionFound = new Collection();
			foreach ($cursor as $documentFound) {
				$enumeratedCollectionFound->add(array('id' => $documentFound['_id'])); // Now id and name are equeals
			}

			return $enumeratedCollectionFound;
		}
		catch( \Exception $e ) {
			return null;
		}
	}
	public function save($enumeratedDo) {
		if (!$this->isInitialized()) {
			$this->initialize();
		}
		$mongoCollection = $this->db->enumerated;
		$insert = $enumeratedDo->tokenizeData();

		$id = $enumeratedDo->getId();
		unset ($insert['id']);
		$insert['save_ts'] = time(); // Log, timestamp for last save / update operation
		$mongoCollection->updateOne(['_id' => $id], ['$set' => $insert], ['upsert' => true]);

		return $enumeratedDo;
	}
	public function delete($id) {
		if (!$this->isInitialized()) {
			$this->initialize();
		}
		$mongoCollection = $this->db->enumerated;
		return $mongoCollection->deleteOne(['_id' => $id]);
	}
	public function getIndexes() {
		// Currently no indexes are required
	}
	public function createIndexes() {
		// Currently no indexes are required
	}
	public function dropIndexes() {
		// Currently no indexes are required
	}
	public function normalizeDocument($document) {
		$document['id'] = (string) $document['_id'];
		unset($document['_id']);
		return $document;
	}
}