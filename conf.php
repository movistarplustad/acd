<?php
namespace Acd;
//Ficheros
define('DIR_BASE', dirname(__FILE__));
define('DIR_DATA', DIR_BASE.'/data');
define('DIR_TEST', DIR_BASE.'/test');
define('DIR_TEMPLATES', DIR_BASE.'/app/view');

class conf {
	public static $DIR_TEMPLATES;
	public static $DATA_PATH;
	public static $STORAGE_TYPES;
	public static $FIELD_TYPES;
	public static $PERMISSION_PATH;
	public static $USE_AUTHENTICATION;
	public static $AUTH_PERSITENT_EXPIRATION_TIME;
	public static $PATH_AUTH_CREDENTIALS_FILE;
	public static $PATH_AUTH_PREMANENT_LOGIN_DIR;
}
conf::$DIR_TEMPLATES = DIR_BASE.'/app/view';
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
conf::$PERMISSION_PATH = DIR_DATA.'/permission.json';
conf::$USE_AUTHENTICATION = true;
conf::$AUTH_PERSITENT_EXPIRATION_TIME = 31536000; // 1 year
conf::$PATH_AUTH_CREDENTIALS_FILE = DIR_DATA.'/auth.json';
conf::$PATH_AUTH_PREMANENT_LOGIN_DIR = DIR_DATA.'/auth_permanent_login';

/* Debug */
if (file_exists(DIR_BASE.'/../tools/kint/Kint.class.php')) {
	require DIR_BASE.'/../tools/kint/Kint.class.php';
}