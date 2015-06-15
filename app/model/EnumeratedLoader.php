<?php
namespace Acd\Model;

class EnumeratedLoader
{
	private function getManager() {
		switch (\Acd\conf::$DEFAULT_STORAGE) {
			case \Acd\conf::$STORAGE_TYPE_MONGODB:
				return new PersistentEnumeratedManagerMongoDB();
				break;
/*
			case \Acd\conf::$STORAGE_TYPE_TEXTPLAIN:
				// TODO implement
				return new PersistentEnumeratedManagerTextPlain();
				break;
			case \Acd\conf::$STORAGE_TYPE_MYSQL:
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
		d($query, $query->getCondition('id'),$query->getCondition('pun'), $query->getType());
		$dataManager = $this->getManager();
		return $dataManager->load($query);
	}

	public function save($enumeratedDo) {
		$dataManager = $this->getManager();
		$NewEnumeratedDo = $dataManager->save($enumeratedDo);
		return $NewEnumeratedDo;
	}

}