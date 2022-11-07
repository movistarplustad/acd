<?php

use Acd\Controller\RolPermissionHttp;
use Acd\Model\SessionNavigation;
use Acd\View\History;

// TODO: In future can be diferent views
require '../config/conf.php';

ini_set('session.gc_maxlifetime', $_ENV[ 'ACD_SESSION_GC_MAXLIFETIME']);
session_start();

if(!RolPermissionHttp::checkUserEditor([$_ENV['ACD_ROL_DEVELOPER'], $_ENV['ACD_ROL_EDITOR']])) die();

$navigation = new SessionNavigation();
$navigation->load();

$historyOu = new History();
$historyOu->setItems($navigation->getStack());

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
echo $historyOu->render();
