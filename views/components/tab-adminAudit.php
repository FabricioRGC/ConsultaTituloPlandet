<?php
require_once __DIR__ . '/../../models/AuditModel.php';

$audit = new AuditModel();
$recentLogins = $audit->getRecentLogins(30);
$selectedSession = trim($_GET['session_token'] ?? '');
$sessionDetails = $selectedSession !== '' ? $audit->getSessionActivities($selectedSession, 500) : [];
?>

<link rel="stylesheet" href="/ConsultaTituloPlandet-1/styles/tab-adminAudit.css">

<div class="audit-page">
    <section class="audit-card">
        <h3 class="audit-title">Ultimas conexiones</h3>
        <?php if (empty($recentLogins)): ?>
            <p class="audit-empty">No hay conexiones registradas.</p>
        <?php else: ?>
            <div class="audit-table-wrap">
                <table class="audit-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>IP</th>
                            <th>Estado</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentLogins as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['created_at'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['user_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['ip_address'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['status'] ?? '-') ?></td>
                                <td>
                                    <?php if (!empty($row['session_token'])): ?>
                                        <a class="audit-btn"
                                           href="?action=dasboard&tab=adminAudit&session_token=<?= urlencode($row['session_token']) ?>">
                                            Ver detalles
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="audit-card">
        <h3 class="audit-title">Detalle de sesion</h3>
        <?php if ($selectedSession === ''): ?>
            <p class="audit-empty">Selecciona una conexion y pulsa "Ver detalles".</p>
        <?php elseif (empty($sessionDetails)): ?>
            <p class="audit-empty">No hay eventos para esta sesion.</p>
        <?php else: ?>
            <p class="audit-session-label">Session: <?= htmlspecialchars($selectedSession) ?></p>
            <div class="audit-cards">
                <?php foreach ($sessionDetails as $row): ?>
                    <article class="event-card">
                        <div class="event-head">
                            <strong><?= htmlspecialchars($row['action'] ?? '-') ?></strong>
                            <span><?= htmlspecialchars($row['created_at'] ?? '') ?></span>
                        </div>
                        <div class="event-row">Usuario: <?= htmlspecialchars($row['user_name'] ?? '-') ?></div>
                        <div class="event-row">Modulo: <?= htmlspecialchars($row['module'] ?? '-') ?></div>
                        <div class="event-row">Entidad: <?= htmlspecialchars(($row['entity_type'] ?? '-') . ':' . ($row['entity_id'] ?? '-')) ?></div>
                        <div class="event-row">Detalle: <?= htmlspecialchars($row['description'] ?? '-') ?></div>
                        <?php if (!empty($row['metadata'])): ?>
                            <pre class="audit-metadata"><?= htmlspecialchars($row['metadata']) ?></pre>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>
