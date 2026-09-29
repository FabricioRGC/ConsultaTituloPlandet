<?php
require_once __DIR__ . '/../../controllers/QRController.php';

$tabKey = 'generarQR';

$controller = new QRController();

$usuarioId = $_SESSION['usuario_id'] ?? 0;
$rolUsuario = $_SESSION['usuario_rol'] ?? 'user';

$auditModel = new AuditModel();
$historialHoyDb = $auditModel->getTodayActivitiesForUserOrAdmin($usuarioId, $rolUsuario);

// Preparamos el JSON de forma segura en PHP antes de pintarlo en el script
$jsonEnvios = json_encode($historialHoyDb, JSON_UNESCAPED_UNICODE);




// 1. Manejo del Formulario de Subida (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->generar();
    // NOTA: Si generar() redirige, asegúrate de incluir &tab= en la URL de redirección
}

// 2. Capturar QR (Si el controlador ha redirigido con el path)
$qrPath = isset($_GET['qr']) ? htmlspecialchars($_GET['qr']) : "";

// 3. Obtener fecha actual en formato YYYY-MM-DD
$fechaActual = date('Y-m-d');
?>

<link rel="stylesheet" href="/ConsultaTituloPlandet/styles/tab-generarQR.css">

<div class="tab-generarQR">
    <div class="main-container">
        <div class="content-wrapper">
            
            <!-- PANEL IZQUIERDO -->
            <aside class="left-panel">
                <div class="left-panel-scroll">
                    <section class="card">
                        <h2 class="card-title">📤 Subir PDF</h2>

                        <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate
                            action="?action=dasboard&tab=<?= urlencode($tabKey) ?>">

                            <button id="clearForm" type="button" class="btn-action btn-clear-form form-clear-btn">
                                <span>🗑️</span> Limpiar Formulario
                            </button>

                            <div class="form-group">
                                <label for="title" class="form-label">Nombre del Título:</label>
                                <input type="text" class="form-control" placeholder="Ingrese el título" id="title" name="title" required>
                                <div class="invalid-feedback">Por favor, proporciona un título.</div>
                            </div>

                            <div class="form-group">
                                <label for="partida" class="form-label">Partida Electrónica:</label>
                                <input type="text" class="form-control" id="partida" name="partida"
                                    placeholder="Ingrese la partida electrónica" required>
                                <div class="invalid-feedback">Ingrese la partida.</div>
                            </div>

                            <div class="form-group">
                                <label for="fecha" class="form-label">Fecha de Emisión:</label>
                                <input type="date" class="form-control" id="fecha" name="fecha"
                                    value="<?= $fechaActual ?>" required>
                                <div class="invalid-feedback">Ingrese la fecha.</div>
                            </div>

                            <div class="form-group">
                                <label for="pdf" class="form-label">Selecciona PDF:</label>
                                <input type="file" class="form-control" id="pdf" name="pdf"
                                    accept="application/pdf" required>
                                <div class="invalid-feedback">Por favor, sube un archivo PDF válido.</div>
                            </div>

                            <div style="display: flex; gap: 8px; margin-top: 15px;">
                                <button type="button" class="btn btn-submit" onclick="validarYSubir(this.form);" style="flex: 2;">
                                    📁 Subir Archivo
                                </button>
                                <button type="button" class="btn" onclick="mostrarHistorialHoy()" style="flex: 1; background: #e2e8f0; color: #1e293b; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px;">
                                    📋 Historial
                                </button>
                            </div>
                        </form>
                    </section>

                    <section class="card">
                        <h2 class="card-title">⚙️ Configuración de Posición</h2>

                        <div class="position-grid">
                            <div class="position-group">
                                <h3 class="position-subtitle">Código QR</h3>
                                <div class="input-row">
                                    <label for="qr-x">Posición X:</label>
                                    <input type="number" id="qr-x" step="0.1" value="0.75" class="input-small">
                                </div>
                                <div class="input-row">
                                    <label for="qr-y">Posición Y:</label>
                                    <input type="number" id="qr-y" step="0.1" value="2.1" class="input-small">
                                </div>
                            </div>

                            <div class="position-group">
                                <h3 class="position-subtitle">Número de Partida</h3>
                                <div class="input-row">
                                    <label for="num-x">Posición X:</label>
                                    <input type="number" id="num-x" step="0.1" value="2.20" class="input-small">
                                </div>
                                <div class="input-row">
                                    <label for="num-y">Posición Y:</label>
                                    <input type="number" id="num-y" step="0.1" value="1.43" class="input-small">
                                </div>
                            </div>
                        </div>

                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="includeQR" checked>
                                <span>Incluir QR</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" id="includePartida" checked>
                                <span>Incluir Partida Electrónica</span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="number-input" class="form-label">Partida (Previsualización):</label>
                            <input type="text" id="number-input" class="form-control"
                                placeholder="Partida Electrónica para el PDF">
                        </div>

                        <div class="form-group">
                            <label for="number-titulo" class="form-label">Título (Previsualización):</label>
                            <input type="text" id="number-titulo" class="form-control"
                                placeholder="Título para el nombre del archivo">
                        </div>

                        <div class="action-buttons-grid">
                            <input type="file" id="qr-code-file" accept="image/*" style="display:none;">
                            <button id="upload-button" class="btn btn-secondary">
                                📷 Cargar QR
                            </button>
                            <button id="preview-button" class="btn btn-preview">
                                👁️ Previsualizar
                            </button>
                            <button id="export-button" class="btn btn-primary">
                                💾 Descargar PDF
                            </button>
                        </div>

                        <div class="qr-preview-container">
                            <p class="preview-label">Vista previa del código QR:</p>
                            <img id="qr-preview" class="qr-preview" alt="Vista previa del QR"
                                src="<?= $qrPath ?>">
                        </div>
                    </section>
                </div>
            </aside>

            <!-- PANEL DERECHO -->
            <main class="right-panel">
                <div class="preview-container">
                    <h2 class="preview-title">📄 Vista Previa del Documento</h2>
                    <iframe class="pdf-viewer" id="pdfViewer" title="Visor de PDF original"></iframe>
                </div>

                <div class="preview-container">
                    <h2 class="preview-title">🔍 Previsualización con QR</h2>
                    <iframe class="pdf-viewer" id="pdf-preview" title="Previsualización del PDF con QR"></iframe>
                </div>
            </main>

        </div>
    </div>
