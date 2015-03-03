<?php
namespace Acd\Model;

class TypeKeyInvalidException extends \exception {}
class FieldDo
{
	const TYPE_TEXT_SIMPLE = 'text_simple';
	const TYPE_TEXT_MULTILINE = 'text_multiline';
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

	private $id;
	private $type;
	private $name;
	private $value;

	public static function getAvailableTypes() {
		return array(
			self::TYPE_TEXT_SIMPLE => 'Simple text',
			self::TYPE_TEXT_MULTILINE => 'Multiline text area',
			self::TYPE_INTEGER => 'Integer number',
			self::TYPE_FLOAT => 'Decimal number',
			self::TYPE_RANGE => 'Range',
			self::TYPE_BOOLEAN => 'Boolean',
			self::TYPE_DATE => 'Date',
			self::TYPE_DATE_TIME => 'Date with time',
			self::TYPE_DATE_RANGE => 'Range of dates',
			self::TYPE_DATE_TIME_RANGE => 'Range of dates with time',
			self::TYPE_COLLECTION => 'Collection of other elements',
			self::TYPE_CONTENT => 'Reference to other content'
		);
	}

	public function __construct() {
	}
	public function setId($id) {
		$this->id = (string)$id;
	}
	public function getId() {
		return $this->id;
	}
	public function generateId($name) {
		$id = preg_replace('/[^a-z0-9_\-]/', '', strtolower($name));
		if ($id === '') {
			$id = 'id';
		}
		return $id;
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
		$this->value = $value;
	}
	public function getValue() {
		return $this->value;
	}
	public function load($data) {
		$id = key($data);
		$this->setid($id);
		$this->setType($data[$id]['type']);
		$this->setName($data[$id]['name']);
	}
	public function tokenizeData() {
		return array(
			$this->getId() => array(
				'type' => $this->getType(),
				'name' => $this->getName()
			)
		);
	}

}