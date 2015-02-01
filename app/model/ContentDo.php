<?php
namespace Acd\Model;

class ContentKeyInvalidException extends \exception {}
class ContentDo
{
	private $id;
	private $idStructure;
	private $title;
	//private $data; /* Array key/value of variable fields */
	private $fields;

	public function __construct() {
		$this->id = null;
		$this->fields = new FieldsDo();
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
	public function setData($keyData, $data = null) {
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
		if ($key === null) {
			return $this->data;
		}
		else {
			if (isset($this->data[$key])) {
				return $this->data[$key];
			}
			else {
				throw new ContentKeyInvalidException("Invalid conten key $key.");
			}
		}
	}
	public function load($rawData, $idStructure = null) {
		$this->setId($rawData['id']);
		$this->setTitle($rawData['title']);
		$this->setIdStructure($idStructure);
		foreach ($rawData['data'] as $key => $value) {
			$this->setData($key, $value);
		}
	}
	public function tokenizeData() {
		$aFieldsData = array();
		// TODO usar fields
		$aFieldsData = $this->getData();
		return  array(
			'id' => $this->getId(),
			'title' => $this->getTitle(),
			'data' => $aFieldsData
		);
	}
}