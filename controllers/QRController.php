<?php
require_once __DIR__ . '/../models/DocumentModel.php';
require_once __DIR__ . '/../models/AuditModel.php';
require_once __DIR__ . '/../services/QRService.php';
require_once __DIR__ . '/../config/urls.php';

class QRController
{
    public function generar()
    {
        error_log("--- ENTRÓ A GENERAR() --- " . date('H:i:s'));
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        if (!isset($_FILES['pdf'])) return;

        $pdf = $_FILES['pdf'];
        $title = $_POST['title'] ?? '';
        $partida = $_POST['partida'] ?? '';
        $fecha = $_POST['fecha'] ?? '';

        if ($pdf['type'] !== 'application/pdf') {
            echo "Debe subir un PDF valido.";
            return;
        }

        $model = new DocumentModel();
        $qrService = new QRService();

        $pdfPath = $qrService->moverPDF($pdf);
        $uniqueId = uniqid('', true);
        $urlConsulta = URL_VIEW_DOCUMENT . $uniqueId;
        $qrPath = $qrService->generarQR($urlConsulta, $uniqueId);

        if ($model->insert($partida, $title, $pdfPath, $qrPath, $uniqueId, $fecha)) {
            if (session_status() !== PHP_SESSION_ACTIVE) session_start();
            $audit = new AuditModel();
            
            // 🔥 AQUÍ CAMBIAMOS LA DESCRIPCIÓN GENÉRICA POR UNA DETALLADA:
            $nombreArchivoOriginal = $pdf['name'] ?? 'Desconocido';
            $descripcionDetallada = "Subió el PDF: '{$nombreArchivoOriginal}' (Título: {$title})";

            $audit->logActivity(
                (int)($_SESSION['usuario_id'] ?? 0),
                'CREATE_QR',
                'QR',
                'documents',
                $uniqueId,
                $descripcionDetallada, // <--- Aquí se inyecta el nombre del archivo y el título
                [
                    'title' => $title,
                    'partida' => $partida,
                    'pdf_path' => $pdfPath,
                    'qr_path' => $qrPath
                ],
                $_SESSION['audit_session_token'] ?? session_id()
            );

            header("Location: /ConsultaTituloPlandet/index.php?action=dasboard&tab=generarQR&uploaded=1&qr=" . urlencode($qrPath));
            exit();
        }

        echo "Error guardando en la base de datos.";
    }

    public function subirMultiples()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        if (!isset($_FILES['pdfs'])) return;

        $model = new DocumentModel();
        $qrService = new QRService();
        $audit = new AuditModel();
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $userId = (int)($_SESSION['usuario_id'] ?? 0);

        foreach ($_FILES['pdfs']['tmp_name'] as $i => $tmp) {
            $file = [
                "name" => $_FILES['pdfs']['name'][$i],
                "tmp_name" => $tmp,
                "type" => $_FILES['pdfs']['type'][$i]
            ];

            if ($file["type"] !== "application/pdf") {
                continue;
            }

            $pdfPath = $qrService->moverPDF($file);
            $uniqueId = uniqid('', true);
            $urlConsulta = URL_VIEW_DOCUMENT . $uniqueId;
            $qrPath = $qrService->generarQR($urlConsulta, $uniqueId);

            $ok = $model->insert(
                "",
                $file["name"],
                $pdfPath,
                $qrPath,
                $uniqueId,
                date("Y-m-d")
            );

            if ($ok) {
                $audit->logActivity(
                    $userId,
                    'CREATE_QR_BATCH',
                    'QR',
                    'documents',
                    $uniqueId,
                    'Carga masiva de documento',
                    [
                        'file_name' => $file["name"],
                        'pdf_path' => $pdfPath,
                        'qr_path' => $qrPath
                    ],
                    $_SESSION['audit_session_token'] ?? session_id()
                );
            }
        }
    }
}
