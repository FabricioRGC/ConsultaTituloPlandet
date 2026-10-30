<?php
function render_navbar($rol = '', $nombre = '')
{
?>
    <link rel="stylesheet" href="/ConsultaTituloPlandet/styles/navbar.css">
    <nav class="navbar" style="background-color: #091224; border-bottom: 3px solid #2563eb; box-shadow: 0 4px 20px rgba(37, 99, 235, 0.35); position: relative; z-index: 50; padding: 12px 20px;">
        <a href="/ConsultaTituloPlandet/index.php" class="navbar-logo">
            <!-- Forzamos las dimensiones directamente en el HTML para anular cualquier tamaño gigante -->
            <img src="/ConsultaTituloPlandet/logo/logoplandet-white.png" alt="Plandet Logo" width="32" height="32" style="height: 32px !important; width: auto !important; max-width: 160px !important; object-fit: contain !important; display: block !important;">
        </a>

        <div class="navbar-user">
            <p class="user-info">
                <?php echo htmlspecialchars($nombre); ?>
                <span class="user-role">(<?php echo htmlspecialchars($rol); ?>)</span>
            </p>

            <a href="/ConsultaTituloPlandet/index.php?action=logout" class="btn-logout">
                Cerrar Sesión
            </a>
        </div>
    </nav>
<?php
}
?>