<?php
namespace Acd;
use \Acd\Model\SessionNavigation;

// TODO: In future can be diferent views
require('../autoload.php');

session_start();

$navigation = new SessionNavigation();
$navigation->load();

$historyOu = new View\History();
$historyOu->setItems($navigation->getStack());
echo $historyOu->render();