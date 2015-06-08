<?php
namespace Acd\Model;

class StorageKeyInvalidException extends \exception {}
class StructureDo
{
	protected $id;
	protected $name; /* name, storage */
	protected $storage;
	protected $fields;
	public function __construct() {
		$this->id = null;
		$this->fields = new FieldsDo();
	}
	/* Setters and getters attributes */
	public function setId($id) {
		$this->id = (string)$id;
	}
	public function getId() {
		return $this->id;
	}
	public function generateId($name) {
		// Temporarily disabled. I think it makes no sense
		return $name;
		/*
		$id = preg_replace('/[^a-z0-9_\-]/', '', strtolower($name));
		if ($id === '') {
			$id = 'id';
		}
		return $id;
		*/
	}
	public function setName($name) {
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	public function setStorage($storage) {
		if (array_key_exists($storage, \Acd\conf::$STORAGE_TYPES)) {
			$this->storage = $storage;
		}
		else {
			throw new StorageKeyInvalidException("Invalid storage key $storage.");
		}
	}
	public function getStorage() {
		return $this->storage;
	}
	public function addField($field) {
		$this->getFields()->add($field);
	}
	public function getFields() {
		return $this->fields;
	}

	public function getEnumeratedIds() {
		// In metadata of contents are "PROFILE" enumerated element and the fields can add their enumerated lists
		$aEnumeratedIds = ['PROFILE'];
		foreach ($this->getFields() as $field) {
			if ($field->getType() === fieldDO::TYPE_LIST_MULTIPLE) {
				d("TODO, repasar que funcione", $field->getType(), fieldDO::TYPE_LIST_MULTIPLE);
				$aEnumeratedIds[] = $field->getOptions()->getId();
			}
		}

		return $aEnumeratedIds;
	}

	// TODO Repetido en StructuresDO
	private function getManager() {
		switch (\Acd\conf::$DEFAULT_STORAGE) {
			case \Acd\conf::$STORAGE_TYPE_TEXTPLAIN:
				//echo "tipo texto";
				return new PersistentStructureManagerTextPlain();
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

	public function load($data) {
		$this->setName($data['name']);
		$this->setStorage($data['storage']);
		foreach ($data['fields'] as $dataField) {
			$field = new FieldDo();
			$field->load($dataField);
			$this->addField($field);
		}
	}

	/* TODO: Bad name loadFromFile, change for loadFromPersistentStorage */
	public function loadFromFile($options = []) {
		$bLoadEnumerated = isset($options['loadEnumerated']) && $options['loadEnumerated'] === true;
		$dataManager = $this->getManager();
		$document = $dataManager->loadById($this->getId());
		$bLoaded = false;
		if ($document) {
			$this->load($document);
			if($bLoadEnumerated) {
				foreach ($this->getEnumeratedIds() as $idEnumeratedGroup) {
					# code...
					$enumeratedDo = $dataManager->loadEnumerated($idEnumeratedGroup);
					d("asignarlo a los campos", $enumeratedDo);
				}
			}
			$bLoaded = true;
		}

		return $bLoaded;
	}

	/* Serializes */
	public function setFromJson($jsonData) {
		// TODO
		var_dump($jsonData);
	}
	public function tokenizeData() {
		$aFieldsData = array();
		$aIdFields = $this->getFields()->keys();

		foreach ($aIdFields as $id) {
			$field = $this->getFields()->get($id);
			$aFieldsData[] = $field->tokenizeData();
		}

		return array(
			$this->getId() => array(
				'name' => $this->getName(),
				'storage' => $this->getStorage(),
				'fields' => $aFieldsData
			)
		);
	}
}