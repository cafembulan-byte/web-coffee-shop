<?php

declare(strict_types=1);

session_start();
require __DIR__ . '/app-config.php';
require SUMMIT_PRIVATE_ROOT . '/src/functions.php';
requireRole('admin');
$user = currentUser();
?><!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin | Summit Split</title><link rel="stylesheet" href="style.css"></head>
<body>
    <header class="site-header"><a class="brand" href="index.php"><span class="brand-mark">SS</span><span>Summit Split</span></a><nav class="nav-links"><span class="user-chip"><?= e($user['name']) ?> · admin</span><a href="logout.php">Log out</a></nav></header>
    <main class="page-width admin-page">
        <p class="eyebrow">Trail desk</p><h1>Route data</h1><p class="intro-copy">The current comparison catalog and its source notes.</p>
        <div class="admin-table"><div class="admin-row admin-row--head"><span>Route</span><span>Base time</span><span>Source</span></div><?php foreach (routeCatalog() as $route): ?><div class="admin-row"><strong><?= e($route['routeName']) ?></strong><span><?= e(formatDuration($route['baseMinutes'])) ?></span><span><?= e($route['sourceNote']) ?></span></div><?php endforeach; ?></div>
        <p class="eyebrow admin-section-label">Member list</p>
        <div class="admin-table"><div class="admin-row admin-row--head"><span>Name</span><span>Role</span><span>Registered</span></div><?php foreach (allMembers() as $member): ?><div class="admin-row"><strong><?= e($member['name']) ?></strong><span><?= e($member['role']) ?></span><span><?= e($member['created_at']) ?></span></div><?php endforeach; ?></div>
    </main>
</body>
</html>