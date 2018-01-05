<?php
namespace Acd;
use Acd\conf;
use Acd\Model\UserLoader;
use \Acd\Model\Query;

require ('../autoload.php');
session_start();

$returnUrl = isset($_POST['re']) ? $_POST['re'] : 'index.php';
$queryStringSeparator = strpos($returnUrl, '?') ? '&' : '?';
// First: check is loged
$loginCookie = isset($_COOKIE[conf::$COOKIE_PREFIX.'login']) ? $_COOKIE[conf::$COOKIE_PREFIX.'login'] : null;
$token = isset($_COOKIE[conf::$COOKIE_PREFIX.'token']) ? $_COOKIE[conf::$COOKIE_PREFIX.'token'] : null;
$loginForm = isset($_POST['login']) && $_POST['login'] !== '' ? $_POST['login'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;
$remember = isset($_POST['remember']) && ($_POST['remember'] === '1');

try {
	if (Model\Auth::loginByCredentials($loginForm, $password, $remember)) {
		$returnUrl .= $queryStringSeparator.'r=okcred';
	}
	elseif (Model\Auth::loginByPersintence($loginCookie, $token)) {
		$returnUrl .= $queryStringSeparator.'r=okpers';
	}
	else {
		$result = 'kologin';
		Model\Auth::logout();
		// Count total users un database, report if zero users found
		$userLoader = new UserLoader();
		$query = new Query();
		$query->setType('all');
		if($userLoader->load($query)->length() === 0) {
			$result = 'kologinzerouser';
		}
		$returnUrl = './?re='.urlencode($returnUrl).'&login='.urlencode($loginForm).'&remember='.($remember ? '1' : '0');
	}

	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	header("Location:$returnUrl");
}
catch(Model\AuthInvalidUserException $e) {
	header("HTTP/1.0 404 Not Found");
	die("Error 404. ".$e->getMessage());
}
