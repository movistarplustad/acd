<?php
namespace Acd\Model;

class PersistentStorageQueryTypeNotImplemented extends \Exception {} // TODO mover a sitio común
class PersistentManagerMongoDB implements iPersistentManager
{
	const STRUCTURE_TYPE_COLLECTION = '_collection';
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
		//dd($contentDo);
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		// TODO	 revisar
		// TODO Faltan guardar las relaciones
		$aIdChildsRelated = []; // Store all related content id
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
				//d("Relation", $value, $insert['data'][$key]['id_structure'], $value['id_structure']);
				$insert['data'][$key]['ref'] = \MongoDBRef::create('content', new \MongoId($value['ref']));
				$insert['data'][$key]['id_structure'] = $value['id_structure'];

				$aIdChildsRelated[] = $insert['data'][$key]['ref'] ; // For table relations
			}
			elseif (is_array($value)) {
				// Collection relation
				foreach ($value as $id => $item) {
					//d("Collection", $item);
					$value[$id]['ref'] = \MongoDBRef::create('content', new \MongoId($item['ref']));
					$value[$id]['id_structure'] = $item['id_structure'];


					$aIdChildsRelated[] = $value[$id]['ref']; // For table relations
				}
				$insert['data'][$key]= [
					'id_structure' => self::STRUCTURE_TYPE_COLLECTION,
					'ref' => $value
					];
			}
		}
		unset ($insert['id']);
		$insert['save_ts'] = time(); // Log, timestamp for last save / update operation
		if ($contentDo->getId()) {
			$oId = new \MongoId($contentDo->getId());

			$mongoCollection->update(array('_id' => $oId), array('$set' => $insert));
		}
		else {
			$mongoCollection->insert($insert);
			$contentDo->setId($insert['_id']);
			$oId = new \MongoId($insert['_id']);
		}

		$this->updateRelations($db, \MongoDBRef::create('content', $oId), $aIdChildsRelated);

		return $contentDo;
	}
	private function updateRelations($db, $parent, $children) {
		// Redundant cache content relations 
		$mongoCollection = $db->selectCollection('relation');
		//d("Padre . Hijos", $parent, $children);
		// emptying old relations & add news
		$mongoCollection->remove(array('parent' => $parent));
		foreach ($children as $child) {
			$mongoCollection->insert([
				'parent' => $parent,
				'child' => $child
				]);
		}
	}

	public function delete($structureDo, $idContent) {
		// TODO No permitir elminar elementos que están asociados a algo
		if ($this->isInitialized($structureDo)) {
		// TODO revisar
			$mongo = new \MongoClient();
			$db = $mongo->acd;
			$mongoCollection = $db->selectCollection('content');
			$oId = new \MongoId($idContent);
			$mongoCollection->remove(array('_id' => $oId));
		}

		$this->updateRelations($db,  \MongoDBRef::create('content', $oId), array());
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
		//var_dump($DBRef);
		return [
				'ref' => (string) $DBRef['ref']['$id'],
				'id_structure' => $DBRef['id_structure']
				// value
				// TODO instance
			];
	}
	/* 
	Sample of docs
	Simple relation
	----------------------
	{
	        "_id" : ObjectId("54f5c87f6803fa670c8b4567"),
	        "data" : {
	                "titulo" : "Foto 1",
	                "imagen" : {
	                        "ref" : DBRef("content", ObjectId("54f5c8016803fa7c058b4568")),
	                        "id_structure" : "imagen",
	                },
	                "enlace" : {
	                        "ref" : DBRef("content", ObjectId("54f5c82b6803fabb068b4567")),
	                        "id_structure" : "enlace",
	                }
	        },
	        "id_structure" : "item_mosaico",
	        "title" : "El segundo del mosaico"
	}

	Collection relation
	---------------------------
	{
	        "_id" : ObjectId("54f5abce6803fa59088b4567"),
	        "data" : {
	                "elementos" : {
	                        "id_structure" : "_collection",
	                        "ref" : [
	                                {
	                                        "ref" : DBRef("content", ObjectId("54f5a6f66803fa7c058b4567")),
	                                        "id_structure" : "item_mosaico"
	                                },
	                                {
	                                        "ref" : DBRef("content", ObjectId("54f5c87f6803fa670c8b4567")),
	                                        "id_structure" : "item_mosaico"
	                                }
	                        ]
	                },
	                "titulo" : "¡Un mosaico!"
	        },
	        "id_structure" : "mosaico",
	        "title" : "Primer mosaico"
	}

	*/
	private function normalizeDocument($document) {
		//d($document);
		$document['id'] = (string) $document['_id'];

		foreach ($document['data'] as $key => $value) {
			// External content
			if(isset($value['ref']) && \MongoDBRef::isRef($value['ref'])) {
				$document['data'][$key] = $this->normalizeRef($value);
			}
			// Collection
			elseif (is_array($value) && isset($value['id_structure']) && $value['id_structure'] === self::STRUCTURE_TYPE_COLLECTION) {
				// Atention: $value for simple relation it is also an array
				$normalizedRef = array();
				foreach ($value['ref'] as $collectionValue) {
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