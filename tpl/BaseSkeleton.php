<?php
namespace Acd\Ou;
require_once (DIR_BASE.'/class/Template.php');
// Output
class BaseSkeleton extends \acd\Template {
	public function __construct() {
		$this->__set('bodyClass', '');
		$this->__set('headTitle', 'ACD');
		$this->__set('headerMenu', '');
		$this->__set('content', '');
		$this->__set('tools', '');
	}
	public function setBodyClass($bodyClass) {
		$this->__set('bodyClass', $bodyClass);
	}
	public function setHeadTitle($headTitle) {
		$this->__set('headTitle', $headTitle);
	}
	public function setHeaderMenu($headerMenu) {
		$this->__set('headerMenu', $headerMenu);
	}
	public function setContent($content) {
		$this->__set('content', $content);
	}
	public function setTools($tools) {
		$this->__set('tools', $tools);
	}
	public function render($tpl = '') {
		return parent::render(DIR_TEMPLATES.'/BaseSkeleton.tpl');
	}
}