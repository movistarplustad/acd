<?php
namespace Acd\View;
// Output
class Relation extends Template {
	public function __construct() {
		$this->__set('resultDesc', '');
		$this->__set('resultCode', '');
	}
	public function setContentTitle($contentTitle) {
		$this->__set('contentTitle', $contentTitle);
	}
	public function setParentList($parentList) {
		$this->__set('parentList', $parentList);
	}

	public function render($tpl = '') {
		$tpl = \Acd\conf::$DIR_TEMPLATES.'/Relation.tpl';
		return parent::render($tpl);
	}
}