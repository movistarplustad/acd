<?php
namespace Acd\View;
// Output
class ContentEditContent extends Template {
	public function __construct() {
		$this->__set('resultDesc', '');
		$this->__set('resultCode', '');
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
	public function setContent($content, $profiles) {
		$this->__set('content', $content);
		$this->__set('contentTitle', \Acd\Model\ValueFormater::encode($content->getTitle(), \Acd\Model\ValueFormater::TYPE_TEXT_SIMPLE, \Acd\Model\ValueFormater::FORMAT_EDITOR));
		$this->__set('aliasId', \Acd\Model\ValueFormater::encode($content->getAliasId(), \Acd\Model\ValueFormater::TYPE_TEXT_SIMPLE, \Acd\Model\ValueFormater::FORMAT_EDITOR));
		$this->__set('contentTags', \Acd\Model\ValueFormater::encode($content->getTags(), \Acd\Model\ValueFormater::TYPE_TAGS, \Acd\Model\ValueFormater::FORMAT_EDITOR));
		//$this->__set('profiles', \Acd\Model\ValueFormater::encode($content->getTags(), \Acd\Model\ValueFormater::TYPE_TAGS, \Acd\Model\ValueFormater::FORMAT_EDITOR));
		$this->__set('profiles', $profiles);
	}
	public function setUserRol($rol) {
		$this->__set('tagsReadonly', $rol === \Acd\conf::$ROL_DEVELOPER ? '' : ' readonly="readonly"');
	}
	public function newContent($bnewContent) {
		$this->__set('bNew', true);
	}
	public function setResultDesc($description, $code) {
		$this->__set('resultDesc', $description);
		$this->__set('resultCode', $code);
	}
	public function setSummary($jsonSummary) {
		$this->__set('jsonSummary', $jsonSummary);
	}
	public function render($tpl = '') {
		$tpl = \Acd\conf::$DIR_TEMPLATES.'/ContentEditContent.tpl';
		return parent::render($tpl);
	}
}