<?php
namespace Acd\View;
// Output
class UserList extends Template {
	public function __construct() {
	}

	public function setUserList($userList) {
		$this->__set('userList', $userList);
	}

	public function render($tpl = '') {
		$tpl = \Acd\conf::$DIR_TEMPLATES.'/UserList.tpl';
		return parent::render($tpl);
	}
}
