<?php

declare(strict_types=1);

namespace Goug\Framework\Core\Configuration;

defined('ABSPATH') || exit;

use InvalidArgumentException;

/**
 * Represents the installation configuration of GOUG Framework.
 *
 * Configuration describes where the framework is installed and how
 * its public resources may be addressed.
 */
final class Configuration
{
    /**
     * Absolute filesystem path to the framework root.
     */
    private string $rootPath;

    /**
     * Public URL to the framework root.
     */
    private string $rootUrl;

    /**
     * Create the framework configuration.
     *
     * @param string $rootPath Absolute filesystem path.
     * @param string $rootUrl  Public framework URL.
     */
    public function __construct(
        string $rootPath,
        string $rootUrl
    ) {
        $rootPath = rtrim(
            trim($rootPath),
            '/\\'
        );

        $rootUrl = rtrim(
            trim($rootUrl),
            '/'
        );

        if ($rootPath === '') {
            throw new InvalidArgumentException(
                'The framework root path cannot be empty.'
            );
        }

        if ($rootUrl === '') {
            throw new InvalidArgumentException(
                'The framework root URL cannot be empty.'
            );
        }

        $this->rootPath = $rootPath;
        $this->rootUrl  = $rootUrl;
    }

    /**
     * Return the absolute framework root path.
     */
    public function rootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * Return the public framework root URL.
     */
    public function rootUrl(): string
    {
        return $this->rootUrl;
    }

    /**
     * Build an absolute path relative to the framework root.
     */
    public function path(string $relativePath = ''): string
    {
        $relativePath = ltrim(
            trim($relativePath),
            '/\\'
        );

        return $relativePath === ''
            ? $this->rootPath
            : $this->rootPath . DIRECTORY_SEPARATOR . $relativePath;
    }

    /**
     * Build a public URL relative to the framework root.
     */
    public function url(string $relativePath = ''): string
    {
        $relativePath = ltrim(
            trim($relativePath),
            '/'
        );

        return $relativePath === ''
            ? $this->rootUrl
            : $this->rootUrl . '/' . $relativePath;
    }
}