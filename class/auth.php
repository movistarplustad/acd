<?php
class auth  {
	public static function isLoged($nick, $hash) {
		if (conf::$USE_AUTHENTICATION === false) {
			return true;
		}
		$path_data = DIR_DATA.'/auth.json';
		return $path_data;
	}
}