<?php
namespace Acd\Controller;

use \Acd\Model\EnumeratedLoader;
use \Acd\Model\Query;
// Output
class Enumerated {
	private  $id;

	/* Setters and getters attributes */
	public function setId($id) {
		$this->id = (string)$id;
	}
	public function getId() {
		return $this->id;
	}
	public function getTitle() {
		return 'Collection of enumerated values';
	}

	public function load() {
		$enumeratedLoader = new EnumeratedLoader();
		$query = new Query();
		if ($this->getId()) {
			$query->setType('id');
			$query->setCondition(['id' => 'PROFILE']);
		}
		else {
			$query->setType('all');
		}
		d($enumeratedLoader->load($query));

	}
	public function render() {
		$ou = new \ACD\View\EnumeratedList();
		$ou->setkk($this->getId());

		return $ou->render();
	}
}