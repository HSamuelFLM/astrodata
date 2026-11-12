<?php
header('Content-Type: text/css; charset=utf-8');
header('Cache-Control: public, max-age=3600');

$file = __DIR__ . '/admin.css';
if (is_file($file)) {
    readfile($file);
} else {
    http_response_code(404);
}
