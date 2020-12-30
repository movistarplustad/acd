<?php
namespace Acd\View;
use \Acd\Model\ValueFormater;
// Output
class Field extends Template {
	const EXCEPTION_TEMPLATE_FORM_TYPE = 1;

	private $field;
	public function __construct() {
		$this->__set('resultDesc', '');
		$this->__set('resultCode', '');
	}

	public function setId($id) {
		$this->__set('id', $id);
	}
	public function setField($field) {
		$this->field = $field;
		$this->__set('fieldId', $field->getId());
		$this->__set('fieldName', $field->getName());

		$this->__set('fieldValue', ValueFormater::encode($field->getValue(), $field->getType(), ValueFormater::FORMAT_EDITOR)); // Antes $field->getValue());
		//$ref = $field->getRef();
		$ref = $field->getValue();
		if($field->getType() === 'content' && $ref) {
			$id = $field->getRef()['ref'];
		}

		//$ref = is_string($ref) ? $ref : '';
		$this->__set('fieldRef', $ref);
		$this->__set('fieldStructureRef', $field->getStructureRef());
		$this->__set('fieldOptions', $field->getOptions());

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
		$this->__set('idParent', ValueFormater::encode($parent->getId(), ValueFormater::TYPE_ID, ValueFormater::FORMAT_EDITOR));
		$this->__set('bNew', !$parent->getId());
	}
	/* Quarantine
	public function newContent($bnewContent) {
		$this->__set('bNew', true);
	}
	*/
	public function setResultDesc($description, $code) {
		$this->__set('resultDesc', $description);
		$this->__set('resultCode', $code);
	}
	private function getFormTemplate() {
		$type = $this->field->getType();
		if (array_key_exists($type, $this->field->getAvailableTypes())) {
			$tpl = \Acd\conf::$DIR_TEMPLATES."/field/$type.tpl";
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
