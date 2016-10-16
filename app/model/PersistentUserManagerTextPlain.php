<?php
namespace Acd\Model;

class PersistentManagerTextPlainException extends \exception {} // TODO Unificar
class PersistentUserManagerTextPlain implements iPersistentUserManager
{
	private $db; // BORRAR
	private function getStoragePath($structureDo) {
		return \Acd\conf::$DATA_DIR_PATH.'/'.$structureDo->getId().'.json';
	}
	public function initialize() {
		if (!$this->isInitialized()){
			$emptyData = '{}';
			$path = \Acd\conf::$PATH_AUTH_CREDENTIALS_FILE;
			if (!$handle = fopen($path, 'a')) {
				throw new PersistentManagerTextPlainException("Cannot open file ($path) to append data", 1);
				exit;
			}

			if (fwrite($handle, $emptyData) === FALSE) {
				throw new PersistentManagerTextPlainException("Cannot write to file ($path)", 1);
				exit;
			}
			fclose($handle);
		}
	}

	public function isInitialized() {
		return is_readable(\Acd\conf::$PATH_AUTH_CREDENTIALS_FILE);
	}
	public function load($query) {
		if (!$this->isInitialized()) {
			$this->initialize();
		};
		if($query->getCondition('id')) {
			return $this->loadById($query);
		}
		else {
			return $this->loadAll($query);
		}
	}
	private function loadById($query) {
		$allUsers = $this->loadAll($query);

		$result = null;
		$id = $query->getCondition('id');
		foreach ($allUsers as $user) {
				if($user->getId() === $id) {
					$result = $user;
					break;
				}
		}
		return $result;
	}
	private function loadAll($query) {
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
				$userCollectionFound->add($userFound);
			}
			return $userCollectionFound;
			//return $aCredentials;
		}
		catch( \Exception $e ) {
			return null;
		}
	}
	public function save($userDo) {
		// TODO
		if (!$this->isInitialized()) {
			$this->initialize();
		}
		$mongoCollection = $this->db->selectCollection('user');
		$insert = $userDo->tokenizeData();

		$id = $userDo->getId();
		unset ($insert['id']);
		$insert['save_ts'] = time(); // Log, timestamp for last save / update operation
		$mongoCollection->update(array('_id' => $id), $insert, array('upsert' => true));

		return $userDo;
	}
	public function delete($id) {
		// TODO
		if (!$this->isInitialized()) {
			$this->initialize();
		}
		$mongoCollection = $this->db->selectCollection('user');
		return $mongoCollection->remove(array('_id' => $id));
	}
	public function normalizeDocument($document) {
		unset($document['_id']);
		return $document;
	}
}
