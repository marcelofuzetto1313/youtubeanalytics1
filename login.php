<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Helpers;

$client = Helpers::buildClient();
Helpers::loadToken($client);

if ($client->getAccessToken() && !$client->isAccessTokenExpired()) {
  header('Location: /');
  exit;
}

header('Location: ' . $client->createAuthUrl());
exit;
