<?php
namespace Acd\Model;

class PersistentEnumeratedManagerMongoDB implements iPersistentEnumeratedManager
{
	public function initialize($structureDo) {
		$mongo = new \MongoClient(\Acd\conf::$MONGODB_SERVER);
		$this->db = $mongo->acd;
		//$db->createCollection('content', false);
	}
	public function isInitialized($structureDo) {
		return isset($this->db);
	}
	public function load($id) {
		if (!$this->isInitialized()) {
			$this->initialize();
		}
		$mongoCollection = $this->db->selectCollection('enumerated');
	}
}