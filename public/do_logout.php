<?php
namespace Acd;

require ('../autoload.php');

$returnUrl = 'index.php';
//session_start();
Model\Auth::logout();

header("Location:$returnUrl");