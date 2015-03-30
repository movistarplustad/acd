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
	public static $DATA_DIR_PATH;
	public static $STORAGE_TYPES;
	public static $STORAGE_TYPE_TEXTPLAIN;
	public static $STORAGE_TYPE_MONGODB;
	public static $STORAGE_TYPE_MYSQL;
	public static $DEFAULT_STORAGE;
	public static $FIELD_TYPES;
	public static $PERMISSION_PATH;
	public static $USE_AUTHENTICATION;
	public static $AUTH_PERSITENT_EXPIRATION_TIME;
	public static $PATH_AUTH_CREDENTIALS_FILE;
	public static $PATH_AUTH_PREMANENT_LOGIN_DIR;
	public static $ROL_DEVELOPER;
	public static $ROL_EDITOR;
	public static $MYSQL_SERVER;
	public static $MYSQL_USER;
	public static $MYSQL_PASSWORD;
	public static $MYSQL_SCHEMA;
}
conf::$DIR_TEMPLATES = DIR_BASE.'/app/view';
conf::$DATA_PATH = DIR_DATA.'/structures.json';
conf::$DATA_DIR_PATH = DIR_DATA.'/structures';
conf::$STORAGE_TYPE_TEXTPLAIN  = 'text/plain';
conf::$STORAGE_TYPE_MONGODB  = 'mongodb';
conf::$STORAGE_TYPE_MYSQL  = 'mysql';
conf::$STORAGE_TYPES = array(
		conf::$STORAGE_TYPE_TEXTPLAIN => 'text/plain',
		conf::$STORAGE_TYPE_MONGODB => 'Mongo DB',
		conf::$STORAGE_TYPE_MYSQL => 'MySql'
	);
conf::$DEFAULT_STORAGE = conf::$STORAGE_TYPE_MYSQL;

conf::$PERMISSION_PATH = DIR_DATA.'/permission.json';
conf::$USE_AUTHENTICATION = true;
conf::$AUTH_PERSITENT_EXPIRATION_TIME = 31536000; // 1 year
conf::$PATH_AUTH_CREDENTIALS_FILE = DIR_DATA.'/auth.json';
conf::$PATH_AUTH_PREMANENT_LOGIN_DIR = DIR_DATA.'/auth_permanent_login';

conf::$ROL_DEVELOPER = 'developer';
conf::$ROL_EDITOR = 'editor';

conf::$MYSQL_SERVER = 'localhost';
conf::$MYSQL_USER = 'usuarioweb';
conf::$MYSQL_PASSWORD = 'strip';
conf::$MYSQL_SCHEMA = 'acd';

/* Debug */
if (file_exists(DIR_BASE.'/../tools/kint/Kint.class.php')) {
	require DIR_BASE.'/../tools/kint/Kint.class.php';
}