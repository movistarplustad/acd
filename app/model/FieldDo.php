<?php
namespace Acd\Model;

class TypeKeyInvalidException extends \exception {}
class FieldDo
{
	protected $id;
	protected $type;
	protected $name;
	protected $value;
	public function __construct() {
	}
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
	public function setType($type) {
		if (array_key_exists($type, \Acd\conf::$FIELD_TYPES)) {
			$this->type = $type;
		}
		else {
			throw new TypeKeyInvalidException("Invalid type key $type.");
		}
	}
	public function getType() {
		return $this->type;
	}
	public function setName($name) {
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	public function setValue($value) {
		$this->value = $value;
	}
	public function getValue() {
		return $this->value;
	}
	public function load($data) {
		$id = key($data);
		$this->setid($id);
		$this->setType($data[$id]['type']);
		$this->setName($data[$id]['name']);
	}
	public function tokenizeData() {
		return array(
			$this->getId() => array(
				'type' => $this->getType(),
				'name' => $this->getName()
			)
		);
	}

}