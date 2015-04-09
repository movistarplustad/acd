<?php
namespace Acd\Model;

class ValueFormater
{
	const TYPE_DATE = 'date';

	// Formats to getting and setting values
	const FORMAT_INTERNAL = 0;
	const FORMAT_EDITOR = 1;
//, $format = self::FORMAT_INTERNAL
	public function format($value) {
		switch ($format) {
			case self::FORMAT_EDITOR:
				$this->value = $this->formatFromEditor($value);
				break;
			default: //FORMAT_INTERNAL
				$this->value = $value;
				break;
		}
	}
	public function getValue($format = self::FORMAT_INTERNAL) {
		switch ($format) {
			case self::FORMAT_EDITOR:
				return $this->formatToEditor($this->value);
				break;
			default:
				return $this->value;
				break;
			}

	private function formatFromEditor($value) {
		switch ($this->getType()) {
			case self::TYPE_DATE:
				d("fecha", $value);
				return $value;
				break;
			default:
				return $value;
				break;
		}
	}
	private function formatToEditor($value) {
		switch ($this->getType()) {
			case self::TYPE_DATE:
				d("fecha", $value);
				return $value;
				break;
			default:
				return $value;
				break;
		}
	}
}