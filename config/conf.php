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

// Configuration: environment variables (container) first, then the .env files, which only
// fill in what is missing. .env.APPLICATION_ENV (.env.local, .env.fatpre...) goes after .env
// because, within the same load, a later file overrides an earlier one.
$applicationEnv = getenv('APPLICATION_ENV');
$envFiles = $applicationEnv ? ['.env', '.env.' . $applicationEnv] : ['.env'];
Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/../', $envFiles, false)->safeLoad();

// ACD reads its configuration from $_ENV, but with variables_order = "GPCS" (the PHP default)
// the container variables are not there, so every ACD_* variable is copied into $_ENV.
foreach (array_merge($_SERVER, getenv()) as $name => $value) {
  if (strpos($name, 'ACD_') === 0 && !isset($_ENV[$name])) {
    $_ENV[$name] = $value;
  }
}

// ACD_STORAGE_TYPE_<TYPE>_NAME / _DISABLED; the old dotted names (ACD_STORAGE_TYPE.<TYPE>.NAME) are
// still accepted, but variables with dots do not reach PHP from a container.
$_ENV['ACD_STORAGE_TYPES'] = [
  $_ENV['ACD_STORAGE_TYPE_TEXTPLAIN'] =>
  [
    'name' => $_ENV['ACD_STORAGE_TYPE_TEXTPLAIN_NAME'] ?? $_ENV['ACD_STORAGE_TYPE.TEXTPLAIN.NAME'] ?? null,
    'disabled' => filter_var($_ENV['ACD_STORAGE_TYPE_TEXTPLAIN_DISABLED'] ?? $_ENV['ACD_STORAGE_TYPE.TEXTPLAIN.DISABLED'] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
  ],
  $_ENV[ 'ACD_STORAGE_TYPE_MONGODB'] =>
  [
    'name' => $_ENV['ACD_STORAGE_TYPE_MONGODB_NAME'] ?? $_ENV['ACD_STORAGE_TYPE.MONGODB.NAME'] ?? null,
    'disabled' => filter_var($_ENV['ACD_STORAGE_TYPE_MONGODB_DISABLED'] ?? $_ENV['ACD_STORAGE_TYPE.MONGODB.DISABLED'] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
  ],
  $_ENV[ 'ACD_STORAGE_TYPE_MYSQL'] =>
  [
    'name' => $_ENV['ACD_STORAGE_TYPE_MYSQL_NAME'] ?? $_ENV['ACD_STORAGE_TYPE.MYSQL.NAME'] ?? null,
    'disabled' => filter_var($_ENV['ACD_STORAGE_TYPE_MYSQL_DISABLED'] ?? $_ENV['ACD_STORAGE_TYPE.MYSQL.DISABLED'] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
  ],
  $_ENV[ 'ACD_STORAGE_TYPE_MONGODB_LEGACY'] =>
  [
    'name' => $_ENV['ACD_STORAGE_TYPE_MONGODB_LEGACY_NAME'] ?? $_ENV['ACD_STORAGE_TYPE.MONGODB_LEGACY.NAME'] ?? null,
    'disabled' => filter_var($_ENV['ACD_STORAGE_TYPE_MONGODB_LEGACY_DISABLED'] ?? $_ENV['ACD_STORAGE_TYPE.MONGODB_LEGACY.DISABLED'] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
  ]
];