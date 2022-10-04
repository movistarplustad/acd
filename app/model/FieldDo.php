<?php
namespace Acd\Model;

class TypeKeyInvalidException extends \exception {}
class FieldDo
{
	const EMPTY_ID = '__NEW';

	const TYPE_TEXT_SIMPLE = 'text_simple';
	const TYPE_TEXT_MULTILINE = 'text_multiline';
	const TYPE_RICH_TEXT = 'rich_text';
	const TYPE_TEXT_HANDMADE_HTML = 'text_handmade_html';
	const TYPE_INTEGER = 'integer';
	const TYPE_FLOAT = 'float';
	const TYPE_RANGE = 'range';
	const TYPE_BOOLEAN = 'boolean';
	const TYPE_DATE = 'date';
	const TYPE_DATE_TIME = 'date_time';
	const TYPE_DATE_RANGE = 'date_range';
	const TYPE_DATE_TIME_RANGE = 'date_time_range';
	const TYPE_COLLECTION = 'collection';
	const TYPE_CONTENT = 'content';
	const TYPE_FILE = 'file';
	const TYPE_LINK = 'link';
	const TYPE_LIST_OPTIONS = 'list_options';
	const TYPE_LIST_MULTIPLE = 'list_multiple_options';
	const TYPE_COORDINATE = 'coordinate';
	const TYPE_COLOR_RGB = 'color_rgb';
	const TYPE_COLOR_RGBA = 'color_rgba';
	const TYPE_LIST_MULTIPLE_STICKY = 'list_multiple_options_sticky'; // TODO Temporal  hasta unificar campos fijos y variables
	// const TYPE_TAGS = 'tags'; // TODO do in future

	private $id;
	private $type;
	private $name;
	private $value;
	private $ref; // For fields that are external content
	private $refStructure; // Id of type of external content
	private $instance; // Attributes for the relation width external content, eg. date validation
	private $options; // Enumerated posible options
	private $bDirty; // Field indicator to store if the value has modified without save
	private $restrictedStructures; // In field type collection or content restrict to certain structures

	public static function getAvailableTypes() {
		return array(
			self::TYPE_TEXT_SIMPLE => 'Simple text',
			self::TYPE_TEXT_MULTILINE => 'Multiline text area',
			self::TYPE_RICH_TEXT => 'Rich text area',
			self::TYPE_TEXT_HANDMADE_HTML => 'Handmade html textarea',
			self::TYPE_INTEGER => 'Integer number',
			self::TYPE_FLOAT => 'Decimal number',
			self::TYPE_RANGE => 'Range',
			self::TYPE_BOOLEAN => 'Boolean',
			self::TYPE_DATE => 'Date',
			self::TYPE_DATE_TIME => 'Date with time',
			self::TYPE_DATE_RANGE => 'Range of dates',
			self::TYPE_DATE_TIME_RANGE => 'Range of dates with time',
			self::TYPE_CONTENT => 'Reference to other content',
			self::TYPE_COLLECTION => 'Collection of other contents',
			self::TYPE_FILE => 'File upload',
			self::TYPE_LINK => 'Link',
			self::TYPE_LIST_OPTIONS => 'Selection of options',
			self::TYPE_LIST_MULTIPLE => 'List with zero or more selecting options',
			self::TYPE_COORDINATE => 'Geospatial coordinate',
			self::TYPE_COLOR_RGB => 'Color RGB',
			self::TYPE_COLOR_RGBA => 'Color RGBA',
			self::TYPE_LIST_MULTIPLE_STICKY => 'Do not use, temporal list with zero or more selecting options only for sticky fields'
		);
	}

