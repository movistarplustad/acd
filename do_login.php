<?php
require ('conf.php');
require_once (DIR_BASE.'/class/auth.php');

$login = $_POST['login'];
$password = $_POST['password'];
$remember = isset($_POST['remember']) && ($_POST['remember'] === '1');
auth::login($login, $password, $remember);
session_start();
$_SESSION['loged']  = true;
$returnUrl = 'index.php';

header('Location:$returnUrl');