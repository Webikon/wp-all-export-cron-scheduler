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
     * Add cron jobs events.
     */
    public static function add()
    {
        if (!self::getEvents()) {
            return;
        }

        foreach (self::getEvents() as $id => $event) {
            if (!wp_next_scheduled($event['name'] . '_processing')) {
                wp_schedule_event(strtotime($event['processing']['next_run']), $event['processing']['recurrence'], $event['name'] . '_processing');
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
            if (wp_next_scheduled($event['name'] . '_processing')) {
                wp_clear_scheduled_hook($event['name'] . '_processing');
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
                'name' => WPAE_CRSCH_PREFIX . WPAE::getExportNameByID($export['id']),
                'processing' => $export['processing'],
                'trigger' => $export['trigger']
            ];
        }

        return $events;
    }
}
