<?php
namespace Acd\View;
use Acd\conf;
// Output
class InstallInfo extends Template {
	protected $collectionsIndexes;

    public function __construct() {
		$this->collectionsIndexes = [];
    }

	public function addCollectionIndexes($collectionsIndexes) {
		array_push($this->collectionsIndexes, $collectionsIndexes);
		$this->__set('collectionsIndexes', print_r($this->collectionsIndexes, true));
	}

    public function render($tpl = '') {
        $tpl = conf::$DIR_TEMPLATES.'/InstallInfo.tpl';
        return parent::render($tpl);
    }
}
