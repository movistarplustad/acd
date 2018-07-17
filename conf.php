<?php
namespace Acd;
//Ficheros
define(__NAMESPACE__ .'\DIR_BASE', dirname(__FILE__));

class conf {
	public static $DIR_TEMPLATES;
	public static $DATA_PATH;
	public static $DATA_DIR_PATH;
	public static $DATA_CONTENT_PATH;
	public static $DATA_CONTENT_BINARY_ORIGIN_FORM_UPLOAD;
	public static $DATA_CONTENT_BINARY_ORIGIN_FORM_PATH;
	public static $STORAGE_TYPES;
	public static $STORAGE_TYPE_TEXTPLAIN;
	public static $STORAGE_TYPE_MONGODB;
	public static $STORAGE_TYPE_MYSQL;
	public static $STORAGE_TYPE_MONGODB_LEGACY;
	public static $DEFAULT_STORAGE;
	public static $FIELD_TYPES;
	public static $PERMISSION_PATH;
	public static $USE_AUTHENTICATION;
	public static $AUTH_PERSITENT_EXPIRATION_TIME;
	public static $PATH_AUTH_CREDENTIALS_FILE;
	public static $PATH_AUTH_PERMANENT_LOGIN_DIR;
	public static $ROL_DEVELOPER;
	public static $ROL_EDITOR;
	public static $MYSQL_SERVER;
	public static $MYSQL_USER;
	public static $MYSQL_PASSWORD;
	public static $MYSQL_SCHEMA;
	public static $MONGODB_SERVER;
	public static $MONGODB_DB;
	public static $SESSION_GC_MAXLIFETIME;
	public static $COOKIE_PREFIX;
}
conf::$DIR_TEMPLATES = DIR_BASE.'/app/view';
conf::$DATA_PATH = '/mnt/content/acd/structures.json';
conf::$DATA_DIR_PATH = '/mnt/content/acd/structures';
conf::$DATA_CONTENT_PATH = '/mnt/content/acd/contents';
conf::$DATA_CONTENT_BINARY_ORIGIN_FORM_UPLOAD = 'FORM_UPLOAD';
conf::$DATA_CONTENT_BINARY_ORIGIN_FORM_PATH = 'PATH';
conf::$STORAGE_TYPE_TEXTPLAIN  = 'text/plain';
conf::$STORAGE_TYPE_MONGODB  = 'mongodb';
conf::$STORAGE_TYPE_MYSQL  = 'mysql';
conf::$STORAGE_TYPE_MONGODB_LEGACY  = 'mongodb-legacy';
conf::$STORAGE_TYPES = [
		conf::$STORAGE_TYPE_TEXTPLAIN =>
			[
				'name' => 'text/plain',
				'disabled' => true
			],
		conf::$STORAGE_TYPE_MONGODB =>
			[
				'name' => 'Mongo DB',
				'disabled' => false
			],
		conf::$STORAGE_TYPE_MYSQL =>
			[
				'name' => 'MySql',
				'disabled' => true
			],
		conf::$STORAGE_TYPE_MONGODB_LEGACY =>
			[
				'name' => 'Mongo DB Legacy',
				'disabled' => true
			],
	];
conf::$DEFAULT_STORAGE = conf::$STORAGE_TYPE_MONGODB;

conf::$PERMISSION_PATH = '/mnt/content/acd/permission.json';
conf::$USE_AUTHENTICATION = true;
conf::$AUTH_PERSITENT_EXPIRATION_TIME = 31536000; // 1 year
conf::$PATH_AUTH_CREDENTIALS_FILE = '/mnt/content/acd/auth.json';
conf::$PATH_AUTH_PERMANENT_LOGIN_DIR = '/mnt/content/acd/auth_permanent_login';

conf::$ROL_DEVELOPER = 'developer';
conf::$ROL_EDITOR = 'editor';

conf::$MYSQL_SERVER = 'localhost';
conf::$MYSQL_USER = 'usuarioweb';
conf::$MYSQL_PASSWORD = '';
conf::$MYSQL_SCHEMA = 'acd';

conf::$MONGODB_SERVER = 'mongodb://localhost:27017'; // mongodb://hosting01.int:27017,hosting02.int:27017,hosting03.int:27017/?replicaSet=Replica
conf::$MONGODB_DB = 'acd';

conf::$SESSION_GC_MAXLIFETIME = 14400;
ini_set('session.gc_maxlifetime', conf::$SESSION_GC_MAXLIFETIME);

conf::$COOKIE_PREFIX = 'acd_';

// Developer / local / personal  configuration
// Default  environment  for develop is 'local', in production environment  conf.devel.php does not exist
$environment = getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'local';
if (file_exists(DIR_BASE.'/conf.'.$environment.'.php')) {
	require DIR_BASE.'/conf.'.$environment.'.php';
}
/* Debug */
if (file_exists(DIR_BASE.'/../tools/kint/Kint.class.php')) {
	require DIR_BASE.'/../tools/kint/Kint.class.php';
}
