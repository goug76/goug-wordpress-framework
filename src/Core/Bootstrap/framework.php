<?php

declare(strict_types=1);

namespace Goug\Framework\Core\Bootstrap;

defined('ABSPATH') || exit;

final class Framework
{
    private static ?Application $application = null;

    private function __construct()
    {
    }

    public static function boot(): void
    {
        if (self::$application !== null) {
            return;
        }

        self::$application = new Application();
        self::$application->boot();
    }

    public static function application(): Application
    {
        if (self::$application === null) {
            self::boot();
        }

        return self::$application;
    }
}