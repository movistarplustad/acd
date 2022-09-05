<?php
// var_dump(__DIR__ . '/..');die;
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