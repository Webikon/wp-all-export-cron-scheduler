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
        if (!self::validateWPAE() || !self::getEvents()) {
            return;
        }

        foreach (self::getEvents() as $id => $event) {
            self::ensureCronJobScheduled($event);
        }
    }

    /**
     * Ensures that cron jobs are properly scheduled and reschedules them if missing
     * 
     * @param array $event The event configuration
     */
    private static function ensureCronJobScheduled($event)
    {
        // Validate if PMXE classes exist
        if (!class_exists('PMXE_Plugin') || !class_exists('PMXE_Export_Record')) {
            return;
        }

        $processing_hook = $event['name'] . '_processing';
        $trigger_hook = $event['name'] . '_trigger';

        // Check and schedule processing job
        if (!wp_next_scheduled($processing_hook)) {
            $processing_time = strtotime($event['processing']['next_run']);
            
            // If date is in the past, adjust to today while keeping the time
            if ($processing_time < time()) {
                $time_parts = date('H:i:s', $processing_time);
                $processing_time = strtotime(date('Y-m-d ') . $time_parts);
            }

            wp_schedule_event(
                $processing_time,
                $event['processing']['recurrence'],
                $processing_hook
            );
        }

        // Check and schedule trigger job
        if (!wp_next_scheduled($trigger_hook)) {
            $trigger_time = strtotime($event['trigger']['next_run']);
            
            // If date is in the past, adjust to today while keeping the time
            if ($trigger_time < time()) {
                $time_parts = date('H:i:s', $trigger_time);
                $trigger_time = strtotime(date('Y-m-d ') . $time_parts);
            }

            wp_schedule_event(
                $trigger_time,
                $event['trigger']['recurrence'],
                $trigger_hook
            );
        }
    }

    /**
     * Validate if WP All Export Pro plugin is properly loaded
     * 
     * @return bool
     */
    private static function validateWPAE()
    {
        if (!class_exists('PMXE_Plugin') || !class_exists('PMXE_Export_Record')) {
            return false;
        }
        return true;
    }

    /**
     * Monitor and fix missing cron jobs
     */
    public static function monitorCronJobs()
    {
        if (!self::validateWPAE() || !self::getEvents()) {
            return;
        }

        foreach (self::getEvents() as $id => $event) {
            self::ensureCronJobScheduled($event);
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

        // Validate PMXE classes before proceeding
        if (!self::validateWPAE()) {
            return [];
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

        self::$events = $events;
        return $events;
    }
}
