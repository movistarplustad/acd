<?php
namespace Acd\View;
use Acd\conf;
// Output
class UserDetail extends Template {
	public function __construct() {
		$this->__set('resultDesc', '');
		$this->__set('resultCode', '');
		$this->__set('authPermanentList', false);
	}

	// INDEX
	public function setUserElement($userElement) {
		$this->__set('userElement', $userElement);
		$this->__set('roles', [
			conf::$ROL_DEVELOPER => conf::$ROL_DEVELOPER,
			conf::$ROL_EDITOR => conf::$ROL_EDITOR
		]);
	}
	public function setAuthPermanentList($authPermanentList) {
		$this->__set('authPermanentList', $authPermanentList);
	}

	public function render($tpl = '') {
		$tpl = conf::$DIR_TEMPLATES.'/UserDetail.tpl';
		return parent::render($tpl);
	}
}
