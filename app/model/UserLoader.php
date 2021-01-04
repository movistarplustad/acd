<?php
namespace Acd\Model;

class UserLoader
{
    private function getManager()
    {
        switch (\Acd\conf::$DEFAULT_STORAGE) {
            case \Acd\conf::$STORAGE_TYPE_MONGODB_LEGACY:
                return new PersistentUserManagerMongoDBLegacy();
                break;
            case \Acd\conf::$STORAGE_TYPE_MONGODB:
                return new PersistentUserManagerMongoDB();
                break;
            case \Acd\conf::$STORAGE_TYPE_TEXTPLAIN:
                // TODO implement
                return new PersistentUserManagerTextPlain();
                break;
/*
            case \Acd\conf::$STORAGE_TYPE_MYSQL:
                // TODO implement
                return new PersistentEnumeratedManagerMySql();
                break;
*/
            default:
                throw new PersistentStorageUnknownInvalidException("Invalid type of persistent storage ".$this->getStorage().".");
                break;
        }
    }

    // User
    public function load($query)
    {
        $dataManager = $this->getManager();
        return $dataManager->load($query);
    }
    public function save($userDO)
    {
        $dataManager = $this->getManager();
        $NewUserDO = $dataManager->save($userDO);
        return $NewUserDO;
    }
    public function delete($id)
    {
        $dataManager = $this->getManager();
        return $dataManager->delete($id);
    }

    // Persistent sessions
    public function persistSession($userDO)
    {
        $dataManager = $this->getManager();
        return $dataManager->persistSession($userDO);
    }
    public function loadPersistSession($token)
    {
        $dataManager = $this->getManager();
        return $dataManager->loadPersistSession($token);
    }
    public function deletePersistSession($token)
    {
        $dataManager = $this->getManager();
        return $dataManager->deletePersistSession($token);
    }
    public function loadUserPersistSessions($id)
    {
        $dataManager = $this->getManager();
        return $dataManager->loadUserPersistSessions($id);
    }
    // Install
    public function getIndexes() {
        $dataManager = $this->getManager();
        return $dataManager->getIndexes();
    }
    public function createIndexes() {
        $dataManager = $this->getManager();
        return $dataManager->createIndexes();
    }
    public function dropIndexes() {
        $dataManager = $this->getManager();
        return $dataManager->dropIndexes();
    }
}
