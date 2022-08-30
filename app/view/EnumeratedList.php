<?php
namespace Acd\View;
// Output
class EnumeratedList extends Template {
	public function __construct() {
	}	

	public function setEnumeratedList($enumeratedList) {
		$this->__set('enumeratedList', $enumeratedList);
	}

	public function render($tpl = '') {
		$tpl = \Acd\conf::$DIR_TEMPLATES.'/EnumeratedList.tpl';
		return parent::render($tpl);
	}
}