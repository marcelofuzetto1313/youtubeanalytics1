<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Db;
use App\Helpers;

$cfg = Helpers::config();
$days = isset($_GET['days']) ? max(1, min(3650, (int)$_GET['days'])) : 90;
$format = isset($_GET['format']) ? strtolower((string)$_GET['format']) : 'json';

$tz = new DateTimeZone($cfg['TIMEZONE']);
$sinceUtc = (new DateTimeImmutable('now', new DateTimeZone('UTC')))
  ->sub(new DateInterval('P' . $days . 'D'))
  ->format('Y-m-d');

$pdo = Db::pdo();

// Join por data
$stmt = $pdo->prepare("
  SELECT a.date as date,
         a.views as views, a.likes as likes, a.subscribers_gained as subscribers_gained,
         COALESCE(u.videos_published, 0) as videos_published,
         COALESCE(u.shorts_estimated, 0) as shorts_estimated
  FROM daily_analytics a
  LEFT JOIN daily_uploads u ON u.date = a.date
  WHERE a.date >= :since
  ORDER BY a.date ASC
");
$stmt->execute([':since'=>$sinceUtc]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
  Helpers::json(['ok'=>false,'error'=>'Sem dados. Rode /sync.php primeiro.'], 400);
}

function weekday_pt(DateTimeImmutable $d): string {
  $map=[1=>'Seg',2=>'Ter',3=>'Qua',4=>'Qui',5=>'Sex',6=>'Sáb',7=>'Dom'];
  return $map[(int)$d->format('N')] ?? $d->format('N');
}

$byWeekday = [];
foreach (['Seg','Ter','Qua','Qui','Sex','Sáb','Dom'] as $w) {
  $byWeekday[$w]=[
    'views'=>0,'likes'=>0,'subscribers_gained'=>0,
    'videos_published'=>0,'shorts_estimated'=>0
  ];
}

foreach ($rows as $r) {
  $dt = new DateTimeImmutable($r['date'].' 00:00:00', new DateTimeZone('UTC'));
  $local = $dt->setTimezone($tz);
  $w = weekday_pt($local);

  $byWeekday[$w]['views'] += (int)$r['views'];
  $byWeekday[$w]['likes'] += (int)$r['likes'];
  $byWeekday[$w]['subscribers_gained'] += (int)$r['subscribers_gained'];
  $byWeekday[$w]['videos_published'] += (int)$r['videos_published'];
  $byWeekday[$w]['shorts_estimated'] += (int)$r['shorts_estimated'];
}

function top(array $agg, string $metric): array {
  $bestK=null; $bestV=-INF;
  foreach ($agg as $k=>$v) {
    if ($v[$metric] > $bestV) { $bestV=$v[$metric]; $bestK=$k; }
  }
  return ['key'=>$bestK,'value'=>$bestV];
}

$out = [
  'ok'=>true,
  'days'=>$days,
  'timezone'=>$cfg['TIMEZONE'],
  'rows'=>count($rows),
  'byWeekday'=>$byWeekday,
  'top'=>[
    'views'=>top($byWeekday,'views'),
    'likes'=>top($byWeekday,'likes'),
    'subscribers_gained'=>top($byWeekday,'subscribers_gained'),
    'videos_published'=>top($byWeekday,'videos_published'),
    'shorts_estimated'=>top($byWeekday,'shorts_estimated'),
  ],
  'notes'=>[
    'Analytics é diário (day).',
    'Uploads vêm da Data API; Shorts é estimado (config.php).'
  ]
];

if ($format === 'csv') {
  $csv=[];
  $csv[]=['Dia semana','Views','Likes','Inscritos ganhos','Vídeos publicados','Shorts (estimado)'];
  foreach ($byWeekday as $w=>$v) {
    $csv[]=[
      $w,(string)$v['views'],(string)$v['likes'],(string)$v['subscribers_gained'],
      (string)$v['videos_published'],(string)$v['shorts_estimated']
    ];
  }
  Helpers::csv('youtubestats_weekday_'.$days.'d.csv', $csv);
}

Helpers::json($out);
