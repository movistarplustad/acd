<?php
namespace Acd\Model;

class PersistentStructureManagerMySqlException extends \exception {} // TODO Unificar
class PersistentStructureManagerMySql implements iPersistentStructureManager
{
	private $mysqli;
	public function initialize() {
		//Datos de global.php
		$dbHost = \Acd\conf::$MYSQL_SERVER;
		$dbUser = \Acd\conf::$MYSQL_USER;
		$dbPassword = \Acd\conf::$MYSQL_PASSWORD;
		$db = \Acd\conf::$MYSQL_SCHEMA;

		$this->mysqli = new \mysqli($dbHost, $dbUser, $dbPassword, $db);
		if ($this->mysqli->connect_errno) {
			throw new PersistentManagerMySqlException("Failed to connect to MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error, self::NO_CONNECTION);
		}
		return true;
	}
	public function isInitialized() {
		try {
			$this->initialize();
			return true;
		}
		catch ( PersistentManagerMySqlException $e ) {
			return false;
		}
	}
	public function loadAll() {
		if (!$this->isInitialized()) {
			$this->initialize();
		}
		$select = "SELECT id, name, storage, fields FROM structure"; // TODO LIMIT $limit
		$result = [];
		if ($dbResult = $this->mysqli->query($select)) {
			while($obj = $dbResult->fetch_object()){
				$documentFound = array();
				$documentFound[$obj->id]['name'] = $obj->name;
				$documentFound[$obj->id]['storage'] = $obj->storage;
				$documentFound[$obj->id]['fields'] = json_decode( $obj->fields, true);
				$result[] = $documentFound;
			}
		}
		d($documentFound);
		return $documentFound;
		$path = \ACD\conf::$DATA_PATH;
		$content = file_get_contents($path);
		d( json_decode($content, true));
		return json_decode($content, true);
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
			throw new PersistentStructureManagerMySqlException("Cannot write to file ($tempPath)", self::SAVE_FAILED);
			exit;
		}
		fclose($handle);
		rename($tempPath, $path);
	}
}