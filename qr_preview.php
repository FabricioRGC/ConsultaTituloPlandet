<?php
require_once __DIR__ . '/config/urls.php';
require_once __DIR__ . '/libraries/phpqrcode/qrlib.php';

$uid = trim($_GET['uid'] ?? '');
if ($uid === '') {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'UID requerido';
    exit;
}

$url = URL_VIEW_DOCUMENT . urlencode($uid);

header('Content-Type: image/png');
QRcode::png($url, false, QR_ECLEVEL_L, 6, 2);
exit;
