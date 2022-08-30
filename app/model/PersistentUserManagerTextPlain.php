<?php

namespace Acd\Model;

use Acd\conf;

class PersistentManagerTextPlainException extends \exception
{
} // TODO Unificar
class PersistentUserManagerTextPlain implements iPersistentUserManager
{
    private function getStoragePath($structureDo)
    {
        return \Acd\conf::$DATA_DIR_PATH . '/' . $structureDo->getId() . '.json';
    }
    private static function persistentFilePath($token)
    {
        return conf::$PATH_AUTH_PERMANENT_LOGIN_DIR . '/' . $token;
    }
    public function initialize()
    {
        if (!$this->isInitialized()) {
            $emptyData = '{}';
            $path = \Acd\conf::$PATH_AUTH_CREDENTIALS_FILE;
            if (!$handle = fopen($path, 'a')) {
                throw new PersistentManagerTextPlainException("Cannot open file ($path) to append data", 1);
                exit;
            }

            if (fwrite($handle, $emptyData) === false) {
                throw new PersistentManagerTextPlainException("Cannot write to file ($path)", 1);
                exit;
            }
            fclose($handle);
        }
    }

    public function isInitialized()
    {
        return is_readable(\Acd\conf::$PATH_AUTH_CREDENTIALS_FILE);
    }
    public function load($query)
    {
        if (!$this->isInitialized()) {
            $this->initialize();
        };
        if ($query->getCondition('id')) {
            return $this->loadById($query);
        } else {
            return $this->loadAll($query);
        }
    }
    private function loadById($query)
    {
        $allUsers = $this->loadAll($query);

        $result = null;
        $id = $query->getCondition('id');
        foreach ($allUsers as $user) {
            if ($user->getId() === $id) {
                $result = $user;
                break;
            }
        }
        return $result;
    }
    private function loadAll($query)
    {
        try {
            $path = \Acd\conf::$PATH_AUTH_CREDENTIALS_FILE;
            $content = file_get_contents($path);
            $aCredentials = json_decode($content, true);
            $userCollectionFound = new Collection();
            foreach ($aCredentials as $id => $documentFound) {
                $documentFound['id'] = $id;
                $documentFound = $this->normalizeDocument($documentFound);
                $userFound = new UserDo();
                $userFound->load($documentFound);
                $userCollectionFound->add($userFound, $userFound->getId());
            }
            return $userCollectionFound;
        } catch (\Exception $e) {
            return null;
        }
    }
    private function saveAll($allUsers)
    {
        $path = \Acd\conf::$PATH_AUTH_CREDENTIALS_FILE;
        $aData = array();
        foreach ($allUsers as $user) {
            $aData[$user->getId()] = $user->tokenizeData();
        }
        $tempPath = $path . '.tmp';
        $somecontent = json_encode($aData);

        if (!$handle = fopen($tempPath, 'a')) {
            throw new PersistentStructureManagerTextPlainException("Cannot open file ($tempPath)");
        }

        // Write $somecontent to our opened file.
        if (fwrite($handle, $somecontent) === false) {
            throw new PersistentStructureManagerTextPlainException("Cannot write to file ($tempPath)", self::SAVE_FAILED);
        }
        fclose($handle);
        rename($tempPath, $path);
    }
    public function save($userDo)
    {
        /* Construct the json */
        $allUsers = $this->loadAll(null);
        $allUsers->set($userDo, $userDo->getId());
        $this->saveAll($allUsers);
        return $userDo;
    }
    public function delete($id)
    {
        /* Construct the json */
        $allUsers = $this->loadAll(null);
        $allUsers->remove($id);
        $this->saveAll($allUsers);
        return $allUsers;
    }
    public function persistSession($userDo)
    {
        $persistentData = array(
            'id' => hash('sha1', uniqid()),
            'login' => $userDo->getId(),
            'rol' => $userDo->getRol(),
            'timestamp' => time()
        );
        $jsonCredentials = json_encode($persistentData);
        $path = $this->persistentFilePath($persistentData['id']);
        if (!$handle = fopen($path, 'w')) {
            throw new PersistentManagerTextPlainException("Cannot open file ($path)");
        }

        // Write $jsonCredentials to our opened file.
        if (fwrite($handle, $jsonCredentials) === false) {
            throw new PersistentManagerTextPlainException("Cannot write to file ($path)");
        }
        fclose($handle);
        return $persistentData['id'];
    }
    public function loadPersistSession($token)
    {
        $path = $this->persistentFilePath($token);
        $userDo = new UserDo();
        if ($token && file_exists($path)) {
            // If exists persistent session mark the lastuse timestamp
            // useful for future purges
            $content = file_get_contents($path);
            $aPersistentCredentials = json_decode($content, true);
            $userDo->setId($aPersistentCredentials['login']);
            $userDo->setRol($aPersistentCredentials['rol']);

            $aPersistentCredentials['lastUse'] = time();
            $jsonCredentials = json_encode($aPersistentCredentials);
            if (!$handle = fopen($path, 'w')) {
                throw new PersistentManagerTextPlainException("Cannot open file ($path)");
            }

            // Write $jsonCredentials to our opened file.
            if (fwrite($handle, $jsonCredentials) === false) {
                throw new PersistentManagerTextPlainException("Cannot write to file ($path)");
            }
            fclose($handle);
        }
        return $userDo;
    }
    public function deletePersistSession($token)
    {
        $path = $this->persistentFilePath($token);
        if (file_exists($path)) {
            unlink($path);
            return true;
        } else {
            return false;
        }
    }
    public function loadUserPersistSessions($id)
    {
        if ($id && $handle = opendir(conf::$PATH_AUTH_PERMANENT_LOGIN_DIR)) {
            $authPersistentCollectionFound = new Collection();
            while (false !== ($entry = readdir($handle))) {
                if (is_file(conf::$PATH_AUTH_PERMANENT_LOGIN_DIR . "/$entry")) {
                    $documentFound = file_get_contents(conf::$PATH_AUTH_PERMANENT_LOGIN_DIR . "/$entry");
                    $documentFound = json_decode($documentFound, true);
                    if ($documentFound['login'] === $id) {
                        $authPersistent = new AuthPersistentDo();
                        $authPersistent->load($documentFound);
                        $authPersistentCollectionFound->add($authPersistent);
                    }
                }
            }
            closedir($handle);
            return $authPersistentCollectionFound;
        }
    }
    public function getIndexes()
    {
        throw new PersistentManagerTextPlainException("Not implemented", self::GET_INDEXES_FAILED);
    }
    public function createIndexes()
    {
        throw new PersistentManagerTextPlainException("Not implemented", self::CREATE_INDEXES_FAILED);
    }
    public function dropIndexes()
    {
        throw new PersistentManagerTextPlainException("Not implemented", self::DROP_INDEXES_FAILED);
    }
    public function normalizeDocument($document)
    {
        unset($document['_id']);
        return $document;
    }
}
