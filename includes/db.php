<?php
$config = require __DIR__ . '/../config.php';

if (!is_dir(dirname($config['db_path']))) {
    mkdir(dirname($config['db_path']), 0777, true);
}

$db = new SQLite3($config['db_path']);
$db->exec('PRAGMA foreign_keys = ON;');

function getDb(): SQLite3
{
    global $db;
    return $db;
}