</div>

<!-- Contenedor de Alertas Toast Flotante en la esquina SUPERIOR DERECHA -->
<div class="toast-container" style="position: fixed !important; top: 80px !important; right: 20px !important; bottom: auto !important; left: auto !important; width: auto !important; max-width: 350px !important; z-index: 9999 !important;">
    <div id="liveToast" class="toast fade" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000" style="width: 100% !important; background: white !important; box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important; border-radius: 8px !important;">
        <div class="toast-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-top-left-radius: 8px; border-top-right-radius: 8px; padding: 8px 12px;">
            <strong class="me-auto" style="font-weight: 600; color: #1e293b;">⚠️ Aviso</strong>
        </div>
        <div class="toast-body" style="padding: 12px; color: #475569; font-size: 14px;">
            Mensaje de notificación
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>

<script>
    // 🔥 NUEVO: Obtener tab actual desde PHP
    const currentTab = "<?= $tabKey ?>";
    const uploadSucceeded = "<?= ($_GET['uploaded'] ?? '') === '1' ? '1' : '0' ?>" === "1";
    const serverQrPath = "<?php echo $qrPath; ?>";
    const mainForm = document.querySelector('.needs-validation');
    const enviosHoy = <?php echo $jsonEnvios; ?>;

    // ================================
    // VARIABLES GLOBALES
    // ================================
    const pdfInput = document.getElementById("pdf");
    const pdfViewer = document.getElementById("pdfViewer");
    const titleInput = document.getElementById("title");
    const partidaInput = document.getElementById("partida");
    const tituloNumberInput = document.getElementById("number-titulo");
    const numberInput = document.getElementById("number-input");

    const qrPreview = document.getElementById("qr-preview");
    const qrFile = document.getElementById("qr-code-file");
    const previewBtn = document.getElementById("preview-button");
    const exportBtn = document.getElementById("export-button");
    const pdfPreview = document.getElementById("pdf-preview");
    const qrX = document.getElementById("qr-x");
    const qrY = document.getElementById("qr-y");
    const numX = document.getElementById("num-x");
    const numY = document.getElementById("num-y");

    let qrCodeImageUrl = null;

    // ================================
    // FUNCIONES DE PERSISTENCIA (LOCALSTORAGE)
    // ================================

    function loadTitleAndPartida() {
        const storedTitle = localStorage.getItem('qr_form_title');
        const storedPartida = localStorage.getItem('qr_form_partida');

        if (storedTitle) {
            titleInput.value = storedTitle;
            tituloNumberInput.value = storedTitle;
        }
        if (storedPartida) {
            partidaInput.value = storedPartida;
            numberInput.value = storedPartida;
        }
    }

    function saveToLocalStorageOnChange(element, key) {
        element.addEventListener('input', function() {
            localStorage.setItem(key, this.value);
        });
    }

    // ================================
    // CARGA INICIAL DE DATOS
    // ================================
    function resetGenerationFormState() {
        mainForm.reset();
        titleInput.value = "";
        partidaInput.value = "";
        tituloNumberInput.value = "";
        numberInput.value = "";
        pdfViewer.src = "";
        pdfPreview.src = "";
        qrPreview.src = "";
        qrCodeImageUrl = null;
        localStorage.removeItem('qr_form_title');
        localStorage.removeItem('qr_form_partida');
    }

    function validarYSubir(form) {
    // 1. Capturar los campos
    const titulo = document.getElementById('title');
    const partida = document.getElementById('partida');
    const archivo = document.getElementById('pdf');

    // 2. Validar que no estén vacíos
    if (titulo && titulo.value.trim() === '') {
        showToast('Por favor, ingresa el nombre del título.', 'error');
        titulo.focus();
        return;
    }

    if (partida && partida.value.trim() === '') {
        showToast('Por favor, ingresa la partida electrónica.', 'error');
        partida.focus();
        return;
    }

    if (archivo && archivo.files.length === 0) {
        showToast('Por favor, selecciona un archivo PDF válido.', 'error');
        return;
    }

    // 3. Capturar los valores para mostrarlos en la ventana de confirmación
    const valorTitulo = titulo.value.trim();
    const valorPartida = partida.value.trim();
    const nombreArchivo = archivo.files[0] ? archivo.files[0].name : 'Sin archivo';

    // 4. Mostrar la ventana elegante con el resumen para verificar
    Swal.fire({
        title: 'Verifica los datos de envío',
        html: `
        <div style="text-align: left; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 14px;">
            <p style="margin: 4px 0;"><strong>Título:</strong> ${valorTitulo}</p>
            <p style="margin: 4px 0;"><strong>Partida:</strong> ${valorPartida}</p>
            <p style="margin: 4px 0;"><strong>Archivo:</strong> ${nombreArchivo}</p>
        </div>
        <p style="margin-top: 12px; font-size: 14px;">¿Deseas proceder con la subida?</p>
    `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2563eb', // Azul corporativo
        cancelButtonColor: '#dc2626', // Rojo cancelar
        confirmButtonText: 'Sí, subir archivo',
        cancelButtonText: 'Revisar',
        allowOutsideClick: false // Evita que se cierre haciendo clic fuera por error
    }).then((result) => {
        if (result.isConfirmed) {
            // 🔥 BLOQUEO DE DOBLE ENVÍO: Desactivar el botón del formulario visualmente
            const btnSubmit = form.querySelector('.btn-submit');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '⏳ Subiendo...';
            }

            // Mostrar una alerta de carga de SweetAlert para bloquear la interfaz
            Swal.fire({
                title: 'Subiendo archivo...',
                text: 'Por favor espere un momento.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            form.submit(); // Envía el formulario una sola vez de forma segura
        }
    });
}

    function mostrarHistorialHoy() {
        if (!enviosHoy || enviosHoy.length === 0) {
            Swal.fire({
                title: 'Historial de hoy',
                text: 'No hay registros de actividad en el sistema para el día de hoy.',
                icon: 'info',
                confirmButtonColor: '#2563eb'
            });
            return;
        }

        // Construimos las filas de la tabla con los datos que llegaron
        let filasHTML = '';
        enviosHoy.forEach((item) => {
            filasHTML += `
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 8px; text-align: center; color: #64748b; font-size: 12px;">${item.hora}</td>
                    <td style="padding: 8px; font-weight: 500; color: #1e293b;">${item.user_name || 'Sistema'}</td>
                    <td style="padding: 8px; color: #475569;">${item.action}</td>
                    <td style="padding: 8px; color: #475569; font-size: 13px;">${item.description || 'Sin descripción'}</td>
                </tr>
            `;
        });

        // Mostramos la ventana flotante
        Swal.fire({
            title: '📋 Registros y Envíos de Hoy',
            html: `
                <div style="max-height: 280px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px; margin-top: 10px;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
                        <thead style="background: #f8fafc; position: sticky; top: 0; border-bottom: 2px solid #e2e8f0;">
                            <tr>
                                <th style="padding: 8px; text-align: center;">Hora</th>
                                <th style="padding: 8px;">Usuario</th>
                                <th style="padding: 8px;">Acción</th>
                                <th style="padding: 8px;">Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${filasHTML}
                        </tbody>
                    </table>
                </div>
                <p style="margin-top: 12px; font-size: 12px; color: #64748b;">Total de acciones hoy: <strong>${enviosHoy.length}</strong></p>
            `,
            width: '650px',
            confirmButtonText: 'Cerrar',
            confirmButtonColor: '#2563eb'
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        if (uploadSucceeded) {
            resetGenerationFormState();
            const url = new URL(window.location.href);
            url.searchParams.delete('uploaded');
            window.history.replaceState({}, '', url.toString());
        }

        loadTitleAndPartida();

        if (serverQrPath) {
            qrPreview.src = serverQrPath;
            qrCodeImageUrl = serverQrPath;
        }
    });

    // ================================
    // SINCRONIZAR Y PERSISTIR INPUTS
    // ================================

    titleInput.addEventListener('input', function() {
        localStorage.setItem('qr_form_title', this.value);
        tituloNumberInput.value = this.value;
    });

    partidaInput.addEventListener('input', function() {
        localStorage.setItem('qr_form_partida', this.value);
        numberInput.value = this.value;
    });

    saveToLocalStorageOnChange(tituloNumberInput, 'qr_form_title');
    saveToLocalStorageOnChange(numberInput, 'qr_form_partida');


    // ================================
    // FUNCIONES DE LIMPIEZA
    // ================================
    document.getElementById("clearForm").addEventListener("click", function() {
        resetGenerationFormState();

        mainForm.classList.remove('was-validated');

        // 🔥 MODIFICADO: Mantener tab al limpiar
        window.location.href = "?action=dasboard&tab=" + encodeURIComponent(currentTab);

        showToast("Formulario y datos persistentes limpiados.", "success");
    });

    // ================================
    // VALIDACIÓN DE FORMULARIOS
    // ================================
    (function() {
        'use strict';
        Array.prototype.slice.call(document.querySelectorAll('.needs-validation')).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();

    // ================================
    // CARGA Y PREVISUALIZACIÓN DEL PDF
    // ================================
    pdfInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file && file.type === 'application/pdf') {
            const fileName = file.name.replace(/\.pdf$/i, '');

            if (!titleInput.value) {
                titleInput.value = fileName;
                localStorage.setItem('qr_form_title', fileName);
            }
            if (!tituloNumberInput.value) {
                tituloNumberInput.value = fileName;
            }

            const fileURL = URL.createObjectURL(file);
            // 🔥 AGREGAR #toolbar=1 para mostrar barra de herramientas
            pdfViewer.src = fileURL + '#toolbar=1&navpanes=0&scrollbar=1';

            showToast("PDF cargado correctamente", "success");
        } else {
            showToast('Por favor, selecciona un archivo PDF válido.', "danger");
            pdfInput.value = "";
        }
    });

    // ================================
    // CARGA MANUAL DE QR
    // ================================
    qrFile.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const imageUrl = URL.createObjectURL(file);
            qrPreview.src = imageUrl;
            qrCodeImageUrl = imageUrl;
            showToast("Código QR cargado correctamente", "success");
        } else {
            showToast("Por favor selecciona una imagen válida", "warning");
        }
    });

    document.getElementById("upload-button").addEventListener('click', function() {
        qrFile.click();
    });

    // ================================
    // OBTENER VALORES DE CONFIGURACIÓN
    // ================================
    function getConfigurationValues() {
        return {
            partidaValue: numberInput.value.trim(),
            tituloValue: tituloNumberInput.value.trim(),
            includeQR: document.getElementById('includeQR').checked,
            includePartida: document.getElementById('includePartida').checked,
            qrXVal: parseFloat(qrX.value),
            qrYVal: parseFloat(qrY.value),
            numXVal: parseFloat(numX.value),
            numYVal: parseFloat(numY.value),
            qrCodeImageUrl: qrCodeImageUrl
        };
    }

    // ================================
    // GENERACIÓN DE PDF CON JSPDF
    // ================================
    function generatePDF(callback) {
        const config = getConfigurationValues();

        if (config.includeQR && !config.qrCodeImageUrl) {
            showToast("Debes cargar o generar un código QR para incluirlo.", "danger");
            return;
        }

        const {
            jsPDF
        } = window.jspdf;
        const doc = new jsPDF({
            orientation: 'landscape',
            unit: 'in',
            format: [11.69, 16.54]
        });

        if (config.includeQR && config.qrCodeImageUrl) {
            const qrImage = new Image();
            qrImage.crossOrigin = "anonymous";
            qrImage.src = config.qrCodeImageUrl;

            qrImage.onload = function() {
                try {
                    doc.addImage(qrImage, 'PNG', config.qrXVal, config.qrYVal, 1.10, 1.10);

                    if (config.includePartida && config.partidaValue) {
                        doc.setFontSize(14);
                        doc.text(config.partidaValue, config.numXVal, config.numYVal);
                    }
                    callback(doc);
                } catch (error) {
                    console.error("Error al agregar imagen QR:", error);
                    showToast("Error al agregar el código QR al PDF", "danger");
                }
            };

            qrImage.onerror = function() {
                console.error("Error al cargar imagen QR");
                showToast("Error al cargar la imagen del código QR", "danger");
            };
        } else {
            if (config.includePartida && config.partidaValue) {
                doc.setFontSize(14);
                doc.text(config.partidaValue, config.numXVal, config.numYVal);
            }
            callback(doc);
        }
    }

    // ================================
    // PREVISUALIZAR PDF
    // ================================
    previewBtn.addEventListener('click', function() {
        const config = getConfigurationValues();

        // 🔥 REMOVIDO: Validación de título
        // Ya no es necesario tener un título para previsualizar

        generatePDF(function(doc) {
            if (doc) {
                // 🔥 AGREGAR #toolbar=1 para mostrar barra de herramientas
                const pdfDataUri = doc.output('datauristring');
                pdfPreview.src = pdfDataUri + '#toolbar=1&navpanes=0&scrollbar=1';
                showToast("Vista previa generada correctamente", "success");
            }
        });
    });

    // ================================
    // EXPORTAR PDF
    // ================================
    exportBtn.addEventListener('click', function() {
        const config = getConfigurationValues();

        // 🔥 MODIFICADO: Usar título o nombre por defecto
        const fileName = config.tituloValue || 'documento-' + Date.now();

        generatePDF(function(doc) {
            if (doc) {
                doc.save(fileName + '.pdf');
                showToast("PDF descargado exitosamente: " + fileName + ".pdf", "success");
            }
        });
    });

    // ================================
    // SISTEMA DE NOTIFICACIONES (TOAST)
    // ================================
    function showToast(message, type = "warning") {
        const toastEl = document.getElementById("liveToast");

        // Verificar que el toast existe
        if (!toastEl) {
            console.warn("Toast element not found");
            return;
        }

        const toastHeader = toastEl.querySelector(".toast-header strong");
        const toastBody = toastEl.querySelector(".toast-body");

        if (toastBody) {
            toastBody.textContent = message;
        }

        toastEl.classList.remove("success", "danger", "warning");

        if (type === "success") {
            toastEl.classList.add("success");
            if (toastHeader) toastHeader.textContent = "✅ Éxito";
        } else if (type === "danger") {
            toastEl.classList.add("danger");
            if (toastHeader) toastHeader.textContent = "❌ Error";
        } else {
            toastEl.classList.add("warning");
            if (toastHeader) toastHeader.textContent = "⚠️ Aviso";
        }

        const toast = new bootstrap.Toast(toastEl, {
            delay: 3000
        });
        toast.show();
    }
    // ================================
// SINCRONIZAR Y PERSISTIR INPUTS (Protegido)
// ================================
if (titleInput && tituloNumberInput) {
    titleInput.addEventListener('input', function() {
        localStorage.setItem('qr_form_title', this.value);
        tituloNumberInput.value = this.value;
    });
    saveToLocalStorageOnChange(tituloNumberInput, 'qr_form_title');
}

if (partidaInput && numberInput) {
    partidaInput.addEventListener('input', function() {
        localStorage.setItem('qr_form_partida', this.value);
        numberInput.value = this.value;
    });
    saveToLocalStorageOnChange(numberInput, 'qr_form_partida');
}

const clearFormBtn = document.getElementById("clearForm");
if (clearFormBtn && mainForm) {
    clearFormBtn.addEventListener("click", function() {
        resetGenerationFormState();
        mainForm.classList.remove('was-validated');
        window.location.href = "?action=dasboard&tab=" + encodeURIComponent(currentTab);
        showToast("Formulario y datos persistentes limpiados.", "success");
    });
}
</script>