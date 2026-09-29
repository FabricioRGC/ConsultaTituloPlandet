<?php

require_once __DIR__ . '/controllers/DocumentController.php';

if (empty($_GET['id'])) {
    http_response_code(400);
    echo "ID no proporcionado.";
    exit;
}

$controller = new DocumentController();
$controller->viewPdf($_GET['id']);
