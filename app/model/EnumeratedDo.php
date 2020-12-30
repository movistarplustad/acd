<?php
namespace Acd\Model;

class EnumeratedDoException extends \exception {}
class EnumeratedDo
{
	const EMPTY_ID = '__NEW';

	private $id;
	private $items;

	public function __construct() {
		$this->id = null;
		$this->items = Array();
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
		// Copy all values to $result['out'] and move matchs to $result['in']
		$result = array('in' => [], 'out' => []);
		$result['out'] = $this->getItems();
		foreach ($aFieldValue as $key) {
			if(isset($result['out'][$key])) {
				$result['in'][$key] = $result['out'][$key];
				unset($result['out'][$key]);
			}
			else {
				$result['in'][$key] = "!!!$key - not found in collection";
			}
		}
		return $result;
	}
	public function setItems($items) {
		// Array($key => $value, $key => $value);
		$this->items = $items;
	}
	public function load($rawData) {
		$this->setId($rawData['id']);
		$this->setItems(isset($rawData['items']) ? $rawData['items'] : Array()) ;
	}
	public function tokenizeData() {
		$aFieldsData = array(
			'id' => $this->getId(),
			'items' => $this->getItems()
		);
		return $aFieldsData;
	}
}