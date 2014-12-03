<?php
require ('conf.php');
require_once (DIR_BASE.'/class/auth.php');

$returnUrl = 'index.php';
//session_start();
auth::logout();

header("Location:$returnUrl");