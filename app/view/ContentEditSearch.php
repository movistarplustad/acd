<?php
namespace Acd\View;
// Output
class ContentEditSearch extends Template {
	public function __construct() {
		$this->__set('resultDesc', '');
	}	

	public function setId($id) {
		$this->__set('id', $id);
	}
	public function setType($type) {
		$this->__set('type', $type);
	}
	public function setStructures($structures) {
		$this->__set('structures', $structures);
	}

	public function render($tpl = '') {
		$tpl = DIR_TEMPLATES.'/ContentEditSearch.tpl';
		return parent::render($tpl);
	}
}