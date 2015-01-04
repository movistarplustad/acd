<?php
namespace Acd\Model;

//class TypeKeyInvalidException extends exception {}
class Permission {
	protected $map;
	public function __construct() {
	}
	public function load() {
		$content = file_get_contents(\Acd\conf::$PERMISSION_PATH);
		$this->map = json_decode($content);
	}
	public function hasAccess($rol, $item) {
		if ($bPresent = isset($this->map->{$rol})) {
			$bPresent = in_array($item, $this->map->{$rol});
		}
		return $bPresent;
	}
}