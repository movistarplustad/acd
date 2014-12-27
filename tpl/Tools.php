<?php
namespace Acd\Ou;
require_once (DIR_BASE.'/class/Template.php');
// Output
class Tools extends \acd\Template {
	public function setLogin($login) {
		$this->__set('login', $login);
	}
	public function setRol($rol) {
		$this->__set('rol', $rol);
	}
	public function render($tpl = '') {
		return parent::render(DIR_TEMPLATES.'/Tools.tpl');
	}
}