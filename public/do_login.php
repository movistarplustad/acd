<?php
namespace Acd;

require ('../conf.php');
require_once (DIR_BASE.'/class/auth.php');
session_start();

$returnUrl = 'index.php';
// First: check is loged
$loginCookie = isset($_COOKIE['login']) ? $_COOKIE['login'] : null;
$token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
$loginForm = isset($_POST['login']) ? $_POST['login'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;
$remember = isset($_POST['remember']) && ($_POST['remember'] === '1');

if (auth::loginByCredentials($loginForm, $password, $remember)) {
	$returnUrl .= '?r=okcred';
}
elseif (auth::loginByPersintence($loginCookie, $token)) {
	$returnUrl .= '?r=okpers';
}
else {
	auth::logout();
	$returnUrl .= '?r=kologin&login='.urlencode($loginForm);
}

header("Location:$returnUrl");