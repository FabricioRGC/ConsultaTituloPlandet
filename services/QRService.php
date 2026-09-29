<?php
require_once __DIR__ . '/../libraries/phpqrcode/qrlib.php';
class QRService
{
    public function generarQR($url, $uniqueId)
    {
        $dir = __DIR__ . '/../qrcodes/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $filename = $uniqueId . ".png";
        $path = $dir . $filename;

        QRcode::png($url, $path);

        return "qrcodes/" . $filename;
    }

    public function moverPDF($file)
    {
        $dir = __DIR__ . '/../uploads/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $dest = $dir . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $dest);

        return "uploads/" . basename($file['name']);
    }
}
