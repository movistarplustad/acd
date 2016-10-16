<?php
namespace Acd\Model;

class PersistentUserManagerMongoDB implements iPersistentUserManager
{
	private $db;
	public function initialize() {
		$mongo = new \MongoClient(\Acd\conf::$MONGODB_SERVER);
		$this->db = $mongo->selectDB(\Acd\conf::$MONGODB_DB);
	}
	public function isInitialized() {
		return isset($this->db);
	}
	public function load($query) {
		if (!$this->isInitialized()) {
			$this->initialize();
		};
		if($query->getCondition('id')) {
			return $this->loadById($query);
		}
		else {
			return $this->loadAll($query);
		}
	}
	private function loadById($query) {
		$mongoCollection = $this->db->selectCollection('user');
		try {
			$id = $query->getCondition('id');
			$documentFound = $mongoCollection->findOne(array("_id" => $id));
			$documentFound = $this->normalizeDocument($documentFound);
			$userFound = new UserDo();
			$userFound->load($documentFound);

			return $userFound;
		}
		catch( \Exception $e ) {
			return null;
		}
	}
	private function loadAll($query) {
		$mongoCollection = $this->db->selectCollection('user');
		try {
			$cursor = $mongoCollection->find(array());
			$cursor->sort(array( '_id' => 1));
			$userCollectionFound = new Collection();
			foreach ($cursor as $documentFound) {
				$documentFound = $this->normalizeDocument($documentFound);
				$userFound = new UserDo();
				$userFound->load($documentFound);
				$userCollectionFound->add($userFound);
			}
			return $userCollectionFound;
		}
		catch( \Exception $e ) {
			return null;
		}
	}
	public function save($userDo) {
		if (!$this->isInitialized()) {
			$this->initialize();
		}
		$mongoCollection = $this->db->selectCollection('user');
		$insert = $userDo->tokenizeData();

		$id = $userDo->getId();
		unset ($insert['id']);
		$insert['save_ts'] = time(); // Log, timestamp for last save / update operation
		$mongoCollection->update(array('_id' => $id), $insert, array('upsert' => true));

		return $userDo;
	}
	public function delete($id) {
		if (!$this->isInitialized()) {
			$this->initialize();
		}
		$mongoCollection = $this->db->selectCollection('user');
		return $mongoCollection->remove(array('_id' => $id));
	}
	public function normalizeDocument($document) {
		$document['id'] = (string) $document['_id'];
		unset($document['_id']);
		return $document;
	}
}
