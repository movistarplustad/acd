<?php
namespace Acd\View;
// Output
class EnumeratedList extends Template {
	public function __construct() {
		$this->__set('resultDesc', '');
		$this->__set('resultCode', '');
	}	

	// INDEX
	public function setkk($resultDesc) {
		$this->__set('resultDesc', $resultDesc);
	}

	public function render($tpl = '') {
		$tpl = \Acd\conf::$DIR_TEMPLATES.'/EnumeratedList.tpl';
		return parent::render($tpl);
	}
}