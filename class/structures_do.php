<?php
include_once (DIR_BASE.'/class/collection.php');
include_once (DIR_BASE.'/class/structure_do.php');

class structures_do extends collection {
	public function __construct() {
		parent::__construct();
	}
	/* Overwrite add method using id of element */
	public function add($element, $key = null) {
		$_id = $element->getId();
		if ($this->hasKey($_id)) {
			return false;
		}
		else {
			$this->elements[$_id] = $element;
			return true;
		}
	}
	public function get($key) {
		try {
			return parent::get($key);
		} catch (Exception $e) {
			return null;
		}
	}
	public function set($element, $key) {
		try {
			parent::set($element, $key);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	public function remove($key) {
		try {
			parent::remove($key);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	public function loadFromFile($path = null) {
		if ($path === null) {
			$path = DIR_DATA.'/structures.json';
		}
		$content = file_get_contents($path);
		$json_a = json_decode($content, true);
		// TODO: controlar errores
		foreach ($json_a as $estructura) {
			foreach ($estructura as $key => $value) {
				$structure = new structure_do();
				$structure->setId($key);
				$structure->load($value);

				$this->add($structure);
			}
		}

		return true;
	}
	public function save($path = null) {
		if ($path === null) {
			$path = DIR_DATA.'/structures.json';
		}
		/* Construct the json */
		$data = $this->tokenizeData();
		$tempPath = DIR_DATA.'/temp.json';
		$somecontent = json_encode($data);

		if (!$handle = fopen($tempPath, 'a')) {
			 echo "Cannot open file ($tempPath)";
			 exit;
		}

		// Write $somecontent to our opened file.
		if (fwrite($handle, $somecontent) === FALSE) {
			echo "Cannot write to file ($tempPath)";
			exit;
		}
		fclose($handle);
		rename($tempPath, $path);

	}

	public function tokenizeData() {
		$aIds = $this->getAllStructures();
		$aData = array();
		foreach ($aIds as $id) {
			$estructura = $this->get($id);
			$aData[] = $estructura->tokenizeData();
		}

		return $aData;

	}

	/* Return array of ids of all structures */
	/* TODO mover a collection */
	public function getAllStructures() {
		$structuresList = array();
		foreach ($this->elements as $key => $value) {
			$structuresList[] = $value->getId();
		}

		return $structuresList;
	}
}