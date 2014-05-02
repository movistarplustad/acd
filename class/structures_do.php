<?php
include_once (DIR_BASE.'/class/collection.php');
include_once (DIR_BASE.'/class/structure_do.php');

class structures_do extends collection {
	public function __construct() {
		parent::__construct();
	}
	public function load($path = null) {
		if ($path === null) {
			$path = DIR_DATA.'/structures.json';
		}
		$content = file_get_contents($path);
		$json_a = json_decode($content, true);
		// TODO: controlar errores
		foreach ($json_a as $key => $value) {
			$structure = new structure_do();
			$structure->setId($key);
			$structure->setName($value['name']);
			$structure->setStorage($value['storage']);
			$this->add($structure);
		}

		return true;
	}

	/* Return array of ids of all structures */
	public function getAllStructures() {
		$structuresList = array();
		foreach ($this->elements as $key => $value) {
			$structuresList[] = $value->getId();
		}

		return $structuresList;
	}
}