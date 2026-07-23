<?php

$database = $argv[1] ?? (dirname(__DIR__) . '/data/demo.sqlite');
$migration = dirname(__DIR__) . '/database/migrations/001_geo_publish_api.sql';
if (!is_file($database) || !is_file($migration)) {
    fwrite(STDERR, "Database or migration file was not found.\n");
    exit(1);
}
$pdo = new PDO('sqlite:' . $database, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$duplicates = (int)$pdo->query(
    'SELECT COUNT(*) FROM (SELECT id FROM zw_cms_article GROUP BY id HAVING COUNT(*) > 1)'
)->fetchColumn();
if ($duplicates > 0) {
    fwrite(STDERR, "Migration stopped: zw_cms_article contains duplicate ids.\n");
    exit(1);
}
$pdo->exec((string)file_get_contents($migration));
fwrite(STDOUT, "GEO publish API migration completed.\n");
