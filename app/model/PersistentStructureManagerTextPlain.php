<?php
namespace Acd\Model;

class PersistentStructureManagerTextPlainException extends \exception {} // TODO Unificar
class PersistentStructureManagerTextPlain implements iPersistentStructureManager
{
	public function loadAll() {
		$path = \ACD\conf::$DATA_PATH;
		$content = file_get_contents($path);
		//+d(json_decode($content, true));
		return json_decode($content, true);
	}
	public function loadById($id) {
		$allStructures = $this->loadAll();

		$result = [];
		foreach ($allStructures as $structure) {
				if(strval(key($structure)) === $id) {
					$result = $structure[$id];
					$result['id'] = $id;
				}
		}
		return $result;
	}
	public function save($structuresDo) {
		$path = \ACD\conf::$DATA_PATH;
		/* Construct the json */
		$data = $structuresDo->tokenizeData();
		$tempPath = DIR_DATA.'/temp.json';
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
}