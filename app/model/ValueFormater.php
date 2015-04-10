<?php
namespace Acd\Model;

class ValueFormaterInvalidFormatException extends \exception {}
class ValueFormater
{
	const TYPE_DATE = 'date';

	// Formats to getting and setting values
	const FORMAT_INTERNAL = 0;
	const FORMAT_EDITOR = 1;

	public function decode($value, $type, $format) {
		switch ($format) {
			case self::FORMAT_EDITOR:
				switch ($type) {
					case self::TYPE_DATE:
						$valueDecode = \DateTime::createFromFormat('d-m-Y', $value);
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
	public function encode($value, $type, $format) {
		switch ($format) {
			case self::FORMAT_EDITOR:
				switch ($type) {
					case self::TYPE_DATE:
						return date(DATE_ATOM, $value);
						break;
					default:
						return $value;
						break;
			}
		}
	}
}