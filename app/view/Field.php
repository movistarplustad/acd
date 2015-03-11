<?php
namespace Acd\View;
//require_once (DIR_BASE.'/app/view/Template.php');
// Output
class Field extends Template {
	const EXCEPTION_TEMPLATE_FORM_TYPE = 1;

	private $field;
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
	public function setId($id) {
		$this->__set('id', $id);
	}
	public function setField($field) {
		$this->field = $field;
		$this->__set('fieldName', $field->getName());
		$this->__set('fieldValue', $field->getValue());
		$this->__set('fieldRef', $field->getRef());
		$this->__set('fieldStructureRef', $field->getStructureRef());

		/*
		switch ($field->getType()) {
			case 'content':
			d($field->getValue());
				$this->__set('fieldValue', $field->getValue());
				break;
			default:
				$this->__set('fieldValue', $field->getValue());
				break;
		}
		*/
	}
	public function setParent($parent) {
		$this->__set('idStructureParent', $parent->getIdStructure());
		$this->__set('idParent', $parent->getId());
	}
	public function newContent($bnewContent) {
		$this->__set('bNew', true);
	}
	public function setResultDesc($resultDesc) {
		$this->__set('resultDesc', $resultDesc);
	}
	private function getFormTemplate() {
		$type = $this->field->getType();
		if (array_key_exists($type, $this->field->getAvailableTypes())) {
			$tpl = DIR_TEMPLATES."/field/$type.tpl";
			//d($tpl);
			return $tpl;
		}
		else {
			throw new Exception("Template form not defined [$type]", EXCEPTION_TEMPLATE_FORM_TYPE);
			
		}
	}
	public function render($tpl = '') {
		$tpl = $this->getFormTemplate();
		return parent::render($tpl);
	}
}