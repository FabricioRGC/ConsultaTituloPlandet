<?php
require_once __DIR__ . '/../../controllers/DocumentController.php';

$data = (new DocumentController())->handleUpdateQRPDF();
extract($data);

$tabKey = 'updateQR-PDF';
?>

<link rel="stylesheet" href="/ConsultaTituloPlandet-1/styles/tab-updateQR-PDF.css">

<div class="upd-container" data-component="update-qr-pdf">

    <?php if ($mode === 'search'): ?>
    
    <!-- 🔍 MODO BÚSQUEDA -->
    <div class="upd-card upd-search-card">
        <div class="upd-card-header">
            <h3 class="upd-title">🔍 Buscar Documento para Actualizar</h3>
        </div>
        <div class="upd-card-body">
            <form method="post" action="?action=dasboard&tab=<?= urlencode($tabKey) ?>" class="upd-search-form">
                <div class="upd-form-row">
                    <div class="upd-form-group">
                        <label class="upd-label">Título del Documento</label>
                        <input type="text" name="numero" class="upd-input" placeholder="Ej: 183" required>
                        <small class="upd-help-text">💡 Si existen varios documentos con el mismo título, se mostrarán todos para que selecciones el correcto.</small>
                    </div>
                    <div class="upd-form-group">
                        <label class="upd-label">Año</label>
                        <select name="year" class="upd-select" required>
                            <option value="">Seleccione año</option>
                            <option value="2026">2026</option>
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                            <option value="2022">2022</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="upd-btn upd-btn-primary">
                    🔍 Buscar Documento
                </button>
            </form>
        </div>
    </div>

    <?php elseif ($mode === 'list'): ?>
    
    <!-- 📋 LISTA DE RESULTADOS (Cuando hay duplicados) -->
    <div class="upd-card upd-results-card">
        <div class="upd-card-header">
            <h2 class="upd-title">📋 Resultados de Búsqueda</h2>
            <span class="upd-badge"><?= count($searchResults) ?> documento(s) encontrado(s)</span>
        </div>
        <div class="upd-card-body">
            <div class="upd-alert upd-alert-warning">
                <strong>⚠️ Se encontraron múltiples documentos</strong>
                <p>Selecciona el documento exacto que deseas actualizar.</p>
            </div>
            
            <div class="upd-results-grid">
                <?php foreach ($searchResults as $doc): ?>
                <div class="upd-result-card">
                    <div class="upd-result-header">
                        <span class="upd-result-id">ID: <?= $doc['id'] ?></span>
                        <span class="upd-result-date">📅 <?= htmlspecialchars($doc['fecha'] ?? 'N/A') ?></span>
                    </div>
                    <h3 class="upd-result-title"><?= htmlspecialchars($doc['title']) ?></h3>
                    <div class="upd-result-meta">
                        <?php if (!empty($doc['partida'])): ?>
                        <span>📄 Partida: <?= htmlspecialchars($doc['partida']) ?></span>
                        <?php endif; ?>
                    </div>
                    <a href="?action=dasboard&tab=<?= urlencode($tabKey) ?>&select_doc=<?= $doc['id'] ?>" 
                       class="upd-btn upd-btn-secondary upd-btn-sm">
                        Seleccionar →
                    </a>
                </div>
                <?php endforeach; ?>
            </div>

            <a href="?action=dasboard&tab=<?= urlencode($tabKey) ?>" class="upd-btn upd-btn-outline">
                ← Nueva Búsqueda
            </a>
        </div>
    </div>

    <?php elseif ($mode === 'update'): ?>
    
    <!-- ✏️ MODO ACTUALIZACIÓN - VISTA COMPACTA -->
    <div class="upd-update-layout">
        
        <!-- Información del Documento -->
        <div class="upd-doc-header">
            <div class="upd-doc-main-info">
                <span class="upd-doc-id">ID: <?= $selectedDocument['id'] ?></span>
                <h2 class="upd-doc-title-main"><?= htmlspecialchars($selectedDocument['title']) ?></h2>
                <div class="upd-doc-metadata">
                    <span>📄 Partida: <?= htmlspecialchars($selectedDocument['partida'] ?? 'N/A') ?></span>
                    <span>📅 Fecha: <?= htmlspecialchars($selectedDocument['fecha'] ?? 'N/A') ?></span>
                </div>
            </div>
            <a href="?action=dasboard&tab=<?= urlencode($tabKey) ?>" class="upd-btn upd-btn-outline upd-btn-sm">
                ← Volver
            </a>
        </div>

        <?php if ($error): ?>
        <div class="upd-alert upd-alert-danger">
            <strong>❌ Error:</strong> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <!-- Grid Compacto: Formulario + Previews -->
        <div class="upd-compact-grid">
            
            <!-- Columna 1: Formulario -->
            <div class="upd-form-section">
                <div class="upd-section-card">
                    <h3 class="upd-section-title">✏️ Actualizar</h3>
                    
                    <form method="post" enctype="multipart/form-data" class="upd-update-form" 
                          action="?action=dasboard&tab=<?= urlencode($tabKey) ?>">
                        
                        <input type="hidden" name="document_id" value="<?= $selectedDocument['id'] ?>">

                        <!-- Tipo de Actualización -->
                        <div class="upd-form-group-compact">
                            <label class="upd-label-compact">Tipo de Actualización</label>
                            <div class="upd-radio-group-compact">
                                <label class="upd-radio-label-compact">
                                    <input type="radio" name="update_type" value="pdf_and_qr" class="upd-radio" required>
                                    <span class="upd-radio-content">
                                        <strong>📄+📱 PDF + QR</strong>
                                        <small>Genera nuevo QR</small>
                                    </span>
                                </label>
                                <label class="upd-radio-label-compact">
                                    <input type="radio" name="update_type" value="pdf_only" class="upd-radio">
                                    <span class="upd-radio-content">
                                        <strong>📄 Solo PDF</strong>
                                        <small>Mantiene QR actual</small>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Archivo PDF -->
                        <div class="upd-form-group-compact">
                            <label class="upd-label-compact">Nuevo PDF</label>
                            <div class="upd-file-input-wrapper-compact">
                                <input type="file" name="pdf_nuevo" id="upd-pdf-input" 
                                       class="upd-file-input" accept="application/pdf" required>
                                <label for="upd-pdf-input" class="upd-file-label-compact">
                                    <span class="upd-file-icon">📎</span>
                                    <span class="upd-file-text">Seleccionar PDF...</span>
                                </label>
                            </div>
                        </div>


                        <!-- Botón Submit -->
                        <button type="submit" class="upd-btn upd-btn-primary upd-btn-full">
                            💾 Actualizar Documento
                        </button>
                    </form>
                </div>
            </div>

            <!-- Columna 2: PDF Actual -->
            <div class="upd-preview-section">
                <div class="upd-section-card upd-preview-card-compact">
                    <h3 class="upd-section-title">📄 PDF Actual</h3>
                    <div class="upd-preview-wrapper-compact">
                        <?php if (!empty($selectedDocument['pdf_path']) && file_exists($selectedDocument['pdf_path'])): ?>
                        <iframe src="<?= htmlspecialchars($selectedDocument['pdf_path']) ?>" 
                                class="upd-pdf-viewer-compact" 
                                style="display: block;"
                                title="PDF Actual"></iframe>
                        <?php else: ?>
                        <div class="upd-preview-placeholder-compact">
                            <span>📄</span>
                            <p>Sin PDF</p>
                            <?php if (!empty($selectedDocument['pdf_path'])): ?>
                            <small style="font-size: 0.7rem; color: #ef4444;">
                                Path: <?= htmlspecialchars($selectedDocument['pdf_path']) ?>
                            </small>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Columna 3: Nuevo PDF -->
            <div class="upd-preview-section">
                <div class="upd-section-card upd-preview-card-compact">
                    <h3 class="upd-section-title">📄 Nuevo PDF</h3>
                    <div class="upd-preview-wrapper-compact">
                        <iframe id="upd-new-pdf-preview" class="upd-pdf-viewer-compact" title="Nuevo PDF"></iframe>
                        <div class="upd-preview-placeholder-compact" id="upd-pdf-placeholder">
                            <span>📎</span>
                            <p>Selecciona un PDF</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna 4: QR Actual -->
            <div class="upd-preview-section">
                <div class="upd-section-card upd-qr-card-compact">
                    <h3 class="upd-section-title">📱 QR Actual</h3>
                    <div class="upd-qr-wrapper-compact">
                        <?php if (!empty($selectedDocument['qr_code'])): ?>
                        <img src="<?= htmlspecialchars($selectedDocument['qr_code']) ?>" 
                             alt="QR Actual" class="upd-qr-image-compact">
                        <?php else: ?>
                        <div class="upd-preview-placeholder-compact">
                            <span>📱</span>
                            <p>Sin QR</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php elseif ($mode === 'empty'): ?>
    
    <!-- 🔍 SIN RESULTADOS -->
    <div class="upd-card upd-empty-card">
        <div class="upd-card-body upd-empty-body">
            <div class="upd-empty-icon">🔍</div>
            <h3 class="upd-empty-title">No se encontraron resultados</h3>
            <p class="upd-empty-text">
                <?= htmlspecialchars($error ?: 'No hay documentos que coincidan con tu búsqueda') ?>
            </p>
            <form method="get" style="margin-top: 1rem;">
                <input type="hidden" name="action" value="dasboard">
                <input type="hidden" name="tab" value="<?= urlencode($tabKey) ?>">
                <button type="submit" class="upd-btn upd-btn-primary">
                    🔍 Nueva Búsqueda
                </button>
            </form>
        </div>
    </div>

    <?php elseif ($mode === 'success'): ?>
    
    <!-- ✅ ÉXITO -->
    <div class="upd-card upd-success-card">
        <div class="upd-card-body upd-success-body">
            <div class="upd-success-icon">✅</div>
            <h3 class="upd-success-title">Actualización Exitosa</h3>
            
            <div class="upd-success-details">
                <div class="upd-detail-item">
                    <span class="upd-detail-label">Documento ID:</span>
                    <span class="upd-detail-value"><?= $success['document_id'] ?></span>
                </div>
                <div class="upd-detail-item">
                    <span class="upd-detail-label">Nuevo PDF:</span>
                    <span class="upd-detail-value"><?= basename($success['new_pdf']) ?></span>
                </div>
                <?php if ($success['qr_regenerated']): ?>
                <div class="upd-detail-item upd-detail-highlight">
                    <span class="upd-detail-label">Estado QR:</span>
                    <span class="upd-detail-value">✅ Regenerado correctamente</span>
                </div>
                <?php else: ?>
                <div class="upd-detail-item">
                    <span class="upd-detail-label">Estado QR:</span>
                    <span class="upd-detail-value">🔄 Se mantiene el QR anterior</span>
                </div>
                <?php endif; ?>
            </div>

            <div class="upd-success-actions">
                <a href="?action=dasboard&tab=<?= urlencode($tabKey) ?>" class="upd-btn upd-btn-primary">
                    ✏️ Actualizar Otro Documento
                </a>
            </div>
        </div>
    </div>

    <?php endif; ?>

