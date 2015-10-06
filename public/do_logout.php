<?php
namespace Acd;

require ('../autoload.php');

$returnUrl = 'index.php';
//session_start();
Model\Auth::logout();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Location:$returnUrl");