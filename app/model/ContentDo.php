<?php

namespace Acd\Model;

class ContentKeyInvalidException extends \exception
{ }
class ContentDoException extends \exception
{ }
class ContentDo
{
	const PERIOD_OF_VALIDITY_START = 'start';
	const PERIOD_OF_VALIDITY_END = 'end';
	const PERIOD_OF_VALIDITY_RAW = 'raw';
	const PERIOD_OF_VALIDITY_TOKENIZE = 'tokenize'; // For tokenize
	const PROFILE_ENUMERATED_ID = 'PROFILE';
	private $id;
	private $idStructure;
	private $title;
	private $periodOfValidity;
	private $aliasId;
	private $saveTime;
	private $tags;
	private $profile;
	//private $data; /* Array key/value of variable fields */
	private $fields;
	private $parent; /* ContentDo Relation in complex structures */
	private $countParents; /* Number of parent, used in content editor for info */
	private $countAliasId; /* Number aliad_id in all contents, used in content editor for info */

	public function __construct()
	{
		$this->id = null;
		$this->idStructure = null;
		$this->periodOfValidity = array(
			ContentDo::PERIOD_OF_VALIDITY_START => -INF,
			ContentDo::PERIOD_OF_VALIDITY_END => INF
		);
		$this->aliasId = null;
		$this->tags = array();
		$this->profile = new FieldDo();
		$this->profile->setType(FieldDo::TYPE_LIST_MULTIPLE);
		$this->profile->setId(self::PROFILE_ENUMERATED_ID);
		$this->fields = new FieldsDo();
		$this->parent = null;
	}
	/* Setters and getters attributes */
	public function setId($id)
	{
		$this->id = (string) $id;
	}
	public function getId()
	{
		return $this->id;
	}
	public function setIdStructure($idStructure)
	{
		$this->idStructure = (string) $idStructure;
	}
	public function getIdStructure()
	{
		return $this->idStructure;
	}
	public function setTitle($title)
	{
		$this->title = $title;
	}
	public function getTitle()
	{
		return $this->title;
	}
	private function checkExpirityAttribute($attributeName)
	{
		return ($attributeName === ContentDo::PERIOD_OF_VALIDITY_START || $attributeName === ContentDo::PERIOD_OF_VALIDITY_END);
	}
	public function setPeriodOfValidity($periodOfValidity)
	{
		// $attributeName acepted PERIOD_OF_VALIDITY_START | PERIOD_OF_VALIDITY_END
		$this->periodOfValidity = $periodOfValidity;
	}
	public function getPeriodOfValidity($attributeName = ContentDo::PERIOD_OF_VALIDITY_RAW)
	{
		// TODO. Future period_of_validity class
		switch ($attributeName) {
			case ContentDo::PERIOD_OF_VALIDITY_RAW:
				return $this->periodOfValidity;
				break;
			case ContentDo::PERIOD_OF_VALIDITY_TOKENIZE:
				// Purge infinite values, transform to empty string (infinite values not accepted in json grammar)
				$periodOfValidityTmp = [];
				foreach ($this->periodOfValidity as $key => $value) {
					$periodOfValidityTmp[$key] = is_finite((double) $value) ? $value : '';
				}

				return $periodOfValidityTmp;
				break;
			default:
				if ($this->checkExpirityAttribute($attributeName)) {
					return isset($this->periodOfValidity[$attributeName]) ? $this->periodOfValidity[$attributeName] : null;
				} else {
					throw new ContentDoException("Unknown period of validity attibute [$attributeName]", 1);
				}
				break;
		}
	}
	public function checkValidityDate($date)
	{
		$inDate = true;
		$start = $this->getPeriodOfValidity(ContentDo::PERIOD_OF_VALIDITY_START) ? $this->getPeriodOfValidity(ContentDo::PERIOD_OF_VALIDITY_START) : -INF;
		$end = $this->getPeriodOfValidity(ContentDo::PERIOD_OF_VALIDITY_END) ? $this->getPeriodOfValidity(ContentDo::PERIOD_OF_VALIDITY_END) : INF;
		if ($date) {
			$inDate = ($start  <= $date) && ($date  <= $end);
		}
		return $inDate;
	}
	public function checkProfile($profile)
	{
		$profilesContent = $this->getProfile()->getValue();
		// empty profile or content un-profiled
		if ($profile == ''  || count($profilesContent) == 0) {
			$profileAllowed = true;
		} else {
			$profileAllowed = in_array($profile, $this->getProfile()->getValue());
		}

		return $profileAllowed;
	}
	public function setAliasId($aliasId)
	{
		$this->aliasId = $aliasId;
	}
	public function getAliasId()
	{
		return $this->aliasId;
	}
	public function setTags($tags)
	{
		if (is_array($tags)) {
			$this->tags = $tags;
		} else {
			throw new ContentDoException("Input data should be an array", 1);
		}
	}
	public function setProfile($profile)
	{
		$this->profile = $profile;
	}
	public function setProfileValues($aProfile)
	{
		$this->profile->setValue($aProfile);
	}
	public function getProfile()
	{
		return $this->profile;
	}
	public function getTags()
	{
		return $this->tags;
	}
	public function addField($field)
	{
		$this->getFields()->add($field);
	}
	public function getFields()
	{
		return $this->fields;
	}
	private function setFields($fields)
	{
		foreach ($fields as $field) {
			$this->fields->add(clone $field);
		}
	}
	public function getFieldType($fieldName)
	{
		try {
			return $this->getFields()->getType($fieldName);
		} catch (KeyInvalidException $e) {
			return '';
		}
	}
	public function getFieldValue($fieldName)
	{
		try {
			return $this->getFields()->getValue($fieldName);
		} catch (KeyInvalidException $e) {
			return '';
		}
	}
	public function setFieldValue($fieldName, $value)
	{
		$this->getFields()->setValue($fieldName, $value);
	}
	public function setStructureRef($fieldName, $structureRef)
	{
		$this->getFields()->setStructureRef($fieldName, $structureRef);
	}
	public function setData($keyData, $data = null)
	{
		echo "TODO";
		/* Setting full structure */
		if ($data === null) {
			// TODO errores si campo existe y tipo dato válido
			$this->data = $keyData;
		}
		/* Set key / value */ else {
			$this->data[$keyData] = $data;
		}
	}
	public function getData($key = null)
	{
		$data = array();
		foreach ($this->getFields() as $field) {
			$data[$field->getId()] = $field->getValue();
		}

		return $data;
	}
	public function setParent($parent)
	{
		$this->parent = $parent; // ContentDO
	}
	public function getParent()
	{
		return $this->parent;
	}
	public function setCountParents($countParents)
	{
		$this->countParents = $countParents;
	}
	public function getCountParents()
	{
		return $this->countParents;
	}
	public function setCountAliasId($countAliasId)
	{
		$this->countAliasId = $countAliasId;
	}
	public function getCountAliasId()
	{
		return $this->countAliasId;
	}

