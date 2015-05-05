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
	const TYPE_BOOLEAN = 'boolean';

	// Formats to getting and setting values
	const FORMAT_INTERNAL = 0;
	const FORMAT_EDITOR = 1;
	const FORMAT_HUMAN = 2;

	const PERIOD_OF_VALIDITY_START = 'start';
	const PERIOD_OF_VALIDITY_END = 'end';

	public static function decode($value, $type, $format) {
		//throw new StorageKeyInvalidException("Invalid format type $format.");

		// Array of procesing functions
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
			$result = [
				ValueFormater::PERIOD_OF_VALIDITY_START => -INF,
				ValueFormater::PERIOD_OF_VALIDITY_END => INF,
			];
			if (is_array($aValue)){
				foreach ($aValue as $attributeName => $value) {
					if($value) {
						$valueDecode = \DateTime::createFromFormat('Y-m-d', $value);
						$valueDecode->setTime(0, 0, 0); 
						$result[$attributeName] = $valueDecode->getTimeStamp();
					}
				}
			}
			//$result = array_pad($result, 2, '');
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
			$result = [
				ValueFormater::PERIOD_OF_VALIDITY_START => -INF,
				ValueFormater::PERIOD_OF_VALIDITY_END => INF,
			];

//d($aValue, debug_backtrace());
			if (is_array($aValue)){
				foreach ($aValue as $attributeName => $value) {
					if($value) {
						$valueDecode = \DateTime::createFromFormat('Y-m-d*H:i:s*', $value);
						$result[$attributeName] = $valueDecode->getTimeStamp();
					}
				}
			}
			//$result = array_pad($result, 2, '');
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
		$formater[self::TYPE_BOOLEAN][self::FORMAT_EDITOR] = function ($value) {
			return $value == 1;
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
				$result = [
					ValueFormater::PERIOD_OF_VALIDITY_START => '',
					ValueFormater::PERIOD_OF_VALIDITY_END => '',
				];
				foreach ($aValue as $attributeName => $value) {
					if($value && is_finite($value)) {
						$result[$attributeName] = date('Y-m-d', $value);
					}
				}
				//$result = array_pad($result, 2, '');
				return $result;
			};
			$formater[self::TYPE_DATE_TIME][self::FORMAT_EDITOR] = function ($value) {
				return date('Y-m-d\TH:i:s\Z', $value);
			};
			$formater[self::TYPE_DATE_TIME_RANGE][self::FORMAT_EDITOR] = function ($aValue) {
				$result = [
					ValueFormater::PERIOD_OF_VALIDITY_START => '',
					ValueFormater::PERIOD_OF_VALIDITY_END => '',
				];
				foreach ($aValue as $attributeName => $value) {
					if($value && is_finite($value)) {
						$result[$attributeName] = date('Y-m-d\TH:i:s\Z', $value);
					}
				}
				//$result = array_pad($result, 2, '');
				return $result;
			};
			$formater[self::TYPE_TAGS][self::FORMAT_EDITOR] = function ($value) {
				return implode(' ', $value);
			};
			$formater[self::TYPE_BOOLEAN][self::FORMAT_EDITOR] = function ($value) {
				return $value ? ' checked="checked"' : '';
			};
			$formater[self::TYPE_DATE_RANGE][self::FORMAT_HUMAN] = function ($aValue) {
				$result = [
					ValueFormater::PERIOD_OF_VALIDITY_START => '∞',
					ValueFormater::PERIOD_OF_VALIDITY_END => '∞',
				];
				foreach ($aValue as $attributeName => $value) {
					if($value && is_finite($value)) {
						$result[$attributeName] = date('j M', $value);
					}
				}
				//$result = array_pad($result, 2, '');
				return implode(' - ', $result);
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