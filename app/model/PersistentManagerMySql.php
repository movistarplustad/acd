<?php
namespace Acd\Model;

class PersistentStorageQueryTypeNotImplemented extends \exception {} // TODO mover a sitio común
class PersistentManagerMySqlException extends \exception {} // TODO Unificar
class PersistentManagerMySql implements iPersistentManager
{
	const NO_CONNECTION = 1;
	const UPDATE_FAILED = 2;
	const INSERT_FAILED = 3;
	private $mysqli;
	public function initialize($structureDo) {
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
	public function isInitialized($structureDo) {
		try {
			$this->initialize($structureDo);
			return true;
		}
		catch ( PersistentManagerMySqlException $e ) {
			return false;
		}
	}

	public function load($structureDo, $query) {
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		switch ($query->getType()) {
			case 'id':
				return $this->loadById($structureDo, $query);
				break;
			case 'all':
				return $this->loadAll($structureDo, $query);
			default:
				throw new PersistentStorageQueryTypeNotImplemented('Query type ['.$query->getType().'] not implemented');
				break;
		}

	}
	public function save($structureDo, $contentDo) {
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		// TODO	 revisar


		if ($contentDo->getId()) {
			// Update
			$id = $this->mysqli->real_escape_string($contentDo->getId());
			$title = $this->mysqli->real_escape_string($contentDo->getTitle());
			$data = $this->mysqli->real_escape_string(serialize($contentDo->getData()));
			// Log, timestamp for last save / update operation
			$select = "UPDATE content SET title = '$title', data ='$data', save_ts = CURRENT_TIMESTAMP WHERE id = '$id'";
			if ($this->mysqli->query($select) !== true) {
				throw new PersistentManagerMySqlException("Update failed when save document", self::UPDATE_FAILED);
			}
		}
		else {
			// Insert
			$title = $this->mysqli->real_escape_string($contentDo->getTitle());
			$data = $this->mysqli->real_escape_string(serialize($contentDo->getData()));
			$idStructure = $this->mysqli->real_escape_string($structureDo->getId());
			// Log, timestamp for last save / update operation
			$select = "INSERT INTO content (title, data, id_structure, save_ts) VALUES ('$title', '$data', '$idStructure', CURRENT_TIMESTAMP)";
			if ($this->mysqli->query($select) !== true) {
				throw new PersistentManagerMySqlException("Insert failed when save document", self::INSERT_FAILED);
			}
			$contentDo->setId($this->mysqli->insert_id);
		}
		
		return $contentDo;
	}

	public function delete($structureDo, $idContent) {
		if ($this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		$id = $this->mysqli->real_escape_string($idContent);
		$select = "DELETE FROM content WHERE id = '$id'";
		if ($this->mysqli->query($select) !== true) {
			throw new PersistentManagerMySqlException("Delete failed", self::DELETE_FAILED);
		}
	}

	private function loadById($structureDo, $query) {
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		$id = $query->getCondition();

		try {
			$id = $this->mysqli->real_escape_string($query->getCondition());
			$select = "SELECT id, title, data FROM content WHERE id = '$id'";
			if ($dbResult = $this->mysqli->query($select)) {
				$result = new ContentsDo();
				while($obj = $dbResult->fetch_object()){
					$documentFound = array();
					$documentFound['id'] = $obj->id;
					$documentFound['title'] = $obj->title;
					$documentFound['data'] = unserialize($obj->data);

					$contentFound = new ContentDo();
					$contentFound->load($documentFound, $structureDo);
					
					$result->add($contentFound, $id);
				}
			}
		}
		catch( \Exception $e ) {
			$result = null;
		}

		return $result;
	}

	private function loadAll($structureDo, $query) {
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}

		$result = new ContentsDo();
		/* Select queries return a resultset */
		$idStructure = $this->mysqli->real_escape_string($structureDo->getId());
		$limit = $query->getLimits()->getUpper();
		$select = "SELECT id, title, data FROM content WHERE id_structure = '$idStructure' LIMIT $limit";

		if ($dbResult = $this->mysqli->query($select)) {
			while($obj = $dbResult->fetch_object()){
				$documentFound = array();
				$documentFound['id'] = $obj->id;
				$documentFound['title'] = $obj->title;
				$documentFound['data'] = unserialize($obj->data);

				$contentFound = new ContentDo();
				$contentFound->load($documentFound, $structureDo);
				$result->add($contentFound, $obj->id);
			} 
			/* free result set */
			$dbResult->close();
		}
		//TODO revisar
		// Purge to limits
		//$limits = $query->getLimits();
		//$limits->setTotal(count($aContents));


		return $result;
	}
}