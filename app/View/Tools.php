<?php
namespace Acd\View;
// Output
class Tools extends \Acd\View\Template {
	public function setLogin($login) {
		$this->__set('login', $login);
	}
	public function setRol($rol) {
		$this->__set('rol', $rol);
	}
	public function render($tpl = '') {
		return parent::render(\Acd\conf::$DIR_TEMPLATES.'/Tools.tpl');
	}
}