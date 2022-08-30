<?php
namespace Acd\Model;

class UserLoadException extends \exception {}
class UserDo
{
	protected $id; /* Nickname */
	protected $password;
	protected $rol; /* Editor, developer... */
		public function __construct() {
		$this->id = null;
		$this->password	 = null;
	}
	/* Setters and getters attributes */
	public function setId($id) {
		$this->id = (string)$id;
	}
	public function getId() {
		return $this->id;
	}
	public function setPassword($password) {
		$this->password = (string)$password !== '' ? Auth::hashPassword($password) : null;
	}
	// Don't proccess the password, modifications not necesary
	public function putPassword($password) {
		$this->password = $password;
	}
	public function getPassword() {
		return $this->password;
	}
	public function setRol($rol) {
		$this->rol = (string)$rol;
	}
	public function getRol() {
		return $this->rol;
	}


	private function getManager() {
		switch (\Acd\conf::$DEFAULT_STORAGE) {
			case \Acd\conf::$STORAGE_TYPE_TEXTPLAIN:
				//echo "tipo texto";
				return new PersistentStructureManagerTextPlain();
				break;
			case \Acd\conf::$STORAGE_TYPE_MONGODB_LEGACY:
				//echo "tipo mongo";
				return new PersistentStructureManagerMongoDBLegacy();
				break;
			case \Acd\conf::$STORAGE_TYPE_MONGODB:
				//echo "tipo mongo";
				return new PersistentStructureManagerMongoDB();
				break;
			case \Acd\conf::$STORAGE_TYPE_MYSQL:
				//echo "tipo mysql";
				return new PersistentStructureManagerMySql();
				break;
			default:
				throw new PersistentStorageUnknownInvalidException("Invalid type of persistent storage ".$this->getStorage().".");
				break;
		}
	}

	public function load($data) {
		if(isset($data['id']) && isset($data['password']) && isset($data['rol'])){
			$this->setId($data['id']);
			$this->putPassword($data['password']);
			$this->setRol($data['rol']);
		}
		else {
			throw new UserLoadException("No data loaded, user-id probably does not exist.");
		}
	}

	/* TODO: Bad name loadFromFile, change for loadFromPersistentStorage */
	public function BORRARloadFromFile($options = []) {
		$bLoadEnumerated = isset($options['loadEnumerated']) && $options['loadEnumerated'] === true;
		$dataManager = $this->getManager();
		$document = $dataManager->loadById($this->getId());
		$bLoaded = false;
		if ($document) {
			$this->load($document);
			if($bLoadEnumerated) {
				$this->assignEnumeratedOptionsToFieds();
			}
			$bLoaded = true;
		}

		return $bLoaded;
	}

	private function BORRARassignEnumeratedOptionsToFieds() {
		$dataManager = $this->getManager();
		$query = new Query();
		$query->setType('id');

		$multiple = new \AppendIterator();
		$multiple->append($this->getStickyFields()->getIterator());
		$multiple->append( $this->getFields()->getIterator());
		foreach ($multiple as $field) {
			if($field->getOptions()->getId()) {
				$query->setCondition(['id' => $field->getOptions()->getId()]);
				$enumeratedDo = $dataManager->loadEnumerated($query);
				$field->setOptions($enumeratedDo);
			}
		}
	}

	/* Serializes */
	public function setFromJson($jsonData) {
		// TODO
		var_dump($jsonData);
	}
	public function tokenizeData() {
		$userData = array(
			'_id' => $this->getId(),
			'rol' => $this->getRol()
		);
		if ($this->getPassword() !== null) {
			$userData['password'] = $this->getPassword();
		}
		return $userData;
	}
}
