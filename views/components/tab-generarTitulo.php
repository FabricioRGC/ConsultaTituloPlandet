<link rel="stylesheet" href="styles/tab-generarTitulo.css">

<body onload="cargarDatos()" data-component="generar-titulo" class="gt-body">

  <main class="gt-main container-fluid">

    <!-- HEADER DEL MÓDULO -->
    <div class="gt-module-header">
      <div class="gt-module-info">
        <h3 class="gt-module-title">Generador de Títulos</h3>
        <p class="gt-module-desc">
          Consulta por partida, previsualiza y genera el título en PDF.
        </p>
      </div>

      <div class="gt-module-actions">
        <a
          class="gt-module-link"
          target="_blank"
          href="https://docs.google.com/spreadsheets/d/1Vb7zt3Y5SoPDqNrjjirnnOeJoV5jcG5cG2liLO5RyNs/edit?usp=sharing">
          Almacenamiento
        </a>

        <a
          class="gt-module-link"
          target="_blank"
          href="https://docs.google.com/spreadsheets/d/1PDYLZHsedFmZSVF9pHA6VTJouT2DjRdDSxqEIg18HoM/edit?usp=sharing">
          Registro de Títulos
        </a>
      </div>
    </div>

    <!-- GRID PRINCIPAL -->
    <div class="row g-3">

      <!-- PANEL IZQUIERDO -->
      <aside class="col-lg-4">
        <div class="gt-card">

          <h4 class="gt-title">Buscar por Partida</h4>

          <label class="form-label small text-muted">
            Partida
          </label>

          <input
            type="text"
            id="buscarPartida"
            class="form-control"
            placeholder="Ejemplo: P14224007"
            oninput="buscarPorPartida()">

          <div id="mensaje" class="gt-message"></div>

          <div class="d-grid gap-2 mt-3">
            <button class="btn btn-success" onclick="generarPDF()">
              Generar PDF
            </button>

            <button class="btn btn-warning" onclick="previsualizarPDF()">
              Previsualizar PDF
            </button>

            <button class="btn btn-primary" onclick="cargarDatos()">
              Recargar datos
            </button>
          </div>

          <span id="estadoCarga" class="gt-status"></span>
        </div>
      </aside>

      <section class="gt-preview-section">

        <div class="gt-preview-header">
          <h5>Vista previa del PDF</h5>
        </div>

        <div class="gt-preview-frame">
          <iframe
            id="pdfPreview"
            title="Vista previa del PDF"
            loading="lazy"></iframe>
        </div>

      </section>

    </div>
  </main>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>
  <script>
    const meses = ["enero", "febrero", "marzo", "abril", "mayo", "junio",
      "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"
    ];

    let datosGlobales = [];
    let datosCargados = null;

    async function cargarDatos() {
      const estado = document.getElementById("estadoCarga");
      estado.textContent = "⏳ Cargando...";
      estado.style.color = "blue";

      try {
        const url = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vRkiCrlo9iLU6Mmp1CtGyoJXlQJPOeDUwl-peU94hPF3Rvrci2jx-h9akXH9-F_XPHkydsIkLkCTFKq/pub?output=csv&t=' + new Date().getTime();
        const response = await fetch(url);
        const csvText = await response.text();

        Papa.parse(csvText, {
          header: true,
          skipEmptyLines: true,
          complete: function(results) {
            datosGlobales = results.data.map(row => {
              const cleaned = {};
              for (let key in row) {
                if (key) cleaned[key.trim().toLowerCase()] = row[key].trim();
              }
              return cleaned;
            });

            estado.textContent = "✅ Datos cargados.";
            estado.style.color = "green";
          }
        });
      } catch (error) {
        estado.textContent = "❌ Error al cargar.";
        estado.style.color = "red";
        console.error("Error cargando los datos:", error);
      }
    }

    function buscarPorPartida() {
      const partidaInput = document.getElementById("buscarPartida").value.trim().toLowerCase();
      const mensaje = document.getElementById("mensaje");

      if (!partidaInput) {
        mensaje.textContent = "⚠️ Ingrese un número de partida.";
        mensaje.style.color = "orange";
        return;
      }

      const resultado = datosGlobales.find(fila => {
        const partidaFila = (fila["partida"] || "").toLowerCase().replace(/[,.\s]/g, "");
        const partidaBuscada = partidaInput.replace(/[,.\s]/g, "");
        return partidaFila === partidaBuscada;
      });

      if (resultado) {
        datosCargados = resultado;
        mensaje.textContent = "✅ Partida encontrada.";
        mensaje.style.color = "green";
      } else {
        datosCargados = null;
        mensaje.textContent = "❌ No se encontró la partida.";
        mensaje.style.color = "red";
      }
    }

    function previsualizarPDF() {
      if (!datosCargados) {
        alert("Primero debes buscar una partida :3.");
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



      if (datosCargados.fecha_u && datosCargados.fecha_u.trim() !== "") {
        const fecha = (datosCargados.fecha_u || ""); // Ejemplo: "24/06/2025"
        let dia = "",
          mes = "",
          año = "";

        if (fecha.includes("/")) {
          const partes = fecha.split("/");
          dia = partes[0];
          mes = partes[1];
          año = partes[2];
        }
        let mesTexto = "";
        if (mes) {
          mesTexto = meses[parseInt(mes, 10) - 1]; // restamos 1 porque los arrays empiezan en 0
        }

        doc.setFontSize(10);
        doc.text(dia + " de " + mesTexto + " del " + año, 11.55, 1.1);

        const titulo = datosCargados.tipo_titulo?.trim().toUpperCase();
        if (titulo === "PRIVADAS") {
          if (datosCargados.fecha_p && datosCargados.fecha_p.trim() !== "") {
            const fecha = (datosCargados.fecha_p || ""); // Ejemplo: "24/06/2025"
            let dia = "",
              mes = "",
              año = "";

            if (fecha.includes("/")) {
              const partes = fecha.split("/");
              dia2 = partes[0];
              mes2 = partes[1];
              año2 = partes[2];
            }
            let mesTexto2 = "";
            if (mes2) {
              mesTexto2 = meses[parseInt(mes2, 10) - 1]; // restamos 1 porque los arrays empiezan en 0
            }
            doc.setFontSize(10);
            doc.text(dia2, 9.545, 8.66);
            doc.setFontSize(10);
            doc.text(mesTexto2, 10.8, 8.66);
            doc.setFontSize(10);
            doc.text(año2, 12.15, 8.66);
          }
        } else {
          doc.setFontSize(10);
          doc.text(dia, 9.545, 8.66);
          doc.setFontSize(10);
          doc.text(mesTexto, 10.8, 8.66);
          doc.setFontSize(10);
          doc.text(año, 12.15, 8.66);
        }
      }

      if (datosCargados.pvalor && datosCargados.pvalor.trim() !== "") {
        let pvalor = parseFloat(datosCargados.pvalor || "0");
        let pvalorFormateado = "S/ " + pvalor.toLocaleString("es-PE", {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });

        doc.setFontSize(10);
        doc.text(pvalorFormateado, 11.9, 1.74, {
          align: 'left'
        }); // 14.5 es el borde derecho donde se "ancla"
      }

      doc.setFontSize(10);
      doc.text((datosCargados.n_titulo || ""), 11.7, 0.5);

      doc.setFontSize(10);
      doc.text((datosCargados.sector || ""), 1, 6.98);
      doc.setFontSize(10);
      doc.text((datosCargados.manzana || ""), 5.05, 6.98);
      doc.setFontSize(10);
      doc.text((datosCargados.lote || ""), 6.18, 6.98);
      doc.setFontSize(10);
      doc.text((datosCargados.distrito || ""), 1.4, 7.28);

      //doc.setFontSize(10); doc.text((datosCargados.pvalor || ""), 12.3, 1.75);

      const area = datosCargados.area && !isNaN(datosCargados.area) ? parseFloat(datosCargados.area).toFixed(2) : "";
      const perimetro = datosCargados.perimetro && !isNaN(datosCargados.perimetro) ? parseFloat(datosCargados.perimetro).toFixed(2) : "";

      doc.setFontSize(10);
      doc.text(area, 1.02, 7.4);
      doc.setFontSize(10);
      doc.text(perimetro, 2.94, 7.4);

      doc.setFontSize(10);
      doc.text((datosCargados.frente || ""), 1.6, 7.73);
      doc.setFontSize(10);
      doc.text((datosCargados.derecha || ""), 1.6, 8.0);
      doc.setFontSize(10);
      doc.text((datosCargados.isquierda || ""), 1.6, 8.28);
      doc.setFontSize(10);
      doc.text((datosCargados.fondo || ""), 1.6, 8.56);

      // Mensajes personalizados
      // Validación propietario 1
      if (datosCargados.propietario_1 && datosCargados.propietario_1.trim() !== "") {
        doc.setFontSize(10);
        doc.text(datosCargados.propietario_1, 0.5, 2.35);
        doc.setFontSize(10);
        doc.text(datosCargados.propietario_1, 9.0, 4.68);
        doc.setFontSize(10);
        doc.text(datosCargados.dni_1 || "", 3.7, 2.35);
        doc.setFontSize(10);
        doc.text(datosCargados.estado_civil_1 || "", 5.6, 2.35);
        doc.setFontSize(10);
        doc.text(datosCargados.acc_der || "", 12.25, 4.68);
        doc.setFontSize(10);
        doc.text(datosCargados.c_bien || "", 13.95, 4.68);
      }

      // Validación propietario 2
      if (datosCargados.propietario_2 && datosCargados.propietario_2.trim() !== "") {
        doc.setFontSize(10);
        doc.text(datosCargados.propietario_2, 0.5, 2.66);
        doc.setFontSize(10);
        doc.text(datosCargados.propietario_2, 9.0, 5.0);
        doc.setFontSize(10);
        doc.text(datosCargados.dni_2 || "", 3.7, 2.66);
        doc.setFontSize(10);
        doc.text(datosCargados.estado_civil_2 || "", 5.6, 2.66);
        doc.setFontSize(10);
        doc.text(datosCargados.acc_der || "", 12.25, 5.0);
        doc.setFontSize(10);
        doc.text(datosCargados.c_bien || "", 13.95, 5.0);
      }

      // Validación propietario 3
      if (datosCargados.propietario_3 && datosCargados.propietario_3.trim() !== "") {
        doc.setFontSize(10);
        doc.text(datosCargados.propietario_3, 0.5, 2.95);
        doc.setFontSize(10);
        doc.text(datosCargados.propietario_3, 9.0, 5.3);
        doc.setFontSize(10);
        doc.text(datosCargados.dni_3 || "", 3.7, 2.95);
        doc.setFontSize(10);
        doc.text(datosCargados.estado_civil_3 || "", 5.6, 2.95);
        doc.setFontSize(10);
        doc.text(datosCargados.acc_der || "", 12.25, 5.3);
        doc.setFontSize(10);
        doc.text(datosCargados.c_bien || "", 13.95, 5.3);
      }

      if (datosCargados.propietario_4 && datosCargados.propietario_4.trim() !== "") {
        doc.setFontSize(10);
        doc.text(datosCargados.propietario_4, 0.5, 3.22);
        doc.setFontSize(10);
        doc.text(datosCargados.propietario_4, 9.0, 5.47);
        doc.setFontSize(10);
        doc.text(datosCargados.dni_4 || "", 3.7, 3.22);
        doc.setFontSize(10);
        doc.text(datosCargados.estado_civil_4 || "", 5.6, 3.22);
        doc.setFontSize(10);
        doc.text(datosCargados.acc_der || "", 12.25, 5.47);
        doc.setFontSize(10);
        doc.text(datosCargados.c_bien || "", 13.95, 5.47);
      }

      if (datosCargados.cargas?.toUpperCase() === "TRUE") {
        doc.setFontSize(10);
        doc.text("Prohibición de transferencia de dominio por un período de 05 años (Ley N° 31056, Art 5.2)", 9.1, 2.91); // o el texto que tú quieras mostrar
      }

      if (datosCargados.otros?.toUpperCase() === "TRUE") {
        doc.setFontSize(10);
        doc.text("De incumplirse la prohibición, se revertirá el predio al dominio del Estado (DS Nº 002-2021-VIVIENDA, Art. 7.5)", 9.1, 3.54); // o tu texto personalizado
      }
      //doc.setFontSize(10); doc.text((datosCargados.cargas || ""), 9.1, 2.91);
      //doc.setFontSize(10); doc.text((datosCargados.otros || ""), 9.1, 3.54);

      const tipo = datosCargados.tipo_titulo?.trim().toUpperCase();

      if (tipo === "PRIVADAS") {
        doc.setFontSize(10);
        doc.text(" Declaración de Propiedad por Prescripción Adquisitiva de Dominio", 1, 5.44);
        doc.setFontSize(10);
        doc.text("el Reglamento de los Procesos de Formalización de la Propiedad Informal en la Provincia de Trujillo", 1, 5.68);
        doc.setFontSize(10);
        doc.text("aprobado mediante Ordenanza Municipal N° 026-2023-MPT", 1, 5.91);

        doc.setFontSize(10);
        doc.text("Declaración de Propiedad por", 4.2, 9.775);
        doc.setFontSize(10);
        doc.text("Prescripción Adquisitiva de Dominio", 1.7, 9.98);
        doc.setFontSize(10);
        doc.text("la Ley N° 28687, Ley N° 31056 y sus respectivos reglamentos, y la", 3.45, 10.165);
        doc.setFontSize(10);
        doc.text("Ordenanza Municipal N° 026-2023-MPT", 1.5, 10.36);
      } else if (tipo === "CONTINGENCIA") {
        doc.setFontSize(10);
        doc.text("Continuación del proceso de formalización de los lotes de contingencia", 1, 5.44);
        doc.setFontSize(10);
        doc.text("el Reglamento de los Procesos de Formalización de la Propiedad Informal en la provincia de Trujillo,", 1, 5.68);
        doc.setFontSize(10);
        doc.text("aprobado mediante Ordenanza Municipal N° 026-2023-MPT", 1, 5.91);

        doc.setFontSize(10);
        doc.text("Continuación del proceso de", 4.2, 9.775);
        doc.setFontSize(10);
        doc.text("formalización de los lotes de contingencia", 1.7, 9.98);
        doc.setFontSize(10);
        doc.text("la Ley N° 28687, Ley N° 31056 y sus respectivos reglamentos, y la", 3.45, 10.165);
        doc.setFontSize(10);
        doc.text("Ordenanza Municipal N° 026-2023-MPT", 1.5, 10.36);
      } else if (tipo === "ESTATAL") {
        doc.setFontSize(10);
        doc.text("Formalización de Posesiones Informales en Propiedad Estatal", 1, 5.44);
        doc.setFontSize(10);
        doc.text("el Reglamento de los Procesos de Formalización de la Propiedad Informal en la provincia de Trujillo,", 1, 5.68);
        doc.setFontSize(10);
        doc.text("aprobado mediante Ordenanza Municipal N° 026-2023-MPT", 1, 5.91);

        doc.setFontSize(10);
        doc.text("Formalización de Posesiones", 4.2, 9.775);
        doc.setFontSize(10);
        doc.text("Informales en Propiedad Estatal", 1.7, 9.98);
        doc.setFontSize(10);
        doc.text("la Ley N° 28687, Ley N° 31056 y sus respectivos reglamentos, y la", 3.45, 10.165);
        doc.setFontSize(10);
        doc.text("Ordenanza Municipal N° 026-2023-MPT", 1.5, 10.36);
      } else {
        // texto por defecto nada xd
      }

      const modali_da = datosCargados.modalidad?.trim().toUpperCase();

      if (modali_da === "PROCEDIMIENTO ONEROSO") {
        doc.setFontSize(10);
        doc.text("Procedimiento Oneroso", 11.53, 1.41);
      } else if (modali_da === "GRATUITO") {
        doc.setFontSize(10);
        doc.text("Gratuito", 12.38, 1.41);
      } else {
        // texto por defecto o no hacer nada xd
      }




      // Convertir a blob y mostrar en iframe
      const pdfBlob = doc.output("blob");
      const pdfUrl = URL.createObjectURL(pdfBlob);
      document.getElementById("pdfPreview").src = pdfUrl;
    }


    function generarPDF() {
      if (!datosCargados) {
        alert("Primero debes buscar una partida :3.");
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

      //Codigo pruevas, solo usar si se cambia de plantilla
      /*
      // Dibujar cuadrícula guía cada 0.25 pulgadas con líneas delgadas
      doc.setFontSize(4); // texto más pequeño para no estorbar
      doc.setTextColor(150);
      doc.setDrawColor(200);
      doc.setLineWidth(0.01); // línea delgada

      // Líneas verticales cada 0.25 pulgadas
      for (let x = 0; x <= 16.5; x += 0.10) {
        doc.line(x, 0, x, 11.7);
        if (x % 1 === 0) { // solo etiqueta en números enteros
          doc.text(x.toFixed(2), x + 0.02, 0.15);
        }
      }

      // Líneas horizontales cada 0.25 pulgadas
      for (let y = 0; y <= 11.7; y += 0.10) {
        doc.line(0, y, 16.5, y);
        if (y % 1 === 0) { // solo etiqueta en números enteros
          doc.text(y.toFixed(2), 0.05, y - 0.05);
        }
      }*/


      if (datosCargados.fecha_u && datosCargados.fecha_u.trim() !== "") {
        const fecha = (datosCargados.fecha_u || ""); // Ejemplo: "24/06/2025"
        let dia = "",
          mes = "",
          año = "";

        if (fecha.includes("/")) {
          const partes = fecha.split("/");
          dia = partes[0];
          mes = partes[1];
          año = partes[2];
        }
        let mesTexto = "";
        if (mes) {
          mesTexto = meses[parseInt(mes, 10) - 1]; // restamos 1 porque los arrays empiezan en 0
        }

        doc.setFontSize(10);
        doc.text(dia + " de " + mesTexto + " del " + año, 11.55, 1.1);

        const titulo = datosCargados.tipo_titulo?.trim().toUpperCase();
        if (titulo === "PRIVADAS") {
          if (datosCargados.fecha_p && datosCargados.fecha_p.trim() !== "") {
            const fecha = (datosCargados.fecha_p || ""); // Ejemplo: "24/06/2025"
            let dia = "",
              mes = "",
              año = "";

            if (fecha.includes("/")) {
              const partes = fecha.split("/");
              dia2 = partes[0];
              mes2 = partes[1];
              año2 = partes[2];
            }
            let mesTexto2 = "";
            if (mes2) {
              mesTexto2 = meses[parseInt(mes2, 10) - 1]; // restamos 1 porque los arrays empiezan en 0
            }
            doc.setFontSize(10);
            doc.text(dia2, 9.545, 8.64);
            doc.setFontSize(10);
            doc.text(mesTexto2, 10.8, 8.64);
            doc.setFontSize(10);
            doc.text(año2, 12.15, 8.64);
          }
        } else {
          doc.setFontSize(10);
          doc.text(dia, 9.545, 8.64);
          doc.setFontSize(10);
          doc.text(mesTexto, 10.8, 8.64);
          doc.setFontSize(10);
          doc.text(año, 12.15, 8.64);
        }
      }

      if (datosCargados.pvalor && datosCargados.pvalor.trim() !== "") {
        let pvalor = parseFloat(datosCargados.pvalor || "0");
        let pvalorFormateado = "S/ " + pvalor.toLocaleString("es-PE", {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });

        doc.setFontSize(10);
        doc.text(pvalorFormateado, 11.9, 1.74, {
          align: 'left'
        }); // 14.5 es el borde derecho donde se "ancla"
      }

      doc.setFontSize(10);
      doc.text((datosCargados.n_titulo || ""), 11.7, 0.5);

      doc.setFontSize(10);
      doc.text((datosCargados.sector || ""), 1.05, 6.96);
      doc.setFontSize(10);
      doc.text((datosCargados.manzana || ""), 5.05, 6.96);
      doc.setFontSize(10);
      doc.text((datosCargados.lote || ""), 6.18, 6.96);
      doc.setFontSize(10);
      doc.text((datosCargados.distrito || ""), 1.3, 7.25);

      //doc.setFontSize(10); doc.text((datosCargados.pvalor || ""), 12.3, 1.75);

      const area = datosCargados.area && !isNaN(datosCargados.area) ? parseFloat(datosCargados.area).toFixed(2) : "";
      const perimetro = datosCargados.perimetro && !isNaN(datosCargados.perimetro) ? parseFloat(datosCargados.perimetro).toFixed(2) : "";

      doc.setFontSize(10);
      doc.text(area, 1.06, 7.41);
      doc.setFontSize(10);
      doc.text(perimetro, 2.94, 7.41);

      doc.setFontSize(10);
      doc.text((datosCargados.frente || ""), 1.65, 7.73);
      doc.setFontSize(10);
      doc.text((datosCargados.derecha || ""), 1.65, 8.0);
      doc.setFontSize(10);
      doc.text((datosCargados.isquierda || ""), 1.65, 8.28);
      doc.setFontSize(10);
      doc.text((datosCargados.fondo || ""), 1.65, 8.55);

      // Mensajes personalizados
      // Validación propietario 1
      if (datosCargados.propietario_1 && datosCargados.propietario_1.trim() !== "") {
        doc.setFontSize(10);
        doc.text(datosCargados.propietario_1, 0.5, 2.35);
        doc.setFontSize(10);
        doc.text(datosCargados.propietario_1, 9.0, 4.68);
        doc.setFontSize(10);
        doc.text(datosCargados.dni_1 || "", 3.7, 2.35);
        doc.setFontSize(10);
        doc.text(datosCargados.estado_civil_1 || "", 5.6, 2.35);
        doc.setFontSize(10);
        doc.text(datosCargados.acc_der || "", 12.25, 4.68);
        doc.setFontSize(10);
        doc.text(datosCargados.c_bien || "", 13.95, 4.68);
      }

      // Validación propietario 2
      if (datosCargados.propietario_2 && datosCargados.propietario_2.trim() !== "") {
        doc.setFontSize(10);
        doc.text(datosCargados.propietario_2, 0.5, 2.66);
        doc.setFontSize(10);
        doc.text(datosCargados.propietario_2, 9.0, 5.0);
        doc.setFontSize(10);
        doc.text(datosCargados.dni_2 || "", 3.7, 2.66);
        doc.setFontSize(10);
        doc.text(datosCargados.estado_civil_2 || "", 5.6, 2.66);
        doc.setFontSize(10);
        doc.text(datosCargados.acc_der || "", 12.25, 5.0);
        doc.setFontSize(10);
        doc.text(datosCargados.c_bien || "", 13.95, 5.0);
      }

      // Validación propietario 3
      if (datosCargados.propietario_3 && datosCargados.propietario_3.trim() !== "") {
        doc.setFontSize(10);
        doc.text(datosCargados.propietario_3, 0.5, 2.95);
        doc.setFontSize(10);
        doc.text(datosCargados.propietario_3, 9.0, 5.3);
        doc.setFontSize(10);
        doc.text(datosCargados.dni_3 || "", 3.7, 2.95);
        doc.setFontSize(10);
        doc.text(datosCargados.estado_civil_3 || "", 5.6, 2.95);
        doc.setFontSize(10);
        doc.text(datosCargados.acc_der || "", 12.25, 5.3);
        doc.setFontSize(10);
        doc.text(datosCargados.c_bien || "", 13.95, 5.3);
      }

      if (datosCargados.propietario_4 && datosCargados.propietario_4.trim() !== "") {
        doc.setFontSize(10);
        doc.text(datosCargados.propietario_4, 0.5, 3.22);
        doc.setFontSize(10);
        doc.text(datosCargados.propietario_4, 9.0, 5.47);
        doc.setFontSize(10);
        doc.text(datosCargados.dni_4 || "", 3.7, 3.22);
        doc.setFontSize(10);
        doc.text(datosCargados.estado_civil_4 || "", 5.6, 3.22);
        doc.setFontSize(10);
        doc.text(datosCargados.acc_der || "", 12.25, 5.47);
        doc.setFontSize(10);
        doc.text(datosCargados.c_bien || "", 13.95, 5.47);
      }

      if (datosCargados.cargas?.toUpperCase() === "TRUE") {
        doc.setFontSize(10);
        doc.text("Prohibición de transferencia de dominio por un período de 05 años (Ley N° 31056, Art 5.2)", 9.1, 2.91); // o el texto que tú quieras mostrar
      }

      if (datosCargados.otros?.toUpperCase() === "TRUE") {
        doc.setFontSize(10);
        doc.text("De incumplirse la prohibición, se revertirá el predio al dominio del Estado (DS Nº 002-2021-VIVIENDA, Art. 7.5)", 9.1, 3.54); // o tu texto personalizado
      }
      //doc.setFontSize(10); doc.text((datosCargados.cargas || ""), 9.1, 2.91);
      //doc.setFontSize(10); doc.text((datosCargados.otros || ""), 9.1, 3.54);

      const tipo = datosCargados.tipo_titulo?.trim().toUpperCase();

      if (tipo === "PRIVADAS") {
        doc.setFontSize(10);
        doc.text(" Declaración de Propiedad por Prescripción Adquisitiva de Dominio", 1, 5.44);
        doc.setFontSize(10);
        doc.text("el Reglamento de los Procesos de Formalización de la Propiedad Informal en la Provincia de Trujillo", 1, 5.68);
        doc.setFontSize(10);
        doc.text("aprobado mediante Ordenanza Municipal N° 026-2023-MPT", 1, 5.91);

        doc.setFontSize(10);
        doc.text("Declaración de Propiedad por", 4.2, 9.79);
        doc.setFontSize(10);
        doc.text("Prescripción Adquisitiva de Dominio", 1.7, 9.98);
        doc.setFontSize(10);
        doc.text("la Ley N° 28687, Ley N° 31056 y sus respectivos reglamentos, y la", 3.25, 10.165);
        doc.setFontSize(10);
        doc.text("Ordenanza Municipal N° 026-2023-MPT", 1.5, 10.36);
      } else if (tipo === "CONTINGENCIA") {
        doc.setFontSize(10);
        doc.text("Continuación del proceso de formalización de los lotes de contingencia", 1, 5.44);
        doc.setFontSize(10);
        doc.text("el Reglamento de los Procesos de Formalización de la Propiedad Informal en la provincia de Trujillo,", 1, 5.68);
        doc.setFontSize(10);
        doc.text("aprobado mediante Ordenanza Municipal N° 026-2023-MPT", 1, 5.91);

        doc.setFontSize(10);
        doc.text("Continuación del proceso de", 4.2, 9.79);
        doc.setFontSize(10);
        doc.text("formalización de los lotes de contingencia", 1.7, 9.98);
        doc.setFontSize(10);
        doc.text("la Ley N° 28687, Ley N° 31056 y sus respectivos reglamentos, y la", 3.25, 10.165);
        doc.setFontSize(10);
        doc.text("Ordenanza Municipal N° 026-2023-MPT", 1.5, 10.36);
      } else if (tipo === "ESTATAL") {
        doc.setFontSize(10);
        doc.text("Formalización de Posesiones Informales en Propiedad Estatal", 1, 5.44);
        doc.setFontSize(10);
        doc.text("el Reglamento de los Procesos de Formalización de la Propiedad Informal en la provincia de Trujillo,", 1, 5.68);
        doc.setFontSize(10);
        doc.text("aprobado mediante Ordenanza Municipal N° 026-2023-MPT", 1, 5.91);

        doc.setFontSize(10);
        doc.text("Formalización de Posesiones", 4.2, 9.79);
        doc.setFontSize(10);
        doc.text("Informales en Propiedad Estatal", 1.7, 9.98);
        doc.setFontSize(10);
        doc.text("la Ley N° 28687, Ley N° 31056 y sus respectivos reglamentos, y la", 3.25, 10.165);
        doc.setFontSize(10);
        doc.text("Ordenanza Municipal N° 026-2023-MPT", 1.5, 10.36);
      } else {
        // texto por defecto nada xd
      }

      const modali_da = datosCargados.modalidad?.trim().toUpperCase();

      if (modali_da === "PROCEDIMIENTO ONEROSO") {
        doc.setFontSize(10);
        doc.text("Procedimiento Oneroso", 11.53, 1.41);
      } else if (modali_da === "GRATUITO") {
        doc.setFontSize(10);
        doc.text("Gratuito", 12.38, 1.41);
      } else {
        // texto por defecto o no hacer nada xd
      }

      doc.save("reporte_partida.pdf");
    }
  </script>
</body>