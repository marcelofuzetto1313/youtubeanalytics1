<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Db;
use App\Helpers;
use App\YtService;

$cfg = Helpers::config();
$key = (string)($_GET['key'] ?? '');
if ($key !== (string)$cfg['SYNC_KEY']) {
  Helpers::json(['ok'=>false,'error'=>'Unauthorized (bad key)'], 401);
}

$days = isset($_GET['days']) ? max(1, min(3650, (int)$_GET['days'])) : 90;

// Recomendação prática: sincronizar até D-2 para dados mais estáveis
$end = new DateTimeImmutable('today', new DateTimeZone('UTC'));
$start = $end->sub(new DateInterval('P' . $days . 'D'));

$client = Helpers::buildClient();
try {
  Helpers::ensureTokenOrFail($client);
} catch (Throwable $e) {
  Helpers::json(['ok'=>false,'error'=>$e->getMessage(),'hint'=>'Abra /login.php no navegador e autorize.'], 400);
}

$svc = new YtService($client);
$pdo = Db::pdo();
$nowUtc = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format(DateTimeInterface::ATOM);

$startDate = $start->format('Y-m-d');
$endDate = $end->format('Y-m-d');

$summary = [
  'ok' => true,
  'range' => ['start'=>$startDate,'end'=>$endDate,'days'=>$days],
  'writes' => ['daily_analytics'=>0,'daily_uploads'=>0],
  'notes' => [
    'YouTube Analytics é diário; os números podem ajustar nos últimos 1-3 dias.',
    'Shorts é estimado por duração/hashtag (config.php).'
  ]
];

try {
  // 1) Analytics diário
  $daily = $svc->fetchAnalyticsDaily($startDate, $endDate);

  $stmtA = $pdo->prepare("
    INSERT INTO daily_analytics (date, views, likes, subscribers_gained, updated_at_utc)
    VALUES (:d,:v,:l,:s,:u)
    ON CONFLICT(date) DO UPDATE SET
      views=excluded.views,
      likes=excluded.likes,
      subscribers_gained=excluded.subscribers_gained,
      updated_at_utc=excluded.updated_at_utc
  ");

  foreach ($daily as $d=>$m) {
    $stmtA->execute([
      ':d'=>$d,
      ':v'=>(int)$m['views'],
      ':l'=>(int)$m['likes'],
      ':s'=>(int)$m['subscribers_gained'],
      ':u'=>$nowUtc
    ]);
    $summary['writes']['daily_analytics']++;
  }

  // 2) Uploads por dia (Data API)
  $counts = $svc->fetchUploadsCountsByDay($startDate, $endDate);

  $stmtU = $pdo->prepare("
    INSERT INTO daily_uploads (date, videos_published, shorts_estimated, updated_at_utc)
    VALUES (:d,:v,:sh,:u)
    ON CONFLICT(date) DO UPDATE SET
      videos_published=excluded.videos_published,
      shorts_estimated=excluded.shorts_estimated,
      updated_at_utc=excluded.updated_at_utc
  ");

  foreach ($counts as $d=>$c) {
    $stmtU->execute([
      ':d'=>$d,
      ':v'=>(int)$c['videos'],
      ':sh'=>(int)$c['shorts'],
      ':u'=>$nowUtc
    ]);
    $summary['writes']['daily_uploads']++;
  }

} catch (Throwable $e) {
  Helpers::json(['ok'=>false,'error'=>$e->getMessage()], 500);
}

Helpers::json($summary);
