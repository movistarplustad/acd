<?php
namespace Acd;

require ('../autoload.php');
session_start();

$returnUrl = 'index.php';
// First: check is loged
$loginCookie = isset($_COOKIE['login']) ? $_COOKIE['login'] : null;
$token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
$loginForm = isset($_POST['login']) ? $_POST['login'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;
$remember = isset($_POST['remember']) && ($_POST['remember'] === '1');

if (Model\Auth::loginByCredentials($loginForm, $password, $remember)) {
	$returnUrl .= '?r=okcred';
}
elseif (Model\Auth::loginByPersintence($loginCookie, $token)) {
	$returnUrl .= '?r=okpers';
}
else {
	Model\Auth::logout();
	$returnUrl .= '?r=kologin&login='.urlencode($loginForm);
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Location:$returnUrl");