<?php
namespace Acd\View;
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
	public function newContent($bnewContent) {
		$this->__set('bNew', true);
	}
	public function setResultDesc($resultDesc) {
		$this->__set('resultDesc', $resultDesc);
	}
	public function render($tpl = '') {
		$tpl = \Acd\conf::$DIR_TEMPLATES.'/ContentEditContent.tpl';
		return parent::render($tpl);
	}
}