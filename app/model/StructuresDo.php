<?php
namespace Acd\Model;

class StructuresDo extends Collection
{
	private function getManager() {
		switch (\Acd\conf::$DEFAULT_STORAGE) {
			case \Acd\conf::$STORAGE_TYPE_TEXTPLAIN:
				//echo "tipo texto";
				return new PersistentStructureManagerTextPlain();
				break;
			case \Acd\conf::$STORAGE_TYPE_MONGODB_LEGACY:
				//echo "tipo mongo";
				return new PersistentStructureManagerMongoDBLegacy();
				break;
			case \Acd\conf::$STORAGE_TYPE_MONGODB:
				//echo "tipo mongo";
				return new PersistentStructureManagerMongoDB();
				break;
			case \Acd\conf::$STORAGE_TYPE_MYSQL:
				//echo "tipo mysql";
				return new PersistentStructureManagerMySql();
				break;
			default:
				throw new PersistentStorageUnknownInvalidException("Invalid type of persistent storage ".$this->getStorage().".");
				break;
		}
	}

	public function loadFromFile($options = []) {
		$dataManager = $this->getManager();
		$json_a = $dataManager->loadAll();
		//+d($json_a);
		// TODO: controlar errores
		foreach ($json_a as $estructura) {
			foreach ($estructura as $key => $value) {
				$structure = new StructureDo();
				$structure->setId($key);
				$structure->load($value);

				$this->add($structure, $key);
			}
		}

		return true;
	}

	// Create structures each id of array, only id no data
	public function populateFromArray($aId) {
		foreach ($aId as $id) {
			$structure = new StructureDo();
			$structure->setId($id);
			$this->add($structure, $id);
		}
	}

	// Fill data each structure
	public function hydratate() {
		foreach ($this->elements as $key => $value) {
			$value->loadFromFile();
		}
	}

	public function save($path = null) {
		$dataManager = $this->getManager();
		$dataManager->save($this);
	}

	public function tokenizeData() {
		$aIds = $this->getAllStructures();
		$aData = array();
		foreach ($aIds as $id) {
			$estructura = $this->get($id);
			$aData[] = $estructura->tokenizeData();
		}

		return $aData;
	}

	/* Return array of ids of all structures */
	/* TODO mover a Collection */
	public function getAllStructures() {
		$structuresList = array();
		foreach ($this->elements as $key => $value) {
			$structuresList[] = $value->getId();
		}

		return $structuresList;
	}
}