<?php
require_once __DIR__ . '/../../controllers/DocumentController.php';


$tabKey = 'searchQR-PDF';

$controller = new DocumentController();
$documents = $controller->getDocuments();
$currentMonthYear = date('Y-m'); // Formato "2026-10"
$totalMes = 0;
foreach ($documents as $doc) {
    // Tomamos la fecha válida (evaluando fecha o created igual que en tu tabla)
    $fechaDoc = (!empty($doc['fecha']) && strpos($doc['fecha'], '00:00:00') === false)
        ? $doc['fecha']
        : ($doc['created'] ?? '');

    if (!empty($fechaDoc) && strpos($fechaDoc, $currentMonthYear) === 0) {
        $totalMes++;
    }
}
// ⚡ 2. Traducimos el nombre del mes actual al español automáticamente
$mesesEspanol = [
    '01' => 'enero',
    '02' => 'febrero',
    '03' => 'marzo',
    '04' => 'abril',
    '05' => 'mayo',
    '06' => 'junio',
    '07' => 'julio',
    '08' => 'agosto',
    '09' => 'septiembre',
    '10' => 'octubre',
    '11' => 'noviembre',
    '12' => 'diciembre'
];
$numeroMesActual = date('m');
$nombreMesActual = $mesesEspanol[$numeroMesActual] ?? 'mes actual';
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
        value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
        style="border: 1.5px solid #cbd5e1; border-radius: 10px; padding: 10px 14px; outline: none; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.02);"
        onfocus="this.style.borderColor='#2563eb'; this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.15)';"
        onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';">

    <input type="text"
        name="partida"
        id="inputBuscarPartida"
        maxlength="9"
        class="sqr-input"
        placeholder="Buscar por partida"
        value="<?= htmlspecialchars($_POST['partida'] ?? '') ?>"
        style="border: 1.5px solid #cbd5e1; border-radius: 10px; padding: 10px 14px; outline: none; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.02);"
        onfocus="this.style.borderColor='#2563eb'; this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.15)';"
        onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';">

    <input type="date"
        name="fecha"
        class="sqr-input"
        value="<?= htmlspecialchars($_POST['fecha'] ?? '') ?>"
        style="border: 1.5px solid #cbd5e1; border-radius: 10px; padding: 10px 14px; outline: none; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.02);"
        onfocus="this.style.borderColor='#2563eb'; this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.15)';"
        onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';">

    <!-- Selector de orden con el mismo estilo azul elegante -->

            <button class="sqr-btn sqr-btn-primary" type="submit" style="background: linear-gradient(to right, #2563eb, #1d4ed8) !important; color: white !important; border: none !important; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);">
                Buscar
            </button>
        </form>

        <?php if (!empty($documents)): ?>
            <!-- 1. Cabecera y el minifiltro de orden adaptados al azul marino del login -->
            <!-- 1. Cabecera con el azul marino exacto forzado por estilo en línea -->
            <div class="sqr-card-header" style="display: flex; justify-content: space-between; align-items: center; background-color: #1f50b1 !important; background-image: none !important; border-bottom: 1px solid rgba(37, 99, 235, 0.4) !important; padding: 12px 20px; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h3 class="sqr-card-title" style="margin: 0; color: #ffffff; font-weight: 700;">Documentos Disponibles</h3>

                <form method="GET" action="" id="sortForm" style="margin: 0; display: inline-block;">
                    <input type="hidden" name="action" value="dashboard">
                    <input type="hidden" name="tab" value="<?= htmlspecialchars($_GET['tab'] ?? 'buscarQR') ?>">
                    <?php if (!empty($_GET['title'])): ?>
                        <input type="hidden" name="title" value="<?= htmlspecialchars($_GET['title']) ?>">
                    <?php endif; ?>
                    <?php if (!empty($_GET['partida'])): ?>
                        <input type="hidden" name="partida" value="<?= htmlspecialchars($_GET['partida']) ?>">
                    <?php endif; ?>
                    <?php if (!empty($_GET['fecha'])): ?>
                        <input type="hidden" name="fecha" value="<?= htmlspecialchars($_GET['fecha']) ?>">
                    <?php endif; ?>
                    <div style="display: inline-flex; align-items: center; background: #f1f1f7; border: 1px solid rgba(59, 130, 246, 0.4); border-radius: 8px; padding: 6px 12px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);">
                        <span style="font-size: 13px; margin-right: 6px;"></span>
                        <select name="sort_order" id="sort_order_select" style="font-size: 14px; font-weight: 600; color: #000003; background: transparent; border: none; outline: none; cursor: pointer; padding-right: 4px;" onchange="this.form.submit()">
                            <option value="DESC" style="background: #dcdce4; color: #0a0000;" <?= (($_GET['sort_order'] ?? 'DESC') === 'DESC') ? 'selected' : '' ?>>Últimos registrados</option>
                            <option value="ASC" style="background: #ced2da; color: #070000;" <?= (($_GET['sort_order'] ?? '') === 'ASC') ? 'selected' : '' ?>>Primeros registrados</option>
                        </select>
                    </div>
                </form>
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
                                <td class="sqr-td-fecha">
                                    <?php
                                    // Si la fecha tiene hora válida la usamos, de lo contrario usamos 'created'
                                    $fechaMostrar = (!empty($row['fecha']) && strpos($row['fecha'], '00:00:00') === false)
                                        ? $row['fecha']
                                        : ($row['created'] ?? '');

                                    echo !empty($fechaMostrar) ? date('d/m/Y h:i A', strtotime($fechaMostrar)) : '-';
                                    ?>
                                </td>
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
                <p class="sqr-empty-text">No se encontraron documentos registrados.</p>
            </div>
        <?php endif; ?>
    </div>
    <!-- 🌟 Notificación flotante elegante del resumen del mes -->
    <div id="monthlyModalToast" style="position: fixed; bottom: 30px; right: 30px; background: #ffffff; border-left: 5px solid #7c3aed; box-shadow: 0 10px 25px rgba(0,0,0,0.15); padding: 16px 20px; border-radius: 10px; display: flex; align-items: center; gap: 14px; z-index: 9999; opacity: 0; transform: translateY(20px); transition: all 0.4s ease;">
        <div style="background: #ede9fe; color: #06020e; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold;">
            📊
        </div>
        <div>
            <h4 style="margin: 0; font-size: 14px; color: #232b38; font-weight: 700;">Resumen de <?= ucfirst($nombreMesActual) ?></h4>
            <p style="margin: 2px 0 0 0; font-size: 13px; color: #64748b;">
                Este mes se han agregado <strong style="color: #040108;"><?= $totalMes ?></strong> documentos.
            </p>
        </div>
        <button type="button" onclick="closeMonthlyToast()" style="background: transparent; border: none; font-size: 16px; color: #94a3b8; cursor: pointer; margin-left: 10px; padding: 0;">&times;</button>
    </div>
    <!-- VISUALIZACION QR Y PARTIDA -->
    <div class="sqr-card sqr-preview-card">
        <div class="sqr-card-header" style="background: linear-gradient(to right, #2563eb, #1d4ed8) !important; color: white !important; border: none !important; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);">
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
        <div class="sqr-card-header" style="background: linear-gradient(to right, #2563eb, #1d4ed8) !important; color: white !important; border: none !important; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);">
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
                    <button id="search-export-button" class="sqr-btn sqr-btn-primary" style="background: linear-gradient(to right, #2563eb, #1d4ed8) !important; color: white !important; border: none !important; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);">
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
    function showMonthlyToast() {
        const toast = document.getElementById('monthlyModalToast');
        if (toast) {
            // Reiniciamos estados
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
            toast.style.pointerEvents = 'none';

            // Animación de entrada
            setTimeout(() => {
                toast.style.pointerEvents = 'auto';
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0)';
            }, 250);

            // Ocultar automáticamente a los 6 segundos
            setTimeout(() => {
                closeMonthlyToast();
            }, 2600);
        }
    }

    function closeMonthlyToast() {
        const toast = document.getElementById('monthlyModalToast');
        if (toast) {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
            toast.style.pointerEvents = 'none';
        }
    }

    // 1. Ejecutar al cargar la vista por primera vez
    showMonthlyToast();

    // 2. 🔥 Detectar cuando haces clic en la pestaña de "Buscar Qr / Pdf" en el menú superior
    document.addEventListener('click', function(e) {
        const tabButton = e.target.closest('a, button');
        if (tabButton && (tabButton.textContent.includes('Buscar Qr') || tabButton.href?.includes('searchQR-PDF'))) {
            // Damos un pequeño margen para que la pestaña se haga visible y lanzamos el modal
            setTimeout(showMonthlyToast, 300);
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Capturamos cualquiera de los dos campos de partida que existan en la vista actual
        const inputsPartida = [
            document.getElementById('partida'),
            document.getElementById('inputBuscarPartida')
        ].filter(Boolean);

        const btnSubir = document.getElementById('btnSubirArchivo');

        inputsPartida.forEach(inputPartida => {
            // 1. Control estricto en tiempo real para cada input de partida
            inputPartida.addEventListener('input', function(e) {
                let val = this.value;
                if (val.length === 0) {
                    this.value = '';
                    return;
                }

                let first = val.charAt(0);
                let formatted = '';

                // CASO A: Empieza con una letra (ej. P)
                if (/^[a-zA-Z]$/.test(first)) {
                    formatted += first.toUpperCase();
                    if (val.length > 1) {
                        let rest = val.substring(1);
                        let second = rest.charAt(0);
                        // El primer número después de la letra debe ser de 1 al 9 (prohibido el 0)
                        if (/^[1-9]$/.test(second)) {
                            formatted += second;
                            if (rest.length > 1) {
                                // Los siguientes dígitos aceptan números del 0 al 9 (hasta completar 8 en total)
                                let remainingDigits = rest.substring(1, 8).replace(/[^0-9]/g, '');
                                formatted += remainingDigits;
                            }
                        }
                    }
                }
                // CASO B: Empieza con un número del 1 al 9 (prohibido el 0 al inicio)
                else if (/^[1-9]$/.test(first)) {
                    formatted += first;
                    if (val.length > 1) {
                        let rest = val.substring(1, 8).replace(/[^0-9]/g, '');
                        formatted += rest;
                    }
                }
                // Si intenta poner un 0 al inicio o caracteres no válidos, se limpia
                else {
                    formatted = '';
                }

                this.value = formatted;
            });
        });

        // 2. Validación estricta al hacer clic en el botón de subida (si aplica)
        if (btnSubir) {
            const originalOnClick = btnSubir.onclick || btnSubir.getAttribute('onclick');

            btnSubir.onclick = function(e) {
                if (e) e.preventDefault();

                const inputTitulo = document.getElementById('title') || document.querySelector('input[name="title"]');
                const activePartida = document.getElementById('partida');

                // Validar Título
                if (inputTitulo && inputTitulo.value.trim() === '') {
                    showCustomErrorModal('Campo Requerido', 'Por favor, ingrese el nombre del título antes de continuar.');
                    inputTitulo.focus();
                    return false;
                }

                // Validar Partida
                if (activePartida) {
                    const valPartida = activePartida.value.trim();
                    const regexPartida = /^([A-Z][1-9][0-9]{7}|[1-9][0-9]{7})$/;

                    if (!regexPartida.test(valPartida)) {
                        showCustomErrorModal('Partida Inválida', 'La partida debe ser de 8 dígitos (ej. 12345678) o 1 letra seguida de 8 dígitos (ej. P12345678). El primer número nunca puede ser 0.');
                        activePartida.focus();
                        return false;
                    }
                }

                if (typeof originalOnClick === 'function') {
                    return originalOnClick.call(this, e);
                } else if (typeof originalOnClick === 'string') {
                    return eval(originalOnClick);
                }
            };
        }
    });

    function showCustomErrorModal(title, message) {
        const modal = document.getElementById('customErrorModal');
        const titleEl = document.getElementById('customErrorTitle');
        const msgEl = document.getElementById('customErrorMessage');

        if (modal && titleEl && msgEl) {
            titleEl.textContent = title;
            msgEl.textContent = message;
            modal.style.display = 'flex';
        }
    }

    function closeCustomErrorModal() {
        const modal = document.getElementById('customErrorModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

document.addEventListener('DOMContentLoaded', function() {
    const filas = document.querySelectorAll('.sqr-tr');
    const radios = document.querySelectorAll('.search-radio-doc');

    // Función para actualizar la previsualización del QR y los inputs de texto
    function actualizarPrevisualizacion(radio) {
        if (!radio) return;
        const qrCode = radio.getAttribute('data-qr');
        const title = radio.getAttribute('data-title');
        const partida = radio.getAttribute('data-partida');

        const qrDisplay = document.getElementById('search-qrDisplay');
        const qrPlaceholder = document.getElementById('search-qrPlaceholder');
        const inputTitulo = document.getElementById('search-number-titulo');
        const inputPartida = document.getElementById('search-partida-input');

        if (inputTitulo) inputTitulo.value = title || '';
        if (inputPartida) inputPartida.value = partida || '';

        if (qrDisplay && qrPlaceholder) {
            if (qrCode && qrCode.trim() !== '' && qrCode !== 'null') {
                qrDisplay.src = qrCode;
                qrDisplay.style.display = 'block';
                qrPlaceholder.style.display = 'none';
            } else {
                qrDisplay.src = '';
                qrDisplay.style.display = 'none';
                qrPlaceholder.style.display = 'flex';
            }
        }
    }

    // 1. Permitir clic en cualquier parte de la fila para seleccionar el documento
    filas.forEach(fila => {
        fila.style.cursor = 'pointer';
        
        fila.addEventListener('click', function(e) {
            // Si el usuario hace clic en el botón o enlace "Ver PDF", permitimos su acción normal
            if (e.target.closest('a') || e.target.closest('button')) {
                return;
            }

            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                radio.dispatchEvent(new Event('change'));

                // Actualizar clases visuales de selección
                filas.forEach(f => f.classList.remove('selected-row'));
                this.classList.add('selected-row');

                actualizarPrevisualizacion(radio);
            }
        });
    });

    // 2. Evento por si el usuario marca el radio directamente
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            const fila = this.closest('.sqr-tr');
            if (fila) {
                filas.forEach(f => f.classList.remove('selected-row'));
                fila.classList.add('selected-row');
            }
            actualizarPrevisualizacion(this);
        });
    });

    // 3. Control de botones de Previsualizar y Exportar a PDF
    const btnPreview = document.getElementById('search-preview-button');
    const btnExport = document.getElementById('search-export-button');
    const previewWrapper = document.getElementById('search-previewWrapper');
    const pdfPreviewFrame = document.getElementById('search-pdfPreview');
    const closePreviewBtn = document.getElementById('search-closePreview');

    function getSelectedDocumentData() {
        const selectedRadio = document.querySelector('input.search-radio-doc:checked');
        if (!selectedRadio) {
            alert('Por favor, selecciona un documento de la lista primero.');
            return null;
        }
        return {
            uid: selectedRadio.getAttribute('data-uid'),
            title: selectedRadio.getAttribute('data-title'),
            partida: selectedRadio.getAttribute('data-partida'),
            qr: selectedRadio.getAttribute('data-qr')
        };
    }

    if (btnPreview) {
        btnPreview.addEventListener('click', function(e) {
            e.preventDefault();
            const docData = getSelectedDocumentData();
            if (!docData) return;

            const previewUrl = `/ConsultaTituloPlandet/view.php?id=${encodeURIComponent(docData.uid)}&preview=true`;
            if (pdfPreviewFrame && previewWrapper) {
                pdfPreviewFrame.src = previewUrl;
                previewWrapper.style.display = 'block';
                previewWrapper.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }

    if (closePreviewBtn) {
        closePreviewBtn.addEventListener('click', function() {
            if (previewWrapper && pdfPreviewFrame) {
                pdfPreviewFrame.src = '';
                previewWrapper.style.display = 'none';
            }
        });
    }

    if (btnExport) {
        btnExport.addEventListener('click', function(e) {
            e.preventDefault();
            const docData = getSelectedDocumentData();
            if (!docData) return;

            const exportUrl = `/ConsultaTituloPlandet/view.php?id=${encodeURIComponent(docData.uid)}`;
            window.open(exportUrl, '_blank');
        });
    }
});
</script>
