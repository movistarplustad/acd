<?php
namespace Acd\View;
// Output
class ContentEditSearch extends Template {
	public function __construct() {
		$this->__set('resultCode', '');
		$this->__set('resultDesc', '');
		$this->__set('resultSearchContents', null);
	}	

	public function setId($id) {
		$this->__set('id', $id);
	}
	public function setType($type) {
		$this->__set('type', $type);
	}
	public function setIdField($idField) {
		$this->__set('idField', $idField);
	}
	public function setPositionInField($positionInField) {
		$this->__set('positionInField', $positionInField);
	}

	public function setTitleSeach($titleSearch) {
		$this->__set('titleSearch', $titleSearch);
	}
	public function setStructureTypeSeach($idStructureTypeSearch) {
		$this->__set('idStructureTypeSearch', $idStructureTypeSearch);
	}

	public function setStructures($structures) {
		$this->__set('structures', $structures);
	}

	public function setResultSearch($resultSearchContents) {
		$this->__set('resultSearchContents', $resultSearchContents);	
	}

	public function render($tpl = '') {
		$tpl = \Acd\conf::$DIR_TEMPLATES.'/ContentEditSearch.tpl';
		return parent::render($tpl);
	}
}