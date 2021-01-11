<?php
namespace Acd\View;
// Output
class AliasId extends Template {
	public function __construct() {
		$this->__set('resultDesc', '');
		$this->__set('resultCode', '');
	}
	public function setContentTitle($contentTitle) {
		$this->__set('contentTitle', $contentTitle);
	}
	public function setAliasId($aliasId) {
		$this->__set('aliasId', $aliasId);
	}
	public function setMatchList($matchList) {
		$this->__set('matchList', $matchList);
	}
	public function setResultDesc($resultDesc) {
		$this->__set('resultDesc', $resultDesc);
	}

	public function render($tpl = '') {
		$tpl = \Acd\conf::$DIR_TEMPLATES.'/AliasId.tpl';
		return parent::render($tpl);
	}
}