	// With the data structure, build the skeleton of content
	public function buildSkeleton($structure)
	{
		$this->setIdStructure($structure->getId());
		$this->setFields($structure->getFields());
	}
	private function isFilledRef($value)
	{
		return (isset($value[0]['ref']) && $value[0]['ref']);
	}
	public function load($rawData, $structure = null)
	{
		$this->setId($rawData['id']);
		$this->setTitle($rawData['title']);
		$periodOfValidity = isset($rawData['period_of_validity']) ? $rawData['period_of_validity'] : [];
		$this->setPeriodOfValidity($periodOfValidity);
		$aliasId = isset($rawData['alias_id']) ? $rawData['alias_id'] : null;
		$this->setAliasId($aliasId);
		$tags = isset($rawData['tags']) ? $rawData['tags'] : [];
		$this->setTags($tags);
		$profile = isset($rawData['profile']) ? $rawData['profile'] : [];
		$this->setProfileValues($profile);
		$this->setSaveTime($rawData['save_ts']);
		if ($structure !== null) {
			$this->buildSkeleton($structure);
		}
		$fields = $this->getFields();
		if (is_array($rawData['data'])) {
			foreach ($rawData['data'] as $key => $value) {
				$this->getFields()->setValue($key, $value);
				switch ($this->getFields()->get($key)->getType()) {
					case 'content':
					case 'collection':
						if ($this->isFilledRef($value)) { // Non set emptys references
							$this->getFields()->setRef($key, $value);
						}
						break;
				};
			}
			//d($this->getFields());
		}
		//+d($this);
		//$this->setFields($fields);
	}
	public function tokenizeData()
	{
		$aFieldsData = array();
		// Test if the field value is a real value or reference
		foreach ($this->getFields() as $field) {
			$type = $field->getType();
			switch ($type) {
				case 'content':
					$itemValue = $field->getValue();
					$value = is_object($itemValue) ? $itemValue->tokenizeData() : $itemValue;
					break;
				case 'collection':
					$value = [];
					foreach ($field->getValue() as $itemValue) {
						$value[] = is_object($itemValue) ? $itemValue->tokenizeData() : $itemValue;
					}
					break;
				default:
					$value = ValueFormater::encode($field->getValue(), $type, ValueFormater::FORMAT_TOKENIZE);
					break;
			}
			$aFieldsData[$field->getId()] = $value;
		}

		return  array(
			'id' => $this->getId(),
			'id_structure' => $this->getIdStructure(),
			'save_ts' => $this->getSaveTime(),
			'title' => $this->getTitle(),
			'period_of_validity' => $this->getPeriodOfValidity(ContentDo::PERIOD_OF_VALIDITY_TOKENIZE),
			'alias_id' => $this->getAliasId(),
			'tags' => $this->getTags(),
			'profile' => $this->getProfile()->getValue(),
			'data' => $aFieldsData
		);
	}

	/**
	 * Get the value of saveTime
	 */
	public function getSaveTime()
	{
		return $this->saveTime;
	}

	/**
	 * Set the value of saveTime
	 *
	 * @return  self
	 */
	public function setSaveTime($saveTime)
	{
		$this->saveTime = $saveTime;

		return $this;
	}
}
