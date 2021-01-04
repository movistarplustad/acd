<?php
namespace Acd\Model;

class PersistentUserManagerMongoDB implements iPersistentUserManager
{
    private $db;
    public function initialize()
    {
        // If not is initialized do the initializacion
        if (!$this->isInitialized()) {
            $this->mongo = new \MongoDB\Client(\Acd\conf::$MONGODB_SERVER);
            $this->db = $this->mongo->selectDatabase(\Acd\conf::$MONGODB_DB);
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
        $mongoCollection = $this->db->user;
        try {
            $id = $query->getCondition('id');
            $documentFound = $mongoCollection->findOne(["_id" => $id]);
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
        $mongoCollection = $this->db->user;
        try {
            $cursor = $mongoCollection->find([], ['sort' => ['_id' => 1]]);
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
        $mongoCollection = $this->db->user;
        $insert = $userDo->tokenizeData();

        $id = $userDo->getId();
        unset($insert['id']);
        $insert['save_ts'] = time(); // Log, timestamp for last save / update operation
        $mongoCollection->updateOne(['_id' => $id], ['$set' => $insert], ['upsert' => true]);

        return $userDo;
    }
    public function delete($id)
    {
        $this->initialize();
        $mongoCollection = $this->db->selectCollection('user');
        return $mongoCollection->deleteMany(['_id' => $id]);
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
        $mongoCollection->updateOne(array('_id' => $token), ['$set' => $persistentData], array('upsert' => true));
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
                    $mongoCollection->updateOne(array('_id' => $documentFound['_id']), ['$set' => $documentFound], array('upsert' => true));
                }
            } catch (\Exception $e) {
                throw new \Exception("Unable to load loadPersistSession (MongoDB)");
            }
        }
        return $userDo;
    }
    public function deletePersistSession($token)
    {
        $this->initialize();
        $mongoCollection = $this->db->selectCollection('authPermanent');
        return $mongoCollection->deleteMany(['_id' => $token]);
    }
    public function loadUserPersistSessions($id)
    {
        $this->initialize();
        if ($id) {
            $mongoCollection = $this->db->selectCollection('authPermanent');
            try {
                $cursor = $mongoCollection->find(['login' => $id], [ 'sort' => ['lastUse' => 1, 'timestamp' => 1]]);
                //$cursor->sort(array( 'lastUse' => 1, 'timestamp' => 1));
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
        $this->initialize();
        $indexes = [];
        $mongoCollection = $this->db->selectCollection('authPermanent');
        foreach ($mongoCollection->listIndexes() as $index) {
            $indexes[] = $index;
        }
        return $indexes;
    }
    public function createIndexes() {
        $this->initialize();
        $mongoCollection = $this->db->selectCollection('authPermanent');
        $indexNames = $mongoCollection->createIndexes([
            [ 'key' => [ 'login' => 1] ] ,
        ]);
        return $indexNames;
    }
    public function dropIndexes() {
        $this->initialize();
        $mongoCollection = $this->db->selectCollection('authPermanent');
        $resContent = $mongoCollection->dropIndexes();
        return true;
    }
    public function normalizeDocument($document)
    {
        $document['id'] = (string) $document['_id'];
        unset($document['_id']);
        return $document;
    }
}
