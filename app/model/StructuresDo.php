<?php
namespace Acd\Model;

class StructuresDo extends Collection 
{
	public function loadFromFile($path = null) {
		if ($path === null) {
			$path = DIR_DATA.'/structures.json';
		}
		$content = file_get_contents($path);
		$json_a = json_decode($content, true);
		// TODO: controlar errores
		foreach ($json_a as $estructura) {
			foreach ($estructura as $key => $value) {
				$structure = new StructureDo();
				$structure->setId($key);
				$structure->load($value);

				$this->add($structure, $key);
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
	/* TODO mover a Collection */
	public function getAllStructures() {
		$structuresList = array();
		foreach ($this->elements as $key => $value) {
			$structuresList[] = $value->getId();
		}

		return $structuresList;
	}
}