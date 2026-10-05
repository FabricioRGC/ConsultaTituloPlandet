<?php

require_once __DIR__ . '/../models/DocumentModel.php';
require_once __DIR__ . '/../models/AuditModel.php';
require_once __DIR__ . '/../services/QRService.php';
require_once __DIR__ . '/../config/urls.php';

class DocumentController
{
    private DocumentModel $model;
    private QRService $qr;

    public function __construct()
    {
        $this->model = new DocumentModel();
        $this->qr    = new QRService();
    }

    /**
     * ÚNICO método que usa el componente
     */
    public function getDocuments()
{
    // 1. Capturamos los filtros (tanto de POST como de GET por si se recarga con el orden)
    $titulo  = $_POST['title']   ?? $_GET['title']   ?? '';
    $partida = $_POST['partida'] ?? $_GET['partida'] ?? '';
    $fecha   = $_POST['fecha']   ?? $_GET['fecha']   ?? '';
    
    // 🔥 Capturamos el parámetro de ordenamiento (por defecto DESC)
    $sortOrder = $_GET['sort_order'] ?? 'DESC';

    // 2. Ejecutamos la búsqueda en el modelo pasándole el orden
    // (Asegúrate de usar la misma forma en la que llamas a tu modelo, por ejemplo $this->DocumentModel o $this->model)
    $results = $this->model->search($titulo, $partida, $fecha, $sortOrder);

    // 3. Registro de auditoría si hay filtros activos
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $hasFilters = ($titulo !== '' || $partida !== '' || $fecha !== '');
    
    if ($hasFilters) {
        $audit = new AuditModel();
        $audit->logActivity(
            (int)($_SESSION['usuario_id'] ?? 0),
            'SEARCH_DOCUMENTS',
            'DOCUMENT',
            'documents',
            null,
            'Busqueda de documentos',
            [
                'title' => $titulo,
                'partida' => $partida,
                'fecha' => $fecha,
                'sort_order' => $sortOrder,
                'results' => is_array($results) ? count($results) : 0
            ],
            $_SESSION['audit_session_token'] ?? session_id()
        );
    }

    return $results;
}

    public function handleUpdateQRPDF(): array
    {
        // Estados iniciales
        $mode             = 'search';
        $searchResults    = null;
        $selectedDocument = null;
        $success          = null;
        $error            = null;

        /* ==========================
           1️⃣ BUSCAR DOCUMENTO
        ========================== */
        if (isset($_POST['numero'], $_POST['year'])) {
            try {
                $results = $this->model->searchDocuments(
                    $_POST['numero'],
                    $_POST['year']
                );

                $searchResults = is_array($results) ? $results : [];

                if (count($searchResults) === 0) {
                    $mode = 'empty';
                    $error = 'No se encontro el documento.';
                } elseif (count($searchResults) === 1) {
                    $selectedDocument = $searchResults[0];
                    $mode = 'update';
                } else {
                    $mode = 'list';
                }

                if (session_status() !== PHP_SESSION_ACTIVE) session_start();
                $audit = new AuditModel();
                $audit->logActivity(
                    (int)($_SESSION['usuario_id'] ?? 0),
                    'SEARCH_UPDATE_DOCUMENT',
                    'DOCUMENT',
                    'documents',
                    null,
                    'Busqueda en modulo actualizar',
                    [
                        'numero' => (string)$_POST['numero'],
                        'year' => (string)$_POST['year'],
                        'results' => count($searchResults)
                    ],
                    $_SESSION['audit_session_token'] ?? session_id()
                );
            } catch (Throwable $e) {
                $searchResults = [];
                $mode = 'empty';
                $error = 'No se encontro el documento.';
            }
        }

        /* ==========================
           2️⃣ SELECCIONAR DOCUMENTO
        ========================== */
        if (isset($_GET['select_doc'])) {
            $selectedDocument = $this->model->getById((int)$_GET['select_doc']);
            if ($selectedDocument) {
                $mode = 'update';
                if (session_status() !== PHP_SESSION_ACTIVE) session_start();
                $audit = new AuditModel();
                $audit->logActivity(
                    (int)($_SESSION['usuario_id'] ?? 0),
                    'SELECT_DOCUMENT',
                    'DOCUMENT',
                    'documents',
                    (string)$selectedDocument['id'],
                    'Seleccion de documento para actualizar',
                    null,
                    $_SESSION['audit_session_token'] ?? session_id()
                );
            }
        }

        /* ==========================
           3️⃣ ACTUALIZAR PDF / QR
        ========================== */
        if (isset($_POST['document_id'], $_FILES['pdf_nuevo'])) {
            try {
                $doc = $this->model->getById((int)$_POST['document_id']);
                if (!$doc) {
                    throw new Exception('Documento no encontrado');
                }

                // Mover PDF
                $newPdfPath = $this->qr->moverPDF($_FILES['pdf_nuevo']);

                $updateType = $_POST['update_type'] ?? 'pdf_only';
                $qrPath     = $doc['qr_code'];

                // 🔁 Regenerar QR si corresponde
                if ($updateType === 'pdf_and_qr') {
                    $uniqueId = uniqid('', true);
                    $url      = URL_VIEW_DOCUMENT . $uniqueId;

                    $qrPath = $this->qr->generarQR($url, $uniqueId);
                }

                // Guardar cambios
                $this->model->updateDocument(
                    $doc['id'],
                    $newPdfPath,
                    $qrPath,
                    $uniqueId ?? $doc['unique_id']
                );

                if (session_status() !== PHP_SESSION_ACTIVE) session_start();
                $audit = new AuditModel();
                $audit->logActivity(
                    (int)($_SESSION['usuario_id'] ?? 0),
                    'UPDATE_DOCUMENT',
                    'DOCUMENT',
                    'documents',
                    (string)$doc['id'],
                    $updateType === 'pdf_and_qr'
                        ? 'Actualizacion de PDF y QR'
                        : 'Actualizacion solo de PDF',
                    [
                        'update_type' => $updateType,
                        'pdf_path' => $newPdfPath,
                        'qr_changed' => $updateType === 'pdf_and_qr'
                    ],
                    $_SESSION['audit_session_token'] ?? session_id()
                );

                $success = [
                    'document_id'   => $doc['id'],
                    'new_pdf'       => $newPdfPath,
                    'qr_regenerated' => $updateType === 'pdf_and_qr'
                ];

                $mode = 'success';
            } catch (Throwable $e) {
                $error = $e->getMessage();
                $mode  = 'update';
            }
        }

        return compact(
            'mode',
            'searchResults',
            'selectedDocument',
            'success',
            'error'
        );
    }

    public function viewPdf(string $uniqueId): void
    {
        $document = $this->model->getByUniqueId($uniqueId);

        if (!$document) {
            http_response_code(404);
            echo "Documento no encontrado.";
            exit;
        }

        $pdfPath = $document['pdf_path'];
        $title   = $document['title'];

        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        
        $audit = new AuditModel();
        $audit->logActivity(
            (int)($_SESSION['usuario_id'] ?? 0),
            'VIEW_PDF',
            'DOCUMENT',
            'documents',
            (string)($document['id'] ?? ''),
            'Visualizacion de PDF',
            ['unique_id' => $uniqueId, 'title' => $title],
            $_SESSION['audit_session_token'] ?? session_id()
        );

        if (!file_exists($pdfPath)) {
            http_response_code(404);
            echo "El archivo no existe.";
            exit;
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($title) . '.pdf"');
        header('Content-Length: ' . filesize($pdfPath));

        readfile($pdfPath);
        exit;
    }
}
