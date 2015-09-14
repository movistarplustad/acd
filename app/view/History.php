<?php
namespace Acd\View;
// Output
class History extends \Acd\View\Template {
	public function __construct() {
		$this->setMaxSizeItems(15);
		$this->setPositionCurrentItem(0);
	}
	public function setMaxSizeItems($maxSizeItems) {
		$this->__set('maxSizeItems', $maxSizeItems);
	}
	public function setPositionCurrentItem($positionCurrentItem) {
		$this->__set('positionCurrentItem', $positionCurrentItem);
	}
	public function setItems($items) {
		$this->__set('items', $items);
	}
	public function render($tpl = '') {
		return parent::render(\Acd\conf::$DIR_TEMPLATES.'/History.tpl');
	}
}