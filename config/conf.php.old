<?php
namespace Acd;

/* Manage the configuration of the different routes, connection string, enabled storages...

	First load .env file which production configuration, that can be overwritten if there is an .env.APPLICATION_ENV_NAME in the same directory.
	where APPLICATION_ENV_NAME is the value of the environment variable.
	An APPLICATION_ENV environment variable will trigger this overwriting mechanism, a common development configuration is to have the APPLICATION_ENV set to 'local' and keep the changes from production in the .env.local

	To enhance the development experience, this task is performed transparently on the objects that need this configuration
	Internally, a conf::envLoad() is made which loads the .env and if the .env exists.APPLICATION_ENV_NAME overwite the values
	Then it establishes the values that apply to it in the static class conf by calling conf::load().
*/
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

	/* Almacena en el objeto las variables de entorno getEnv('ACD_*')
		los objetos \ACD\* las utilizan através de esta misma clase estática: echo \Acd\conf::$STORAGE_TYPES
	*/
	public static function load()
	{
		self::$DIR_TEMPLATES = getenv('ACD_DIR_TEMPLATES');
		self::$DATA_PATH = getenv('ACD_DATA_PATH');
		self::$DATA_DIR_PATH = getenv('ACD_DATA_DIR_PATH');
		self::$DATA_CONTENT_PATH = getenv('ACD_DATA_CONTENT_PATH');
		self::$DATA_CONTENT_BINARY_ORIGIN_FORM_UPLOAD = getenv('ACD_DATA_CONTENT_BINARY_ORIGIN_FORM_UPLOAD');
		self::$DATA_CONTENT_BINARY_ORIGIN_FORM_PATH = getenv('ACD_DATA_CONTENT_BINARY_ORIGIN_FORM_PATH');
		self::$STORAGE_TYPE_TEXTPLAIN = getenv('ACD_STORAGE_TYPE_TEXTPLAIN');
		self::$STORAGE_TYPE_MONGODB = getenv('ACD_STORAGE_TYPE_MONGODB');
		self::$STORAGE_TYPE_MYSQL = getenv('ACD_STORAGE_TYPE_MYSQL');
		self::$STORAGE_TYPE_MONGODB_LEGACY = getenv('ACD_STORAGE_TYPE_MONGODB_LEGACY');
		self::$STORAGE_TYPES = [
			self::$STORAGE_TYPE_TEXTPLAIN =>
				[
					'name' => getenv('ACD_STORAGE_TYPE.TEXTPLAIN.NAME'),
					'disabled' => filter_var(getenv('ACD_STORAGE_TYPE.TEXTPLAIN.DISABLED'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
				],
			self::$STORAGE_TYPE_MONGODB =>
				[
					'name' => getenv('ACD_STORAGE_TYPE.MONGODB.NAME'),
					'disabled' => filter_var(getenv('ACD_STORAGE_TYPE.MONGODB.DISABLED'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
				],
			self::$STORAGE_TYPE_MYSQL =>
				[
					'name' => getenv('ACD_STORAGE_TYPE.MYSQL.NAME'),
					'disabled' => filter_var(getenv('ACD_STORAGE_TYPE.MYSQL.DISABLED'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
				],
			self::$STORAGE_TYPE_MONGODB_LEGACY =>
				[
					'name' => getenv('ACD_STORAGE_TYPE.MONGODB_LEGACY.NAME'),
					'disabled' => filter_var(getenv('ACD_STORAGE_TYPE.MONGODB_LEGACY.DISABLED'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
				]
		];
		self::$DEFAULT_STORAGE = getenv('ACD_DEFAULT_STORAGE');
		self::$PERMISSION_PATH = getenv('ACD_PERMISSION_PATH');
		self::$USE_AUTHENTICATION = filter_var(getenv('ACD_USE_AUTHENTICATION'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		self::$AUTH_PERSITENT_EXPIRATION_TIME = (int) getenv('ACD_AUTH_PERSITENT_EXPIRATION_TIME'); //*
		self::$PATH_AUTH_CREDENTIALS_FILE = getenv('ACD_PATH_AUTH_CREDENTIALS_FILE');
		self::$PATH_AUTH_PERMANENT_LOGIN_DIR = getenv('ACD_PATH_AUTH_PERMANENT_LOGIN_DIR');
		self::$ROL_DEVELOPER = getenv('ACD_ROL_DEVELOPER');
		self::$ROL_EDITOR = getenv('ACD_ROL_EDITOR');
		self::$MYSQL_SERVER = getenv('ACD_MYSQL_SERVER');
		self::$MYSQL_USER = getenv('ACD_MYSQL_USER');
		self::$MYSQL_PASSWORD = getenv('ACD_MYSQL_PASSWORD');
		self::$MYSQL_SCHEMA = getenv('ACD_MYSQL_SCHEMA');
		self::$MONGODB_SERVER = getenv('ACD_MONGODB_SERVER');
		self::$MONGODB_DB = getenv('ACD_MONGODB_DB');
		self::$SESSION_GC_MAXLIFETIME = (int) getenv('ACD_SESSION_GC_MAXLIFETIME'); //*
		self::$COOKIE_PREFIX = getenv('ACD_COOKIE_PREFIX');
	}

	/**
		* Set a variable using:
		* - putenv
		* - $_ENV
		* - $_SERVER
		*
		* The environment variable value is stripped of single and double quotes.
		*
		* @param $path
		* @param '.env' $file
	*/
		public static function envLoad($path, $file = '.env')
	{
		$fullPath = $path.'/'.$file;
		if(is_readable($fullPath)) {
			$aEnvVariables = \parse_ini_file($fullPath);
			$typePath = [
				'ACD_DIR_TEMPLATES',
				'ACD_DATA_PATH',
				'ACD_DATA_DIR_PATH',
				'ACD_DATA_CONTENT_PATH',
				'ACD_PERMISSION_PATH',
				'ACD_PATH_AUTH_CREDENTIALS_FILE',
				'ACD_PATH_AUTH_PERMANENT_LOGIN_DIR'
			];
			$dir_base = dirname(__FILE__);

			foreach ($aEnvVariables as $name => $value) {
				// Expand relative routes
				if(in_array ($name, $typePath)) {
					if(substr($value, 0, 2) === './') {
						$value = $dir_base.substr($value, 1);
					}
				}

				// Filter only the ACD_* to prevent pollution with other environmental variables
				if(substr($name, 0, 4) === 'ACD_') {
					putenv("$name=$value");
					$_ENV[$name] = $value;
					$_SERVER[$name] = $value;
				}
			}
		}
	}
}

// Autoloading, reading the production configuration and overwrite with that of the local environment
conf::envLoad(__DIR__,'.env');
if(getenv('APPLICATION_ENV')) {
	conf::envLoad(__DIR__,'.env.'.getenv('APPLICATION_ENV'));
}

// Store the corresponding variables in the object \Acd\Conf
conf::load();
// var_dump(conf::$DEFAULT_STORAGE);die;