<?php
namespace Acd\Model;

class ValueFormaterInvalidFormatException extends \exception {}
class ValueFormater
{
	const TYPE_TEXT_SIMPLE = 'text_simple';
	const TYPE_DATE = 'date';
	const TYPE_DATE_TIME = 'date_time';
	const TYPE_DATE_RANGE = 'date_range';
	const TYPE_DATE_TIME_RANGE = 'date_time_range';
	const TYPE_TAGS = 'tags';

	// Formats to getting and setting values
	const FORMAT_INTERNAL = 0;
	const FORMAT_EDITOR = 1;

	public static function decode($value, $type, $format) {
		//throw new StorageKeyInvalidException("Invalid format type $format.");

		$formater[self::TYPE_DATE][self::FORMAT_EDITOR] = function ($value) {
			// Empty values return empty string
			if ($value) {
				$valueDecode = \DateTime::createFromFormat('Y-m-d', $value);
				$valueDecode->setTime(0, 0, 0); 
				return $valueDecode->getTimeStamp();
			}
			else {
				return '';
			}
		};
		$formater[self::TYPE_DATE_RANGE][self::FORMAT_EDITOR] = function ($aValue) {
			// Empty values return empty array
			if (is_array($aValue)){
				$result = [];
				foreach ($aValue as $value) {
					if($value) {
						$valueDecode = \DateTime::createFromFormat('Y-m-d', $value);
						$valueDecode->setTime(0, 0, 0); 
						$result[] = $valueDecode->getTimeStamp();
					}
				}
			}
			else {
				$result = [];
			}
			$result = array_pad($result, 2, '');
			return $result;
		};
		$formater[self::TYPE_DATE_TIME][self::FORMAT_EDITOR] = function ($value) {
			if ($value) {
				$valueDecode = \DateTime::createFromFormat('Y-m-d*H:i:s*', $value);
				return $valueDecode->getTimeStamp();
			}
			else {
				return '';
			}
		};
		$formater[self::TYPE_DATE_TIME_RANGE][self::FORMAT_EDITOR] = function ($aValue) {
			if (is_array($aValue)){
				$result = [];
				foreach ($aValue as $value) {
					if($value) {
						$valueDecode = \DateTime::createFromFormat('Y-m-d*H:i:s*', $value);
						$result[] = $valueDecode->getTimeStamp();
					}
				}
			}
			else {
				$result = [];
			}
			$result = array_pad($result, 2, '');
			return $result;
		};
		$formater[self::TYPE_TAGS][self::FORMAT_EDITOR] = function ($value) {
			
			if($value) {
				$value = trim($value);
				$value = preg_replace('/\s+/', ' ', $value);
				return explode(' ', $value);
			}
			else {
				return array();
			}
		};


		if(isset($formater[$type][$format])) {
			return $formater[$type][$format]($value);
		}
		else {
			return $value;
		}
	}
	public static function encode($value, $type, $format) {
		if($value) {
			// Empty values return empty string
			$formater[self::TYPE_DATE][self::FORMAT_EDITOR] = function ($value) {
				return date('Y-m-d', $value);
			};
			$formater[self::TYPE_DATE_RANGE][self::FORMAT_EDITOR] = function ($aValue) {
				$result = [];
				foreach ($aValue as $value) {
					if($value) {
						$result[] = date('Y-m-d', $value);
					}
				}
				$result = array_pad($result, 2, '');
				return $result;
			};
			$formater[self::TYPE_DATE_TIME][self::FORMAT_EDITOR] = function ($value) {
				return date('Y-m-d\TH:i:s\Z', $value);
			};
			$formater[self::TYPE_DATE_TIME_RANGE][self::FORMAT_EDITOR] = function ($aValue) {
				$result = [];
				foreach ($aValue as $value) {
					if($value) {
						$result[] = date('Y-m-d\TH:i:s\Z', $value);
					}
				}
				$result = array_pad($result, 2, '');
				return $result;
			};
			$formater[self::TYPE_TAGS][self::FORMAT_EDITOR] = function ($value) {
				return implode(' ', $value);
			};

			if(isset($formater[$type][$format])) {
				return $formater[$type][$format]($value);
			}
			else {
				return $value;
			}
		}
	}
}