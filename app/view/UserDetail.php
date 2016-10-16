<?php
namespace Acd\View;
// Output
class UserDetail extends Template {
	public function __construct() {
		$this->__set('resultDesc', '');
		$this->__set('resultCode', '');
	}

	// INDEX
	public function setUserElement($userElement) {
		$this->__set('userElement', $userElement);
		$this->__set('roles', [
			\Acd\conf::$ROL_DEVELOPER => \Acd\conf::$ROL_DEVELOPER,
			\Acd\conf::$ROL_EDITOR => \Acd\conf::$ROL_EDITOR
		]);
	}

	public function render($tpl = '') {
		$tpl = \Acd\conf::$DIR_TEMPLATES.'/UserDetail.tpl';
		return parent::render($tpl);
	}
}
