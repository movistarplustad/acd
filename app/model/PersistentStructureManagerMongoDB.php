<?php
namespace Acd\Model;

class PersistentStructureManagerMongoDBException extends \exception
{ } // TODO Unificar
class PersistentStructureManagerMongoDB implements iPersistentStructureManager
{
  private $mongo;
  private $db;
  private $enumeratedManager;
  public function initialize()
  {
    try {
      //      $this->mongo = new \MongoClient(\Acd\conf::$MONGODB_SERVER);
      //      $this->db = $this->mongo->selectDB(\Acd\conf::$MONGODB_DB);
      //TODO: Ver cóm pasarle el servidor, porque si es '' no funciona.
      $this->mongo = new \MongoDB\Client(\Acd\conf::$MONGODB_SERVER);
      $this->db = $this->mongo->selectDatabase(\Acd\conf::$MONGODB_DB);
      return true;
    } catch (MongoConnectionException $e) {
      throw new PersistentStructureManagerMongoDBException("Failed to connect to MongoDB", self::NO_CONNECTION);
    }
  }
  public function isInitialized()
  {
    try {
      $this->initialize();
      return true;
    } catch (PersistentStructureManagerMongoDBException $e) {
      return false;
    }
  }
  public function loadAll()
  {
    if (!$this->isInitialized()) {
      $this->initialize();
    }
    $mongoCollection = $this->db->structure;
    //    $byStructureQuery = array();

    //    $cursor = $mongoCollection->find($byStructureQuery);
    $cursor = $mongoCollection->find([], ['sort' => ['name' => 1]]);
    //$cursor->sort(array( 'name' => 1));
    $result = [];
    foreach ($cursor as $documentFound) {
      $id = $documentFound['_id'];
      unset($documentFound['_id']);
      $result[] = array($id => $documentFound);
    }
    return $result;
  }
  public function loadById($id)
  {
    if (!$this->isInitialized()) {
      $this->initialize();
    }

    //    $mongoCollection = $this->db->selectCollection('structure');
    //  $documentFound = $mongoCollection->findOne(array("_id" => $id));
    $mongoCollection = $this->db->structure;
    $documentFound = $mongoCollection->findOne(["_id" => $id]);

    // TODO controlar errores
    $documentFound['id'] = $id;
    unset($documentFound['_id']);

    return $documentFound;
  }
  public function save($structuresDo)
  {
    if (!$this->isInitialized()) {
      $this->initialize();
    }
    $mongoCollection = $this->db->structure;
    //$data = $structuresDo->tokenizeData();
    // TODO ir más finos en bbdd que borrar todo y volver a guardarlo
    $mongoCollection->deleteMany([]);
    foreach ($structuresDo as $structure) {
      $id = $structure->getId();
      $insert = $structure->tokenizeData()[$id];
      $insert['_id'] = $id;
      $mongoCollection->insertOne($insert);
    }
    //db.structure.update({'_id' : 'chat_tienda'},{'_id' : 'chat_tienda',       "name": "Chat de tienda online Mongo", "storage" : "mongodb", 'fieds' : []}, {upsert :true})
    //$mongoCollection->update(array('_id' => $oId), array('$set' => $insert), array('upsert' => true));
  }
  private function getEnumeratedManager()
  {
    if (!isset($this->enumeratedManager)) {
      $this->enumeratedManager = new PersistentEnumeratedManagerMongoDB();
    }

    return $this->enumeratedManager;
  }
  public function loadEnumerated($id)
  {
    $enumeratedDataManager = $this->getEnumeratedManager();
    return $enumeratedDataManager->load($id);
  }
  public function getIndexes() {
    // Currently no indexes are required
  }
  public function createIndexes() {
    // Currently no indexes are required
  }
  public function dropIndexes() {
    // Currently no indexes are required
  }
}
