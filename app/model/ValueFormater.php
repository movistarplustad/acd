<?php
namespace Acd\Model;

class ValueFormaterInvalidFormatException extends \exception {}
class ValueFormater
{
	const TYPE_DATE = 'date';
	const TYPE_DATE_TIME = 'date_time';
	const TYPE_DATE_RANGE = 'date_range';
	const TYPE_DATE_TIME_RANGE = 'date_time_range';

	// Formats to getting and setting values
	const FORMAT_INTERNAL = 0;
	const FORMAT_EDITOR = 1;

	public static function decode($value, $type, $format) {
		//throw new StorageKeyInvalidException("Invalid format type $format.");

		if($value) {
			// Empty values return empty string
			$formater[self::TYPE_DATE][self::FORMAT_EDITOR] = function ($value) {
				$valueDecode = \DateTime::createFromFormat('Y-m-d', $value);
				$valueDecode->setTime(0, 0, 0); 
				return $valueDecode->getTimeStamp();
			};
			$formater[self::TYPE_DATE_RANGE][self::FORMAT_EDITOR] = function ($aValue) {
				foreach ($aValue as $value) {
					if($value) {
						$valueDecode = \DateTime::createFromFormat('Y-m-d', $value);
						$valueDecode->setTime(0, 0, 0); 
						$result[] = $valueDecode->getTimeStamp();
					}
				}
				$result = array_pad($result, 2, '');
				return $result;
			};
			$formater[self::TYPE_DATE_TIME][self::FORMAT_EDITOR] = function ($value) {
				$valueDecode = \DateTime::createFromFormat('Y-m-d*H:i:s*', $value);
				return $valueDecode->getTimeStamp();
			};
			$formater[self::TYPE_DATE_TIME_RANGE][self::FORMAT_EDITOR] = function ($aValue) {
				foreach ($aValue as $value) {
					if($value) {
						$valueDecode = \DateTime::createFromFormat('Y-m-d*H:i:s*', $value);
						$result[] = $valueDecode->getTimeStamp();
					}
				}
				$result = array_pad($result, 2, '');
				return $result;
			};

			if(isset($formater[$type][$format])) {
				return $formater[$type][$format]($value);
			}
			else {
				return $value;
			}
		}
	}
	public static function encode($value, $type, $format) {
		if($value) {
			// Empty values return empty string
			$formater[self::TYPE_DATE][self::FORMAT_EDITOR] = function ($value) {
				return date('Y-m-d', $value);
			};
			$formater[self::TYPE_DATE_RANGE][self::FORMAT_EDITOR] = function ($aValue) {
				foreach ($aValue as $value) {
					if($value) {
						$result[] = date('Y-m-d', $value);
					}
				}
				$result = array_pad($result, 2, '');
				return $result;
			};
			$formater[self::TYPE_DATE_TIME][self::FORMAT_EDITOR] = function ($value) {
				return date('Y-m-d\Th:m:s\Z', $value);
			};
			$formater[self::TYPE_DATE_TIME_RANGE][self::FORMAT_EDITOR] = function ($aValue) {
				foreach ($aValue as $value) {
					if($value) {
						$result[] = date('Y-m-d\Th:m:s\Z', $value);
					}
				}
				$result = array_pad($result, 2, '');
				return $result;
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