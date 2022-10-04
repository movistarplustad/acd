<?php
namespace Acd\View;

use Acd\Model\StructureDo;
// Output
class ContentEditListContent extends Template {
	public function __construct() {
		$this->__set('resultDesc', '');
		$this->__set('resultCode', '');
		$structure = new StructureDo();
		$this->__set('structure', $structure);
	}	

	// INDEX
	public function setId($id) {
		$this->structure->setId($id);
	}
	public function setStructure($structure) {
		$this->__set('structure', $structure);
	}
	public function setTitleSeach($titleSearch) {
		$this->__set('titleSearch', $titleSearch);
	}
	public function load() {
		$this->structure->loadFromFile();
	}
	public function setContents($contents) {
		$this->__set('contents', $contents);
	}
	public function setResultDesc($description, $code) {
		$this->__set('resultDesc', $description);
		$this->__set('resultCode', $code);
	}

	public function render($tpl = '') {
		$tpl = $_ENV[ 'ACD_DIR_TEMPLATES'].'/ContentEditListContent.tpl';
		return parent::render($tpl);
	}
}