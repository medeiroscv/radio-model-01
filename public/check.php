<?php

/**
 * RadioCMS — Diagnóstico rápido.
 * Rode pelo navegador: https://seusite.com/check.php
 * Não depende do Laravel: funciona mesmo quando o site está com erro 500.
 * Apague este arquivo após o diagnóstico.
 */

header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');

$root = dirname(__DIR__);
$ok = 'OK';
$fail = 'FALHA';
$line = str_repeat('-', 60);

echo "=== DIAGNOSTICO RADIOCMS ===\n";
echo 'Data/hora: ' . date('Y-m-d H:i:s') . "\n";
echo 'PHP: ' . PHP_VERSION . ' (' . PHP_SAPI . ')' . (PHP_INT_SIZE === 8 ? ' 64-bit' : ' 32-bit!!') . "\n";
echo $line . "\n\n";

echo "=== EXTENSOES OBRIGATORIAS ===\n";
$extensions = ['pdo', 'pdo_mysql', 'openssl', 'mbstring', 'tokenizer', 'xml', 'ctype', 'fileinfo', 'bcmath', 'curl', 'zip'];
$missing = [];
foreach ($extensions as $ext) {
    $loaded = extension_loaded($ext);
    if (!$loaded) { $missing[] = $ext; }
    printf("  %-12s %s\n", $ext, $loaded ? $ok : $fail);
}
$gd = extension_loaded('gd') || extension_loaded('imagick');
if (!$gd) { $missing[] = 'gd|imagick'; }
printf("  %-12s %s\n", 'gd|imagick', $gd ? $ok : $fail);
echo $line . "\n\n";

echo "=== ARQUIVO .env ===\n";
$envFile = $root . '/.env';
if (is_file($envFile)) {
    echo "  .env presente\n";
    $content = file_get_contents($envFile);
    $key = preg_match('/^APP_KEY=(.+)$/m', $content, $m) ? trim($m[1]) : '';
    echo '  APP_KEY definido: ' . (!empty($key) && $key !== 'base64:' ? $ok : $fail) . "\n";
    $dbName = preg_match('/^DB_DATABASE=(.+)$/m', $content, $m) ? trim($m[1], '" ') : '';
    $dbHost = preg_match('/^DB_HOST=(.+)$/m', $content, $m) ? trim($m[1], '" ') : '';
    $dbPort = preg_match('/^DB_PORT=(.+)$/m', $content, $m) ? trim($m[1], '" ') : '3306';
    $dbUser = preg_match('/^DB_USERNAME=(.+)$/m', $content, $m) ? trim($m[1], '" ') : '';
    echo "  DB: host=$dbHost porta=$dbPort banco=$dbName usuario=$dbUser\n";
    echo "  .installed: " . (is_file($root . '/.installed') ? 'EXISTE (site marcado como instalado)' : 'nao existe') . "\n";
} else {
    echo "  " . $fail . " — .env NAO encontrado\n";
}
echo $line . "\n\n";

echo "=== PERMISSOES DE ESCRITA ===\n";
$writable = [$root . '/storage', $root . '/storage/logs', $root . '/bootstrap/cache', $root . '/public/uploads'];
foreach ($writable as $dir) {
    printf("  %-30s %s\n", basename($dir), is_writable($dir) ? $ok : $fail);
}
echo $line . "\n\n";

echo "=== CONEXAO COM O BANCO ===\n";
if (is_file($envFile) && extension_loaded('pdo_mysql')) {
    $content = file_get_contents($envFile);
    $get = function ($k) use ($content) {
        return preg_match('/^' . $k . '=(.*)$/m', $content, $m) ? trim(trim($m[1]), '"') : '';
    };
    try {
        $dsn = 'mysql:host=' . $get('DB_HOST') . ';port=' . ($get('DB_PORT') ?: '3306') . ';dbname=' . $get('DB_DATABASE');
        new PDO($dsn, $get('DB_USERNAME'), $get('DB_PASSWORD'), [PDO::ATTR_TIMEOUT => 5, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        echo "  Conexao: $ok\n";
    } catch (\Throwable $e) {
        echo "  Conexao: $fail — " . $e->getMessage() . "\n";
    }
} else {
    echo "  pdo_mysql ausente ou .env inexistente — nao testado\n";
}
echo $line . "\n\n";

echo "=== ULTIMAS LINHAS DO LOG DO LARAVEL ===\n";
$log = $root . '/storage/logs/laravel.log';
if (is_file($log)) {
    $lines = file($log);
    $last = array_slice($lines, max(0, count($lines) - 25));
    foreach ($last as $l) {
        echo $l;
    }
} else {
    echo "  (sem arquivo de log ainda)\n";
}
echo $line . "\n\n";

echo "=== CLASSES DO VENDOR CARREGAM? ===\n";
$autoload = $root . '/vendor/autoload.php';
if (is_file($autoload)) {
    try {
        require $autoload;
        echo "  vendor/autoload.php: $ok\n";
    } catch (\Throwable $e) {
        echo "  vendor/autoload.php: $fail — " . $e->getMessage() . "\n";
    }
} else {
    echo "  vendor/autoload.php NAO existe — faltou subir a pasta vendor\n";
}
echo $line . "\n";

echo "\nCole a saida acima para diagnosticar.\n";