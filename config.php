<?php
declare(strict_types=1);

return [
  // Domínio real do seu site
  'BASE_URL' => 'https://youtubestats.free.nf',

  // Timezone local usado para relatórios (dia da semana etc.)
  'TIMEZONE' => 'America/Sao_Paulo',

  // Chave para proteger /sync.php (troque!)
  'SYNC_KEY' => 'CHANGE_ME_9fc8909a67155d3c0f9df04c',

  // SQLite
  'SQLITE_PATH' => __DIR__ . '/storage/youtubestats.sqlite',

  // Token OAuth e client secret
  'TOKEN_PATH' => __DIR__ . '/storage/token.json',
  'CLIENT_SECRET_PATH' => __DIR__ . '/client_secret.json',

  // Shorts (estimado)
  // mode: 'duration' (<=60s) | 'duration_or_hashtag'
  'SHORTS_DETECT_MODE' => 'duration_or_hashtag',
  'SHORTS_MAX_SECONDS' => 60,
];
