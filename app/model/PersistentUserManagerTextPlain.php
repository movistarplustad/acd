<?php
namespace Acd\Model;

class PersistentManagerTextPlainException extends \exception {} // TODO Unificar
class PersistentUserManagerTextPlain implements iPersistentUserManager
{
	private $db; // BORRAR
	private function getStoragePath($structureDo) {
		return \Acd\conf::$DATA_DIR_PATH.'/'.$structureDo->getId().'.json';
	}
	private static function persistentFilePath($login) {
		return conf::$PATH_AUTH_PERMANENT_LOGIN_DIR.'/'.hash('sha1', $login);
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
				$userCollectionFound->add($userFound, $userFound->getId());
			}
			return $userCollectionFound;
			//return $aCredentials;
		}
		catch( \Exception $e ) {
			return null;
		}
	}
	private function saveAll($allUsers) {
		$path = \ACD\conf::$PATH_AUTH_CREDENTIALS_FILE;
		$aData = array();
		foreach ($allUsers as $user) {
			$aData[$user->getId()] = $user->tokenizeData();
		}
		$tempPath = $path.'.tmp';
		$somecontent = json_encode($aData);

		if (!$handle = fopen($tempPath, 'a')) {
			 echo "Cannot open file ($tempPath)";
			 exit;
		}

		// Write $somecontent to our opened file.
		if (fwrite($handle, $somecontent) === FALSE) {
			throw new PersistentStructureManagerTextPlainException("Cannot write to file ($tempPath)", self::SAVE_FAILED);
			exit;
		}
		fclose($handle);
		rename($tempPath, $path);
	}
	public function save($userDo) {
		/* Construct the json */
		$allUsers = $this->loadAll(null);
		$allUsers->set($userDo, $userDo->getId());
		$this->saveAll($allUsers);
		return $userDo;
	}
	public function delete($id) {
		/* Construct the json */
		$allUsers = $this->loadAll(null);
		$allUsers->remove($id);
		$this->saveAll($allUsers);
		return $allUsers;
	}
	public function persist($userDo) {
		$persistentData = array(
				'login' => $userDo->getId(),
				'token' => hash('sha1', uniqid()),
				'rol' => $userDo->getRol(),
				'timestamp' => time()
			);
		$jsonCredentials = json_encode($persistentData);
		$path = Auth::persistentFilePath($login);
		if (!$handle = fopen($path, 'w')) {
				echo "Cannot open file ($path)";
				exit;
		}

		// Write $jsonCredentials to our opened file.
		if (fwrite($handle, $jsonCredentials) === FALSE) {
			echo "Cannot write to file ($path)";
			exit;
		}
		fclose($handle);
		dd("Persistir Text Plain", $userDo, $persistentData);
	}
	public function normalizeDocument($document) {
		unset($document['_id']);
		return $document;
	}
}
