<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$autoload = __DIR__ . '/vendor/autoload.php';

if (! file_exists($autoload)) {
    return;
}

require_once $autoload;

\Goug\Framework\Core\Bootstrap\Framework::boot();