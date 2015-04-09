<?php
namespace Acd\Model;

class PersistentStorageUnknownInvalidException extends \exception {}
class ContentLoader extends StructureDo
{
	private $bStructureLoaded;
	private $filters; // TODO
	private $limitis;
	private $persistentManager; // Load / update / save to persitent repository
	public function __construct() {
		$this->setStructureLoaded(false);
		$this->setLimits(new Limits());
		parent::__construct();
	}	

	/* Setters and getters attributes */
	public function getStructureLoaded() {
		return $this->bStructureLoaded;
	}
	public function setStructureLoaded($bStructureLoaded) {
		$this->bStructureLoaded = $bStructureLoaded;
	}
	private function getManager() {
		switch ($this->getStorage()) {
			case \Acd\conf::$STORAGE_TYPE_TEXTPLAIN:
				//echo "tipo texto";
				return new PersistentManagerTextPlain();
				break;
			case \Acd\conf::$STORAGE_TYPE_MONGODB:
				//echo "tipo mongo";
				return new PersistentManagerMongoDB();
				break;
			case \Acd\conf::$STORAGE_TYPE_MYSQL:
				//echo "tipo mysql";
				return new PersistentManagerMySql();
				break;
			default:
				throw new PersistentStorageUnknownInvalidException("Invalid type of persistent storage ".$this->getStorage().".");
				break;
		}
	}
	// TODO Need loadContent and loadConents?
	public function loadContents($method, $params = null) {
		switch ($method) {
			case 'id+countParents':
				$content = $this->loadContents('id', $params);

				// Set the relations number to content, and content is contents->get($id)
				//$content->get($params)->setCountParents($this->loadContents('countParents', $params));
				$content->setCountParents($this->loadContents('countParents', $params));

				return $content;
			break;
			default:
				/* Get metainformation */
				if (!$this->getStructureLoaded()) {
					$this->setStructureLoaded($this->loadFromFile());
				}
				$persistentManager = $this->getManager();

				$query = new Query();
				$query->setType($method);
				$query->setCondition($params);
				$query->setLimits($this->getLimits());
				return $persistentManager->load($this, $query);
			break;
		}

	}
	public function saveContent($contentDo) {
		/* Get metainformation */
		if (!$this->getStructureLoaded()) {
			$this->setStructureLoaded($this->loadFromFile());
		}
		$persistentManager = $this->getManager();
		$NewContentDo = $persistentManager->save($this, $contentDo);
		return $NewContentDo;
	}
	public function deleteContent($id) {
		if (!$this->getStructureLoaded()) {
			$this->setStructureLoaded($this->loadFromFile());
		}
		$persistentManager = $this->getManager();

		return $persistentManager->delete($this, $id);
	}

	public function setLimits($limits) {
		$this->limits = $limits;
	}
	public function getLimits() {
		return $this->limits;
	}
}