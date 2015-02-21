<?php
namespace Acd\Model;

class PersistentStorageQueryTypeNotImplemented extends \exception {}
class MongoConnectionException extends \exception {}
class MongoDocumentNotFound extends \exception {}
class PersistentManagerMongoDB implements iPersistentManager
{
	public function initialize($structureDo) {
		$mongo = new \MongoClient();
		$db = $mongo->acd;
		$db->createCollection('content', false);
	}
	public function isInitialized($structureDo) {
		try {
			$mongo = new \MongoClient();
			$db = $mongo->acd;
			//echo "isInitialized";

			return true;
		}
		catch ( MongoConnectionException $e ) {
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
		echo "falta update ".$contentDo->getId();
		$mongo = new \MongoClient();
		$db = $mongo->acd;
		//$mongoCollection = $db->selectCollection($structureDo->getId());
		$mongoCollection = $db->selectCollection('content');
		$insert = $contentDo->tokenizeData();
		$insert['id_structure'] = $structureDo->getId();
		unset ($insert['id']);
		if ($contentDo->getId()) {
			$oId = new \MongoId($contentDo->getId());

			$mongoCollection->update(array('_id' => $oId), array('$set' => $insert));
		}
		else {
			$mongoCollection->insert($insert);
			$contentDo->setId($insert['_id']);
		}
		
		return $contentDo;
	}

	public function delete($structureDo, $idContent) {
		if ($this->isInitialized($structureDo)) {
		// TODO revisar
			$mongo = new \MongoClient();
			$db = $mongo->acd;
			$mongoCollection = $db->selectCollection('content');
			$oId = new \MongoId($idContent);
			$mongoCollection->remove(array('_id' => $oId));
		}

	}

	private function loadById($structureDo, $query) {
		$id = $query->getCondition();
		// TODO revisar
		$mongo = new \MongoClient();
		$db = $mongo->acd;
		$mongoCollection = $db->selectCollection('content');
		try {
			$oId = new \MongoId($id);
		}
		catch( \MongoException $e ) {
			return null;
		}
		try {
			$documentFound = $mongoCollection->findOne(array("_id" => $oId));
			$documentFound['id'] = $id;
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
		$mongo = new \MongoClient();
		$db = $mongo->acd;
		$mongoCollection = $db->selectCollection('content');
		$byStructureQuery = array('id_structure' => $structureDo->getId());

		$cursor = $mongoCollection->find($byStructureQuery);
		$result = new ContentsDo();
		foreach ($cursor as $documentFound) {
			$id = $documentFound['_id']->{'$id'};
			$documentFound['id'] = $id;
			$contentFound = new ContentDo();
			$contentFound->load($documentFound, $structureDo->getId());
			$result->add($contentFound, $id);
		}
		//TODO revisar
		// Purge to limits
		$limits = $query->getLimits();
		//$limits->setTotal(count($aContents));

		return $result;
	}
}