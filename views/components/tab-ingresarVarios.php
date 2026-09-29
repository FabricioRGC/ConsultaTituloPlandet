<?php
require_once __DIR__ . 'ConsultaTituloPlandet/controllers/QRController.php';

$controller = new QRController();
$controller->subirMultiples();
?>

<link rel="stylesheet" href="ConsultaTituloPlandet/styles/tab-ingresarVarios.css">

<div class="upload-wrapper">
    <div class="upload-container">
        <header class="upload-header">
            <h1 class="upload-title">Subir Múltiples PDFs</h1>
            <p class="upload-subtitle">Selecciona uno o más archivos PDF para generar sus códigos QR</p>
        </header>

        <div class="upload-card">
            <form method="post" enctype="multipart/form-data" class="upload-form" id="uploadForm" novalidate>
                
                <div class="form-group">
                    <label for="archivos-pdf" class="form-label">
                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                        Seleccionar archivos PDF
                    </label>
                    
                    <div class="file-input-wrapper">
                        <input type="file"
                               class="file-input"
                               id="archivos-pdf"
                               name="pdfs[]"
                               accept="application/pdf"
                               multiple 
                               required>
                        <label for="archivos-pdf" class="file-input-label">
                            <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            <span class="file-input-text">Haz clic o arrastra archivos aquí</span>
                            <span class="file-input-hint">Puedes seleccionar múltiples archivos PDF</span>
                        </label>
                    </div>
                    
                    <div class="feedback-invalid">
                        Por favor, selecciona al menos un archivo PDF válido.
                    </div>
                </div>

                <!-- Vista previa de archivos -->
                <div id="previsualizacion-archivos" class="file-preview-container" style="display: none;">
                    <h3 class="preview-title">Archivos seleccionados</h3>
                    <div id="lista-archivos" class="file-list"></div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="btn-subir">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        Generar códigos QR
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS externos -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>

<script>
(function () {
    'use strict';
    
    const formulario = document.getElementById('uploadForm');
    const inputArchivos = document.getElementById('archivos-pdf');
    const previsualizacion = document.getElementById('previsualizacion-archivos');
    const listaArchivos = document.getElementById('lista-archivos');
    const btnSubir = document.getElementById('btn-subir');

    // Validación del formulario
    formulario.addEventListener('submit', function (event) {
        if (!formulario.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        formulario.classList.add('was-validated');
    }, false);

    // Previsualización de archivos seleccionados
    inputArchivos.addEventListener('change', function(e) {
        const archivos = Array.from(this.files);
        
        if (archivos.length > 0) {
            mostrarPrevisualizacion(archivos);
            previsualizacion.style.display = 'block';
            btnSubir.disabled = false;
        } else {
            previsualizacion.style.display = 'none';
            btnSubir.disabled = true;
        }
    });

    // Drag and drop
    const fileInputLabel = document.querySelector('.file-input-label');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        fileInputLabel.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        fileInputLabel.addEventListener(eventName, () => {
            fileInputLabel.classList.add('drag-over');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        fileInputLabel.addEventListener(eventName, () => {
            fileInputLabel.classList.remove('drag-over');
        });
    });

    fileInputLabel.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        inputArchivos.files = files;
        
        const event = new Event('change', { bubbles: true });
        inputArchivos.dispatchEvent(event);
    });

    function mostrarPrevisualizacion(archivos) {
        listaArchivos.innerHTML = '';
        
        archivos.forEach((archivo, index) => {
            const tamanoMB = (archivo.size / (1024 * 1024)).toFixed(2);
            const qrId = `QR_${Date.now()}_${index}`;
            
            const archivoCard = document.createElement('div');
            archivoCard.className = 'file-card';
            archivoCard.innerHTML = `
                <div class="file-info">
                    <div class="file-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <div class="file-details">
                        <div class="file-name" title="${archivo.name}">${archivo.name}</div>
                        <div class="file-meta">
                            <span class="file-size">${tamanoMB} MB</span>
                            <span class="file-separator">•</span>
                            <span class="file-type">PDF</span>
                        </div>
                    </div>
                </div>
                <div class="qr-info">
                    <div class="qr-placeholder">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                    </div>
                    <div class="qr-label">
                        <span class="qr-id">Ejm: ${qrId}</span>
                        <span class="qr-status">Pendiente</span>
                    </div>
                </div>
            `;
            
            listaArchivos.appendChild(archivoCard);
        });
    }
})();
</script>

</body>
</html>