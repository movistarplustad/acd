<?php
//Ficheros
define('DIR_BASE', dirname(__FILE__));
define('DIR_DATA', DIR_BASE.'/data');

class conf {
	public static $DATA_PATH;
	public static $STORAGE_TYPES;
	public static $FIELD_TYPES;
}
conf::$DATA_PATH = DIR_DATA.'/structures.json';
conf::$STORAGE_TYPES = array(
		'text/plain' => 'text/plain',
		'mongodb' => 'Mongo DB',
		'mysql' => 'MySql'
	);
conf::$FIELD_TYPES =  array(
		'text_simple' => 'Simple text',
		'text_multiline' => 'Multiline text area',
		'integer' => 'Integer number',
		'float' => 'Decimal number',
		'range' => 'Range',
		'boolean' => 'Boolean'
	);

/* Debug */
if (file_exists(DIR_BASE.'/../tools/kint/Kint.class.php')) {
	require DIR_BASE.'/../tools/kint/Kint.class.php';
}