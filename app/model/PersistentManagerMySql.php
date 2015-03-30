<?php
namespace Acd\Model;

class PersistentStorageQueryTypeNotImplemented extends \exception {} // TODO mover a sitio común
class PersistentManagerMySqlException extends \exception {} // TODO Unificar
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
				case 'editorSearch':
					return $this->loadEditorSearch($structureDo, $query);
					break;
				case 'countParents':
					return $this->countParents($structureDo, $query);
					break;
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

		foreach ($structureDo->getFields() as $field) {
			switch ($field->getType()) {
				case $field::TYPE_CONTENT:
					// Relation
					$child =  $field->getValue()['ref'];
					if ($child) {
						$oIdChildsRelated[] = $child; // For table relations
					}
					break;
				case $field::TYPE_COLLECTION:
					// Collection relation
					foreach ($field->getValue() as $fieldValue) {
						$child = $fieldValue['ref'];
						if ($child) {
							$oIdChildsRelated[] = $child;
						}
					}
					break;
			}
		}

		if(isset($oIdChildsRelated)) {
			$this->updateRelations($contentDo->getId(), $oIdChildsRelated);
		}

		return $contentDo;
	}

	private function updateRelations($parent, $children) {
		// Redundant cache content relations 
		//d("Padre . Hijos", $parent, $children);
		// emptying old relations & add news
		$parent = $this->mysqli->real_escape_string($parent);
		$select = "DELETE FROM relation WHERE parent = '$parent'";
		if ($this->mysqli->query($select) !== true) {
			throw new PersistentManagerMySqlException("Delete parent relation failed", self::DELETE_FAILED);
		}
		foreach ($children as $child) {
			$child = $this->mysqli->real_escape_string($child);
			$select = "INSERT INTO relation (parent, child) VALUES ($parent, $child)";
			if ($this->mysqli->query($select) !== true) {
				throw new PersistentManagerMySqlException("Insert failed when save parent relation", self::INSERT_FAILED);
			}
		}
	}

	public function delete($structureDo, $idContent) {
		if ($this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		$id = $this->mysqli->real_escape_string($idContent);
		$select = "DELETE FROM content WHERE id = '$id'";
		// It is not allowed to delete a content with relations, beacause break integrity
		$query = new Query();
		$query->setType('countParents');
		$query->setCondition($id);
		$numRelations = $this->countParents($structureDo, $query);
		if ($numRelations > 0) {
			throw new PersistentManagerMySqlException("Delete failed, the content has $numRelations relationships", self::DELETE_FAILED);
		}
		elseif ($this->mysqli->query($select) !== true) {
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

	private function loadEditorSearch($structureDo, $query) {
		// SELECT id, title, data FROM content WHERE id_structure = 'directo' AND title LIKE '%foo%'; 
		$limit = $query->getLimits()->getUpper();
		$filter = array();
		if(isset($query->getCondition()['title'])) {
			$search = $this->mysqli->real_escape_string($query->getCondition()['title']);
			$filter['title'] = "title LIKE '%".$search."%'";
		}
		if(isset($query->getCondition()['idStructure'])) {
			$search = $this->mysqli->real_escape_string($query->getCondition()['idStructure']);
			$filter['id_structure'] = "id_structure = '".$search."'";
		}
		$where = '';
		if ($filter) {
			$where = ' WHERE '.implode(' AND ', $filter);
		}
		$select = "SELECT id, title, data FROM content $where LIMIT $limit";

		$result = new ContentsDo();
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

		return $result;
	}

	private function countParents($structureDo, $query) {
		//SELECT count(*) FROM relation WHERE child = 1

		$id = $this->mysqli->real_escape_string($query->getCondition());
		$select = "SELECT count(*) as total FROM relation WHERE child = $id";
		$total = '?';

		if ($dbResult = $this->mysqli->query($select)) {
			while($obj = $dbResult->fetch_object()){
				$total = $obj->total;
			}
		}

		return $total;
	}
}