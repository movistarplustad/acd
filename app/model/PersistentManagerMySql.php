<?php
namespace Acd\Model;

class PersistentStorageQueryTypeNotImplemented extends \exception {} // TODO mover a sitio común
class MySqlConnectionException extends \exception {}

class PersistentManagerMySql implements iPersistentManager
{
	private $mysqli;
	public function initialize($structureDo) {
		//Datos de global.php
		$dbHost = \Acd\conf::$MYSQL_SERVER;
		$dbUser = \Acd\conf::$MYSQL_USER;
		$dbPassword = \Acd\conf::$MYSQL_PASSWORD;
		$db = \Acd\conf::$MYSQL_SCHEMA;

		$this->mysqli = new \mysqli($dbHost, $dbUser, $dbPassword, $db);
		if ($this->mysqli->connect_errno) {
			die ("Failed to connect to MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error);
		}
		return true;
	}
	public function isInitialized($structureDo) {
		try {


			return true;
		}
		catch ( MySqlConnectionException $e ) {
			return false;
		}
	}

	public function load($structureDo, $query) {
		if ($this->isInitialized($structureDo)) {
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
		else {
			// Structure empty
			return new Collection();
		}
	}
	public function save($structureDo, $contentDo) {
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		// TODO	 revisar


		if ($contentDo->getId()) {
			// Update
		}
		else {
			// Insert
		}
		
		return $contentDo;
	}

	public function delete($structureDo, $idContent) {
		if ($this->isInitialized($structureDo)) {

		}

	}

	private function loadById($structureDo, $query) {
		$id = $query->getCondition();


		try {


			$contentFound = new ContentDo();
			$contentFound->load($documentFound, $structureDo->getId());
			$result = new ContentsDo();
			$result->add($contentFound, $id);
		}
		catch( MongoDocumentNotFound $e ) {
			$result = null;
		}

		return $result;
	}

	private function loadAll($structureDo, $query) {

		$this->initialize($structureDo);


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
				$documentFound['data'] = $obj->data;

				$contentFound = new ContentDo();
				$contentFound->load($documentFound, $structureDo->getId());
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