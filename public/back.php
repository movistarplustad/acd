<?php
namespace Acd;
use \Acd\Model\SessionNavigation;

require ('../autoload.php');
session_start();

$backSteps = isset($_GET['p']) ? (integer) $_GET['p'] : 1;
$navigation = new SessionNavigation();
$navigation->load();
try {
	for ($n = 0; $n < $backSteps; $n++) {
		$navigation->pop();
	}
	$lastNavigation = $navigation->top();
	//d($lastNavigation);
	$returnUrl = $lastNavigation['url'];
	$navigation->save();
}
catch(Model\SessionNavigationException $e) {
	$returnUrl = 'index.php';
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Location:$returnUrl");