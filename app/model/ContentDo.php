<?php
namespace Acd\Model;

class ContentKeyInvalidException extends \exception {}
class ContentDoException extends \exception {}
class ContentDo
{
	private $id;
	private $idStructure;
	private $title;
	private $tags;
	//private $data; /* Array key/value of variable fields */
	private $fields;
	private $parent; /* ContentDo Relation in complex structures */
	private $countParents; /* Number of parent, used in conten editor for info */

	public function __construct() {
		$this->id = null;
		$this->idStructure = null;
		$this->tags = array();
		$this->fields = new FieldsDo();
		$this->parent = null;
	}
	/* Setters and getters attributes */
	public function setId($id) {
		$this->id = (string)$id;
	}
	public function getId() {
		return $this->id;
	}
	public function setIdStructure($idStructure) {
		$this->idStructure = (string)$idStructure;
	}
	public function getIdStructure() {
		return $this->idStructure;
	}
	public function setTitle($title) {
		$this->title = $title;
	}
	public function getTitle() {
		return $this->title;
	}
	public function setTags($tags) {
		if (is_array($tags)) {
			$this->tags = $tags;
		}
		else {
			throw new ContentDoException("Input data should be an array", 1);
		}
	}
	public function getTags() {
		return $this->tags;
	}
	public function addField($field) {
		$this->getFields()->add($field);
	}
	public function getFields() {
		return $this->fields;
	}
	private function setFields($fields) {
		foreach ($fields as $field) {
			$this->fields->add(clone $field);
		}
	}
	public function getFieldType($fieldName) {
		try {
			return $this->getFields()->getType($fieldName);
		} catch (KeyInvalidException $e) {
			return '';
		}
	}
	public function getFieldValue($fieldName) {
		try {
			return $this->getFields()->getValue($fieldName);
		} catch (KeyInvalidException $e) {
			return '';
		}
	}
	public function setFieldValue($fieldName, $value) {
		$this->getFields()->setValue($fieldName, $value);
	}
	public function setStructureRef($fieldName, $structureRef) {
		$this->getFields()->setStructureRef($fieldName, $structureRef);
	}	
	public function setData($keyData, $data = null) {
		echo "TODO";
		/* Setting full structure */
		if ($data === null) {
			// TODO errores si campo existe y tipo dato válido
			$this->data = $keyData;
		}
		/* Set key / value */
		else {
			$this->data[$keyData] = $data;
		}
	}
	public function getData($key = null) {
		$data = array();
		foreach ($this->getFields() as $field) {
			$data[$field->getId()] = $field->getValue();
		}

		return $data;
	}
	public function setParent($parent) {
		$this->parent = $parent; // ContentDO
	}
	public function getParent() {
		return $this->parent;
	}
	public function setCountParents($countParents) {
		$this->countParents = $countParents;
	}
	public function getCountParents() {
		return $this->countParents;
	}
	// With the data structure, build the skeleton of content
	public function buildSkeleton($structure) {
		$this->setIdStructure($structure->getId());
		$this->setFields($structure->getFields());
	}
	public function load($rawData, $structure = null) {
		$this->setId($rawData['id']);
		$this->setTitle($rawData['title']);
		@$tags = $rawData['tags'] ?: [];
		$this->setTags($tags);
		if($structure !== null) {
			$this->buildSkeleton($structure);
		}
		$fields = $this->getFields();
		if (is_array($rawData['data'])){
			foreach ($rawData['data'] as $key => $value) {
				$this->getFields()->setValue($key, $value);
			}
			//d($this->getFields());
		}
//+d($this);
		//$this->setFields($fields);
	}
	public function tokenizeData() {
		$aFieldsData = array();
		// Test if the field value is a real value or reference
		foreach ($this->getFields() as $field) {
			switch ($field->getType()) {
				case 'content':
					$itemValue = $field->getValue();
					$value = is_object($itemValue) ? $itemValue->tokenizeData() : $itemValue;
					break;
				case 'collection':
					$value = [];
					foreach ($field->getValue() as $itemValue) {
						$value[] = is_object($itemValue) ? $itemValue->tokenizeData() : $itemValue;
					}
					break;
				default:
					$value = $field->getValue();
					break;
			}
			$aFieldsData[$field->getId()] = $value;
		}

		return  array(
			'id' => $this->getId(),
			'id_structure' => $this->getIdStructure(),
			'title' => $this->getTitle(),
			'tags' => $this->getTags(),
			'data' => $aFieldsData
		);
	}
}