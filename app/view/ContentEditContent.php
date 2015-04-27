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
		$this->__set('contentTitle', \Acd\Model\ValueFormater::encode($content->getTitle(), \Acd\Model\ValueFormater::TYPE_TEXT_SIMPLE, \Acd\Model\ValueFormater::FORMAT_EDITOR));
		$this->__set('contentTags', \Acd\Model\ValueFormater::encode($content->getTags(), \Acd\Model\ValueFormater::TYPE_TAGS, \Acd\Model\ValueFormater::FORMAT_EDITOR));
	}
	public function setUserRol($rol) {
		$this->__set('userRol', $rol === \Acd\conf::$ROL_DEVELOPER ? '' : ' readonly="readonly"');
	}
	public function newContent($bnewContent) {
		$this->__set('bNew', true);
	}
	public function setResultDesc($resultDesc) {
		$this->__set('resultDesc', $resultDesc);
	}
	public function setSummary($jsonSummary) {
		$this->__set('jsonSummary', $jsonSummary);
	}
	public function render($tpl = '') {
		$tpl = \Acd\conf::$DIR_TEMPLATES.'/ContentEditContent.tpl';
		return parent::render($tpl);
	}
}