<?php
namespace Acd\View;
// Output
class BaseSkeleton extends \Acd\View\Template {
	protected $view;
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
	public function setResultDesc($description, $code) {
		$this->__set('resultDesc', $description);
		$this->__set('resultCode', $code);
	}
	public function setView($view) {
		$this->view = $view;
	}
	protected function getTpl() {
		switch($this->view) {
			case 'ajax':
				$tpl = \Acd\conf::$DIR_TEMPLATES.'/BaseSkeletonAjax.tpl';
				break;
			default:
				$tpl = \Acd\conf::$DIR_TEMPLATES.'/BaseSkeleton.tpl';
				break;
		}
		return $tpl;
	}
	public function render($tpl = '') {
		return parent::render($this->getTpl());
	}
}
