<?php
namespace Acd\Model;

class StorageKeyInvalidException extends \exception {}
class StructureDo
{
	protected $id;
	protected $name; /* name, storage */
	protected $storage;
	protected $stickyFields; /* Fixed fields of content, meta information as title, tags, validity time... */
	protected $fields;
	public function __construct() {
		$this->id = null;
		$this->fields = new FieldsDo();
		// $this->stickyFields initialized in getStickyFields method for performance
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
	public function getStickyFields() {
		if (!isset($this->stickyFields)) {
			$this->stickyFields = new FieldsDo();
			$profile = new FieldDo();
			$profile->setId('profile');
			$profile->setName('Profile');
			$profile->setType(fieldDo::TYPE_LIST_MULTIPLE);
			$profile->getOptions()->setId('PROFILE');

			$this->stickyFields->add($profile);
		}

		return $this->stickyFields;
	}

	public function getEnumeratedIds() {
		// In metadata of contents are "PROFILE" enumerated element and the fields can add their enumerated lists
		$aEnumeratedIds = [];
		$multiple = new \AppendIterator();
		$multiple->append($this->getStickyFields()->getIterator());
		$multiple->append( $this->getFields()->getIterator());
		foreach ($multiple as $field) {
			if ($field->getType() === fieldDo::TYPE_LIST_MULTIPLE) {
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
				$this->assignEnumeratedOptionsToFieds($this->getEnumeratedIds());
			}
			$bLoaded = true;
		}

		return $bLoaded;
	}

	private function assignEnumeratedOptionsToFieds($aEnumeratedIds) {
		$dataManager = $this->getManager();
		$query = new Query();
		$query->setType('id');
		foreach ($aEnumeratedIds as $idEnumeratedGroup) {
			$query->setCondition(['id' => $this->getId()]);
			$enumeratedDo = $dataManager->loadEnumerated($query);

			$multiple = new \AppendIterator();
			$multiple->append($this->getStickyFields()->getIterator());
			$multiple->append( $this->getFields()->getIterator());
			foreach ($multiple as $field) {
				if($field->getOptions()->getId() === $idEnumeratedGroup) {
					$field->setOptions($enumeratedDo);
				}
			}
		}

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