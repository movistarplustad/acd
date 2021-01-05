<?php
namespace Acd\View;
// Output
class ContentEditIndex extends Template {
	public function __construct() {
		$this->__set('resultDesc', '');
		$this->__set('resultCode', '');
	}	

	// INDEX
	public function setStructures($structures) {
		$this->__set('structures', $structures);
	}

	public function render($tpl = '') {
		$tpl = \Acd\conf::$DIR_TEMPLATES.'/ContentEditIndex.tpl';
		return parent::render($tpl);
	}
}