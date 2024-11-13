<?php

namespace Acd\Model;

use Acd\Model\Exception\ContentLoaderException;
use Acd\Model\Exception\PersistentStorageUnknownInvalidException;

class ContentLoader extends StructureDo
{
	const LOAD_ONE = 'ONE';
	const LOAD_MULTIPLE = 'MULTIPLE';
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
	public function getStructureLoaded()
	{
		return $this->bStructureLoaded;
	}
	public function setStructureLoaded($bStructureLoaded)
	{
		$this->bStructureLoaded = $bStructureLoaded;
	}
	private function getManager()
	{
		return $this->getStorageManager($this->getStorage());
		// switch ($this->getStorage()) {
		// 	case $_ENV['ACD_STORAGE_TYPE_TEXTPLAIN:
		// 		//echo "tipo texto";
		// 		return new PersistentManagerTextPlain();
		// 		break;
		// 	case $_ENV['ACD_STORAGE_TYPE_MONGODB_LEGACY:
		// 		//echo "tipo mongo legacy";
		// 		return new PersistentManagerMongoDBLegacy();
		// 		break;
		// 	case $_ENV['ACD_STORAGE_TYPE_MONGODB:
		// 		//echo "tipo mongo";
		// 		return new PersistentManagerMongoDB();
		// 		break;
		// 	case $_ENV['ACD_STORAGE_TYPE_MYSQL:
		// 		//echo "tipo mysql";
		// 		return new PersistentManagerMySql();
		// 		break;
		// 	default:
		// 		throw new PersistentStorageUnknownInvalidException("Invalid type of persistent storage " . $this->getStorage() . ".");
		// 		break;
		// }
	}

	private function getStorageManager(string $storage)
	{
		switch ($storage) {
			case $_ENV[ 'ACD_STORAGE_TYPE_TEXTPLAIN']:
				//echo "tipo texto";
				return new PersistentManagerTextPlain();
				break;
			case $_ENV[ 'ACD_STORAGE_TYPE_MONGODB_LEGACY']:
				//echo "tipo mongo legacy";
				return new PersistentManagerMongoDBLegacy();
				break;
			case $_ENV[ 'ACD_STORAGE_TYPE_MONGODB']:
				//echo "tipo mongo";
				return new PersistentManagerMongoDB();
				break;
			case $_ENV[ 'ACD_STORAGE_TYPE_MYSQL']:
				//echo "tipo mysql";
				return new PersistentManagerMySql();
				break;
			default:
				throw new PersistentStorageUnknownInvalidException("Invalid type of persistent storage " . $this->getStorage() . ".");
				break;
		}
	}

