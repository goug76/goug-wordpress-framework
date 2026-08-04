<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Dashboard\Services;

defined('ABSPATH') || exit;

use Goug\Framework\Modules\Dashboard\Models\Statistic;

/**
 * Provides content statistics for the Dashboard.
 */
final class ContentStatisticsService
{
    /**
     * Return meaningful published content statistics.
     *
     * @return list<Statistic>
     */
    public function statistics(): array
    {
        $statistics = [];

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

            if ($publishedCount === 0) {
                continue;
            }

            $statistics[] = new Statistic(
                $postTypeObject->labels->name,
                $publishedCount,
                $this->iconFor($postType)
            );
        }

        return $statistics;
    }

    /**
     * Return post types appropriate for the Dashboard.
     *
     * @return list<string>
     */
    private function supportedPostTypes(): array
    {
        $postTypes = get_post_types(
            [
                'show_ui' => true,
                'public' => true,
            ],
            'names'
        );

        $excluded = [
            'attachment',
            'wp_block',
            'wp_template',
            'wp_template_part',
            'wp_navigation',

            'gp_elements',
            'gblocks_templates',
            'gblocks_patterns',
            'gblocks_global_styles',

            'acf-field-group',
            'acf-field',
            'acf-post-type',
            'acf-taxonomy',
            'acf-ui-options-page',
        ];

        $postTypes = array_values(
            array_diff(
                $postTypes,
                $excluded
            )
        );

        /**
         * Filter post types shown in Dashboard content statistics.
         *
         * @param list<string> $postTypes
         */
        $postTypes = apply_filters(
            'goug_dashboard_content_statistics_post_types',
            $postTypes
        );

        return array_values(
            array_unique($postTypes)
        );
    }

    private function iconFor(string $postType): string
    {
        return match ($postType) {
            'post' => 'dashicons-admin-post',
            'page' => 'dashicons-admin-page',
            default => 'dashicons-admin-generic',
        };
    }
}