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
}