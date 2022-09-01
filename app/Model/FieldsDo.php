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
	private function newField($key) {
			$element = new FieldDo();
			$element->setId($key);
			$element->setName($key);
			return $element;
	}
	public function setValue($key, $value) {
		if ($this->hasKey($key)) {
			$this->get($key)->setValue($value);
		}
		else {
			$element = $this->newField($key);
			$element->setValue($value);
			$this->add($element, $key);
		}
	}
	public function getValue($key) {
		return $this->get($key)->getValue();
	}
	public function getType($key) {
		return $this->get($key)->getType();
	}
	public function setRef($key, $value) {
		if ($this->hasKey($key)) {
			$this->get($key)->setRef($value);
		}
		else {
			$element = $this->newField($key);
			$element->setRef($value);
			$this->add($element, $key);
		}
	}
	public function setStructureRef($key, $value) {
		if ($this->hasKey($key)) {
			$this->get($key)->setType($value);
		}
		else {
			$element = $this->newField($key);
			$element->setStructureRef($value);
			$this->add($element, $key);
		}
	}
}