<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Helpers;

$client = Helpers::buildClient();

if (!isset($_GET['code'])) {
  http_response_code(400);
  echo "Missing 'code' parameter.";
  exit;
}

$token = $client->fetchAccessTokenWithAuthCode((string)$_GET['code']);
if (isset($token['error'])) {
  http_response_code(500);
  echo "Auth error: " . htmlspecialchars((string)($token['error_description'] ?? $token['error']));
  exit;
}

$cfg = Helpers::config();
$tokenFile = $cfg['TOKEN_PATH'];
if (!is_dir(dirname($tokenFile))) mkdir(dirname($tokenFile), 0777, true);
file_put_contents($tokenFile, json_encode($token, JSON_PRETTY_PRINT));

header('Location: /');
exit;
