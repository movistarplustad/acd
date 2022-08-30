<?php
namespace Acd;

use \Acd\Controller\RolPermissionHttp;

require('../autoload.php');

ini_set('session.gc_maxlifetime', conf::$SESSION_GC_MAXLIFETIME);
session_start();

if(!RolPermissionHttp::checkUserEditor([\Acd\conf::$ROL_DEVELOPER])) die();

$action = isset($_POST['a']) ? strtolower($_POST['a']) : null;
$token = isset($_POST['id']) ? $_POST['id'] : null;
$login = isset($_POST['login']) ? $_POST['login'] : null;

switch ($action) {
    case 'delete':
        $userLoader = new Model\UserLoader();
        $result = $userLoader->deletePersistSession($token) ? 'ok' : 'ko';
        $returnUrl = 'user.php?a=edit&id=' . urlencode($login) . '&r=' . $result;
        break;
    default:
        header("HTTP/1.0 404 Not Found");
        die("Error 404");
        break;
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Location:$returnUrl");
