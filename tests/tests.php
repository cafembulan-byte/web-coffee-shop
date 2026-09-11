<?php

declare(strict_types=1);

require __DIR__ . '/../public/app-config.php';
require SUMMIT_PRIVATE_ROOT . '/src/functions.php';

$normal = rankedRoutes('normal');
assert($normal[0]['routeName'] === 'Granite Steps');
assert($normal[0]['estimatedTimeMinutes'] === 92);
assert($normal[1]['estimatedTimeMinutes'] < $normal[2]['estimatedTimeMinutes']);
assert(rankedRoutes('slow')[0]['estimatedTimeMinutes'] > $normal[0]['estimatedTimeMinutes']);
assert(formatDuration(45) === '45 min');
assert(formatDuration(90) === '1 hr 30 min');

echo "Route ranking checks passed.\n";