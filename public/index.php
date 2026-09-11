<?php

declare(strict_types=1);

session_start();
require __DIR__ . '/app-config.php';
require SUMMIT_PRIVATE_ROOT . '/src/functions.php';

$paceOptions = paceOptions();
$selectedPace = isset($_GET['pace'], $paceOptions[$_GET['pace']]) ? $_GET['pace'] : 'normal';
$routes = rankedRoutes($selectedPace);
$user = currentUser();
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Summit Split | Mount Alder routes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="site-header">
        <a class="brand" href="index.php"><span class="brand-mark">SS</span><span>Summit Split</span></a>
        <nav class="nav-links" aria-label="Main navigation">
            <?php if ($user): ?>
                <span class="user-chip"><?= e($user['name']) ?> · <?= e($user['role']) ?></span>
                <?php if ($user['role'] === 'admin'): ?><a href="admin.php">Admin</a><?php endif; ?>
                <a href="logout.php">Log out</a>
            <?php else: ?>
                <a href="login.php">Sign in</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <section class="intro page-width">
            <p class="eyebrow">Mount Alder · 3 ways up</p>
            <h1>Choose your quickest line to the summit.</h1>
            <p class="intro-copy">Compare realistic time-to-summit estimates across every route. Pace changes the clock; the mountain stays the same.</p>
        </section>

        <section class="controls page-width" aria-labelledby="pace-heading">
            <div>
                <p class="section-label" id="pace-heading">Your pace</p>
                <p class="muted">Estimates include the ascent to the summit.</p>
            </div>
            <div class="pace-switcher" role="group" aria-label="Choose hiking pace">
                <?php foreach ($paceOptions as $paceKey => $pace): ?>
                    <a class="pace-option<?= $paceKey === $selectedPace ? ' is-selected' : '' ?>" href="?pace=<?= e($paceKey) ?>" aria-current="<?= $paceKey === $selectedPace ? 'true' : 'false' ?>">
                        <span><?= e($pace['label']) ?></span>
                        <small><?= $paceKey === 'normal' ? 'baseline' : ($paceKey === 'slow' ? '+25%' : ($paceKey === 'fast' ? '-18%' : '-32%')) ?></small>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="leaderboard page-width" aria-labelledby="routes-heading">
            <div class="leaderboard-heading">
                <div><p class="eyebrow">Live comparison</p><h2 id="routes-heading">Fastest first</h2></div>
                <span class="estimate-badge">Estimated · <?= e($paceOptions[$selectedPace]['label']) ?> pace</span>
            </div>
            <div class="route-list">
                <?php foreach ($routes as $index => $route): ?>
                    <article class="route-card<?= $index === 0 ? ' route-card--fastest' : '' ?>">
                        <div class="rank" aria-label="Rank <?= $index + 1 ?>"><span><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span></div>
                        <div class="route-main">
                            <div class="route-title"><div><p class="route-peak"><?= e($route['peakName']) ?></p><h3><?= e($route['routeName']) ?></h3></div><?php if ($index === 0): ?><span class="fastest-label">Fastest</span><?php endif; ?></div>
                            <div class="route-facts"><span><?= e($route['distanceKm']) ?> km</span><span><?= e($route['elevationGainM']) ?> m gain</span><span><?= e($route['difficulty']) ?></span><span><?= e($route['terrainType']) ?></span></div>
                            <p class="safety-note"><?= e($route['safetyNote']) ?></p>
                        </div>
                        <div class="time-block"><strong><?= e(formatDuration($route['estimatedTimeMinutes'])) ?></strong><span><?= $route['timeGapMinutes'] === 0 ? 'baseline' : '+' . e($route['timeGapMinutes']) . ' min gap' ?></span></div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="notes page-width">
            <div><span class="note-icon">i</span><strong>How to read this</strong></div>
            <p>Times are estimates, not guarantees. They use each route's baseline estimate adjusted for your selected pace and are intended for planning, not as a safety promise.</p>
            <small>Sources: trail association and park service estimates · Last checked September 2026</small>
        </section>
    </main>
</body>
</html>