<?php
namespace Acd\View;
// Output
class EnumeratedDetail extends Template {
	public function __construct() {
		$this->__set('resultDesc', '');
		$this->__set('resultCode', '');
	}	

	// INDEX
	public function setEnumeratedElement($enumeratedElement) {
		$this->__set('enumeratedElement', $enumeratedElement);
	}

	public function render($tpl = '') {
		$tpl = $_ENV[ 'ACD_DIR_TEMPLATES'].'/EnumeratedDetail.tpl';
		return parent::render($tpl);
	}
}