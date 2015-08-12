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
	// Split list of items in 2 lists
	public function detachItems($fieldValue) {
		if ($fieldValue == null) {
			$aFieldValue = [];
		}
		elseif(is_string($fieldValue)) {
			$aFieldValue = [];
			$aFieldValue[] = $fieldValue;
		}
		else {
			$aFieldValue = $fieldValue;
		}
		$result = array('in' => [], 'out' => []);
		$result['out'] = $this->getItems();
		foreach ($aFieldValue as $key) {
			$result['in'][$key] = $result['out'][$key];
			unset($result['out'][$key]);
		}
		return $result;
	}
	public function setItems($items) {
		$this->items = $items;
	}
	public function load($rawData) {
		$this->setId($rawData['id']);
		$this->setItems(isset($rawData['items']) ? $rawData['items'] : new Collection()) ;
	}
	public function tokenizeData() {
		$aFieldsData = array(
			'id' => $this->getId(),
			'items' => $this->getItems()
		);

		return $aFieldsData;
	}

}