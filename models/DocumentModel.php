<?php
require_once __DIR__ . '/../config/database.php';

class DocumentModel
{
    private PDO $db;
    private array $columnCache = [];

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function insert($partida, $title, $pdfPath, $qrPath, $uniqueId, $fecha)
    {
        if ($this->hasColumn('fecha')) {
            $sql = "INSERT INTO documents (partida, title, pdf_path, qr_code, unique_id, fecha)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$partida, $title, $pdfPath, $qrPath, $uniqueId, $fecha]);
        }

        $sql = "INSERT INTO documents (partida, title, pdf_path, qr_code, unique_id)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$partida, $title, $pdfPath, $qrPath, $uniqueId]);
    }

    public function getAll($sortOrder = 'DESC')
{
    // Validación de seguridad para el orden
    $sortOrder = (strtoupper($sortOrder) === 'ASC') ? 'ASC' : 'DESC';

    $sql = "SELECT * FROM documents ORDER BY id " . $sortOrder;
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function search($titulo = '', $partida = '', $fecha = '', $sortOrder = 'DESC')
{
    // Validación de seguridad para el orden
    $sortOrder = (strtoupper($sortOrder) === 'ASC') ? 'ASC' : 'DESC';

    if (empty($titulo) && empty($partida) && empty($fecha)) {
        return $this->getAll($sortOrder);
    }

    $conditions = [];
    $params = [];

    if (!empty($titulo)) {
        $conditions[] = "title LIKE ?";
        $params[] = "%{$titulo}%";
    }

    if (!empty($partida)) {
        $conditions[] = "partida LIKE ?";
        $params[] = "%{$partida}%";
    }

    if (!empty($fecha)) {
        if ($this->hasColumn('fecha')) {
            $conditions[] = "DATE(fecha) = ?";
            $params[] = $fecha;
        } elseif ($this->hasColumn('created')) {
            $conditions[] = "DATE(created) = ?";
            $params[] = $fecha;
        }
    }

    if (empty($conditions)) {
        return $this->getAll($sortOrder);
    }

    // 🔥 Aplicamos la variable $sortOrder de forma dinámica y segura al final del SQL
    $sql = "SELECT * FROM documents WHERE " . implode(' AND ', $conditions) . " ORDER BY id " . $sortOrder;
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function searchDocuments(string $numero, string $year): array
    {
        $sql = "SELECT * FROM documents WHERE title LIKE ?";
        $params = ["%$numero%"];

        if ($this->hasColumn('fecha')) {
            $sql .= " AND YEAR(fecha) = ?";
            $params[] = $year;
        } elseif ($this->hasColumn('created')) {
            $sql .= " AND YEAR(created) = ?";
            $params[] = $year;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM documents WHERE id = ?");
        $stmt->execute([$id]);
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);
        return $doc ?: null;
    }

    public function updateDocument(int $id, string $pdf, string $qr, string $uniqueId): void
    {
        $stmt = $this->db->prepare(
            "UPDATE documents SET pdf_path = ?, qr_code = ?, unique_id = ? WHERE id = ?"
        );
        $stmt->execute([$pdf, $qr, $uniqueId, $id]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM documents WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getByUniqueId($uniqueId)
    {
        $sql = "SELECT * FROM documents WHERE unique_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$uniqueId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function hasColumn(string $columnName): bool
    {
        if (array_key_exists($columnName, $this->columnCache)) {
            return $this->columnCache[$columnName];
        }

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
             AND TABLE_NAME = 'documents'
             AND COLUMN_NAME = ?"
        );
        $stmt->execute([$columnName]);
        $exists = ((int)$stmt->fetchColumn()) > 0;
        $this->columnCache[$columnName] = $exists;

        return $exists;
    }
}
