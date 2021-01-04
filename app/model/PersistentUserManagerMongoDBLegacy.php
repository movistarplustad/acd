<?php
namespace Acd\Model;

class PersistentUserManagerMongoDBLegacyException extends \exception
{
} // TODO Unificar
class PersistentUserManagerMongoDBLegacy implements iPersistentUserManager
{
    private $db;
    public function initialize()
    {
        // If not is initialized do the initializacion
        if (!$this->isInitialized()) {
            $mongo = new \MongoClient(\Acd\conf::$MONGODB_SERVER);
            $this->db = $mongo->selectDB(\Acd\conf::$MONGODB_DB);
        }
    }
    public function isInitialized()
    {
        return isset($this->db);
    }
    public function load($query)
    {
        $this->initialize();
        if ($query->getCondition('id')) {
            return $this->loadById($query);
        } else {
            return $this->loadAll($query);
        }
    }
    private function loadById($query)
    {
        $mongoCollection = $this->db->selectCollection('user');
        try {
            $id = $query->getCondition('id');
            $documentFound = $mongoCollection->findOne(array("_id" => $id));
            $documentFound = $this->normalizeDocument($documentFound);
            $userFound = new UserDo();
            $userFound->load($documentFound);

            return $userFound;
        } catch (\Exception $e) {
            return null;
        }
    }
    private function loadAll($query)
    {
        $mongoCollection = $this->db->selectCollection('user');
        try {
            $cursor = $mongoCollection->find(array());
            $cursor->sort(array( '_id' => 1));
            $userCollectionFound = new Collection();
            foreach ($cursor as $documentFound) {
                $documentFound = $this->normalizeDocument($documentFound);
                $userFound = new UserDo();
                $userFound->load($documentFound);
                $userCollectionFound->add($userFound);
            }
            return $userCollectionFound;
        } catch (\Exception $e) {
            return null;
        }
    }
    public function save($userDo)
    {
        $this->initialize();
        $mongoCollection = $this->db->selectCollection('user');
        $insert = $userDo->tokenizeData();

        $id = $userDo->getId();
        unset($insert['id']);
        $insert['save_ts'] = time(); // Log, timestamp for last save / update operation
        $mongoCollection->update(array('_id' => $id), $insert, array('upsert' => true));

        return $userDo;
    }
    public function delete($id)
    {
        $this->initialize();
        $mongoCollection = $this->db->selectCollection('user');
        return $mongoCollection->remove(array('_id' => $id));
    }
    public function persistSession($userDo)
    {
        $this->initialize();
        $token = hash('sha1', uniqid());
        $persistentData = array(
                'login' => $userDo->getId(),
                'rol' => $userDo->getRol(),
                'timestamp' => time()
            );
        $mongoCollection = $this->db->selectCollection('authPermanent');
        $mongoCollection->update(array('_id' => $token), $persistentData, array('upsert' => true));
        return $token;
    }
    public function loadPersistSession($token)
    {
        $this->initialize();
        $userDo = new UserDo();
        if ($token) {
            $mongoCollection = $this->db->selectCollection('authPermanent');
            try {
                // If exists persistent session mark the lastuse timestamp
                // useful for future purges
                $documentFound = $mongoCollection->findOne(['_id' => $token]);
                if ($documentFound) {
                    $userDo->setId($documentFound['login']);
                    $userDo->setRol($documentFound['rol']);
                    $documentFound['lastUse'] = time();
                    $mongoCollection->update(array('_id' => $documentFound['_id']), $documentFound, array('upsert' => true));
                }
            } catch (\Exception $e) {
                throw new PersistentUserManagerMongoDBLegacyException("Unable to load loadPersistSession (MongoDB)");
            }
        }
        return $userDo;
    }
    public function deletePersistSession($token)
    {
        $this->initialize();
        $mongoCollection = $this->db->selectCollection('authPermanent');
        return $mongoCollection->remove(array('_id' => $token));
    }
    public function loadUserPersistSessions($id)
    {
        $this->initialize();
        if ($id) {
            $mongoCollection = $this->db->selectCollection('authPermanent');
            try {
                $cursor = $mongoCollection->find(['login' => $id]);
                $cursor->sort(array( 'lastUse' => 1, 'timestamp' => 1));
                $authPersistentCollectionFound = new Collection();
                foreach ($cursor as $documentFound) {
                    $documentFound = $this->normalizeDocument($documentFound);
                    $authPersistent = new AuthPersistentDo();
                    $authPersistent->load($documentFound);
                    $authPersistentCollectionFound->add($authPersistent);
                }
                return $authPersistentCollectionFound;
            } catch (\Exception $e) {
                return null;
            }
        }
    }
	public function getIndexes() {
		throw new PersistentUserManagerMongoDBLegacyException("Not implemented", self::GET_INDEXES_FAILED);
	}
	public function createIndexes() {
		throw new PersistentUserManagerMongoDBLegacyException("Not implemented", self::CREATE_INDEXES_FAILED);
	}
	public function dropIndexes() {
		throw new PersistentUserManagerMongoDBLegacyException("Not implemented", self::DROP_INDEXES_FAILED);
	}
    public function normalizeDocument($document)
    {
        $document['id'] = (string) $document['_id'];
        unset($document['_id']);
        return $document;
    }
}
