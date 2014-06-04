<?php
include_once (DIR_BASE.'/class/collection.php');

class TypeKeyInvalidException extends exception {}
class field_do {
	protected $type;
	protected $name;
	public function __construct() {
	}
	public function setType($type) {
		if (array_key_exists($type, conf::$FIELD_TYPES)) {
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

}