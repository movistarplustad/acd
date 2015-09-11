<?php
// TODO: In future can be diferent views
require('../autoload.php');

session_start();

$navigation = new \Acd\Controller\SessionNavigation();
$navigation->load();
echo '<ul>';
foreach ($navigation->getStack() as $item) {
	echo '<li><a href="'.$item['url'].'">'.$item['hash'].'</a></li>';
}
echo '</ul>';
