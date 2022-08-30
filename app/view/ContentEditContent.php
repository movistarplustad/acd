<?php
namespace Acd\View;
use \Acd\Model\ValueFormater;
// Output
class ContentEditContent extends Template {
	private $structure;
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
		// atention order, 1º set structure & 2º set content
		$this->structure = $structure;
		$this->__set('structure', $structure);
	}
	public function setContent($content) {
		$this->__set('content', $content);
		$this->__set('idContent', ValueFormater::encode($content->getId(), ValueFormater::TYPE_ID, ValueFormater::FORMAT_EDITOR));
		$this->__set('contentTitle', ValueFormater::encode($content->getTitle(), ValueFormater::TYPE_TEXT_SIMPLE, ValueFormater::FORMAT_EDITOR));
		$this->__set('aliasId', ValueFormater::encode($content->getAliasId(), ValueFormater::TYPE_TEXT_SIMPLE, ValueFormater::FORMAT_EDITOR));
		$this->__set('contentTags', ValueFormater::encode($content->getTags(), ValueFormater::TYPE_TAGS, ValueFormater::FORMAT_EDITOR));
		// TODO: add all sticky fields al Field objects
		// Create fieldOutput object and set options for structure and set value for content
		$this->structure->getStickyFields()->get('profile')->setValue($content->getProfile()->getValue());
		$fieldOU = new \Acd\View\Field();
		$fieldOU->setId('profile');
		$fieldOU->setField($this->structure->getStickyFields()->get('profile'));
		$fieldOU->setParent($content);
		$this->__set('profileOU', $fieldOU);
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
