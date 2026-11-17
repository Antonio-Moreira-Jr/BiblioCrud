<?php
require_once __DIR__ . '/../vendor/autoload.php';

set_exception_handler(static function (Throwable $e) {
    error_log('[App] ' . $e);
    http_response_code(500);
    require __DIR__ . '/../src/views/500.php';
});

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../src/rotas.php';