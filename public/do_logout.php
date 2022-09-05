<?php
namespace Acd;

require '../autoload.php';
require '../config/conf2.php';


ini_set('session.gc_maxlifetime', $_ENV[ 'ACD_SESSION_GC_MAXLIFETIME']);
session_start();

$returnUrl = 'index.php';
Model\Auth::logout();
session_destroy();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Location:$returnUrl");