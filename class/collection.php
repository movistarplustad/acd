<?php
class collection {
	static $elements;

	public function __construct() {
		$this->elements = array(); /* Create empty structure */
	}

	public function add($element) {
		//var_dump($element);
		$this->elements[] = $element;
	}

	public function get($id) {
		// TODO improve speed width index
		$elementMatched = null;
		foreach ($this->elements as $key => $element) {
			if ($element->getId() === $id) {
				$elementMatched = $element;
			}
		}
		//var_dump($this->elements);
		return $elementMatched;
	}
}