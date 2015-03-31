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
		//+d($result);
		return $result;
	}
	public function save($structuresDo) {
		if (!$this->isInitialized()) {
			$this->initialize();
		}
		$data = $structuresDo->tokenizeData();
		foreach ($structuresDo as $structure) {
			$id = $this->mysqli->real_escape_string($structure->getId());
			$name = $this->mysqli->real_escape_string($structure->getName());
			$storage = $this->mysqli->real_escape_string($structure->getStorage());
			//d(json_encode($structure->tokenizeData()[$structure->getId()]['fields']));
			$fields = $this->mysqli->real_escape_string(json_encode($structure->tokenizeData()[$structure->getId()]['fields']));
			$select = "INSERT INTO structure (id, name, storage, fields)
				VALUE ('$id', '$name', '$storage', '$fields')
				ON DUPLICATE KEY UPDATE
				id = '$id', name = '$name', storage = '$storage', fields = '$fields'";
			if ($this->mysqli->query($select) !== true) {
				throw new PersistentManagerMySqlException("Update failed when save structure", self::SAVE_FAILED);
			}
		}
		return;
	}
}