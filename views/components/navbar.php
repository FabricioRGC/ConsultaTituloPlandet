<?php
function render_navbar($rol = '', $nombre = '')
{
?>
    <link rel="stylesheet" href="/ConsultaTituloPlandet-1/styles/navbar.css">
    <nav class="navbar">
        <h1 class="navbar-logo">
            Plandet
        </h1>

        <div class="navbar-user">
            <p class="user-info">
                <?php echo htmlspecialchars($nombre); ?>
                <span class="user-role">(<?php echo htmlspecialchars($rol); ?>)</span>
            </p>

            <a href="/ConsultaTituloPlandet-1/index.php?action=logout" class="btn-logout">
                Logout
            </a>
        </div>
    </nav>
<?php
}
?>
