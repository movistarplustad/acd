<?php
namespace Acd\View;
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
		return parent::render(\Acd\conf::$DIR_TEMPLATES.'/HeaderMenu.tpl');
	}
}