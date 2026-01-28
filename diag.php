<?php
declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "PHP_VERSION=" . PHP_VERSION . PHP_EOL;
echo "BASE_DIR=" . __DIR__ . PHP_EOL;

$autoload = __DIR__ . "/vendor/autoload.php";
echo "AUTOLOAD_PATH=" . $autoload . PHP_EOL;
echo "AUTOLOAD_EXISTS=" . (file_exists($autoload) ? "YES" : "NO") . PHP_EOL;

$need = [
  __DIR__ . "/vendor/composer/autoload_real.php",
  __DIR__ . "/vendor/composer/autoload_static.php",
  __DIR__ . "/vendor/composer/ClassLoader.php",
  __DIR__ . "/vendor/autoload.php",
];

foreach ($need as $f) {
  echo basename($f) . "=" . (file_exists($f) ? "OK" : "MISSING") . PHP_EOL;
}

echo "---- trying require autoload ----" . PHP_EOL;
require $autoload;

echo "OK: autoload carregou" . PHP_EOL;
