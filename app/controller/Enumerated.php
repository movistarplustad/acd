<?php
namespace Acd\Controller;
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
		$enumeratedLoader = new \ACD\Model\EnumeratedLoader();

	}
	public function render() {
		$ou = new \ACD\View\EnumeratedList();
		$ou->setkk($this->getId());

		return $ou->render();
	}
}