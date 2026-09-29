<?php
require_once __DIR__ . '/../../controllers/DocumentController.php';

$tabKey = 'searchQR-PDF';

$controller = new DocumentController();
$documents = $controller->getDocuments();
$hasFilters = !empty($_POST['title'] ?? '') || !empty($_POST['partida'] ?? '') || !empty($_POST['fecha'] ?? '');
?>
<link rel="stylesheet" href="/ConsultaTituloPlandet/styles/tab-searchQR-PDF.css">
<div class="tab-searchqr sqr-container" id="searchQRTab">

    <!-- BUSQUEDA + LISTADO DE DOCUMENTOS -->
    <div class="sqr-card sqr-documents-card">
        <form method="post" class="sqr-search-bar" action="?action=dasboard&tab=<?= urlencode($tabKey) ?>">
            <input type="text"
                name="title"
                class="sqr-input"
                placeholder="Buscar por titulo"
                value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">

            <input type="text"
                name="partida"
                class="sqr-input"
                placeholder="Buscar por partida"
                value="<?= htmlspecialchars($_POST['partida'] ?? '') ?>">

            <input type="date"
                name="fecha"
                class="sqr-input"
                value="<?= htmlspecialchars($_POST['fecha'] ?? '') ?>">

            <button class="sqr-btn sqr-btn-primary" type="submit">
                Buscar
            </button>
        </form>

        <?php if (!empty($documents)): ?>
            <div class="sqr-card-header">
                <h3 class="sqr-card-title">Documentos Disponibles</h3>
            </div>
            <div class="sqr-table-wrapper">
                <table class="sqr-table">
                    <thead>
                        <tr>
                            <th class="sqr-th-select">Seleccionar</th>
                            <th class="sqr-th-id">ID</th>
                            <th class="sqr-th-title">Titulo</th>
                            <th class="sqr-th-partida">Partida</th>
                            <th class="sqr-th-fecha">Fecha</th>
                            <th class="sqr-th-actions">PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $row): ?>
                            <tr class="sqr-tr">
                                <td class="sqr-td-center">
                                    <input type="radio"
                                        name="search_selected_doc"
                                        class="sqr-radio search-radio-doc"
                                        data-qr="<?= htmlspecialchars($row['qr_code'], ENT_QUOTES) ?>"
                                        data-title="<?= htmlspecialchars($row['title']) ?>"
                                        data-partida="<?= htmlspecialchars($row['partida']) ?>"
                                        data-uid="<?= htmlspecialchars($row['unique_id'] ?? '', ENT_QUOTES) ?>">
                                </td>
                                <td class="sqr-td-id"><?= (int)$row['id'] ?></td>
                                <td class="sqr-td-title"><?= htmlspecialchars($row['title']) ?></td>
                                <td class="sqr-td-partida"><?= htmlspecialchars($row['partida']) ?></td>
                                <td class="sqr-td-fecha"><?= htmlspecialchars($row['fecha']) ?></td>
                                <td class="sqr-td-center">
                                    <a href="/ConsultaTituloPlandet/view.php?id=<?= urlencode($row['unique_id'] ?? '') ?>"
                                        target="_blank"
                                        class="sqr-btn sqr-btn-info">
                                        Ver PDF
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="sqr-empty-state">
                <div class="sqr-empty-icon">-</div>
                <?php if ($hasFilters): ?>
                    <p class="sqr-empty-text">No se encontraron documentos con esos filtros.</p>
                    <form method="get" action="" style="margin-top: 0.75rem;">
                        <input type="hidden" name="action" value="dasboard">
                        <input type="hidden" name="tab" value="<?= htmlspecialchars($tabKey) ?>">
                        <button type="submit" class="sqr-btn sqr-btn-secondary">Limpiar busqueda</button>
                    </form>
                <?php else: ?>
                    <p class="sqr-empty-text">No hay documentos registrados.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <!-- VISUALIZACION QR Y PARTIDA -->
    <div class="sqr-card sqr-preview-card">
        <div class="sqr-card-header">
            <h3 class="sqr-card-title">Previsualizacion</h3>
        </div>
        <div class="sqr-preview-grid">
            <div class="sqr-qr-section">
                <h4 class="sqr-section-title">Codigo QR</h4>
                <div class="sqr-qr-display">
                    <img id="search-qrDisplay" src="" alt="Codigo QR" class="sqr-qr-image">
                    <div class="sqr-qr-placeholder" id="search-qrPlaceholder">
                        <span>QR</span>
                        <p>Selecciona un documento</p>
                    </div>
                </div>
            </div>

            <div class="sqr-partida-section">

                <h4 class="sqr-section-title">Nombre del Titulo</h4>
                <input type="text"
                    id="search-number-titulo"
                    maxlength="50"
                    class="sqr-input sqr-input-large"
                    placeholder="Ingrese el nombre del titulo">

                <h4 class="sqr-section-title">Partida Electronica</h4>
                <input type="text"
                    id="search-partida-input"
                    class="sqr-input sqr-input-large"
                    placeholder="Ingrese la partida electronica">

                <div class="sqr-checkboxes">
                    <label class="sqr-checkbox-label">
                        <input type="checkbox" id="search-chkQR" class="sqr-checkbox" checked>
                        <span>Incluir QR</span>
                    </label>
                    <label class="sqr-checkbox-label">
                        <input type="checkbox" id="search-chkPartida" class="sqr-checkbox" checked>
                        <span>Incluir Partida Electronica</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- CONFIGURACION DE POSICIONES -->
    <div class="sqr-card sqr-position-card">
        <div class="sqr-card-header">
            <h3 class="sqr-card-title">Configuracion de Posiciones en PDF</h3>
        </div>
        <div class="sqr-position-grid">
            <div class="sqr-position-group">
                <h5 class="sqr-group-title">Posicion del QR</h5>
                <div class="sqr-input-row">
                    <div class="sqr-input-group">
                        <label class="sqr-label">Posicion X</label>
                        <input type="number" id="search-qr-x" value="0.75" step="0.1" class="sqr-input">
                    </div>
                    <div class="sqr-input-group">
                        <label class="sqr-label">Posicion Y</label>
                        <input type="number" id="search-qr-y" value="2.1" step="0.1" class="sqr-input">
                    </div>
                </div>
            </div>

            <div class="sqr-position-group">
                <h5 class="sqr-group-title">Posicion del Numero</h5>
                <div class="sqr-input-row">
                    <div class="sqr-input-group">
                        <label class="sqr-label">Posicion X</label>
                        <input type="number" id="search-num-x" value="2.20" step="0.1" class="sqr-input">
                    </div>
                    <div class="sqr-input-group">
                        <label class="sqr-label">Posicion Y</label>
                        <input type="number" id="search-num-y" value="1.43" step="0.1" class="sqr-input">
                    </div>
                </div>
            </div>
            <div class="sqr-export-content">
                <div class="sqr-actions">
                    <button id="search-preview-button" class="sqr-btn sqr-btn-secondary">
                        Previsualizar PDF
                    </button>
                    <button id="search-export-button" class="sqr-btn sqr-btn-primary">
                        Exportar a PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- PREVIEW PDF -->
    <div class="sqr-preview-wrapper" id="search-previewWrapper" style="display:none;">
        <div class="sqr-preview-header">
            <h3>Vista Previa del PDF</h3>
            <button class="sqr-btn sqr-btn-close" id="search-closePreview">
                âœ– Cerrar
            </button>
        </div>
        <iframe id="search-pdfPreview" class="sqr-pdf-frame"></iframe>
    </div>

