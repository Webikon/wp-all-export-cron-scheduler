<?php

namespace Webikon\WpAllExport\Scheduler;

use Inpsyde\Wonolog\Channels;
use Monolog\Logger;
use PMXE_Plugin;

class CronJobs
{
    /**
     * @var array Local storage for list of events.
     */
    private static $events;

    /**
     * Define cron jobs for automated exports.
     */
    public static function define()
    {
        // Get cron jobs key from WP All Export
        $cron_job_key = PMXE_Plugin::getInstance()->getOption('cron_job_key');

        if (!self::getEvents()) {
            return;
        }

        foreach (self::getEvents() as $id => $event) {
            $name = $event['name'];

            // Processing
            add_action($name . '_exec', function () use ($id, $name, $cron_job_key) {
                $response = wp_remote_get(home_url("/wp-load.php?export_key=$cron_job_key&export_id=$id&action=processing"), array('timeout' => 600));

                if (is_wp_error($response)) {
                    do_action('wonolog.log', $response, 300, 'HTTP');
                }
            });

            // Trigger
            add_action($name . '_trigger', function () use ($id, $name, $cron_job_key) {
                $response = wp_remote_get(home_url("/wp-cron.php?export_key=$cron_job_key&export_id=$id&action=trigger"), array('timeout' => 600));

                if (is_wp_error($response)) {
                    do_action('wonolog.log', $response, 300, 'HTTP');
                }
            });
        }
    }

    /**
     * Add cron jobs events.
     */
    public static function add()
    {
        if (!self::getEvents()) {
            return;
        }

        foreach (self::getEvents() as $id => $event) {
            if (!wp_next_scheduled($event['name'] . '_exec')) {
                wp_schedule_event(strtotime($event['processing']['next_run']), $event['processing']['recurrence'], $event['name'] . '_exec');
            }

            if (!wp_next_scheduled($event['name'] . '_trigger')) {
                wp_schedule_event(strtotime($event['trigger']['next_run']), $event['trigger']['recurrence'], $event['name'] . '_trigger');
            }
        }
    }

    /**
     * Remove cron jobs events.
     */
    public static function remove()
    {
        if (!self::getEvents()) {
            return;
        }

        foreach (self::getEvents() as $event) {
            if (wp_next_scheduled($event['name'] . '_exec')) {
                wp_clear_scheduled_hook($event['name'] . '_exec');
            }

            if (wp_next_scheduled($event['name'] . '_trigger')) {
                wp_clear_scheduled_hook($event['name'] . '_trigger');
            }
        }
    }

    /**
     * Get cron jobs events.
     *
     * @return array
     */
    public static function getEvents()
    {
        if (is_array(self::$events)) {
            //  event already loaded
            return self::$events;
        }

        $exports = get_option('wpae_cron_scheduler_exports');
        if (!$exports) {
            return [];
        }

        $events = [];
        foreach ($exports as $export) {

            $events[$export['id']] = [
                'name' => WPAE::getExportNameByID($export['id']),
                'processing' => $export['processing'],
                'trigger' => $export['trigger']
            ];
        }

        return $events;
    }
}
