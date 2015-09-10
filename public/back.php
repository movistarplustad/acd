<?php
namespace Acd;

require ('../autoload.php');
session_start();

$navigation = new \Acd\Controller\SessionNavigation();
$navigation->load();
try {
	$navigation->pop();
	$lastNavigation = $navigation->top();
	//d($lastNavigation);
	$returnUrl = $lastNavigation['url'];
	$navigation->save();
}
catch(Controller\SessionNavigationException $e) {
	$returnUrl = 'index.php';
}

header("Location:$returnUrl");