	private function getManagers()
	{
		// Return all active persistent managers
		// TODO Unify with getManager -> similar code
		$persistentManagers = [];
		foreach ($_ENV['ACD_STORAGE_TYPES'] as $idStorage => $storageType) {
			if (!$storageType['disabled']) {
				$persistentManagers[] = $this->getStorageManager($idStorage);
				// switch ($idStorage) {
				// 	case $_ENV['ACD_STORAGE_TYPE_TEXTPLAIN:
				// 		$persistentManagers[] = new PersistentManagerTextPlain();
				// 		break;
				// 	case $_ENV['ACD_STORAGE_TYPE_MONGODB_LEGACY:
				// 		$persistentManagers[] = new PersistentManagerMongoDBLegacy();
				// 		break;
				// 	case $_ENV['ACD_STORAGE_TYPE_MONGODB:
				// 		$persistentManagers[] = new PersistentManagerMongoDB();
				// 		break;
				// 	case $_ENV['ACD_STORAGE_TYPE_MYSQL:
				// 		$persistentManagers[] = new PersistentManagerMySql();
				// 		break;
				// 	default:
				// 		throw new PersistentStorageUnknownInvalidException("Invalid type of persistent storage " . $this->getStorage() . ".");
				// 		break;
				// }
			}
		}
		return $persistentManagers;
	}
	// Load structure
	public function loadStructure()
	{
		/* Get metainformation */
		if (!$this->getStructureLoaded()) {
			$this->setStructureLoaded($this->loadFromFile());
		}
	}
	// Aseptic loadContent/loadContents return ContentsDo, ContentDo, null, etc loadContent and loadConents resolve this situation
	private function _loadContents($method, $resultType, $params = null)
	{
		switch ($method) {
			case 'id+countParents':
				//$content = $this->loadContents('id', $params);
				$contents = $this->loadContents('id-deep', ['id' => $params, 'depth' => 2]);
				$content = $contents->one();

				if ($content) {
					// Set the relations number to content, and content is contents->get($id)
					//$content->get($params)->setCountParents($this->loadContents('countParents', $params));
					$content->setCountParents($this->_loadContents('countParents', ContentLoader::LOAD_MULTIPLE, $params));
					$content->setCountAliasId($this->_loadContents('count-alias-id', ContentLoader::LOAD_MULTIPLE, ['alias_id' => $content->getAliasId()]));
				}

				return $content;
				break;
			case 'difuse-alias-id':
			case 'meta-information':
				if ($this->getId()) {
					$this->loadStructure();
					$persistentManagers = [];
					$persistentManagers[] = $this->getManager();
				} else {
					$persistentManagers = $this->getManagers();
				}
				$query = new Query();
				$query->setType($method);
				$query->setCondition($params);
				$result = null;
				// Return only one content
				foreach ($persistentManagers as $persistentManager) {
					if (!$result) {
						$result = $persistentManager->load($this, $query);
					}
				}
				return $result;
				break;
			default:
				/* Get metainformation */
				$this->loadStructure();
				$persistentManager = $this->getManager();

				$query = new Query();
				$query->setType($method);
				$query->setCondition($params);
				if ($resultType === contentLoader::LOAD_ONE) {
					$this->getLimits()->setStep(1);
				}
				$query->setLimits($this->getLimits());
				return $persistentManager->load($this, $query);
				break;
		}
	}
	// Return always a ContentsDo collection or throw exception
	public function loadContents($method, $params = null)
	{
		$result = $this->_loadContents($method, ContentLoader::LOAD_MULTIPLE, $params);
		if (is_object($result) && get_class($result) === 'Acd\Model\ContentDo') {
			// Add ContentDo to ContentsDo collection
			$resultContents = new ContentsDo();
			$resultContents->add($result);
			return $resultContents;
		} elseif (is_object($result) && get_class($result) === 'Acd\Model\ContentsDo') {
			// It's a ContentsDo
			return $result;
		} else {
			// Error is not a ContentDo or ContentsDo
			throw new ContentLoaderException("Error in loadContents '$method' does not support load return ContentsDo", 1);
			return $result;
		}
	}
	// Return ContentDo object, other value like number or null
	public function loadContent($method, $params = null)
	{
		$result = $this->_loadContents($method, ContentLoader::LOAD_ONE, $params);
		if (is_object($result) && get_class($result) === 'Acd\Model\ContentsDo') {
			// Extract first ContentDo from collection
			return $result->one();
		} else {
			// It's a ContentDo or an expected value (number in a count query)
			return $result;
		}
	}
	public function saveContent($contentDo)
	{
		/* Get metainformation */
		$this->loadStructure();
		$persistentManager = $this->getManager();
		$contentDo = $this->saveUpload($contentDo);
		$NewContentDo = $persistentManager->save($this, $contentDo);
		return $NewContentDo;
	}
	private function saveUpload($contentDo)
	{
		$contentId = $contentDo->getId();
		$structureId = $contentDo->getIdStructure();
		$filesystem = File::getFileSystemFromEnvConfiguration();
		foreach ($contentDo->getFields() as $field) {
			if ($field->getType() === $field::TYPE_FILE) {
				$uploadData = $field->getValue();
				$fieldId = $field->getId();
				$idFile = md5(urlencode($structureId) . '/' . urlencode($fieldId) . '/' . $contentId);
				if (isset($uploadData['delete']) && $uploadData['delete']) {
					try {
						$filesystem->delete($idFile);
						// after the attribute 'delete' is removed
						$uploadData['alt'] = '';
						$uploadData['value'] = '';
						$uploadData['original_name'] = '';
						$uploadData['type'] = '';
						$uploadData['size'] = '';
					} catch (FilesystemException | UnableToDeleteFile $exception) {
						throw new ContentLoaderException($exception->getMessage());
					}
				}
				if (isset($uploadData['tmp_name']) && $uploadData['tmp_name'] && isset($uploadData['size']) && $uploadData['size']) {
					try {
						$contents = file_get_contents($uploadData['tmp_name']);
						$config = [];
						if(!empty($uploadData['type'])) {
							$config['mimetype'] = $uploadData['type'];
						}
						switch ($uploadData['origin']) {
							case $_ENV[ 'ACD_DATA_CONTENT_BINARY_ORIGIN_FORM_UPLOAD']:
								$filesystem->write($idFile, $contents, $config);
								unlink($uploadData['tmp_name']);
								break;
							case $_ENV['ACD_DATA_CONTENT_BINARY_ORIGIN_FORM_PATH ']:
								$filesystem->write($idFile, $contents, $config);
								break;
							default:
								throw new ContentLoaderException('Error on save file, origin method not supported.', 1);
								break;
						}
						$uploadData['value'] = $idFile;
						unset($uploadData['tmp_name']);
						unset($uploadData['delete']);
						$field->setValue($uploadData);
					} catch (FilesystemException | UnableToWriteFile $exception) {
						throw new ContentLoaderException($exception->getMessage(), 1);
					}
				}
			}
		}

		return $contentDo;
	}
	public function deleteContent($id)
	{
		$this->loadStructure();
		$persistentManager = $this->getManager();

		$contentDo = $this->loadContent('id', $id);
		$this->deleteUpload($contentDo);

		return $persistentManager->delete($this, $id);
	}
	private function deleteUpload($contentDo)
	{
		$contentId = $contentDo->getId();
		$structureId = $contentDo->getIdStructure();
		foreach ($contentDo->getFields() as $field) {
			if ($field->getType() === $field::TYPE_FILE) {
				// TODO unify with saveUpload
				$fieldId = $field->getId();
				$idFile = md5(urlencode($structureId) . '/' . urlencode($fieldId) . '/' . $contentId);
				$filesystem = File::getFileSystemFromEnvConfiguration();
				try {
					$filesystem->delete($idFile);
				} catch (FilesystemException | UnableToDeleteFile $exception) {
					throw new ContentLoaderException($exception->getMessage());
				}
			}
		}
	}
	// Installation
	public function getIndexes()
	{
		// For each manager it returns a list of indexes
		$indexes = [];
		foreach ($this->getManagers() as $persistentManager) {
			$indexes[get_class($persistentManager)] = $persistentManager->getIndexes();
		}
		return $indexes;
	}
	public function createIndexes(): array
	{
		// For each manager it returns a list of indexes
		$indexes = [];
		foreach ($this->getManagers() as $persistentManager) {
			$indexes[get_class($persistentManager)] = $persistentManager->createIndexes();
		}
		return $indexes;
	}
	public function dropIndexes(): array
	{
		// For each manager it returns a list of indexes
		$indexes = [];
		foreach ($this->getManagers() as $persistentManager) {
			$indexes[get_class($persistentManager)] = $persistentManager->dropIndexes();
		}
		return $indexes;
	}

	public function setLimits($limits)
	{
		$this->limits = $limits;
	}
	public function getLimits()
	{
		return $this->limits;
	}
}
