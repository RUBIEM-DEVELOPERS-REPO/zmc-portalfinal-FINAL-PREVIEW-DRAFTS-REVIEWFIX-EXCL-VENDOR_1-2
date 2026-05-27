<?php

$_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_SERVER['SERVER_NAME'] = $_SERVER['SERVER_NAME'] ?? 'localhost';
$_SERVER['SERVER_PORT'] = $_SERVER['SERVER_PORT'] ?? '5000';
$_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$_SERVER['SERVER_PROTOCOL'] = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1';
$_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $_SERVER['SCRIPT_FILENAME'] ?? __DIR__ . '/index.php';
$_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] ?? __DIR__;
$_SERVER['PHP_SELF'] = $_SERVER['PHP_SELF'] ?? '/index.php';

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($path === '/__repl_health' || $path === '/healthz') {
    http_response_code(200);
    header('Content-Type: text/plain');
    echo 'OK';
    return;
}

if ($path === '/' && $method === 'GET') {
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $isHealthCheck = empty($ua) ||
        stripos($ua, 'Replit') !== false ||
        stripos($ua, 'curl') !== false ||
        stripos($ua, 'health') !== false ||
        stripos($ua, 'kube') !== false ||
        stripos($ua, 'GoogleHC') !== false ||
        (stripos($accept, 'text/html') === false && stripos($ua, 'Mozilla') === false);

    if ($isHealthCheck) {
        http_response_code(200);
        header('Content-Type: text/plain');
        echo 'OK';
        return;
    }
}

$filePath = realpath(__DIR__ . $path);
$publicDir = realpath(__DIR__);
if ($filePath && $path !== '/' && strpos($filePath, $publicDir . DIRECTORY_SEPARATOR) === 0 && is_file($filePath)) {
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'webp' => 'image/webp',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject',
        'pdf' => 'application/pdf',
        'mp4' => 'video/mp4',
        'webm' => 'video/webm',
    ];
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $mime = $mimeTypes[$ext] ?? mime_content_type($filePath) ?: 'application/octet-stream';
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: public, max-age=86400');
    readfile($filePath);
    return;
}

ini_set('display_errors', '0');
error_reporting(E_ALL);

$logFile = __DIR__ . '/../storage/logs/production_errors.log';
set_error_handler(function($errno, $errstr, $errfile, $errline) use ($logFile) {
    $msg = date('Y-m-d H:i:s') . " PHP Error [$errno]: $errstr in $errfile:$errline\n";
    @file_put_contents($logFile, $msg, FILE_APPEND);
    return false;
});

set_exception_handler(function($e) use ($logFile) {
    $msg = date('Y-m-d H:i:s') . " Uncaught Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
    @file_put_contents($logFile, $msg, FILE_APPEND);
    http_response_code(500);
    echo "Server Error - check logs";
});

require __DIR__ . '/index.php';
