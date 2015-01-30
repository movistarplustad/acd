<?php
namespace Acd\Model;

class ContentKeyInvalidException extends \exception {}
class ContentDo
{
	private $id;
	private $idStructure;
	private $titulo;
	private $data; /* Array key/value of variable fields */
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
	public function setTitulo($titulo) {
		$this->titulo = $titulo;
	}
	public function getTitulo() {
		return $this->titulo."TODO ".$this->getId();
		//return $this->titulo;
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
}