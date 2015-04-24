<?php
namespace Acd\Model;

class PersistentStorageQueryTypeNotImplemented extends \Exception {} // TODO mover a sitio común
class PersistentManagerMongoDBException extends \exception {} // TODO Unificar
class PersistentManagerMongoDB implements iPersistentManager
{
	private $db;
	const STRUCTURE_TYPE_COLLECTION = '_collection';
	private $structuresCache;
	public function initialize($structureDo) {
		$mongo = new \MongoClient(\Acd\conf::$MONGODB_SERVER);
		$this->db = $mongo->acd;
		//$db->createCollection('content', false);
	}
	public function isInitialized($structureDo) {
		return isset($this->db);
	}
	public function load($structureDo, $query) {
		//if ($this->isInitialized($structureDo)) {
			switch ($query->getType()) {
				case 'id':
					return $this->loadById($structureDo, $query->getCondition());
					break;
				case 'id-deep':
					return $this->loadIdDepth($structureDo, $query->getCondition('id'), $query->getDepth());
					break;
				case 'tag-one-deep': // First element matching with tag
					return $this->loadTagOneDepth($structureDo, $query->getCondition('tags'), $query->getDepth());
					break;
				case 'all':
					return $this->loadAll($structureDo, $query);
					break;
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
		//}
		//else {
		//	// Structure empty
		//	return new Collection();
		//}
	}
	public function save($structureDo, $contentDo) {
		//dd($contentDo);
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		// TODO	 revisar
		$aIdChildsRelated = []; // Store all related content id
		$mongoCollection = $this->db->selectCollection('content');
		$insert = $contentDo->tokenizeData();
		$insert['id_structure'] = $structureDo->getId();
		// Replace relations by MongoDBRefs
		//d($structureDo->getFields(), $contentDo->getFields());
		$bChildsRelated = false;
		$oIdChildsRelated = [];
		foreach ($structureDo->getFields() as $field) {
			$key = $field->getId();
			$value = $insert['data'][$key];
			switch ($field->getType()) {
				case $field::TYPE_CONTENT:
					// Relation
					if($value['ref']) {
						$insert['data'][$key]['ref'] = \MongoDBRef::create('content', new \MongoId($value['ref']));
						$insert['data'][$key]['id_structure'] = $value['id_structure'];
	
						$oIdChildsRelated[] = $insert['data'][$key]['ref']; // For table relations
					}
					$bChildsRelated = true;
					break;
				case $field::TYPE_COLLECTION:
					// Collection relation
					if(!is_array($value)) {
						$value = [];
					}
					foreach ($value as $id => $item) {
						$value[$id]['ref'] = \MongoDBRef::create('content', new \MongoId($item['ref']));
						$value[$id]['id_structure'] = $item['id_structure'];


						$oIdChildsRelated[] = $value[$id]['ref']; // For table relations
					}
					$insert['data'][$key]= [
						'id_structure' => self::STRUCTURE_TYPE_COLLECTION,
						'ref' => $value
						];
					$bChildsRelated = true;
					break;
				default:
					// Other type no treatment is necessary
					break;
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
		if($bChildsRelated) {
			$this->updateRelations($this->db, \MongoDBRef::create('content', $oId), $oIdChildsRelated);
		}

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
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		// TODO revisar
		// It is not allowed to delete a content with relations, beacause break integrity
		$query = new Query();
		$query->setType('countParents');
		$query->setCondition($idContent);
		$numRelations = $this->countParents($structureDo, $query);
		if ($numRelations > 0) {
			throw new PersistentManagerMongoDBException("Delete failed, the content has $numRelations relationships", self::DELETE_FAILED);
		}
		else {
			$mongoCollection = $this->db->selectCollection('content');
			$oId = new \MongoId($idContent);
			$mongoCollection->remove(array('_id' => $oId));
		}

		$this->updateRelations($this->db,  \MongoDBRef::create('content', $oId), array());
	}

	private function loadById($structureDo, $id) {
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		//$id = $query->getCondition();
		// TODO revisar
		$mongoCollection = $this->db->selectCollection('content');
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
			$contentFound->load($documentFound, $structureDo);
			//+d($documentFound);
			//+d($contentFound);
			//$result = new ContentsDo();
			//$result->add($contentFound, $id);
		}
		catch( \Exception $e ) {
			//$result = null;
			$contentFound = null;
		}

		//return $result;
		return $contentFound;
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
	private function loadTagOneDepth ($structureDo, $tags, $depth) {
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		$mongoCollection = $this->db->selectCollection('content');
		// db.content.find({"tags":{ $in : ["portadacine"]}, "id_structure" : "padre"}).pretty()
		$documentFound = $mongoCollection->findOne(array('tags' => array('$in' => $tags), 'id_structure' => $structureDo->getId()));
		if ($documentFound) {
			return $this->loadIdDepth ($structureDo, (string) $documentFound['_id'], $depth);
		}
		else {
			return null;
		}
	}
	private function loadIdDepth ($structureDo, $idContent, $depth) {
		if ($depth > 0) {
			$depth--;
			$content = $this->loadById($structureDo, $idContent);
			$fields = $content->getFields();
			// Walk fields and fill their values
			foreach ($fields as $field) {
				switch($field->getType()) {
					case 'content' :
						// Has relation info?
						if($field->getValue() && $field->getValue()['id_structure']) {
							$structureTmp = $this->getStructure($field->getValue()['id_structure']);
							$field->setValue($this->loadIdDepth ($structureTmp, $field->getValue()['ref'], $depth));
						}
						break;
					case 'collection' :
						$newVal = [];
						foreach ($field->getValue() as $itemCollection) {
							$structureTmp = $this->getStructure($itemCollection['id_structure']);
							$newVal[] = $this->loadIdDepth ($structureTmp, $itemCollection['ref'], $depth);
						}

						$field->setValue($newVal);
						break;
				}
			}

			return $content;
		}
	}

	private function loadAll($structureDo, $query) {
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		$mongoCollection = $this->db->selectCollection('content');
		$byStructureQuery = array('id_structure' => $structureDo->getId());

		$cursor = $mongoCollection->find($byStructureQuery);
		$cursor->sort(array( '_id' => -1));
		$result = new ContentsDo();
		foreach ($cursor as $documentFound) {
			$documentFound = $this->normalizeDocument($documentFound);
			$contentFound = new ContentDo();
			$contentFound->load($documentFound, $structureDo);
			$result->add($contentFound, $documentFound['id']);
		}
		//TODO revisar
		// Purge to limits
		//$limits = $query->getLimits();
		//$limits->setTotal(count($aContents));

		return $result;
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

		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		$mongoCollection = $this->db->selectCollection('content');
		$cursor = $mongoCollection->find($filter);
		$cursor->sort(array( '_id' => -1));
		$result = new ContentsDo();
		foreach ($cursor as $documentFound) {
			$documentFound = $this->normalizeDocument($documentFound);
			$contentFound = new ContentDo();
			$contentFound->load($documentFound, $structureDo);
			$result->add($contentFound, $documentFound['id']);
		}
		return $result;
	}

	private function countParents($structureDo, $query) {
		$id = $query->getCondition();
		$filter = ['child' => \MongoDBRef::create('content', new \MongoId($id))];

		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		$mongoCollection = $this->db->selectCollection('relation');
		$cursor = $mongoCollection->find($filter);

		return $cursor->count();
		// db.relation.find({"child" : DBRef("content", ObjectId("5510618a06b13a931ca41c07"))}).pretty()
	}
}