<?php
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
  // Dependencies were installed with Composer and this is the main project
  $loader = require_once __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../../autoload.php')) {
  // We're installed as a dependency in another project's `vendor` directory
  $loader = require_once __DIR__ . '/../../../autoload.php';
} else {
  throw new Exception('Can\'t find autoload.php. Did you install dependencies with Composer?');
}

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$_ENV['ACD_STORAGE_TYPES'] = [
  $_ENV['ACD_STORAGE_TYPE_TEXTPLAIN'] =>
  [
    'name' => getenv('ACD_STORAGE_TYPE.TEXTPLAIN.NAME'),
    'disabled' => filter_var(getenv('ACD_STORAGE_TYPE.TEXTPLAIN.DISABLED'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
  ],
  $_ENV[ 'ACD_STORAGE_TYPE_MONGODB'] =>
  [
    'name' => getenv('ACD_STORAGE_TYPE.MONGODB.NAME'),
    'disabled' => filter_var(getenv('ACD_STORAGE_TYPE.MONGODB.DISABLED'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
  ],
  $_ENV[ 'ACD_STORAGE_TYPE_MYSQL'] =>
  [
    'name' => getenv('ACD_STORAGE_TYPE.MYSQL.NAME'),
    'disabled' => filter_var(getenv('ACD_STORAGE_TYPE.MYSQL.DISABLED'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
  ],
  $_ENV[ 'ACD_STORAGE_TYPE_MONGODB_LEGACY'] =>
  [
    'name' => getenv('ACD_STORAGE_TYPE.MONGODB_LEGACY.NAME'),
    'disabled' => filter_var(getenv('ACD_STORAGE_TYPE.MONGODB_LEGACY.DISABLED'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
  ]
];