<?php
namespace Acd\Model;

class EnumeratedLoader
{
	private function getManager() {
		switch ($_ENV['ACD_DEFAULT_STORAGE']) {
			case $_ENV[ 'ACD_STORAGE_TYPE_MONGODB_LEGACY']:
				return new PersistentEnumeratedManagerMongoDBLegacy();
				break;
			case $_ENV[ 'ACD_STORAGE_TYPE_MONGODB']:
				return new PersistentEnumeratedManagerMongoDB();
				break;
/*
			case $_ENV['ACD_STORAGE_TYPE_TEXTPLAIN:
				// TODO implement
				return new PersistentEnumeratedManagerTextPlain();
				break;
			case $_ENV['ACD_STORAGE_TYPE_MYSQL:
				// TODO implement
				return new PersistentEnumeratedManagerMySql();
				break;
*/
			default:
				throw new PersistentStorageUnknownInvalidException("Invalid type of persistent storage ".$this->getStorage().".");
				break;
		}
	}

	public function load($query) {
		$dataManager = $this->getManager();
		return $dataManager->load($query);
	}

	public function save($enumeratedDo) {
		$dataManager = $this->getManager();
		$NewEnumeratedDo = $dataManager->save($enumeratedDo);
		return $NewEnumeratedDo;
	}
	public function delete($id) {
		$dataManager = $this->getManager();
		return $dataManager->delete($id);
	}
	// Install
	public function getIndexes() {
		$dataManager = $this->getManager();
		return $dataManager->getIndexes();
	}
	public function createIndexes() {
		$dataManager = $this->getManager();
		return $dataManager->createIndexes();
	}
	public function dropIndexes() {
		$dataManager = $this->getManager();
		return $dataManager->dropIndexes();
	}
}