</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script>
    // ðŸ”¥ NUEVO: Pasar tab actual a JavaScript
    const CURRENT_TAB = "<?= $tabKey ?>";
    function trackEvent(action, module, description, metadata = {}) {
        fetch('/ConsultaTituloPlandet/index.php?action=track_event', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action, module, description, metadata })
        }).catch(() => {});
    }

    // Encapsular todo en un namespace Ãºnico para evitar conflictos
    window.SearchQRModule = (function() {
        'use strict';

        let initialized = false;

        function init() {
            // Evitar inicializaciÃ³n mÃºltiple
            if (initialized) {
                console.log('SearchQR: Ya estÃ¡ inicializado');
                return;
            }

            console.log('SearchQR: Inicializando mÃ³dulo...');

            // Verificar que el tab estÃ© visible
            const tabElement = document.getElementById('searchQRTab');
            if (!tabElement) {
                console.log('SearchQR: Tab no encontrado');
                return;
            }

            function extractNumber(inputText) {
                let extracted = '';
                let parts = inputText.split('-');
                if (parts.length >= 2) {
                    extracted = parts[1];
                } else {
                    const match = inputText.match(/\d+/);
                    if (match) {
                        extracted = match[0];
                    }
                }
                if (extracted && /^\d+$/.test(extracted)) {
                    extracted = parseInt(extracted, 10).toString();
                }
                return extracted;
            }

            // Evento para radio buttons
            const radios = document.querySelectorAll('.search-radio-doc');
            console.log('SearchQR: Radio buttons encontrados:', radios.length);

            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        const qrUrl = this.getAttribute('data-qr');
                        const title = this.getAttribute('data-title');
                        const partida = this.getAttribute('data-partida');
                        const uniqueId = this.getAttribute('data-uid');
                        seleccionarDocumento(qrUrl, title, partida, uniqueId);
                    }
                });
            });

            const rows = document.querySelectorAll('.sqr-tr');

            rows.forEach(row => {

                // CLICK SIMPLE = SELECCIONAR
                row.addEventListener('click', function(e) {

                    // âŒ No reaccionar si es el botÃ³n PDF
                    if (e.target.closest('.sqr-btn')) return;

                    const radio = row.querySelector('.search-radio-doc');
                    if (!radio) return;

                    radio.checked = true;
                    radio.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
                });

                // DOBLE CLICK = PREVIEW
                row.addEventListener('dblclick', function(e) {
                    if (e.target.closest('.sqr-btn')) return;

                    const previewBtn = document.getElementById('search-preview-button');
                    if (previewBtn) previewBtn.click();
                });
            });

            // BotÃ³n exportar
            const exportButton = document.getElementById('search-export-button');
            if (exportButton) {
                exportButton.addEventListener('click', async function() {
                    console.log('SearchQR: Click en Exportar');
                    try {
                        const titulo = document.getElementById('search-number-titulo').value.trim() || 'SIN-TITULO';
                        const doc = await generatePDF();
                        doc.save(`${titulo}.pdf`);
                        trackEvent('DOWNLOAD_PDF', 'DOCUMENT', 'Descarga de PDF desde Buscar QR/PDF', { titulo });
                        console.log('SearchQR: PDF exportado');
                    } catch (error) {
                        console.error('SearchQR: Error al exportar PDF:', error);
                        alert('Error al exportar el PDF: ' + error.message);
                    }
                });
            }

            // BotÃ³n preview
            const previewButton = document.getElementById('search-preview-button');
            if (previewButton) {
                previewButton.addEventListener('click', async function() {
                    console.log('SearchQR: Click en Preview');
                    try {
                        const doc = await generatePDF();
                        const blobUrl = doc.output('bloburl');
                        const iframe = document.getElementById('search-pdfPreview');
                        const wrapper = document.getElementById('search-previewWrapper');

                        iframe.src = blobUrl;
                        wrapper.style.display = 'block';

                        setTimeout(() => {
                            wrapper.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }, 100);

                        trackEvent('PREVIEW_PDF', 'DOCUMENT', 'Previsualizacion de PDF en Buscar QR/PDF');
                        console.log('SearchQR: Preview mostrado');
                    } catch (error) {
                        console.error('SearchQR: Error al previsualizar PDF:', error);
                        alert('Error al previsualizar el PDF: ' + error.message);
                    }
                });
            }

            // BotÃ³n cerrar preview
            const closePreview = document.getElementById('search-closePreview');
            if (closePreview) {
                closePreview.addEventListener('click', function() {
                    document.getElementById('search-previewWrapper').style.display = 'none';
                });
            }

            initialized = true;
            console.log('SearchQR: MÃ³dulo inicializado correctamente');
        }

        function limpiarSeleccionVisual() {
            document.querySelectorAll('.sqr-tr.is-selected')
                .forEach(row => row.classList.remove('is-selected'));
        }

        function normalizeQrPath(rawPath) {
            if (!rawPath) return '';
            const normalized = rawPath.replace(/\\/g, '/').trim();
            if (/^https?:\/\//i.test(normalized) || normalized.startsWith('/')) {
                return normalized;
            }
            return '/ConsultaTituloPlandet/' + normalized.replace(/^\/+/, '');
        }

        function seleccionarDocumento(qrUrl, title, partida, uniqueId) {
            console.log('SearchQR: Seleccionando documento:', {
                qrUrl,
                title,
                partida,
                uniqueId
            });

            const qrDisplay = document.getElementById('search-qrDisplay');
            const qrPlaceholder = document.getElementById('search-qrPlaceholder');
            const tituloInput = document.getElementById('search-number-titulo');
            const partidaInput = document.getElementById('search-partida-input');

            if (qrDisplay && qrPlaceholder) {
                qrDisplay.src = normalizeQrPath(qrUrl);
                qrDisplay.onerror = function() {
                    if (uniqueId) {
                        qrDisplay.src = '/ConsultaTituloPlandet/qr_preview.php?uid=' + encodeURIComponent(uniqueId) + '&t=' + Date.now();
                    }
                };
                qrDisplay.style.display = 'block';
                qrPlaceholder.style.display = 'none';
            }

            if (tituloInput && partidaInput) {
                tituloInput.value = title;
                partidaInput.value = partida;
                console.log('SearchQR: TÃ­tulo y Partida asignados:', title, partida);
            }
        }

        async function getDataUrlFromImage(imageUrl) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.crossOrigin = 'Anonymous';
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    canvas.getContext('2d').drawImage(img, 0, 0);
                    resolve(canvas.toDataURL('image/png'));
                };
                img.onerror = (error) => {
                    console.error('SearchQR: Error al cargar imagen:', error);
                    reject(error);
                };
                img.src = imageUrl;
            });
        }

        async function generatePDF() {
            console.log('SearchQR: Generando PDF...');

            if (typeof window.jspdf === 'undefined') {
                throw new Error('jsPDF no estÃ¡ cargado');
            }

            const {
                jsPDF
            } = window.jspdf;

            const doc = new jsPDF({
                orientation: 'landscape',
                unit: 'in',
                format: [11.69, 16.54]
            });

            const qrX = parseFloat(document.getElementById('search-qr-x').value);
            const qrY = parseFloat(document.getElementById('search-qr-y').value);
            const numX = parseFloat(document.getElementById('search-num-x').value);
            const numY = parseFloat(document.getElementById('search-num-y').value);

            if (document.getElementById('search-chkQR').checked) {
                const qrUrl = document.getElementById('search-qrDisplay').src;
                if (qrUrl && qrUrl !== '' && !qrUrl.endsWith('/')) {
                    try {
                        const img = await getDataUrlFromImage(qrUrl);
                        doc.addImage(img, 'PNG', qrX, qrY, 1.10, 1.10);
                    } catch (error) {
                        console.error('SearchQR: Error al agregar QR:', error);
                    }
                }
            }

            if (document.getElementById('search-chkPartida').checked) {
                const partida = document.getElementById('search-partida-input').value.trim();
                if (partida) {
                    doc.setFontSize(14);
                    doc.text(partida, numX, numY);
                }
            }

            return doc;
        }

        // Exponer solo la funciÃ³n de inicializaciÃ³n
        return {
            init: init,
            destroy: function() {
                initialized = false;
                console.log('SearchQR: MÃ³dulo destruido');
            }
        };
    })();

    // Auto-inicializar cuando el DOM estÃ© listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => window.SearchQRModule.init(), 100);
        });
    } else {
        setTimeout(() => window.SearchQRModule.init(), 100);
    }

    // TambiÃ©n intentar inicializar cuando el tab se haga visible
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            const tabElement = document.getElementById('searchQRTab');
            if (tabElement && tabElement.offsetParent !== null) {
                window.SearchQRModule.init();
            }
        });
    });

    // Observar cambios en el DOM
    if (document.body) {
        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['style', 'class']
        });
    }
</script>
