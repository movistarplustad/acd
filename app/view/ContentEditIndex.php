<?php
namespace Acd\View;
//require_once (DIR_BASE.'/app/view/Template.php');
// Output
class ContentEditIndex extends Template {
	public function __construct() {
		$this->__set('resultDesc', '');
	}	

	// INDEX
	public function setStructures($structures) {
		$this->__set('structures', $structures);
	}

	public function render($tpl = '') {
		$tpl = DIR_TEMPLATES.'/ContentEditIndex.tpl';
		return parent::render($tpl);
	}
}