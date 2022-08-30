<?php

namespace Acd\Model;

class PersistentStructureManagerTextPlainException extends \exception
{
} // TODO Unificar
class PersistentStructureManagerTextPlain implements iPersistentStructureManager
{
	public function loadAll()
	{
		$path = \Acd\conf::$DATA_PATH;
		$content = file_get_contents($path);
		//+d(json_decode($content, true));
		return json_decode($content, true);
	}
	public function loadById($id)
	{
		$allStructures = $this->loadAll();

		$result = [];
		foreach ($allStructures as $structure) {
			if (strval(key($structure)) === $id) {
				$result = $structure[$id];
				$result['id'] = $id;
			}
		}
		return $result;
	}
	public function save($structuresDo)
	{
		$path = \Acd\conf::$DATA_PATH;
		/* Construct the json */
		$data = $structuresDo->tokenizeData();
		$tempPath = $path . '.tmp';
		$somecontent = json_encode($data);

		if (!$handle = fopen($tempPath, 'a')) {
			echo "Cannot open file ($tempPath)";
			exit;
		}

		// Write $somecontent to our opened file.
		if (fwrite($handle, $somecontent) === FALSE) {
			throw new PersistentStructureManagerTextPlainException("Cannot write to file ($tempPath)", self::SAVE_FAILED);
			exit;
		}
		fclose($handle);
		rename($tempPath, $path);
	}
	public function loadEnumerated($id)
	{
		throw new PersistentStructureManagerMySqlException("Not implemented", self::SAVE_FAILED);
	}
	public function getIndexes()
	{
		// Currently no indexes are required
	}
	public function createIndexes()
	{
		// Currently no indexes are required
	}
	public function dropIndexes()
	{
		// Currently no indexes are required
	}
}
