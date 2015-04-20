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
	// Load structure
	public function loadStructure() {
		/* Get metainformation */
		if (!$this->getStructureLoaded()) {
			$this->setStructureLoaded($this->loadFromFile());
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
				$this->loadStructure();
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
		$this->loadStructure();
		$persistentManager = $this->getManager();
		$contentDo = $this->saveUpload($contentDo);
		$NewContentDo = $persistentManager->save($this, $contentDo);
		return $NewContentDo;
	}
	private function saveUpload($contentDo) {
		$contentId = $contentDo->getId();
		$structureId = $contentDo->getIdStructure();
		foreach ($contentDo->getFields() as $field) {
			if ($field->getType() === $field::TYPE_FILE){
				$uploadData = $field->getValue();
				$fieldId = $field->getId();
				$fileName = md5(urlencode($structureId ).'/'.urlencode($fieldId).'/'.$contentId);
				$relativePath = substr($fileName, 0, 3);
				$destinationPath = \Acd\conf::$DATA_CONTENT_PATH.'/'.$relativePath;
				if($uploadData['delete']) {
					if (is_writable($destinationPath.'/'.$fileName)){
						unlink ($destinationPath.'/'.$fileName);
						if (count(scandir($destinationPath)) == 2) {
							rmdir($destinationPath);
						}
						// after the attribute 'delete' is removed
						$uploadData['alt'] = '';
						$uploadData['value'] = '';
						$uploadData['original_name'] = '';
						$uploadData['type'] = '';
						$uploadData['size'] = '';
					}
				}
				if($uploadData['tmp_name'] && $uploadData['size']) {
					if (!is_dir($destinationPath)){
						mkdir($destinationPath, 0755, true);
					}
					move_uploaded_file($uploadData['tmp_name'], $destinationPath.'/'.$fileName);
					$uploadData['value'] = $fileName;
				}
				unset($uploadData['tmp_name']);
				unset($uploadData['delete']);
				$field->setValue($uploadData);
				//d($contentId, $field, $uploadData, $field->getValue());
			}
		}

		return $contentDo;
	}
	public function deleteContent($id) {
		$this->loadStructure();
		$persistentManager = $this->getManager();

		$contentDo = $this->loadContents('id', $id);
		$this->deleteUpload($contentDo);

		return $persistentManager->delete($this, $id);
	}
	private function deleteUpload($contentDo) {
		$contentId = $contentDo->getId();
		$structureId = $contentDo->getIdStructure();
		foreach ($contentDo->getFields() as $field) {
			if ($field->getType() === $field::TYPE_FILE){
				// TODO unify with saveUpload
				$fieldId = $field->getId();
				$fileName = md5(urlencode($structureId ).'/'.urlencode($fieldId).'/'.$contentId);
				$relativePath = substr($fileName, 0, 3);
				$destinationPath = \Acd\conf::$DATA_CONTENT_PATH.'/'.$relativePath;
				if (is_writable($destinationPath.'/'.$fileName)){
					unlink ($destinationPath.'/'.$fileName);
					if (count(scandir($destinationPath)) == 2) {
						rmdir($destinationPath);
					}
				}
			}
		}
	}

	public function setLimits($limits) {
		$this->limits = $limits;
	}
	public function getLimits() {
		return $this->limits;
	}
}