<?php
namespace Acd\Model;

class ValueFormaterInvalidFormatException extends \exception {}
class ValueFormater
{
	const TYPE_DATE = 'date';

	// Formats to getting and setting values
	const FORMAT_INTERNAL = 0;
	const FORMAT_EDITOR = 1;

	public static function decode($value, $type, $format) {
		switch ($format) {
			case self::FORMAT_EDITOR:
				switch ($type) {
					case self::TYPE_DATE:
						$valueDecode = \DateTime::createFromFormat('Y-m-d', $value);
						$valueDecode->setTime(0, 0, 0); 
						return $valueDecode->getTimeStamp();
						break;
					default:
						return $value;
						break;
				}
				break;
			default:
				throw new StorageKeyInvalidException("Invalid format type $format.");
				break;
		}
	}
	public static function encode($value, $type, $format) {
		$formater[self::TYPE_DATE][self::FORMAT_EDITOR] = function ($value) {
			return date('Y-m-d', $value);
		};
		if(isset($formater[$type][$format])) {
			return $formater[$type][$format]($value);
		}
		else {
			return $value;
		}
	}
}