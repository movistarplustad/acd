<?php
namespace Acd;

require ('../conf.php');
require_once (DIR_BASE.'/app/model/Auth.php');

$returnUrl = 'index.php';
//session_start();
Model\Auth::logout();

header("Location:$returnUrl");