</div>

<script>
(() => {
    const root = document.querySelector('[data-component="update-qr-pdf"]');
    if (!root) return;

    // Manejo de radio buttons (mostrar/ocultar password)
    const passwordBox = root.querySelector('.upd-password-box-compact');
    const radios = root.querySelectorAll('input[name="update_type"]');

    radios.forEach(r => {
        r.addEventListener('change', () => {
            if (passwordBox) {
                passwordBox.style.display = r.value === 'pdf_only' ? 'block' : 'none';
            }
        });
    });

    // Previsualización del nuevo PDF
    const pdfInput = document.getElementById('upd-pdf-input');
    const newPdfPreview = document.getElementById('upd-new-pdf-preview');
    const pdfPlaceholder = document.getElementById('upd-pdf-placeholder');
    const fileLabel = root.querySelector('.upd-file-text');

    if (pdfInput) {
        pdfInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            
            if (file && file.type === 'application/pdf') {
                // Actualizar label
                if (fileLabel) {
                    fileLabel.textContent = file.name.length > 25 
                        ? file.name.substring(0, 25) + '...' 
                        : file.name;
                }

                // Mostrar preview
                const fileURL = URL.createObjectURL(file);
                if (newPdfPreview && pdfPlaceholder) {
                    newPdfPreview.src = fileURL;
                    newPdfPreview.style.display = 'block';
                    pdfPlaceholder.style.display = 'none';
                }
            } else {
                alert('Por favor selecciona un archivo PDF válido');
                pdfInput.value = '';
                if (fileLabel) {
                    fileLabel.textContent = 'Seleccionar PDF...';
                }
            }
        });
    }

    // Confirmación al actualizar con QR
    const form = root.querySelector('.upd-update-form');
    if (form) {
        form.addEventListener('submit', (e) => {
            const selected = root.querySelector('input[name="update_type"]:checked');
            if (selected?.value === 'pdf_and_qr') {
                if (!confirm('⚠️ El QR anterior dejará de funcionar.\n\n¿Estás seguro de continuar?')) {
                    e.preventDefault();
                }
            }
        });
    }
})();
</script>
