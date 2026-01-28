<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('Content-Type: text/plain; charset=utf-8');

echo "=== Diagnóstico básico ===\n\n";

echo "PHP_VERSION: " . PHP_VERSION . "\n";
echo "SAPI: " . PHP_SAPI . "\n";
echo "DocumentRoot: " . ($_SERVER['DOCUMENT_ROOT'] ?? '(vazio)') . "\n";
echo "Script: " . (__FILE__) . "\n";
echo "CWD: " . getcwd() . "\n\n";

$exts = ['curl','json','mbstring','openssl','pdo','pdo_sqlite','sqlite3'];
foreach ($exts as $e) {
  echo "ext_$e: " . (extension_loaded($e) ? "OK" : "FALTA") . "\n";
}
echo "\n";

$paths = [
  'vendor/autoload.php' => __DIR__ . '/vendor/autoload.php',
  'config.php'          => __DIR__ . '/config.php',
  'storage/'            => __DIR__ . '/storage',
  'storage/token.json'  => __DIR__ . '/storage/token.json',
  'storage/youtubestats.sqlite' => __DIR__ . '/storage/youtubestats.sqlite',
  'client_secret.json'  => __DIR__ . '/client_secret.json',
];

foreach ($paths as $label => $p) {
  $exists = file_exists($p) ? "EXISTE" : "NÃO EXISTE";
  $read   = is_readable($p) ? "LEITURA_OK" : "LEITURA_NOK";
  $write  = (is_dir($p) ? is_writable($p) : (file_exists($p) ? is_writable($p) : (is_writable(dirname($p)) ? "DIR_GRAVÁVEL" : "DIR_NOK")));
  echo str_pad($label, 28) . " : " . $exists . " | " . $read . " | " . $write . "\n";
}

echo "\n=== Teste do Composer autoload ===\n";
try {
  require __DIR__ . '/vendor/autoload.php';
  echo "autoload: OK\n";
} catch (Throwable $e) {
  echo "autoload: ERRO -> " . $e->getMessage() . "\n";
  echo $e->getTraceAsString() . "\n";
  exit;
}

echo "\n=== Teste de load config via App\\Helpers ===\n";
try {
  if (class_exists('App\\Helpers')) {
    $cfg = App\Helpers::config();
    echo "Helpers::config(): OK\n";
    echo "BASE_URL: " . ($cfg['BASE_URL'] ?? '(vazio)') . "\n";
  } else {
    echo "Classe App\\Helpers NÃO encontrada (autoload/namespace/caminho).\n";
  }
} catch (Throwable $e) {
  echo "Helpers::config(): ERRO -> " . $e->getMessage() . "\n";
  echo $e->getTraceAsString() . "\n";
}

echo "\nPronto.\n";
