<?php
namespace Acd\Model;

include_once (DIR_BASE.'/app/model/Collection.php');
include_once (DIR_BASE.'/app/model/FieldDo.php');

class FieldsDo extends Collection {
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