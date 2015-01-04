<?php
namespace Acd\View;
require_once (DIR_BASE.'/app/view/Template.php');
// Output
class ContentAdmin extends \Acd\View\Template {
	protected $actionType;
	public function __construct() {
		$this->__set('resultDesc', '');
	}
	public function setActionType($actionType) {
		$this->actionType = $actionType;
		$this->actionValue = $actionType == 'edit' ? 'save' : 'new'; // Save exists structure or clone (new structure)
	}
	public function getActionType() {
		return $this->actionType;
	}
	public function setResultDesc($resultDesc) {
		$this->__set('resultDesc', $resultDesc);
	}
	public function setStorageTypes($storageTypes) {
		$this->__set('storageTypes', $storageTypes);
	}
	// LOGIN
	public function setLogin($login) {
		$this->__set('login', $login);
	}
	// INDEX
	public function setStructures($structures) {
		$this->__set('structures', $structures);
	}
	public function setTODO($estructuras) {
		$this->__set('estructuras', $estructuras);
	}
	// EDIT
	public function setStructureId($structureId) {
		$this->__set('structureId', $structureId);
	}
	public function setStructureName($structureName) {
		$this->__set('structureName', $structureName);
	}
	public function setStorage($storage){
		$this->__set('storage', $storage);
	}
	public function setFieldTypes($fieldTypes) {
		$this->__set('fieldTypes', $fieldTypes);
	}
	public function setFields($fields){
		$this->__set('fields', $fields);
	}
	
	public function render($tpl = '') {
		switch ($this->getActionType()) {
			case 'login':
				$tpl = DIR_TEMPLATES.'/ContentLogin.tpl';
				break;
			case 'error':
				$tpl = DIR_TEMPLATES.'/ContentError.tpl';
				break;
			case 'index':
				$tpl = DIR_TEMPLATES.'/ContentAdminIndex.tpl';
				break;
			case 'new':
				$tpl = DIR_TEMPLATES.'/ContentAdminNew.tpl';
				break;
			case 'edit':
			case 'clone':
				$tpl = DIR_TEMPLATES.'/ContentAdminEdit.tpl';
				break;
		}
		return parent::render($tpl);
	}
}