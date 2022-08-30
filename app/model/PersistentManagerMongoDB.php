<?php

namespace Acd\Model;

use \MongoDB\BSON\ObjectID;
use Acd\Model\Exception\PersistentManagerMongoDBException;
use Acd\Model\Mongodb\Filter;

class PersistentManagerMongoDB implements iPersistentManager
{
	private $db;
	const STRUCTURE_TYPE_COLLECTION = '_collection';
	private $structuresCache;
	public function initialize($structureDo)
	{
		//		$mongo = new \MongoClient(\Acd\conf::$MONGODB_SERVER);
		//		$this->db = $mongo->selectDB(\Acd\conf::$MONGODB_DB);

		//TODO: Ver cóm pasarle el servidor, porque si es '' no funciona.
		$mongo = new \MongoDB\Client(\Acd\conf::$MONGODB_SERVER);
		$this->db = $mongo->selectDatabase(\Acd\conf::$MONGODB_DB);
	}
	public function isInitialized($structureDo)
	{
		return isset($this->db);
	}
	private function getFilters($query)
	{
		$filters = [];
		if ($query->getCondition('validity-date')) {
			$filters['validity-date'] = $query->getCondition('validity-date');
		}
		if ($query->getCondition('profile')) {
			$filters['profile'] = $query->getCondition('profile');
		}
		return $filters;
	}
	public function load($structureDo, $query)
	{
		$filters = $this->getFilters($query);
		//if ($this->isInitialized($structureDo)) {
		switch ($query->getType()) {
			case 'id':
				return $this->loadById($structureDo, $query->getCondition());
				break;
			case 'id-deep':
				return $this->loadIdDepth($structureDo, $query->getCondition('id'), $query->getDepth(), $filters);
				break;
			case 'alias-id-deep':
				return $this->loadAliasIdDepth($structureDo, $query->getCondition('id'), $query->getDepth(), $filters);
				break;
			case 'tag-one-deep': // First element matching with tag
				$contentsTemp = $this->loadTagOneDepth($structureDo, $query->getCondition('tags'), $query->getDepth(), $filters);
				return is_null($contentsTemp) ? null : $contentsTemp->one();
				break;
			case 'tag-deep': // Elements matching with tag
				return $this->loadTagDepth($structureDo, $query->getCondition('tags'), $query, $filters);
				break;
			case 'field-value':
				return $this->loadFieldValue($structureDo, $query->getCondition('data_value_query'), $query);
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
			case 'parents':
				return $this->parents($structureDo, $query);
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
				throw new PersistentStorageQueryTypeNotImplemented('Query type [' . $query->getType() . '] not implemented');
				break;
		}
		//}
		//else {
		//	// Structure empty
		//	return new Collection();
		//}
	}
	public function save($structureDo, $contentDo)
	{
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		// TODO	 revisar
		$aIdChildsRelated = []; // Store all related content id
		$mongoCollection = $this->db->content;
		$insert = $contentDo->tokenizeData();
		$insert['id_structure'] = $structureDo->getId();
		// Replace relations by MongoDBRefs
		$bChildsRelated = false;
		$oIdChildsRelated = [];
		foreach ($structureDo->getFields() as $field) {
			$key = $field->getId();
			$value = $insert['data'][$key];
			switch ($field->getType()) {
				case $field::TYPE_CONTENT:
					// Relation
					if ($value['ref']) {
						$mongoId = new ObjectID($value['ref']);
						//						$insert['data'][$key]['ref'] = \MongoDBRef::create('content', new \MongoId($value['ref']));
						$insert['data'][$key]['ref'] = \Acd\Lib\MongoDBRef::create('content', $mongoId);
						$insert['data'][$key]['id_structure'] = $value['id_structure'];

						$oIdChildsRelated[] = $insert['data'][$key]['ref']; // For table relations
					}
					$bChildsRelated = true;
					break;
				case $field::TYPE_COLLECTION:
					// Collection relation
					if (!is_array($value)) {
						$value = [];
					}
					foreach ($value as $id => $item) {
						$mongoId = new ObjectID($item['ref']);
						//						$value[$id]['ref'] = \MongoDBRef::create('content', new \MongoId($item['ref']));
						$value[$id]['ref'] = \Acd\Lib\MongoDBRef::create('content', $mongoId);
						$value[$id]['id_structure'] = $item['id_structure'];


						$oIdChildsRelated[] = $value[$id]['ref']; // For table relations
					}
					$insert['data'][$key] = [
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
		unset($insert['id']);
		$insert['save_ts'] = time(); // Log, timestamp for last save / update operation
		if ($contentDo->getId()) {
			$oId = new ObjectID($contentDo->getId());

			$mongoCollection->updateOne(['_id' => $oId], ['$set' => $insert]);
		} else {
			$result = $mongoCollection->insertOne($insert);
			$contentDo->setId($result->getInsertedId());
			$oId = new ObjectID($result->getInsertedId());
		}
		if ($bChildsRelated) {
			$this->updateRelations($this->db, \Acd\Lib\MongoDBRef::create('content', $oId), $oIdChildsRelated);
		}

		return $contentDo;
	}

	private function updateRelations($db, $parent, $children)
	{
		// Redundant cache content relations
		$mongoCollection = $db->relation;
		// emptying old relations & add news
		$mongoCollection->deleteMany(['parent' => $parent]);
		foreach ($children as $child) {
			$mongoCollection->insertOne([
				'parent' => $parent,
				'child' => $child
			]);
		}
	}

	public function delete($structureDo, $idContent)
	{
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
		} else {
			$mongoCollection = $this->db->selectCollection('content');
			$oId = new ObjectID($idContent);
			$mongoCollection->deleteOne(['_id' => $oId]);
		}

		$this->updateRelations($this->db, \Acd\Lib\MongoDBRef::create('content', $oId), array());
	}

	private function loadById($structureDo, $id)
	{
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		//$id = $query->getCondition();
		// TODO revisar
		$mongoCollection = $this->db->content;
		try {
			$oId = new ObjectID($id);
		} catch (\Exception $e) {
			return NULL;
		}
		try {
			$documentFound = $mongoCollection->findOne(["_id" => $oId], [
				'typeMap' => [
					'array' => 'array',
					'root' => 'array',
					'document' => 'array'
				]
			]);
			$documentFound = $this->normalizeDocument($documentFound);
			$contentFound = new ContentDo();
			$contentFound->load($documentFound, $structureDo);
		} catch (\Exception $e) {
			$contentFound = null;
		}

		return $contentFound;
	}

	// Transform a mongodb document to normalized document (aseptic persistent storage)
	//TODO ver por qué no puede meterse dentro de normalizeDocument
	function normalizeRef($DBRef)
	{
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
	private function normalizeDocument($document)
	{
		$document['id'] = (string) $document['_id'];

		foreach ($document['data'] as $key => $value) {
			// External content
			if (isset($value['ref']) && \Acd\Lib\MongoDBRef::isRef($value['ref'])) {
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
	private function getStructure($id)
	{
		if (!isset($this->structuresCache[$id])) {
			$structure = new structureDo();
			$structure->setId($id);
			$structure->loadFromFile();
			$this->structuresCache[$id] = $structure;
		}

		return $this->structuresCache[$id];
	}
	private function loadTagOneDepth($structureDo, $tags, $depth, $filters = [])
	{
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		$mongoCollection = $this->db->content;
		// db.content.find({"tags":{ $in : ["portadacine"]}, "id_structure" : "padre"}).pretty()
		$documentFound = $mongoCollection->findOne(['tags' => ['$in' => $tags], 'id_structure' => $structureDo->getId()]);
		if ($documentFound) {
			return $this->loadIdDepth($structureDo, (string) $documentFound['_id'], $depth, $filters);
		} else {
			return null;
		}
	}
	private function loadTagDepth($structureDo, $tags, $query, $filters = [])
	{
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		$depth = $query->getDepth();
		// Set pagination limits
		$limits = $query->getLimits();
		$mongoCollection = $this->db->content;
		// db.content.find({"tags":{ $in : ["portadacine"]}, "id_structure" : "padre"}).pretty()
		$query = ['tags' => ['$in' => $tags], 'id_structure' => $structureDo->getId()];
		$cursor = $mongoCollection->find($query, [
			'skip' => $limits->getLower(),
			'limit' => $limits->getUpper() - $limits->getLower(),
			'typeMap' => [
				'array' => 'array',
				'root' => 'array',
				'document' => 'array'
			]
		]);

		$result = new ContentsDo();
		foreach ($cursor as $documentFound) {
			$result->add($this->loadIdDepth($structureDo, (string) $documentFound['_id'], $depth, $filters)->one());
		}

		$limits->setTotal($mongoCollection->count($query));
		$result->setLimits($limits);
		return $result;
	}
	private function loadAliasIdDepth($structureDo, $idContent, $depth, $filters = [])
	{
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		$mongoCollection = $this->db->selectCollection('content');
		$filter = [];
		$filter['alias_id'] = $idContent;
		$filter['id_structure'] = $structureDo->getId();
		$validityDate = $filters['validity-date'] ?? null;
		$filter = Filter::add($filter, Filter::periodOfValidity('period_of_validity', $validityDate));
		$documentFound = $mongoCollection->findOne($filter, ['projection' => ["_id" => 1]]);
		if ($documentFound) {
			return $this->loadIdDepth($structureDo, (string) $documentFound['_id'], $depth, $filters);
		} else {
			return null;
		}
	}
	private function loadIdDepth($structureDo, $idContent, $depth, $filters = [])
	{
		if ($depth > 0) {
			$depth--;
			$content = $this->loadById($structureDo, $idContent);

			$validityDate = $filters['validity-date'] ?? null;
			$profile = $filters['profile'] ?? '';
			$isValid = $content && $content->checkValidityDate($validityDate) && $content->checkProfile($profile);
			// TODO Organize code
			if (!$isValid) return null;
			// else

			$fields = $content->getFields();
			// Walk fields and fill their values
			foreach ($fields as $field) {
				switch ($field->getType()) {
					case 'content':
						// Has relation info?
						if ($field->getValue() && $field->getValue()['id_structure']) {
							$structureTmp = $this->getStructure($field->getValue()['id_structure']);
							$contentsTemp = $this->loadIdDepth($structureTmp, $field->getValue()['ref'], $depth, $filters);
							if ($contentsTemp) {
								$field->setValue($contentsTemp->one());
							}
						} else {
							$field->setValue(null);
						}
						break;
					case 'collection':
						$newVal = new ContentsDo();
						$items = $field->getValue();
						if ($items) {
							foreach ($items as $itemCollection) {
								$structureTmp = $this->getStructure($itemCollection['id_structure']);
								$contentsTemp = $this->loadIdDepth($structureTmp, $itemCollection['ref'], $depth, $filters);
								if ($contentsTemp) {
									$newVal->add($contentsTemp->one());
								}
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

	private function loadAll($structureDo, $query)
	{
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		$mongoCollection = $this->db->content;
		$byStructureQuery = array('id_structure' => $structureDo->getId());
		// Set pagination limits
		$limits = $query->getLimits();

		$cursor = $mongoCollection->find($byStructureQuery, [
			'sort' => ['_id' => -1],
			'skip' => $limits->getLower(),
			'limit' => $limits->getUpper() - $limits->getLower(),
			'typeMap' => [
				'array' => 'array',
				'root' => 'array',
				'document' => 'array'
			]
		]);
		//		$cursor->sort(array( '_id' => -1))
		//			->skip($limits->getLower())->limit($limits->getUpper()-$limits->getLower()); // Limits
		//		$limits->setTotal($cursor->count());
		$result = new ContentsDo();
		foreach ($cursor as $documentFound) {
			$documentFound = $this->normalizeDocument($documentFound);
			$contentFound = new ContentDo();
			$contentFound->load($documentFound, $structureDo);
			$result->add($contentFound, $documentFound['id']);
		}
		$limits->setTotal($mongoCollection->count($byStructureQuery));
		$result->setLimits($limits);

		return $result;
	}

	private function loadEditorSearch($structureDo, $query)
	{
		//db.content.find({"id_structure": "item_mosaico", "title" : /.*quinto.*/i}).pretty()
		//db.getCollection('content').find({$or : [{"alias_id" : "dos"}, {"title" : "dos"}, {"tags" : [{$in : 'dos'}]}], 'id_structure' : 'unimongo'})
		$filter = array();
		$stringFilter = array();
		if (isset($query->getCondition()['title'])) {
			$search = $query->getCondition()['title'];
			$stringFilter[] = array('title' => array('$regex' => new \MongoDB\BSON\Regex("^.*$search.*", 'i')));
			$stringFilter[] = array('alias_id' => array('$regex' => new \MongoDB\BSON\Regex("^.*$search.*", 'i')));
			$stringFilter[] = array('tags' => array('$in' => array($search)));
			$filter['$or'] = $stringFilter;
		}
		if (isset($query->getCondition()['idStructure'])) {
			$filter['id_structure'] = $query->getCondition()['idStructure'];
		}

		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		// Set pagination limits
		$limits = $query->getLimits();
		$mongoCollection = $this->db->content;
		$cursor = $mongoCollection->find($filter, [
			'sort' => ['_id' => -1],
			'skip' => $limits->getLower(),
			'limit' => $limits->getUpper() - $limits->getLower(),
			'typeMap' => [
				'array' => 'array',
				'root' => 'array',
				'document' => 'array'
			]
		]);
		//		$cursor->sort(array( '_id' => -1))
		//			->skip($limits->getLower())->limit($limits->getUpper()-$limits->getLower()); // Limits
		//		$limits->setTotal($cursor->count());
		$result = new ContentsDo();

		foreach ($cursor as $documentFound) {
			$documentFound = $this->normalizeDocument($documentFound);
			$contentFound = new ContentDo();
			$contentFound->load($documentFound, $structureDo);
			$result->add($contentFound, $documentFound['id']);
		}

		$limits->setTotal($mongoCollection->count($filter));
		$result->setLimits($limits);
		return $result;
	}

	private function countParents($structureDo, $query)
	{
		$id = $query->getCondition();
		if ($id == '') {
			return 0;
		}
		$mongold = new ObjectID($id);
		$filter = ['child' => \Acd\Lib\MongoDBRef::create('content', $mongold)];

		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		$mongoCollection = $this->db->relation;
		$cursor = $mongoCollection->find($filter);

		return count($cursor->toArray());
		//		return $cursor->count();
		// db.relation.find({"child" : DBRef("content", ObjectId("5510618a06b13a931ca41c07"))}).pretty()
	}
	private function parents($structureDo, $query)
	{
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		// Id's from parents
		$id = $query->getCondition();
		$mongold = new ObjectID($id);
		$filter = ['child' => \Acd\Lib\MongoDBRef::create('content', $mongold)];
		$mongoCollection = $this->db->relation;
		$cursor = $mongoCollection->find($filter, ['typeMap' => [
			'array' => 'array',
			'root' => 'array',
			'document' => 'array'
		]]);
		$idParents = [];
		foreach ($cursor as $documentFound) {
			$idParents[] = $documentFound['parent']['$id'];
		}

		// Content of parents
		$filter = array('_id' => array('$in' => $idParents));
		$mongoCollection = $this->db->content;
		$cursor = $mongoCollection->find($filter, ['typeMap' => [
			'array' => 'array',
			'root' => 'array',
			'document' => 'array'
		]]);
		$result = new ContentsDo();
		foreach ($cursor as $documentFound) {
			$documentFound = $this->normalizeDocument($documentFound);
			$contentFound = new ContentDo();
			$structureDo->setId($documentFound['id_structure']);
			$contentFound->load($documentFound, $structureDo);
			$result->add($contentFound, $documentFound['id']);
		}

		return $result;
	}
	private function countAliasId($structureDo, $query)
	{
		// Exclude alias_id === ''
		if ($query->getCondition('alias_id')) {
			if (!$this->isInitialized($structureDo)) {
				$this->initialize($structureDo);
			}
			$mongoCollection = $this->db->content;
			$cursor = $mongoCollection->find($query->getCondition());

			return count($cursor->toArray());
		} else {
			return 0;
		}
	}
	private function difuseAliasId($structureDo, $id, $filters = [])
	{
		// return array
		// [
		//	[
		//		'idStructure' => 'foo',
		//		'idContent' => 'var1',
		//		'aliasId' => 'uno'
		//	],
		//	[
		//		'idStructure' => 'foo',
		//		'idContent' => 'var2',
		//		'aliasId' => 'uno/dos'
		//	]
		//];

		// db.content.find({"alias_id" : {$in : ["alias", "alias/dos"]}}, {"_id": true, "id_structure" : true, "alias_id" : true});
		// Select elements with alias-id start match ie. one match with one/two
		$aDirectoryParts = explode('/', $id);
		$aDirectory = [];
		$directoryTmp = '';
		$separator = ''; // First time is '' next is '/'
		foreach ($aDirectoryParts as $directory) {
			// Trim empty directory names
			if ($directory !== '') {
				$directoryTmp .= $separator . $directory;
				$separator = '/';
				$aDirectory[] = $directoryTmp;
			}
		}

		$filter = [
			'alias_id' => [
				'$in' => $aDirectory
			]
		];
		// Add structureId filter
		if ($structureDo->getId()) {
			$filter['id_structure'] = $structureDo->getId();
		}
		$fields['projection'] = [
			'_id' => true,
			'id_structure' => true,
			'alias_id' => true,
			'period_of_validity' => true
		];

		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		$fields['sort'] = ['alias_id' => -1];

		$mongoCollection = $this->db->content;
		$cursor = $mongoCollection->find($filter, $fields);
		//$cursor->sort(array( 'alias_id' => -1));

		$result = [];
		$contentCheckValidity = new ContentDo(); // Object from date validity tester
		$validityDate = $filters['validity-date'] ?? null;
		foreach ($cursor as $documentFound) {
			$periodOfValidity = isset($documentFound['period_of_validity']) ? $documentFound['period_of_validity'] : null; // @$documentFound for retrocompatibility
			$contentCheckValidity->setPeriodOfValidity($periodOfValidity);
			if ($contentCheckValidity->checkValidityDate($validityDate)) {
				$result[] = [
					'id' => (string) $documentFound['_id'],
					'id_structure' => $documentFound['id_structure'],
					'alias_id' => $documentFound['alias_id']
				];
			}
		}

		return $result;
	}
	private function metaInformation($structureDo, $id, $filters = [])
	{
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		$mongoCollection = $this->db->selectCollection('content');
		try {
			$oId = new ObjectID($id);
			//			$oId = new \MongoId($id);
		} catch (\Exception $e) {
			return null;
		}
		try {
			$documentFound = $mongoCollection->findOne(array("_id" => $oId));
			$documentFound = $this->normalizeDocument($documentFound);
			$structureDo = $this->getStructure($documentFound['id_structure']); // Recreate structure information
			$contentFound = new ContentDo();
			$contentFound->load($documentFound, $structureDo);
			$result = $contentFound;
		} catch (\Exception $e) {
			$result = null;
		}
		return $result;
	}
	private function loadFieldValue($structureDo, $dataValueQuery, $query)
	{
		// Transform load by field value in load by id
		if (!$this->isInitialized($structureDo)) {
			$this->initialize($structureDo);
		}
		$mongoCollection = $this->db->selectCollection('content');

		$limits = $query->getLimits();
		$deep = $query->getDepth();
		$filters = $this->getFilters($query);
		$result = new ContentsDo();
		// Build filter (TODO, find use of array_walk or similar)
		$filtersFields = ['id_structure' => $structureDo->getId()];
		foreach ($dataValueQuery as $queryKey => $queryValue) {
			$filtersFields['data.' . $queryKey] = $queryValue; // Search only in data fields
		}
		$cursor = $mongoCollection->find($filtersFields, [
			'skip' => $limits->getLower(),
			'limit' => $limits->getUpper() - $limits->getLower(),
			'typeMap' => [
				'array' => 'array',
				'root' => 'array',
				'document' => 'array'
			]
		]);
		//		$cursor->skip($limits->getLower())->limit($limits->getUpper()-$limits->getLower());
		foreach ($cursor as $documentFound) {
			$documentFound = $this->normalizeDocument($documentFound);
			$idContent = $documentFound['id'];
			$content = $this->loadIdDepth($structureDo, $idContent, $deep, $filters);
			if ($content) {
				$result->add($content->one(), $idContent);
			}
		}

		return $result;
	}
	public function getIndexes() {
		if (!$this->isInitialized(null)) {
			$this->initialize(null);
		}
		$indexes = [];
		$mongoCollection = $this->db->content;
		foreach ($mongoCollection->listIndexes() as $index) {
			$indexes[] = $index;
		}
		$mongoCollection = $this->db->relation;
		foreach ($mongoCollection->listIndexes() as $index) {
			$indexes[] = $index;
		}
		return $indexes;
	}
	public function createIndexes() {
		if (!$this->isInitialized(null)) {
			$this->initialize(null);
		}
		$mongoCollection = $this->db->content;
		$indexNamesContent = $mongoCollection->createIndexes([
			[ 'key' => [ 'alias_id' => 1] ] ,
			[ 'key' => [ 'id_structure' => 1, 'alias_id' => 1] ] ,
			[ 'key' => [ 'id_structure' => 1, 'tags' => 1] ] ,
		]);
		$mongoCollection = $this->db->relation;
		$indexNamesRelation = $mongoCollection->createIndexes([
			[ 'key' => [ 'child' => 1] ] ,
			[ 'key' => [ 'parent' => 1] ] ,
		]);
		return array_merge ($indexNamesContent, $indexNamesRelation);
	}
	public function dropIndexes() {
		if (!$this->isInitialized(null)) {
			$this->initialize(null);
		}
		$mongoCollection = $this->db->content;
		$resContent = $mongoCollection->dropIndexes();
		$mongoCollection = $this->db->relation;
		$resRelation = $mongoCollection->dropIndexes();

		return true;
	}
}
