<?php
namespace Acd\View;
require_once (DIR_BASE.'/app/view/Template.php');
// Output
class HeaderMenu extends \Acd\View\Template {
	protected $type;
	protected $url;
	public function setType($type) {
		$this->type = $type;
	}
	public function setUrl($url) {
		$this->url = $url;
	}
	public function render($tpl = '') {
		return parent::render(DIR_TEMPLATES.'/HeaderMenu.tpl');
	}
}