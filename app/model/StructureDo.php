<?php
namespace Acd\Model;

include_once (DIR_BASE.'/app/model/FieldsDo.php');

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
		$id = preg_replace('/[^a-z0-9_\-]/', '', strtolower($name));
		if ($id === '') {
			$id = 'id';
		}
		return $id;
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

	public function load($data) {
		$this->setName($data['name']);
		$this->setStorage($data['storage']);
		foreach ($data['fields'] as $dataField) {
			$field = new FieldDo();
			$field->load($dataField);
			$this->addField($field);
		}
	}

	public function 	loadFromFile($path = null) {
		if ($path === null) {
			$path = DIR_DATA.'/structures.json';
		}
		$content = file_get_contents($path);
		$json_a = json_decode($content, true);
		// TODO: controlar errores
		foreach ($json_a as $estructura) {
			foreach ($estructura as $key => $value) {
				if(strval($key) === $this->getId()) {
					$this->setId($key);
					$this->load($value);
				}
			}
		}

		return true;
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