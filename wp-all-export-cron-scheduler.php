<?php
/**
 * Plugin Name:       WP All Export Cron Scheduler
 * Plugin URI:        https://webikon.sk/
 * Description:       Cron scheduler for WP All Export plugin.
 * Version:           1.0.0
 * Requires at least: 5.3
 * Requires PHP:      7.2
 * Author:            Webikon
 * Author URI:        https://webikon.sk/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-all-export-cron-scheduler
 * Domain Path:       /languages
 *
 * WP All Export Cron Scheduler is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP All Export Cron Scheduler is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WP All Export Cron Scheduler. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 */

// Security
defined('ABSPATH') or exit;

// Global constants
define('WPAE_CRSCH_VERSION', '1.0.0');
define('WPAE_CRSCH_DIR', plugin_dir_path(__FILE__));
define('WPAE_CRSCH_URL', plugin_dir_url(__FILE__));
define('WPAE_CRSCH_TD', 'wp-all-export-cron-scheduler');

/**
 * Activate the plugin.
 */
register_activation_hook(__FILE__, function () {
    // Die WordPress if is not activated WP All Export plugin
    if (!is_plugin_active('wp-all-export-pro/wp-all-export-pro.php') || !class_exists('PMXE_Plugin')) {
        wp_die('Sorry, but this plugin requires the WP All Export plugin to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to plugins</a>');
    }

    // Add cron jobs
    wpae_crsch_add_cron_jobs();

    // Register uninstall hook
    register_uninstall_hook(__FILE__, 'wpae_crsch_uninstall_hook');
});

/**
 * Deactivation hook.
 */
register_deactivation_hook(__FILE__, function () {
    if (Webikon\WpAllExport\Scheduler\CronJobs::getEvents()) {
        Webikon\WpAllExport\Scheduler\CronJobs::remove();
    }
});

/**
 * Uninstall plugin.
 */
function wpae_crsch_uninstall_hook() {
    if (Webikon\WpAllExport\Scheduler\CronJobs::getEvents()) {
        Webikon\WpAllExport\Scheduler\CronJobs::remove();
    }

    delete_option('wpae_cron_scheduler_exports');
}

// Includes
require_once 'inc' . DIRECTORY_SEPARATOR . 'AdminSettings.php';
require_once 'inc' . DIRECTORY_SEPARATOR . 'WPAE.php';
require_once 'inc' . DIRECTORY_SEPARATOR . 'CronJobs.php';

/**
 * Plugin setup
 */
add_action('init', function () {
    // Cron jobs define/add
    if (isset($_GET['add-wpae-cron-events'])) {
        wpae_crsch_add_cron_jobs();
    }

    // Remove cron jobs
    if (isset($_GET['remove-wpae-cron-events'])) {
        Webikon\WpAllExport\Scheduler\CronJobs::remove();
    }

    /**
     * Admin assets
     */
    add_action('admin_enqueue_scripts', function ($hook) {
        if ($hook != 'settings_page_wpae-cron-scheduler') {
            return;
        }

        wp_enqueue_script('wpae_crsch_admin', WPAE_CRSCH_URL . 'assets/js/wpae-crsch-admin.js', [], WPAE_CRSCH_VERSION);
    });
});

/**
 * Add more cron schedules.
 */
add_filter('cron_schedules', function ($schedules) {
    $schedules['wpae_crsch_every_5_minutes'] = array(
        'interval' => MINUTE_IN_SECONDS * 5,
        'display'  => __('Every 5 minutes', WPAE_CRSCH_TD),
    );

    return $schedules;
});

/**
 * Define and add cron jobs after add setting "wpae_cron_scheduler_exports"
 */
add_action('add_option_wpae_cron_scheduler_exports', function ($option_name, $option_value) {
    wpae_crsch_add_cron_jobs();
}, 10, 2);

/**
 * Define and add cron jobs after update setting "wpae_cron_scheduler_exports"
 */
add_action('update_option_wpae_cron_scheduler_exports', function ($option_name, $old_value, $new_value) {
    wpae_crsch_add_cron_jobs();
}, 10, 3);

/**
 * Add cron jobs helper
 */
function wpae_crsch_add_cron_jobs()
{
    Webikon\WpAllExport\Scheduler\CronJobs::define();
    Webikon\WpAllExport\Scheduler\CronJobs::add();
}

/**
 * Get exports list helper
 *
 * @return array
 */
function wpae_crsch_get_exports_list()
{
    return get_option('wpae_cron_scheduler_exports');;
}
