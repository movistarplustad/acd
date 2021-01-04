<?php
namespace Acd\Model;

class PersistentManagerMySqlException extends \exception {} // TODO Unificar
class PersistentManagerMySql implements iPersistentManager
{
	private $mysqli;
	public function initialize($structureDo) {
		//Data from global.php
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
	private function getFilters($query) {
		$filters = [];
		if ($query->getCondition('validity-date')) {
			$filters['validity-date'] = $query->getCondition('validity-date');
		}
		if ($query->getCondition('profile')) {
			$filters['profile'] = $query->getCondition('profile');
		}
		return $filters;
	}
	public function load($structureDo, $query) {
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		$filters = $this->getFilters($query);
		switch ($query->getType()) {
			case 'id':
				return $this->loadById($structureDo, $query->getCondition());
				break;
				case 'id-deep':
					return $this->loadIdDepth($structureDo, $query->getCondition('id'), $query->getDepth(), $filters);
					break;
				case 'all':
					return $this->loadAll($structureDo, $query);
					break;
				case 'editor-search':
					return $this->loadEditorSearch($structureDo, $query);
					break;
				case 'countParents':
					return $this->countParents($structureDo, $query);
					break;
				case 'count-alias-id':
					return $this->countAliasId($structureDo, $query);
					break;
				case 'difuse-alias-id':
					return $this->difuseAliasId($structureDo, $query->getCondition('id'), $filters);
					break;
				case 'meta-information':
					return $this->metaInformation($structureDo, $query->getCondition('id'));
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
			$alias_id = $this->mysqli->real_escape_string($contentDo->getAliasId());

			$dummyDate = $contentDo->getPeriodOfValidity(ContentDo::PERIOD_OF_VALIDITY_START);
			if (is_finite($dummyDate)) {
				$dummyDate = "FROM_UNIXTIME($dummyDate)";
			}
			else {
				$dummyDate = 'null';
			}
			$period_of_validity_start = $this->mysqli->real_escape_string($dummyDate);

			$dummyDate = $contentDo->getPeriodOfValidity(ContentDo::PERIOD_OF_VALIDITY_END);
			if (is_finite($dummyDate)) {
				$dummyDate = "FROM_UNIXTIME($dummyDate)";
			}
			else {
				$dummyDate = 'null';
			}
			$period_of_validity_end = $this->mysqli->real_escape_string($dummyDate);

			$data = $this->mysqli->real_escape_string(serialize($contentDo->getData()));
			// Log, timestamp for last save / update operation
			$select = "UPDATE content SET title = '$title', period_of_validity_start = $period_of_validity_start, period_of_validity_end = $period_of_validity_end,  alias_id = '$alias_id', data ='$data', save_ts = CURRENT_TIMESTAMP WHERE id = '$id'";
			if ($this->mysqli->query($select) !== true) {
				throw new PersistentManagerMySqlException("Update failed when save document", self::UPDATE_FAILED);
			}
		}
		else {
			// Insert
			$title = $this->mysqli->real_escape_string($contentDo->getTitle());
			$alias_id = $this->mysqli->real_escape_string($contentDo->getAliasId());


			$dummyDate = $contentDo->getPeriodOfValidity(ContentDo::PERIOD_OF_VALIDITY_START);
			if (is_finite($dummyDate)) {
				$dummyDate = "FROM_UNIXTIME($dummyDate)";
			}
			else {
				$dummyDate = 'null';
			}
			$period_of_validity_start = $this->mysqli->real_escape_string($dummyDate);

			$dummyDate = $contentDo->getPeriodOfValidity(ContentDo::PERIOD_OF_VALIDITY_END);
			if (is_finite($dummyDate)) {
				$dummyDate = "FROM_UNIXTIME($dummyDate)";
			}
			else {
				$dummyDate = 'null';
			}
			$period_of_validity_end = $this->mysqli->real_escape_string($dummyDate);

			$data = $this->mysqli->real_escape_string(serialize($contentDo->getData()));
			$idStructure = $this->mysqli->real_escape_string($structureDo->getId());
			// Log, timestamp for last save / update operation
			$select = "INSERT INTO content (title, period_of_validity_start, period_of_validity_end, alias_id, data, id_structure, save_ts) VALUES ('$title', $period_of_validity_start, $period_of_validity_end, '$alias_id', '$data', '$idStructure', CURRENT_TIMESTAMP)";
			if ($this->mysqli->query($select) !== true) {
				throw new PersistentManagerMySqlException("Insert failed when save document", self::INSERT_FAILED);
			}
			$contentDo->setId($this->mysqli->insert_id);
		}

		$this->updateTags($contentDo->getId(), $contentDo->getTags());

		$bChildsRelated = false;
		$oIdChildsRelated = [];
		foreach ($structureDo->getFields() as $field) {
			switch ($field->getType()) {
				case $field::TYPE_CONTENT:
					// Relation
					$child =  $field->getValue()['ref'];
					if ($child) {
						$oIdChildsRelated[] = $child; // For table relations
					}
					$bChildsRelated = true;
					break;
				case $field::TYPE_COLLECTION:
					// Collection relation
					if(is_array($field)) {
						foreach ($field->getValue() as $fieldValue) {
							$child = $fieldValue['ref'];
							if ($child) {
								$oIdChildsRelated[] = $child;
							}
						}
					}
					$bChildsRelated = true;
					break;
			}
		}

		if($bChildsRelated) {
			$this->updateRelations($contentDo->getId(), $oIdChildsRelated);
		}

		return $contentDo;
	}

	private function updateTags($idContent, $aTags) {
		// emptying old tags & add news
		$id = $this->mysqli->real_escape_string($idContent);
		$select = "DELETE FROM content_tag WHERE id = $id";
		if ($this->mysqli->query($select) !== true) {
			throw new PersistentManagerMySqlException("Delete content tags failed", self::DELETE_FAILED);
		}
		foreach ($aTags as $tag) {
			$tag = $this->mysqli->real_escape_string($tag);
			$select = "INSERT INTO content_tag (id, tag) VALUES ($id, '$tag')";
			if ($this->mysqli->query($select) !== true) {
				throw new PersistentManagerMySqlException("Insert failed when save content tags", self::INSERT_FAILED);
			}
		}
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
		$this->updateTags($id, array());
	}

	private function loadById($structureDo, $id) {
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}

		$contentFound = null;
		try {
			$id = $this->mysqli->real_escape_string($id);
			$select = "SELECT id, title, UNIX_TIMESTAMP(period_of_validity_start) as period_of_validity_start, UNIX_TIMESTAMP(period_of_validity_end) as period_of_validity_end, alias_id, data FROM content WHERE id = '$id'";
			if ($dbResult = $this->mysqli->query($select)) {
				//$result = new ContentsDo();
				while($obj = $dbResult->fetch_object()){
					$documentFound = array();
					$documentFound['id'] = $obj->id;
					$documentFound['title'] = $obj->title;
					$documentFound['period_of_validity']['start'] = is_null($obj->period_of_validity_start) ? -INF : $obj->period_of_validity_start;
					$documentFound['period_of_validity']['end'] = is_null($obj->period_of_validity_end) ? INF : $obj->period_of_validity_end;
					$documentFound['alias_id'] = $obj->alias_id;
					$documentFound['data'] = unserialize($obj->data);

					// Tags
					$select = "SELECT id, tag FROM content_tag WHERE id = '$id'";
					if ($dbResult = $this->mysqli->query($select)) {
						$aTags = [];
						while($objTag = $dbResult->fetch_object()){
							$aTags[] = $objTag->tag;
						}
						$documentFound['tags'] = $aTags;
					}

					$contentFound = new ContentDo();
					$contentFound->load($documentFound, $structureDo);

					//$result->add($contentFound, $id);
				}
			}
		}
		catch( \Exception $e ) {
			$contentFound = null;
		}

		//return $result;
		return $contentFound;
	}
	// Cache from structure data
	// TODO Unify in iPersistentStructure Manager?
	private function getStructure($id) {
		if (!isset($this->structuresCache[$id])) {
			$structure = new structureDo();
			$structure->setId($id);
			$structure->loadFromFile();
			$this->structuresCache[$id] = $structure;
		}

		return $this->structuresCache[$id];
	}
	private function loadIdDepth ($structureDo, $idContent, $depth, $filters = []) {
		if ($depth > 0) {
			$depth--;
			//$content = $this->loadById($structureDo, $idContent)->get($idContent);
			$content = $this->loadById($structureDo, $idContent);
			$validityDate = isset($filters['validity-date']) ? $filters['validity-date'] : null;
			$isValid = $content && $content->checkValidityDate($validityDate);
			// TODO Organize code
			if (!$isValid) return null;
			// else

			$fields = $content->getFields();
			// Walk fields and fill their values
			foreach ($fields as $field) {
				switch($field->getType()) {
					case 'content' :
						// Has relation info?
						if($field->getValue() && $field->getValue()['id_structure']) {
							$structureTmp = $this->getStructure($field->getValue()['id_structure']);
							$contentsTemp = $this->loadIdDepth ($structureTmp, $field->getValue()['ref'], $depth, $filters);
							if ($contentsTemp) {
								$field->setValue($contentsTemp->one());
							}
						}
						break;
					case 'collection' :
						$newVal = new ContentsDo();
						foreach ($field->getValue() as $itemCollection) {
							$structureTmp = $this->getStructure($itemCollection['id_structure']);
							$contentsTemp = $this->loadIdDepth ($structureTmp, $itemCollection['ref'], $depth, $filters);
							if ($contentsTemp) {
								$newVal->add($contentsTemp->one());
							}
						}

						$field->setValue($newVal);
						break;
				}
			}
			$result = new ContentsDo();
			$result->add($content, $idContent);
			return $result;
		}
	}
	private function loadAll($structureDo, $query) {
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}

