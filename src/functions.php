<?php

declare(strict_types=1);

require_once __DIR__ . '/database.php';

function routeCatalog(): array
{
    return [
        [
            'id' => 'ridge-line',
            'peakName' => 'Mount Alder',
            'routeName' => 'Ridge Line',
            'distanceKm' => 6.8,
            'elevationGainM' => 520,
            'difficulty' => 'Moderate',
            'terrainType' => 'Rocky ridge',
            'baseMinutes' => 118,
            'safetyNote' => 'Exposed in high winds; check the forecast before setting out.',
            'sourceNote' => 'Trail association estimate',
        ],
        [
            'id' => 'pine-saddle',
            'peakName' => 'Mount Alder',
            'routeName' => 'Pine Saddle',
            'distanceKm' => 7.4,
            'elevationGainM' => 460,
            'difficulty' => 'Easy',
            'terrainType' => 'Forest path',
            'baseMinutes' => 105,
            'safetyNote' => 'The upper trail can be muddy after rain.',
            'sourceNote' => 'Trail association estimate',
        ],
        [
            'id' => 'granite-steps',
            'peakName' => 'Mount Alder',
            'routeName' => 'Granite Steps',
            'distanceKm' => 5.2,
            'elevationGainM' => 610,
            'difficulty' => 'Hard',
            'terrainType' => 'Steep slabs',
            'baseMinutes' => 92,
            'safetyNote' => 'Loose rock near the summit requires careful footing.',
            'sourceNote' => 'Park service estimate',
        ],
    ];
}

function paceOptions(): array
{
    return [
        'slow' => ['label' => 'Slow', 'factor' => 1.25],
        'normal' => ['label' => 'Normal', 'factor' => 1.0],
        'fast' => ['label' => 'Fast', 'factor' => 0.82],
        'elite' => ['label' => 'Elite', 'factor' => 0.68],
    ];
}

function calculateRouteTime(int $baseMinutes, string $pace): int
{
    $options = paceOptions();
    $factor = $options[$pace]['factor'] ?? $options['normal']['factor'];

    return (int) round($baseMinutes * $factor);
}

function rankedRoutes(string $pace = 'normal'): array
{
    $options = paceOptions();
    $pace = isset($options[$pace]) ? $pace : 'normal';
    $routes = array_map(static function (array $route) use ($pace): array {
        $route['paceLabel'] = paceOptions()[$pace]['label'];
        $route['estimatedTimeMinutes'] = calculateRouteTime($route['baseMinutes'], $pace);

        return $route;
    }, routeCatalog());

    $difficultyRank = ['Easy' => 1, 'Moderate' => 2, 'Hard' => 3];
    usort($routes, static function (array $left, array $right) use ($difficultyRank): int {
        return $left['estimatedTimeMinutes'] <=> $right['estimatedTimeMinutes']
            ?: ($difficultyRank[$left['difficulty']] <=> $difficultyRank[$right['difficulty']])
            ?: ($left['elevationGainM'] <=> $right['elevationGainM'])
            ?: ($left['distanceKm'] <=> $right['distanceKm']);
    });

    $fastestMinutes = $routes[0]['estimatedTimeMinutes'] ?? 0;
    foreach ($routes as &$route) {
        $route['timeGapMinutes'] = $route['estimatedTimeMinutes'] - $fastestMinutes;
    }
    unset($route);

    return $routes;
}

function formatDuration(int $minutes): string
{
    if ($minutes < 60) {
        return $minutes . ' min';
    }

    $hours = intdiv($minutes, 60);
    $remainingMinutes = $minutes % 60;

    return $remainingMinutes === 0
        ? $hours . ' hr'
        : $hours . ' hr ' . $remainingMinutes . ' min';
}

function e(string|int|float $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function requireRole(string $role): void
{
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }

    if ($_SESSION['user']['role'] !== $role) {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}