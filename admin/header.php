<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fallbacks
$pageTitle = $pageTitle ?? 'Dashboard';
$userName  = $_SESSION['user_name'] ?? 'User';
?>

<div class="header">
    <div class="page-title">
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
    </div>

    <div class="user-info">
        <div class="user-avatar">
            <?= strtoupper(substr($userName, 0, 2)) ?>
        </div>
        <span><?= htmlspecialchars($userName) ?></span>
    </div>
</div>
