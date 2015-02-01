<?php
namespace Acd\View;
//require_once (DIR_BASE.'/app/view/Template.php');
// Output
class ContentEditContent extends Template {
	public function __construct() {
		$this->__set('resultDesc', '');
	}	

	// INDEX
	/*
	public function setId($id) {
		$this->structure->setId($id);
	}
	public function load() {
		$this->structure->loadFromFile();
	}
	*/
	public function setStructure($structure) {
		$this->__set('structure', $structure);
	}
	public function setContent($content) {
		$this->__set('content', $content);
	}

	public function render($tpl = '') {
		$tpl = DIR_TEMPLATES.'/ContentEditContent.tpl';
		return parent::render($tpl);
	}
}