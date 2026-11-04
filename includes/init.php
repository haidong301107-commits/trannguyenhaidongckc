<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$configPath = __DIR__ . '/../config/config.php';
if (!file_exists($configPath)) {
    throw new \RuntimeException('Config file not found: ' . $configPath);
}

$config = require_once $configPath;
$dbConfig = $config['db'] ?? null;
if (!$dbConfig) {
    throw new \RuntimeException('Database configuration not found in config file.');
}
$index_url = $config['index_url'] ?? null;

DEFINE('INDEX_URL', $index_url);
DEFINE('DB_HOST', $dbConfig['host'] ?? 'localhost');
DEFINE('DB_NAME', $dbConfig['dbname'] ?? 'quanly_sinhvien');
DEFINE('DB_USERNAME', $dbConfig['user'] ?? 'root');
DEFINE('DB_PASSWORD', $dbConfig['pass'] ?? '');
DEFINE('DB_CHARSET', $dbConfig['charset'] ?? 'utf8mb4');

require_once __DIR__ . '/../database/pdo.php';

function isActive($page) {
  return basename($_SERVER['PHP_SELF']) === $page ? 'bg-blue-100 text-blue-600 font-medium' : '';
}