<?php
//Ficheros
define('DIR_BASE', dirname(__FILE__));
define('DIR_DATA', DIR_BASE.'/data');

class conf {
	public static $STORAGE_TYPES = array('text/plain' => 'text/plain', 'mongodb' => 'Mongo DB', 'mysql' => 'MySql');
}