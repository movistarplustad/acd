<?php
class collection {
	static $elements;

	public function __construct() {
		$this->elements = array(); /* Create empty structure */
	}

	private function getInternalIndex($id) {
		// TODO improve speed width associative index?
		foreach ($this->elements as $key => $element) {
			if ($element->getId() === $id) {
				return $key;
			}
		}

		return null;
	}

	public function add($element) {
		//var_dump($element);
		$this->elements[] = $element;
	}

	public function remove($id) {
		$_id = $this->getInternalIndex($id);
		if ($_id === null) {
			return false;
		}
		else {
			unset($this->elements[$_id]);
			return true;
		}
	}

	public function get($id) {
		$_id = $this->getInternalIndex($id);
		if ($_id === null) {
			return null;
		}
		else {
			
			return $this->elements[$_id];
		}
	}

	public function set($id, $element) {
		$_id = $this->getInternalIndex($id);
		$this->elements[$_id] = $element;
	}
}