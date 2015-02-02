<?php
namespace Acd\Model;

class FieldsDo extends Collection
{
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
	public function setValue($key, $value) {
		if ($this->hasKey($key)) {
			$this->get($key)->setValue($value);
		}
		else {
			$element = new FieldDo();
			$element->setId($key);
			$element->setName($key);
			$element->setValue($value);
			$this->add($element, $key);
		}
	}
	public function getValue($key) {
		return $this->get($key)->getValue();

	}
}