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

/**
 * Activate the plugin.
 */
register_activation_hook(__FILE__, function () {});

/**
 * Deactivation hook.
 */
register_deactivation_hook(__FILE__, function () {});

/**
 * Uninstall hook.
 */
register_uninstall_hook(__FILE__, function () {});

add_action('init', function () {
    if (class_exists('PMXE_Plugin')) {

        // Global constants
        define('WPAE_CRSCH_VERSION', '1.0.0');
        define('WPAE_CRSCH_DIR', plugin_dir_path(__FILE__));
        define('WPAE_CRSCH_URL', plugin_dir_url(__FILE__));
        define('WPAE_CRSCH_TD', 'wp-all-export-cron-scheduler');

        // Includes
        require_once 'inc/AdminSettings.php';
    }
});
