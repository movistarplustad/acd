<?php
namespace Acd\Model;

class PersistentManagerTextPlainException extends \exception {} // TODO Unificar
class PersistentManagerTextPlain implements iPersistentManager
{
	private function getStoragePath($structureDo) {
		return \Acd\conf::$DATA_DIR_PATH.'/'.$structureDo->getId().'.json';
	}

	private function getNewId() {
		return uniqid();
	}

	public function initialize($structureDo) {
		$emptyData = json_encode(array());
		$path = $this->getStoragePath($structureDo);
		if (!$handle = fopen($path, 'a')) {
			 echo "Cannot open file ($path)";
			 exit;
		}

		if (fwrite($handle, $emptyData) === FALSE) {
			echo "Cannot write to file ($path)";
			exit;
		}
		fclose($handle);
	}
	public function isInitialized($structureDo) {
		return is_readable($this->getStoragePath($structureDo));
	}
	public function load($structureDo, $query) {
		if ($this->isInitialized($structureDo)) {
			switch ($query->getType()) {
				case 'id':
					return $this->loadById($structureDo, $query);
					break;
				case 'all':
					return $this->loadAll($structureDo, $query);
				case 'editor-search':
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
		else {
			// Structure empty
			return new Collection();
		}
	}
	public function save($structureDo, $contentDo) {
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		/* Set new content */
		/*
		$contentKeyValue = array();
		foreach ($structureDo->getFields() as $key => $field) {
			echo "-".$field->getName() ." - " .$field->getValue()."<br/>";
			$contentKeyValue[$field->getName()] = $field->getValue();
		}
		*/
		/* Add to old data */
		$allElements = $this->_loadAll($structureDo);
		$idContent = $contentDo->getId();
		if (is_null($idContent)) {
			$idContent = $this->getNewId();
			$contentDo->setId($idContent);
		}
		$contentKeyValue = $contentDo->tokenizeData();
		$contentKeyValue['save_ts'] = time(); // Log, timestamp for last save / update operation
		$allElements[$idContent] = $contentKeyValue;

		/* TODO: Se repite en save y delete */
		$jAllElements = json_encode($allElements);
		$path = $this->getStoragePath($structureDo);
		$pathTmp = $path.'.tmp';
		if (!$handle = fopen($pathTmp, 'a')) {
			 echo "Cannot open file ($pathTmp)";
			 exit;
		}

		if (fwrite($handle, $jAllElements) === FALSE) {
			echo "Cannot write to file ($pathTmp)";
			exit;
		}
		fclose($handle);

		rename ($pathTmp, $path);

		return $contentDo;
	}
	public function delete($structureDo, $idContent) {
		if ($this->isInitialized($structureDo)) {
			$allElements = $this->_loadAll($structureDo);
			unset($allElements[$idContent]);
		}

		/* TODO: Se repite en save y delete */
		$jAllElements = json_encode($allElements);
		$path = $this->getStoragePath($structureDo);
		$pathTmp = $path.'.tmp';
		if (!$handle = fopen($pathTmp, 'a')) {
			 echo "Cannot open file ($pathTmp)";
			 exit;
		}

		if (fwrite($handle, $jAllElements) === FALSE) {
			echo "Cannot write to file ($pathTmp)";
			exit;
		}
		fclose($handle);

		rename ($pathTmp, $path);
	}

	/* Internal method from get all contents */
	private function _loadAll($structureDo) {
		$path = $this->getStoragePath($structureDo);
		$content = file_get_contents($path);
		return json_decode($content, true);
	}

	private function loadEditorSearch($structureDo, $query) {
		//db.content.find({"id_structure": "item_mosaico", "title" : /.*quinto.*/i}).pretty()
		$filter = array();
		if(isset($query->getCondition()['title'])) {
			$search = $query->getCondition()['title'];
			$filter['title'] = array('$regex' => new \MongoRegex("/^.*$search.*/i"));
		}
		if(isset($query->getCondition()['idStructure'])) {
			$filter['id_structure'] = $query->getCondition()['idStructure'];
		}

		d("TODO");
		return $result;
	}

	private function countParents($structureDo, $query) {
		//d("TODO");

		return '?';
	}

	private function loadById($structureDo, $query) {
		$id = $query->getCondition();
		$allElements = $this->_loadAll($structureDo);
		if (isset($allElements[$id])) {
			$contentFound = new ContentDo();
			$contentFound->load($allElements[$id], $structureDo);

			return $contentFound;
			//$result = new ContentsDo();
			//$result->add($contentFound, $id);
			//return $result;
		}
	}

	private function loadAll($structureDo, $query) {
		$aContents = $this->_loadAll($structureDo);
		// Purge to limits
		$limits = $query->getLimits();
		$limits->setTotal(count($aContents));
		$contents = new ContentsDo();
		$contents->loadFromArray(array_slice($aContents, $limits->getLower(), $limits->getUpper()), $structureDo);
		$contents->setLimits($limits);

		//d("FER", $aContents, $limits, $contents);
		return $contents;
	}
	public function getIndexes() {
		throw new PersistentManagerTextPlainException("Not implemented", self::GET_INDEXES_FAILED);
	}
	public function createIndexes() {
		throw new PersistentManagerTextPlainException("Not implemented", self::CREATE_INDEXES_FAILED);
	}
	public function dropIndexes() {
		throw new PersistentManagerTextPlainException("Not implemented", self::DROP_INDEXES_FAILED);
	}
}