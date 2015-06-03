<?php
namespace Acd\Model;

class EnumeratedDoException extends \exception {}
class EnumeratedDo
{
	//const FOO = 'var';
	private $id;
	private $items;

	public function __construct() {
		$this->id = null;
		$this->items = new Collection();
	}
	/* Setters and getters attributes */
	public function setId($id) {
		$this->id = (string)$id;
	}
	public function getId() {
		return $this->id;
	}
	public function getItems() {
		return $this->items;
	}
	public function setItems($items) {
		$this->items = $items;
	}
	public function load($rawData) {
		$this->setId($rawData['id']);
		$this->setItems($rawData['items']);
	}
	public function tokenizeData() {
		$aFieldsData = array(
			'id' => $this->getId(),
			'items' => $this->getItems()
		);

		return $aFieldsData;
	}

}