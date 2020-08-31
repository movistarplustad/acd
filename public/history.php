<?php
namespace Acd;
use \Acd\Model\SessionNavigation;

// TODO: In future can be diferent views
require('../autoload.php');

ini_set('session.gc_maxlifetime', conf::$SESSION_GC_MAXLIFETIME);
session_start();

$navigation = new SessionNavigation();
$navigation->load();

$historyOu = new View\History();
$historyOu->setItems($navigation->getStack());

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
echo $historyOu->render();