	public function __construct() {
		$this->setDirty(false);
	}
	public function setId($id) {
		$this->id = (string)$id;
	}
	public function getId() {
		return $this->id;
	}
	public function generateId($name) {
		// Temporarily disabled. I think it makes no sense
		return $name;
		/*
		$id = preg_replace('/[^a-z0-9_\-]/', '', strtolower($name));
		if ($id === '') {
			$id = 'id';
		}
		return $id;
		*/
	}
	public function setType($type) {
		if (array_key_exists($type, $this->getAvailableTypes())) {
			$this->type = $type;
		}
		else {
			throw new TypeKeyInvalidException("Invalid type key $type.");
		}
	}
	public function getType() {
		return $this->type;
	}
	public function setName($name) {
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	public function setValue($value) {
		switch($this->getType()) {
			case self::TYPE_COLLECTION:
				$this->value = $value ? $value : []; // Set empty array in collection case
				break;
			default:
				$this->value = $value;
				break;
		}
	}
	public function getValue() {
		return $this->value;
	}
	public function setRef($ref) {
		$this->ref = $ref;
	}
	public function getRef() {
		if($this->getType() == self::TYPE_CONTENT && $this->ref === null) {
			return $ref = [
				'ref' => '',
				'id_structure' => ''
			];
		}
		elseif($this->getType() == self::TYPE_COLLECTION && $this->ref === null) {
			return [];
		}
		else {
			return $this->ref;
		}

	}
	public function setStructureRef($refStructure) {
		$this->refStructure = $refStructure;
	}
	public function getStructureRef() {
		return $this->refStructure;
	}
	public function setInstance($instance) {
		$this->instance = $instance;
	}
	public function getInstance() {
		return $this->instance;
	}
	public function setOptions($options) {
		$this->options = $options; // EnumeratedDo
	}
	public function getOptions() {
		if (!isset($this->options)) {
			$this->options = new EnumeratedDo();
		}
		return $this->options;
	}
	public function setRestrictedStructures($restrictedStructures) {
		$this->restrictedStructures = $restrictedStructures;
	}
	public function getRestrictedStructures() {
		if (!isset($this->restrictedStructures)) {
			$this->restrictedStructures = new StructuresDo();
		}
		return $this->restrictedStructures;
	}
	public function setDirty($bDirty, $numItem = 0) {
		$this->bDirty = (boolean)$bDirty;
		if($this->bDirty) {
			$this->dirtyNumItem = $numItem; // For collections, other fields numItem === 0
		}
		else {
			$this->dirtyNumItem = false;
		}
	}
	public function getDirty() {
		return $this->bDirty === false ? false : $this->dirtyNumItem; // It field is dirty return position (0 for simple fields)
	}
	// Load structure configuration
	public function load($data) {
		$id = key($data);
		$this->setid($id);
		$this->setType($data[$id]['type']);
		$this->setName($data[$id]['name']);
		if (isset($data[$id]['id_options']) && $data[$id]['id_options'] != '') {
			$options = new EnumeratedDo();
			$options->setId($data[$id]['id_options']);
			$this->setOptions($options);
		}
		if (isset($data[$id]['restricted_structured'])) {
			$restrictedStructures = new StructuresDo();
			$restrictedStructures->populateFromArray($data[$id]['restricted_structured']);
			$this->setRestrictedStructures($restrictedStructures);
		}
	}
	// Load content
	private function setValueReference($value) {
		if (isset($value['ref'])) {
			$this->setRef($value['ref']);
			$this->setStructureRef($value['id_structure']);
			// TODO: add instance data
		}
		elseif (is_array($value)) {
			//d($value, debug_backtrace());
			// Collection
			// Atention: $value for simple relation it is also an array
			$this->setRef($value['value']);
			// TODO: add instance data
		}
		if (isset($value['value'])) {
			$this->setValue($value['value']);
		}
	}
	public function loadData($id, $value, $bOnlyValue) {
		//d(debug_backtrace());
		//$this->setId($id);
		//$this->setName($id);

		if ($bOnlyValue) {
			if (isset($value['ref'])) {
				$this->setRef($value);

			}
			$this->setValue($value);
		}
		else {
			$this->setValueReference($value);
		}
		/*
		if (is_array($value)) {
			// Collection & reference
			$this->setValueReference($value);
		} else {
			// Simple data field (number, string...)
			$this->setValue($value);
		}
		*/
	}
	public function tokenizeData() {
		$id = $this->getId() ? $this->getId() : self::EMPTY_ID;
		$aStructureData = array(
				'type' => $this->getType(),
				'name' => $this->getName(),
				'value' => $this->getValue(),
				'ref' => $this->getRef(),
				'id_structure' => $this->getStructureRef()
			);
		// Field with value from a list of values (enumerated collection)
		if ($this->getOptions()->getId()) {
			$aStructureData['id_options'] = $this->getOptions()->getId();
		}
		// Structures restriction in field type collection or content
		if ($this->getRestrictedStructures()->length() > 0) {

			$aRestrictedStructures = [];
			foreach ($this->getRestrictedStructures() as $structure) {
				$aRestrictedStructures[] = $structure->getId();
			}
			$aStructureData['restricted_structured'] = $aRestrictedStructures;
		}
		return array(
			$id => $aStructureData
		);
	}

}
