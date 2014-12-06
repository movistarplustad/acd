<?php
class AuthInvalidUserException extends exception {}
class auth  {
	private static function hashPassword($password) {
		return hash('sha1', conf::$AUTHENTICATION_SEED.$password);
	}
	private static function persistentFilePath($login) {
		return conf::$PATH_AUTH_PREMANENT_LOGIN_DIR.'/'.hash('sha1', $login);
	}
	public static function isLoged() {
		if (conf::$USE_AUTHENTICATION === false || (isset($_SESSION['loged']) && $_SESSION['loged'] === true)) {
			return true;
		}
		else {
			$loginCookie = isset($_COOKIE['login']) ? $_COOKIE['login'] : null;
			$token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;

			return auth::loginByPersintence($loginCookie, $token);
		}
	}

	private static function loadAllCredentials() {
		$path = conf::$PATH_AUTH_CREDENTIALS_FILE;
		$content = file_get_contents($path);
		$aCredentials = json_decode($content, true);

		return $aCredentials;
	}
	public static function loginByCredentials($login, $password, $remember) {
		$aCredentials = auth::loadAllCredentials();
		// TODO: controlar errores
		$bLoginCorrect = isset($aCredentials[$login]) && ($aCredentials[$login] === auth::hashPassword($password));

		// Remember login
		if ($bLoginCorrect && $remember) {
			$persistentData = array(
				'login' => $login,
				'token' => hash('sha1', uniqid()),
				'timestamp' => time()
			);
			$jsonCredentials = json_encode($persistentData);
			$path = auth::persistentFilePath($login);
			if (!$handle = fopen($path, 'a')) {
				 echo "Cannot open file ($path)";
				 exit;
			}

			// Write $jsonCredentials to our opened file.
			if (fwrite($handle, $jsonCredentials) === FALSE) {
				echo "Cannot write to file ($path)";
				exit;
			}
			fclose($handle);
			$expiration = time()+31536000; // 1 year
			setcookie('login', $persistentData['login'],$expiration , '/', '', 0, 0);
			setcookie('token', $persistentData['token'], $expiration, '/', '', 0, 1);
		}

		if ($bLoginCorrect) {
			$_SESSION['loged'] = true;
			$_SESSION['loged_by_password'] = true;
			$_SESSION['login'] = $login;
		}
		return $bLoginCorrect;
	}
	public static function loginByPersintence($login, $token) {
		$bLoginCorrect = false;
		$path = auth::persistentFilePath($login);
		if(file_exists($path)) {
			$content = file_get_contents($path);
			$aPersistentCredentials = json_decode($content, true);

			$bLoginCorrect = ($aPersistentCredentials['login'] === $login && $aPersistentCredentials['token'] === $token);
		}

		if ($bLoginCorrect) {
			$_SESSION['loged'] = true;
			$_SESSION['loged_by_password'] = false;
			$_SESSION['login'] = $login;
		}

		return $bLoginCorrect;
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

		// Eliminar los datos persistentes
		$loginCookie = isset($_COOKIE['login']) ? $_COOKIE['login'] : null;
		$token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
		$path = auth::persistentFilePath($loginCookie);
		if (file_exists($path)) {
			unlink($path);
		}
		setcookie('login', '', time() - 42000, '/', '', 0, 0);
		setcookie('token', '', time() - 42000, '/', '', 0, 0);
	}
	public static function addUser($login, $password) {
		if ($login === '' || $password === '') {
			throw new AuthInvalidUserException("Invalid login or password [$login] : [$password]");
		}
		$aCredentials = auth::loadAllCredentials();
		$aCredentials[$login] = auth::hashPassword($password);
		$jsonCredentials = json_encode($aCredentials);

		$path = conf::$PATH_AUTH_CREDENTIALS_FILE;
		$tempPath = conf::$PATH_AUTH_CREDENTIALS_FILE.'.tmp';
		if (!$handle = fopen($tempPath, 'a')) {
			 echo "Cannot open file ($tempPath)";
			 exit;
		}

		// Write $jsonCredentials to our opened file.
		if (fwrite($handle, $jsonCredentials) === FALSE) {
			echo "Cannot write to file ($tempPath)";
			exit;
		}
		fclose($handle);
		rename($tempPath, $path);
	}

}