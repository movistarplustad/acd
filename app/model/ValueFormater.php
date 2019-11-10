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
	const TYPE_LINK = 'link';
	const TYPE_LIST_MULTIPLE = 'list_multiple_options';
	const TYPE_TEXT_HANDMADE_HTML = 'text_handmade_html';
	const TYPE_COORDINATE = 'coordinate';
	const TYPE_COLOR_RGB = 'color_rgb';
	const TYPE_COLOR_RGBA = 'color_rgba';
	const TYPE_ID = 'id';

	// Formats to getting and setting values
	const FORMAT_INTERNAL = 0;
	const FORMAT_EDITOR = 1;
	const FORMAT_HUMAN = 2;
	const FORMAT_TOKENIZE = 3;

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
				return explode(',', $value);
			}
			else {
				return array();
			}
		};
		$formater[self::TYPE_BOOLEAN][self::FORMAT_EDITOR] = function ($value) {
			return $value == 1;
		};
		$formater[self::TYPE_LIST_MULTIPLE][self::FORMAT_EDITOR] = function ($value) {
			if($value) {
				return $value;
			}
			else {
				return array();
			}
		};
		$formater[self::TYPE_TEXT_HANDMADE_HTML][self::FORMAT_EDITOR] = function ($value) {
			// cleanHtmlFragment
			if (extension_loaded('tidy') === true) {
				$encoding = 'utf8';
				$tidy_config = array
				(
					//'anchor-as-name' => false, //error en producción
					'break-before-br' => true,
					'char-encoding' => $encoding,
					'decorate-inferred-ul' => false,
					'doctype' => 'omit',
					'drop-empty-paras' => false,
					'drop-font-tags' => true,
					'drop-proprietary-attributes' => false,
					'force-output' => false,
					'hide-comments' => false,
					'indent' => true,
					'indent-attributes' => false,
					'indent-spaces' => 2,
					'input-encoding' => $encoding,
					'join-styles' => false,
					'logical-emphasis' => false,
					'merge-divs' => false,
					//'merge-spans' => false, //error en producción
					'new-blocklevel-tags' => 'main article aside audio details dialog figcaption figure footer header hgroup menutidy nav section source summary track video',
					'new-empty-tags' => 'command embed keygen source track wbr',
					'new-inline-tags' => 'canvas command data datalist embed keygen mark meter output progress time wbr picture',
					'newline' => 0,
					'numeric-entities' => false,
					'output-bom' => false,
					'output-encoding' => $encoding,
					'output-html' => true,
					'preserve-entities' => true,
					'quiet' => true,
					'quote-ampersand' => true,
					'quote-marks' => false,
					'repeated-attributes' => 1,
					'show-body-only' => true,
					'show-warnings' => false,
					'sort-attributes' => 1,
					'tab-size' => 4,
					'tidy-mark' => false,
					'vertical-space' => true,
					'wrap' => 0,
				);
				// Clean previous tags and attributes 'dangerous'
				$value = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $value);  // Clean tag <script>
				$value = preg_replace("/style *= *('|\")([^('|\")]*)('|\")/is", "", $value);  // Clean attributes style="foo:var"
				$tidy = new \tidy();
				$tidy->parseString($value, $tidy_config, 'utf8');
				$tidy->cleanRepair();
				$result = \tidy_get_output($tidy);
				return $result;
			}
			else {
				throw new ValueFormaterInvalidFormatException("Text not validated. Tidy extension not instaled", 1);
				return $value;
			}
		};
		$formater[self::TYPE_COORDINATE][self::FORMAT_EDITOR] = function ($value) {
			$result = ['latitude' => 0.0, 'longitude' => 0.0];
			if (isset($value['latitude'])) {
				$result['latitude'] = floatval($value['latitude']);
			}
			if (isset($value['longitude'])) {
				$result['longitude'] = floatval($value['longitude']);
			}
			return $result;
		};
		$formater[self::TYPE_COLOR_RGB][self::FORMAT_EDITOR] = function ($value) {
			// val[rgb] hex format, ej. #46e7da
			if (isset($value['empty']) && $value['empty']) {
				$result = null;
			}
			else {
				$result = $value['rgb'];
			}
			return $result;
		};
		$formater[self::TYPE_COLOR_RGBA][self::FORMAT_EDITOR] = function ($value) {
			// val[rgb] + val[alfa] y hex format, ej. #46e7da01
			if (isset($value['empty']) && $value['empty']) {
				$result = null;
			}
			else {
				$result = $value['rgb'];
				// 0..1 float value to hex zero-padded
				$result .= sprintf('%02s', dechex($value['alfa']));
			}
			return $result;
		};
		$formater[self::TYPE_ID][self::FORMAT_EDITOR] = function ($value) {
			return $value === '-' ? '' : $value;
		};

		if(isset($formater[$type][$format])) {
			return $formater[$type][$format]($value);
		}
		else {
			return $value;
		}
	}
	public static function encode($value, $type, $format) {
		// Empty values return empty string
		$formater[self::TYPE_DATE][self::FORMAT_EDITOR] = function ($value) {
			return $value ? date('Y-m-d', $value) : '';
		};
		$formater[self::TYPE_DATE_RANGE][self::FORMAT_EDITOR] = function ($aValue) {
			$result = [
				ValueFormater::PERIOD_OF_VALIDITY_START => '',
				ValueFormater::PERIOD_OF_VALIDITY_END => '',
			];
			if(is_array($aValue)) {
				foreach ($aValue as $attributeName => $value) {
					if($value && is_finite($value)) {
						$result[$attributeName] = date('Y-m-d', $value);
					}
				}
			}
			//$result = array_pad($result, 2, '');
			return $result;
		};
		$formater[self::TYPE_DATE_TIME][self::FORMAT_EDITOR] = function ($value) {
			return $value ? date('Y-m-d\TH:i:s\Z', $value) : '';
		};
		$formater[self::TYPE_DATE_TIME_RANGE][self::FORMAT_EDITOR] = function ($aValue) {
			$result = [
				ValueFormater::PERIOD_OF_VALIDITY_START => '',
				ValueFormater::PERIOD_OF_VALIDITY_END => '',
			];
			if(is_array($aValue)) {
				foreach ($aValue as $attributeName => $value) {
					if($value && is_finite($value)) {
						$result[$attributeName] = date('Y-m-d\TH:i:s\Z', $value);
					}
				}
			}
			//$result = array_pad($result, 2, '');
			return $result;
		};
		// Purge infinite values in FORMAT_TOKENIZE, transform to empty string (infinite values not accepted in json grammar)
		$formater[self::TYPE_DATE][self::FORMAT_TOKENIZE] = function ($value) {
			$result = is_finite((double) $value) ? $value : '';
			return $result;
		};
		$formater[self::TYPE_DATE_RANGE][self::FORMAT_TOKENIZE] = function ($aValue) {
			$result = [];
			foreach ($aValue as $key => $value) {
				$result[$key] = is_finite((double) $value) ? $value : '';
			}
			return $result;
		};
		$formater[self::TYPE_DATE_TIME][self::FORMAT_TOKENIZE] = $formater[self::TYPE_DATE][self::FORMAT_TOKENIZE];
		$formater[self::TYPE_DATE_TIME_RANGE][self::FORMAT_TOKENIZE] = $formater[self::TYPE_DATE_RANGE][self::FORMAT_TOKENIZE];
		$formater[self::TYPE_TAGS][self::FORMAT_EDITOR] = function ($value) {
			return $value ? implode(',', $value) : '';
		};
		$formater[self::TYPE_BOOLEAN][self::FORMAT_EDITOR] = function ($value) {
			return $value ? ' checked="checked"' : '';
		};
		$formater[self::TYPE_DATE_TIME][self::FORMAT_HUMAN] = function ($value) {
			if($value) {
				return date('j M G:i\h', $value);
			}
			else {
				return '';
			}
		};
		$formater[self::TYPE_DATE_RANGE][self::FORMAT_HUMAN] = function ($aValue) {
			$result = [
				ValueFormater::PERIOD_OF_VALIDITY_START => '∞',
				ValueFormater::PERIOD_OF_VALIDITY_END => '∞',
			];
			$bModified = false;
			if(is_array($aValue)) {
				foreach ($aValue as $attributeName => $value) {
					if($value && is_finite($value)) {
						$result[$attributeName] = date('j M G:i\h', $value);
						$bModified = true;
					}
				}
			}
			//$result = array_pad($result, 2, '');
			return $bModified ? implode(' - ', $result) : '';
		};

		// Empty values return other content
		$formater[self::TYPE_LINK][self::FORMAT_EDITOR] = function ($value) {
			//return implode(',', $value);
			return $value ? $value : array('href' => '', 'description' => '');
		};
		$formater[self::TYPE_LIST_MULTIPLE][self::FORMAT_EDITOR] = function ($value) {
			//return implode(',', $value);
			return $value ? $value : array();
		};
		$formater[self::TYPE_COORDINATE][self::FORMAT_EDITOR] = function ($value) {
			$result = ['latitude' => 0.0, 'longitude' => 0.0];
			if (isset($value['latitude'])) {
				$result['latitude'] = floatval($value['latitude']);
			}
			if (isset($value['longitude'])) {
				$result['longitude'] = floatval($value['longitude']);
			}
			return $result;
		};
		$formater[self::TYPE_COLOR_RGB][self::FORMAT_EDITOR] = function ($value) {
			//$result = ['rgb' => '#000000'];

			$result['rgb'] = null;
			if($value) {
				$result['rgb'] = '#' . substr($value, 1, 6);
			}

			return $result;
		};
		$formater[self::TYPE_COLOR_RGBA][self::FORMAT_EDITOR] = function ($value) {
			//$result = ['rgb' => '#000000', 'alfa' => 0.0];

			$result['rgb'] = null;
			$result['alfa'] = null;
			if($value) {
				$result['rgb'] = '#' . substr($value, 1, 6);
				$result['alfa'] = hexdec(substr($value, 7, 2));
			}
			return $result;
		};
		$formater[self::TYPE_ID][self::FORMAT_EDITOR] = function ($value) {
			return $value ? $value : '-';
		};

		if(isset($formater[$type][$format])) {
			return $formater[$type][$format]($value);
		}
		else {
			return $value;
		}
	}
}
