<?php
namespace Acd\View;
//require_once (DIR_BASE.'/app/view/Template.php');
// Output
class ContentEditListContent extends Template {
	public function __construct() {
		$this->__set('resultDesc', '');
		$structure = new \Acd\Model\StructureDo();
		$this->__set('structure', $structure);
	}	

	// INDEX
	public function setId($id) {
		$this->structure->setId($id);
	}
	public function setStructure($structure) {
		$this->__set('structure', $structure);
	}
	public function load() {
		$this->structure->loadFromFile();
	}
	public function setContents($contents) {
		$this->__set('contents', $contents);
	}
	public function setResultDesc($resultDesc) {
		$this->__set('resultDesc', $resultDesc);
	}

	public function render($tpl = '') {
		$tpl = DIR_TEMPLATES.'/ContentEditListContent.tpl';
		return parent::render($tpl);
	}
}