		$result = new ContentsDo();
		/* Select queries return a resultset */
		$idStructure = $this->mysqli->real_escape_string($structureDo->getId());
		// Set pagination limits
		$limits = $query->getLimits();
		$limitLower = $limits->getLower();
		$limitUpper = $limits->getUpper();
		// Set pagination limits
		$whereCondition = "FROM content WHERE id_structure = '$idStructure'";
		$select = "SELECT id, title, data $whereCondition LIMIT $limitLower, $limitUpper";
		$selectCount = "SELECT count(*) as total $whereCondition";

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
		$dbResult = $this->mysqli->query($selectCount);
		$data = $dbResult->fetch_assoc();
		$limits->setTotal($data['total']);
		$result->setLimits($limits);

		return $result;
	}

	private function loadEditorSearch($structureDo, $query) {
		// SELECT id, title, data FROM content WHERE id_structure = 'directo' AND title LIKE '%foo%';
		// SELECT distinct c.id as id, title, data FROM content as c, content_tag as ct WHERE
		//  (title LIKE '%zzz%' OR alias_id LIKE '%zzz%' OR
		//  (ct.tag = 'zzz' AND c.id = ct.id)) AND c.id_structure = 'una_mysql' LIMIT 0, 20"
		// Set pagination limits
		$limits = $query->getLimits();
		$limitLower = $limits->getLower();
		$limitUpper = $limits->getUpper();
		$filter = array();
		if(isset($query->getCondition()['title'])) {
			$search = $this->mysqli->real_escape_string($query->getCondition()['title']);
			$filter['title'] = "(title LIKE '%".$search."%' OR alias_id LIKE '%".$search."%' OR (ct.tag = '$search' AND c.id = ct.id))";
		}
		if(isset($query->getCondition()['idStructure'])) {
			$search = $this->mysqli->real_escape_string($query->getCondition()['idStructure']);
			$filter['id_structure'] = "id_structure = '".$search."'";
		}
		$where = '';
		if ($filter) {
			$whereCondition = 'FROM content as c, content_tag as ct WHERE '.implode(' AND ', $filter);
		}
		$select = "SELECT distinct c.id as id, title, data $whereCondition LIMIT $limitLower, $limitUpper";

		$selectCount = "SELECT count(distinct c.id) as total $whereCondition";

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
		$dbResult = $this->mysqli->query($selectCount);
		$data = $dbResult->fetch_assoc();
		$limits->setTotal($data['total']);
		$result->setLimits($limits);

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
	private function countAliasId($structureDo, $query) {
		//SELECT count(*) FROM content WHERE alias_id = '$aliasId'
		if($query->getCondition('alias_id')) {
			$aliasId = $this->mysqli->real_escape_string($query->getCondition('alias_id'));
			$select = "SELECT count(*) as total FROM content WHERE alias_id = '$aliasId'";
			$total = 0;

			if ($dbResult = $this->mysqli->query($select)) {
				while($obj = $dbResult->fetch_object()){
					$total = $obj->total;
				}
			}

			return $total;
		}
		else {
			return 0;
		}
	}
	private function difuseAliasId($structureDo, $id, $filters = []) {
		// SELECT id, id_structure, alias_id FROM content WHERE alias_id IN ('alias','alias/dos','alias/dos/tres','alias/dos/tres/cuatro') AND id_structure = 'contenido_my sql'  ORDER BY alias_id DESC;
		// Select elements with alias-id start match ie. one match with one/two
		$aDirectoryParts = explode('/', $id);
		$aDirectory = [];
		$directoryTmp = '';
		$separator = ''; // First time is '' next is '/'
		foreach ($aDirectoryParts as $directory) {
			$directoryTmp .= $separator.$this->mysqli->real_escape_string($directory);
			$separator = '/';
			$aDirectory[] = $directoryTmp;
		}

		$filter = "IN ('" . implode("','", $aDirectory). "')";
		if ($structureDo->getId()) {
			$filter .= " AND id_structure = '".$this->mysqli->real_escape_string($structureDo->getId())."'";
		}
		$select = "SELECT id, id_structure, alias_id, UNIX_TIMESTAMP(period_of_validity_start) as period_of_validity_start, UNIX_TIMESTAMP(period_of_validity_end) as  period_of_validity_end FROM content WHERE alias_id $filter  ORDER BY alias_id DESC";

		$result = [];
		$contentCheckValidity = new ContentDo(); // Object from date validity tester
		$validityDate = isset($filters['validity-date']) ? $filters['validity-date'] : null;
		if ($dbResult = $this->mysqli->query($select)) {
			while($obj = $dbResult->fetch_object()){
				$documentFound = [];
				$documentFound['period_of_validity']['start'] = is_null($obj->period_of_validity_start) ? -INF : $obj->period_of_validity_start;
				$documentFound['period_of_validity']['end'] = is_null($obj->period_of_validity_end) ? INF : $obj->period_of_validity_end;
				$contentCheckValidity->setPeriodOfValidity($documentFound['period_of_validity']);
				if ($contentCheckValidity->checkValidityDate($validityDate)) {
					$result[] = [
						'id' =>  $obj->id,
						'id_structure' => $obj->id_structure,
						'alias_id' => $obj->alias_id
					];
				}
			}
		}
		return $result;
	}

	private function metaInformation($structureDo, $id, $filters = []) {
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}

		$contentFound = null;
		try {
			$id = $this->mysqli->real_escape_string($id);
			$select = "SELECT id, title, id_structure, UNIX_TIMESTAMP(period_of_validity_start) as period_of_validity_start, UNIX_TIMESTAMP(period_of_validity_end) as period_of_validity_end, alias_id, data FROM content WHERE id = '$id'";
			if ($dbResult = $this->mysqli->query($select)) {
				//$result = new ContentsDo();
				while($obj = $dbResult->fetch_object()){
					$documentFound = array();
					$documentFound['id'] = $obj->id;
					$documentFound['title'] = $obj->title;
					$documentFound['period_of_validity']['start'] = is_null($obj->period_of_validity_start) ? -INF : $obj->period_of_validity_start;
					$documentFound['period_of_validity']['end'] = is_null($obj->period_of_validity_end) ? INF : $obj->period_of_validity_end;
					$documentFound['alias_id'] = $obj->alias_id;
					$documentFound['data'] = unserialize($obj->data);

					// Tags
					$select = "SELECT id, tag FROM content_tag WHERE id = '$id'";
					if ($dbResult = $this->mysqli->query($select)) {
						$aTags = [];
						while($objTag = $dbResult->fetch_object()){
							$aTags[] = $objTag->tag;
						}
						$documentFound['tags'] = $aTags;
					}

					$structureDo = $this->getStructure($obj->id_structure); // Recreate structure information
					$contentFound = new ContentDo();
					$contentFound->load($documentFound, $structureDo);
				}
			}
		}
		catch( \Exception $e ) {
			$contentFound = null;
		}

		//return $result;
		return $contentFound;
	}
	public function getIndexes() {
		throw new PersistentManagerMySqlException("Not implemented", self::GET_INDEXES_FAILED);
	}
	public function createIndexes() {
		throw new PersistentManagerMySqlException("Not implemented", self::CREATE_INDEXES_FAILED);
	}
	public function dropIndexes() {
		throw new PersistentManagerMySqlException("Not implemented", self::DROP_INDEXES_FAILED);
	}
}