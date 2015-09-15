<?php
namespace Acd\View;
use \Acd\Model\SessionNavigation;
// Output
class HeaderMenu extends \Acd\View\Template {
	protected $bBackLink;
	public function __construct() {
		$this->setBack(false);
	}
	public function setBack($bBackLink) {
		$this->bBackLink = $bBackLink;
	}
	public function render($tpl = '') {
		return parent::render(\Acd\conf::$DIR_TEMPLATES.'/HeaderMenu.tpl');
	}
}