<?php
//Ficheros
define('DIR_BASE', dirname(__FILE__));
define('DIR_DATA', DIR_BASE.'/data');

class conf {
	public static $STORAGE_TYPES = array(
		'text/plain' => 'text/plain',
		'mongodb' => 'Mongo DB',
		'mysql' => 'MySql'
	);

	public static $FIELD_TYPES =  array(
		'text_simple' => 'Simple text',
		'text_multiline' => 'Multiline text area'
	);
}

/* Debug */
if (file_exists(DIR_BASE.'/../tools/kint/Kint.class.php')) {
	require DIR_BASE.'/../tools/kint/Kint.class.php';
}