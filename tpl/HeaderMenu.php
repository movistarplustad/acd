<?php
namespace Acd\Ou;
require_once (DIR_BASE.'/class/Template.php');
// Output
class HeaderMenu extends \acd\Template {
	protected $type;
	public function setType($type) {
		$this->type = $type;
	}
	public function render($tpl = '') {
		return parent::render(DIR_TEMPLATES.'/HeaderMenu.tpl');
	}
}