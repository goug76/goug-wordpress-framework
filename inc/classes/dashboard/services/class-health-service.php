<?php
/**
 * Dashboard Site Health service.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Provides normalized WordPress Site Health information.
 */
class Health_Service {

	/**
	 * Cached health data for the current request.
	 *
	 * @var array|null
	 */
	private $health_data = null;

	/**
     * Return Site Health summary data.
     *
     * WordPress stores the combined direct and asynchronous Site Health
     * result counts in a transient after the native Site Health checks run.
     *
     * @return array
     */
    public function get_data() {

        if ( null !== $this->health_data ) {
            return $this->health_data;
        }

        $counts = $this->get_cached_counts();

        /*
        * If WordPress has not generated the combined results yet,
        * run only the lightweight direct checks as a fallback.
        *
        * Asynchronous checks should not run during the dashboard request.
        */
        if ( null === $counts ) {
            $counts = $this->get_direct_test_counts();
        }

        $critical    = isset( $counts['critical'] )
            ? (int) $counts['critical']
            : 0;

        $recommended = isset( $counts['recommended'] )
            ? (int) $counts['recommended']
            : 0;

        $passed = isset( $counts['good'] )
            ? (int) $counts['good']
            : 0;

        if ( $critical > 0 ) {
            $status      = 'critical';
            $label       = __( 'Needs attention', 'goug-framework' );
            $description = sprintf(
                /* translators: %d: Number of critical Site Health issues. */
                _n(
                    '%d critical issue requires attention.',
                    '%d critical issues require attention.',
                    $critical,
                    'goug-framework'
                ),
                $critical
            );
        } elseif ( $recommended > 0 ) {
            $status      = 'recommended';
            $label       = __( 'Good', 'goug-framework' );
            $description = sprintf(
                /* translators: %d: Number of recommended Site Health improvements. */
                _n(
                    '%d recommended improvement is available.',
                    '%d recommended improvements are available.',
                    $recommended,
                    'goug-framework'
                ),
                $recommended
            );
        } else {
            $status      = 'good';
            $label       = __( 'Excellent', 'goug-framework' );
            $description = __(
                'No Site Health issues were detected.',
                'goug-framework'
            );
        }

        $this->health_data = array(
            'status'      => $status,
            'label'       => $label,
            'description' => $description,
            'critical'    => $critical,
            'recommended' => $recommended,
            'passed'      => $passed,
            'url'         => admin_url( 'site-health.php' ),
        );

        /**
         * Filter dashboard Site Health data.
         *
         * @param array $health_data Site Health summary.
         */
        $this->health_data = apply_filters(
            'goug_dashboard_site_health_data',
            $this->health_data
        );

        return is_array( $this->health_data )
            ? $this->health_data
            : array();
    }

    /**
     * Return WordPress's cached combined Site Health counts.
     *
     * @return array|null
     */
    private function get_cached_counts() {

        $cached_result = get_transient(
            'health-check-site-status-result'
        );

        if ( false === $cached_result ) {
            return null;
        }

        if ( is_string( $cached_result ) ) {
            $cached_result = json_decode(
                $cached_result,
                true
            );
        }

        if ( ! is_array( $cached_result ) ) {
            return null;
        }

        return array(
            'critical'    => isset( $cached_result['critical'] )
                ? (int) $cached_result['critical']
                : 0,
            'recommended' => isset( $cached_result['recommended'] )
                ? (int) $cached_result['recommended']
                : 0,
            'good'        => isset( $cached_result['good'] )
                ? (int) $cached_result['good']
                : 0,
        );
    }

    /**
     * Run only WordPress's direct Site Health checks.
     *
     * This is a fallback used before WordPress has cached the full result.
     * Asynchronous tests are intentionally excluded.
     *
     * @return array
     */
    private function get_direct_test_counts() {

        if ( ! class_exists( '\WP_Site_Health' ) ) {
            require_once ABSPATH
                . 'wp-admin/includes/class-wp-site-health.php';
        }

        $site_health = \WP_Site_Health::get_instance();

        if ( ! $site_health ) {
            return array(
                'critical'    => 0,
                'recommended' => 0,
                'good'        => 0,
            );
        }

        $tests = \WP_Site_Health::get_tests();

        $counts = array(
            'critical'    => 0,
            'recommended' => 0,
            'good'        => 0,
        );

        if (
            empty( $tests['direct'] ) ||
            ! is_array( $tests['direct'] )
        ) {
            return $counts;
        }

        foreach ( $tests['direct'] as $test ) {

            if (
                ! is_array( $test ) ||
                empty( $test['test'] )
            ) {
                continue;
            }

            $callback = $test['test'];

            /*
            * Core commonly registers a direct test using a string such
            * as "php_version". WordPress converts that into the matching
            * WP_Site_Health method name before running it.
            */
            if ( is_string( $callback ) ) {
                $callback = array(
                    $site_health,
                    'get_test_' . $callback,
                );
            }

            if ( ! is_callable( $callback ) ) {
                continue;
            }

            $result = call_user_func( $callback );

            if (
                ! is_array( $result ) ||
                empty( $result['status'] )
            ) {
                continue;
            }

            $result_status = (string) $result['status'];

            if ( isset( $counts[ $result_status ] ) ) {
                ++$counts[ $result_status ];
            }
        }

        return $counts;
    }
}