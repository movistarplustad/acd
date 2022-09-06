<?php
namespace Acd\View;

// Output
class HeaderMenu extends Template {
	protected $bBackLink;
	public function __construct() {
		$this->setBack(false);
	}
	public function setBack($bBackLink) {
		$this->bBackLink = $bBackLink;
	}
	public function render($tpl = '') {
		return parent::render($_ENV['ACD_DIR_TEMPLATES'].'/HeaderMenu.tpl');
	}
}