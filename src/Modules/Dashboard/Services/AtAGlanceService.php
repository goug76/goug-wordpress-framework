<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Dashboard\Services;

defined('ABSPATH') || exit;

/**
 * Provides content statistics for the At a Glance panel.
 */
final class AtAGlanceService
{
    /**
     * Return the content counts visible to the current user.
     *
     * @return list<array{
     *     label: string,
     *     count: int,
     *     icon: string
     * }>
     */
    public function contentCounts(): array
    {
        $counts = [];

        foreach ($this->supportedPostTypes() as $postType) {
            $postTypeObject = get_post_type_object(
                $postType
            );

            if ($postTypeObject === null) {
                continue;
            }

            $postCounts = wp_count_posts(
                $postType
            );

            $publishedCount = isset(
                $postCounts->publish
            )
                ? (int) $postCounts->publish
                : 0;

            $counts[] = [
                'label' => $postTypeObject
                    ->labels
                    ->name,
                'count' => $publishedCount,
                'icon' => $this->iconFor(
                    $postType
                ),
            ];
        }

        return $counts;
    }

    /**
     * Return post types appropriate for the summary.
     *
     * @return list<string>
     */
    private function supportedPostTypes(): array
    {
        $postTypes = get_post_types(
            [
                'show_ui' => true,
            ],
            'names'
        );

        $excluded = [
            'attachment',
            'wp_block',
            'wp_template',
            'wp_template_part',
            'wp_navigation',
        ];

        return array_values(
            array_diff(
                $postTypes,
                $excluded
            )
        );
    }

    private function iconFor(
        string $postType
    ): string {
        return match ($postType) {
            'post' => 'dashicons-admin-post',
            'page' => 'dashicons-admin-page',
            default => 'dashicons-admin-generic',
        };
    }
}