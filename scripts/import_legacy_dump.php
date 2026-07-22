<?php
declare(strict_types=1);

/**
 * Convert the supplied legacy MySQL dump into the lightweight SQLite database
 * used by this readable-source version. Only public-site tables are imported.
 *
 * Usage: php scripts/import_legacy_dump.php /absolute/path/to/zhiyuan_com.sql
 */

$dumpPath = $argv[1] ?? '';
if ($dumpPath === '' || !is_file($dumpPath)) {
    fwrite(STDERR, "Usage: php scripts/import_legacy_dump.php /path/to/zhiyuan_com.sql\n");
    exit(1);
}

$tables = [
    'zw_system_config', 'zw_cms_nav', 'zw_cms_product', 'zw_cms_article',
    'zw_cms_cases', 'zw_cms_banner', 'zw_cms_expand', 'zw_cms_link',
    'zw_cms_country', 'zw_cms_photo', 'zw_cms_message',
];
$dump = file_get_contents($dumpPath);
if ($dump === false) {
    fwrite(STDERR, "Unable to read dump: {$dumpPath}\n");
    exit(1);
}

$schema = [];
foreach ($tables as $table) {
    if (!preg_match('/CREATE TABLE `' . preg_quote($table, '/') . '`\s*\((.*?)\) ENGINE/s', $dump, $match)) {
        fwrite(STDERR, "Schema not found for {$table}\n");
        exit(1);
    }
    preg_match_all('/^\s*`([^`]+)`\s+([^\s,]+)/m', $match[1], $columns, PREG_SET_ORDER);
    $schema[$table] = [];
    foreach ($columns as $column) {
        $schema[$table][$column[1]] = stripos($column[2], 'int') !== false ? 'INTEGER' : 'TEXT';
    }
}

$databasePath = dirname(__DIR__) . '/data/demo.sqlite';
@mkdir(dirname($databasePath), 0755, true);
@unlink($databasePath);
$db = new PDO('sqlite:' . $databasePath);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->beginTransaction();

foreach ($schema as $table => $columns) {
    $definitions = [];
    foreach ($columns as $column => $type) {
        $definitions[] = '"' . $column . '" ' . $type;
    }
    $db->exec('CREATE TABLE "' . $table . '" (' . implode(', ', $definitions) . ')');
}

/** Split the comma-separated values from a single MySQL INSERT row. */
function splitValues(string $values): array
{
    $result = [];
    $current = '';
    $quoted = false;
    $escaped = false;
    $length = strlen($values);

    for ($i = 0; $i < $length; $i++) {
        $char = $values[$i];
        if ($escaped) {
            $current .= $char;
            $escaped = false;
            continue;
        }
        if ($quoted && $char === '\\') {
            $current .= $char;
            $escaped = true;
            continue;
        }
        if ($char === "'") {
            $quoted = !$quoted;
            $current .= $char;
            continue;
        }
        if (!$quoted && $char === ',') {
            $result[] = trim($current);
            $current = '';
            continue;
        }
        $current .= $char;
    }
    $result[] = trim($current);
    return $result;
}

function mysqlValue(string $value): ?string
{
    if (strcasecmp($value, 'NULL') === 0) {
        return null;
    }
    if (strlen($value) >= 2 && $value[0] === "'" && substr($value, -1) === "'") {
        $value = substr($value, 1, -1);
        return stripcslashes($value);
    }
    return $value;
}

$inserted = array_fill_keys($tables, 0);
$lines = explode("\n", $dump);
foreach ($lines as $line) {
    if (!preg_match('/^INSERT INTO `(zw_[a-z_]+)` VALUES \((.*)\);\r?$/s', $line, $match)) {
        continue;
    }
    $table = $match[1];
    if (!isset($schema[$table])) {
        continue;
    }
    $values = array_map('mysqlValue', splitValues($match[2]));
    if (count($values) !== count($schema[$table])) {
        throw new RuntimeException("Column count mismatch for {$table}");
    }
    $quotedColumns = array_map(static fn(string $column): string => '"' . $column . '"', array_keys($schema[$table]));
    $statement = $db->prepare(
        'INSERT INTO "' . $table . '" (' . implode(', ', $quotedColumns) . ') VALUES ('
        . implode(', ', array_fill(0, count($values), '?')) . ')'
    );
    $statement->execute($values);
    $inserted[$table]++;
}
$db->commit();

echo "Imported public-site data into {$databasePath}\n";
foreach ($inserted as $table => $count) {
    echo "{$table}: {$count}\n";
}
