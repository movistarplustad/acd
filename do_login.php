<?php
require ('conf.php');
require_once (DIR_BASE.'/class/auth.php');
session_start();

$returnUrl = 'index.php';
// First: check is loged
$loginCookie = isset($_COOKIE['login']) ? $_COOKIE['login'] : null;
$hash = isset($_COOKIE['hash']) ? $_COOKIE['hash'] : null;
$loginForm = isset($_POST['login']) ? $_POST['login'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;
$remember = isset($_POST['remember']) && ($_POST['remember'] === '1');

if (auth::loginByCredentials($loginCookie, $password, $remember)) {
	$_SESSION['loged'] = true;
}
if (auth::loginByPersintence($login, $hash)) {
	$_SESSION['loged']  = true;
}


header("Location:$returnUrl");