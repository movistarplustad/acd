<?php
namespace Acd\Model;

class PersistentStorageQueryTypeNotImplemented extends \Exception {} // TODO mover a sitio común
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
		catch ( \Exception $e ) {
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
		// TODO Faltan guardar las relaciones
		$mongo = new \MongoClient();
		$db = $mongo->acd;
		//$mongoCollection = $db->selectCollection($structureDo->getId());
		$mongoCollection = $db->selectCollection('content');
		$insert = $contentDo->tokenizeData();
		$insert['id_structure'] = $structureDo->getId();
		// Replace relations by MongoDBRefs
		foreach ($insert['data'] as $key => $value) {
			if (isset($value['ref'])) {
				// Relation
				$insert['data'][$key]['ref'] = \MongoDBRef::create('content', new \MongoId($value['ref']));
				//d('Relacion', $insert['data'][$key]['ref'] );
			}
			elseif (is_array($value)) {
				foreach ($value as $id => $item) {
					$value[$id]['ref'] = \MongoDBRef::create('content', new \MongoId($item['ref']));
					$value[$id]['id_structure'] = $item['id_structure'];
				}
				$insert['data'][$key]= $value;
			}
		}
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
		catch( \Exception $e ) {
			return null;
		}
		try {
			$documentFound = $mongoCollection->findOne(array("_id" => $oId));
			$documentFound = $this->normalizeDocument($documentFound);
			$contentFound = new ContentDo();
			$contentFound->load($documentFound, $structureDo->getId());
			//+d($documentFound);
			$result = new ContentsDo();
			$result->add($contentFound, $id);
		}
		catch( \Exception $e ) {
			$result = null;
		}

		return $result;
	}

	// Transform a mongodb document to normalized document (aseptic persistent storage)
	//TODO ver por qué no puede meterse dentro de normalizeDocument
	function normalizeRef($DBRef) {
		return [
				'ref' => (string) $DBRef['ref']['$id'],
				'id_structure' => $DBRef['id_structure']
				// value
				// TODO instance
			];
	}
	private function normalizeDocument($document) {
		$document['id'] = (string) $document['_id'];
		foreach ($document['data'] as $key => $value) {
			// External content
			if (isset($value['ref']) && \MongoDBRef::isRef($value['ref'])) {
				$document['data'][$key] = $this->normalizeRef($value);
			}
			// Collection
			elseif (is_array($value)) {
				// Atention: $value for simple relation it is also an array
				$normalizedRef = array();
				foreach ($value as $collectionValue) {
					$normalizedRef[] = $this->normalizeRef($collectionValue);
				}
				$document['data'][$key] = $normalizedRef;
				// TODO instance
			}
		}
		unset($document['_id']);

		return $document;
	}
	private function loadDepth ($structureDo, $query) {
	}

	private function loadAll($structureDo, $query) {
		$mongo = new \MongoClient();
		$db = $mongo->acd;
		$mongoCollection = $db->selectCollection('content');
		$byStructureQuery = array('id_structure' => $structureDo->getId());

		$cursor = $mongoCollection->find($byStructureQuery);
		$result = new ContentsDo();
		foreach ($cursor as $documentFound) {
			$documentFound = $this->normalizeDocument($documentFound);
			$contentFound = new ContentDo();
			$contentFound->load($documentFound, $structureDo->getId());
			$result->add($contentFound, $documentFound['id']);
		}
		//TODO revisar
		// Purge to limits
		//$limits = $query->getLimits();
		//$limits->setTotal(count($aContents));

		return $result;
	}
}