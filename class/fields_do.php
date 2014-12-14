<?php
include_once (DIR_BASE.'/class/collection.php');
include_once (DIR_BASE.'/class/field_do.php');

class fields_do extends collection {
	public function __construct() {
		parent::__construct();
	}
	/* Overwrite add method using id of element */
	public function add($element, $key = null) {
		$_id = $element->getId();
		if ($this->hasKey($_id)) {
			return false;
		}
		else {
			$this->elements[$_id] = $element;
			return true;
		}
	}
}