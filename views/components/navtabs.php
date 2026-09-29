<?php
$rol = $_SESSION['usuario_rol'] ?? null;

// 🔥 NUEVO: Obtener tab activo de la URL
$activeTab = $_GET['tab'] ?? null;

$tabs = [
    [
        "key" => "generarQR",
        "label" => "Generacion de QRs",
        "color" => "blue",
        "roles" => ["admin", "user"]
    ],
    [
        "key" => "ingresarVarios",
        "label" => "Ingresar varios",
        "color" => "green",
        "roles" => ["test"]
    ],
    [
        "key" => "updateQR-PDF",
        "label" => "Actualizar Qr / Pdf",
        "color" => "purple",
        "roles" => ["admin", "user"]
    ],
    [
        "key" => "searchQR-PDF",
        "label" => "Buscar Qr / Pdf",
        "color" => "purple",
        "roles" => ["admin", "user"]
    ],
    [
        "key" => "generarTitulo",
        "label" => "Generar Titulo",
        "color" => "purple",
        "roles" => ["admin", "user"]
    ],
    [
        "key" => "adminAudit",
        "label" => "Panel Admin",
        "color" => "green",
        "roles" => ["admin"]
    ],
];

// Filtrar tabs según el rol
$tabs_permitidos = array_filter($tabs, function($tab) use ($rol) {
    return in_array($rol, $tab["roles"]);
});

// 🔥 NUEVO: Si no hay tab activo en URL, usar el primero permitido
if (!$activeTab && !empty($tabs_permitidos)) {
    $activeTab = reset($tabs_permitidos)['key'];
}
?>

<link rel="stylesheet" href="/ConsultaTituloPlandet/styles/navtabs.css">

<div class="tabs-container">
    <?php foreach ($tabs_permitidos as $tab): ?>
        <button 
            class="tab-btn <?= $activeTab === $tab['key'] ? 'active' : '' ?>" 
            data-tab="<?= $tab["key"] ?>" 
            data-color="<?= $tab["color"] ?>"
            <?php if ($activeTab === $tab['key']): ?>
                style="background: var(--<?= $tab['color'] ?>); color: white;"
            <?php endif; ?>
        >
            <?= $tab["label"] ?>
        </button>
    <?php endforeach; ?>
</div>

<div id="tab-contenido">
    <?php foreach ($tabs_permitidos as $tab): ?>
        <!-- 🔥 MODIFICADO: display desde PHP según el tab activo -->
        <div class="tab-content" id="tab-<?= $tab["key"] ?>" 
             style="display: <?= $activeTab === $tab['key'] ? 'block' : 'none' ?>">
            <?php include __DIR__ . "/tab-{$tab['key']}.php"; ?>
        </div>
    <?php endforeach; ?>
</div>

<script>
    //AGREGANDO
    /*window.showToast = function(message, type = 'success') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;

    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('show');
    }, 10);

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3500);
}*/
document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll(".tab-btn");
    const contents = document.querySelectorAll(".tab-content");

    if (buttons.length === 0) return;

    buttons.forEach(btn => {
        btn.addEventListener("click", () => {
            const key = btn.dataset.tab;
            
            // Actualizar URL con el tab seleccionado
            const url = new URL(window.location);
            url.searchParams.set('tab', key);
            window.history.pushState({}, '', url);
            
            changeTab(key);
        });
    });

    function changeTab(key) {
        contents.forEach(c => {
            c.style.display = "none";
            c.style.opacity = "0"; 
        });
        
        const visible = document.getElementById("tab-" + key);
        if (visible) {
            visible.style.display = "block";
            // Pequeño retardo para activar la transición suave de opacidad
            setTimeout(() => { 
                visible.style.opacity = "1"; 
            }, 50);
        }

        buttons.forEach(b => {
            b.classList.remove("active");
            b.style.background = "";
            b.style.color = "";
        });

        const activeBtn = document.querySelector(`[data-tab="${key}"]`);
        if (activeBtn) {
            const color = activeBtn.dataset.color;
            activeBtn.classList.add("active");
            activeBtn.style.background = `var(--${color})`;
            activeBtn.style.color = "white";
        }
    }
});
</script>
