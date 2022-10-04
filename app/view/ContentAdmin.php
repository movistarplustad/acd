<?php
namespace Acd\View;

use Acd\View\Template;
// Output
class ContentAdmin extends Template
{
	protected $actionType;
	public function __construct() {
		$this->__set('resultDesc', '');
		$this->__set('resultCode', '');
	}
	public function setActionType($actionType) {
		$this->actionType = $actionType;
		$this->actionValue = $actionType == 'edit' ? 'save' : 'new'; // Save exists structure or clone (new structure)
	}
	public function getActionType() {
		return $this->actionType;
	}
	public function setResultDesc($description, $code) {
		$this->__set('resultDesc', $description);
		$this->__set('resultCode', $code);
	}
	public function setStorageTypes($storageTypes) {
		$this->__set('storageTypes', $storageTypes);
	}
	// LOGIN
	public function setLogin($login) {
		$this->__set('login', $login);
	}
	public function setPostLogin($urlPostLogin) {
		$this->__set('urlPostLogin', $urlPostLogin);
	}
	public function setRemember($bRemember) {
		$this->__set('bRemember', $bRemember);
	}
	// INDEX
	public function setStructures($structures) {
		$this->__set('structures', $structures);
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
	public function setEnumeratedList($enumeratedList){
		$this->__set('enumeratedList', $enumeratedList);
	}

	public function render($tpl = '') {
		$templatePath = $_ENV['ACD_DIR_TEMPLATES'];
		switch ($this->getActionType()) {
			case 'login':
				$tpl = '/ContentLogin.tpl';
				break;
			case 'error':
				$tpl = '/ContentError.tpl';
				break;
			case 'index':
				$tpl = '/ContentAdminIndex.tpl';
				break;
			case 'new':
				$tpl = '/ContentAdminNew.tpl';
				break;
			case 'edit':
			case 'clone':
				$tpl = '/ContentAdminEdit.tpl';
				break;
		}
		return parent::render($templatePath . $tpl);
	}
}
