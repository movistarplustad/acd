<?php
class auth  {
	public static function isLoged() {
		if (conf::$USE_AUTHENTICATION === false || (isset($_SESSION['loged']) && $_SESSION['loged'] === true)) {
			return true;
		}
		else {
			return false;
		}
	}
	public static function loginByPersintence($login, $hash) {
		echo "loginByPersintence";
		$path_data = DIR_DATA.'/permanet_auth';
		$result = !($login === '' || $hash === '');
		return $result;
	}

	public static function loginByCredentials($login, $password, $remember) {
		echo "loginByCredentials";
		$path_data = DIR_DATA.'/auth.json';
		$result = !($login === '' || $password === '');
		return $result;
	}
	public static function logout() {
		// Inicializar la sesión.
		// Si está usando session_name("algo"), ¡no lo olvide ahora!
		session_start();

		// Destruir todas las variables de sesión.
		$_SESSION = array();

		// Si se desea destruir la sesión completamente, borre también la cookie de sesión.
		// Nota: ¡Esto destruirá la sesión, y no la información de la sesión!
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}

		// Finalmente, destruir la sesión.
		session_destroy();
	}
}