<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Helpers;

$cfg = Helpers::config();
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Meu Analytics (YouTube)</title>
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;margin:24px;line-height:1.35}
    .card{border:1px solid #e7e7e7;border-radius:16px;padding:16px;margin:14px 0}
    code{background:#f6f6f6;padding:2px 6px;border-radius:8px}
    a{color:#0b57d0}
    small{color:#666}
  </style>
</head>
<body>
  <h1>Meu Analytics (YouTube)</h1>

  <div class="card">
    <h3>1) Conectar</h3>
    <p><a href="/login.php">Conectar com Google (OAuth)</a></p>
    <small>O token será salvo em <code>storage/token.json</code></small>
  </div>

  <div class="card">
    <h3>2) Sincronizar dados</h3>
    <p>Exemplo (últimos 90 dias):</p>
    <p><code><?= htmlspecialchars($cfg['BASE_URL']) ?>/sync.php?key=SEU_SYNC_KEY&days=90</code></p>
    <small>Troque a SYNC_KEY em <code>public/config.php</code>.</small>
  </div>

  <div class="card">
    <h3>3) Ver relatório</h3>
    <ul>
      <li><a href="/report.php?days=90">Relatório JSON (90 dias)</a></li>
      <li><a href="/report.php?days=90&format=csv">Relatório CSV (90 dias)</a></li>
    </ul>
  </div>

  <div class="card">
    <h3>Config</h3>
    <ul>
      <li>Timezone: <code><?= htmlspecialchars($cfg['TIMEZONE']) ?></code></li>
      <li>Shorts detect: <code><?= htmlspecialchars($cfg['SHORTS_DETECT_MODE']) ?></code> (max <code><?= (int)$cfg['SHORTS_MAX_SECONDS'] ?>s</code>)</li>
    </ul>
  </div>
</body>
</html>
