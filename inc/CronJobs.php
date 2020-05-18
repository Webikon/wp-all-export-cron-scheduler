<?php

namespace WpaeCrsch;

use PMXE_Plugin;
use PMXE_Export_Record;

class CronJobs
{
    public function __construct()
    {
        add_action('init', [$this, 'define']);
        add_action('init', [$this, 'add']);
    }

    /**
     * Define cron jobs for automated exports.
     */
    public function define()
    {
        // Get cron jobs key from WP All Export
        $cron_job_key = PMXE_Plugin::getInstance()->getOption('cron_job_key');

        if (!$this->getEvents()) {
            return;
        }

        foreach ($this->getEvents() as $id => $name) {
            // This check needs to be run every 5-10 minutes
            add_action($name . '_exec', function () use ($id) {
                wp_remote_get(home_url("/wp-load.php?export_key=$cron_job_key&export_id=$id&action=processing"), array('timeout' => 600));
            });

            // Trigger can be run e.g. every 24 hour
            add_action($name . '_trigger', function () use ($id) {
                wp_remote_get(home_url("/wp-cron.php?export_key=$cron_job_key&export_id=$id&action=trigger"), array('timeout' => 600));
            });
        }
    }

    /**
     * Add cron jobs events.
     */
    public function add()
    {
        if (!$this->getEvents()) {
            return;
        }

        foreach ($this->getEvents() as $id => $name) {
            if (!wp_next_scheduled($name . '_exec')) {
                // Run execs every 5 minutes
                wp_schedule_event(time() + 300, MINUTE_IN_SECONDS * 5, $name . '_exec');
            }

            if (!wp_next_scheduled($name . '_trigger')) {
                // Run triggers every 24 hours
                $current_time = current_time('timestamp');
                $next_scheduled_time = strtotime('tomorrow midnight +2 hours', $current_time);
                wp_schedule_event($next_scheduled_time, 'daily', $name . '_trigger');
            }
        }
    }

    /**
     * Remove cron jobs events.
     */
    public function remove()
    {
        if (!$this->getEvents()) {
            return;
        }

        foreach ($this->getEvents() as $name) {
            if (wp_next_scheduled($name . '_exec')) {
                wp_clear_scheduled_hook($name . '_exec');
            }

            if (wp_next_scheduled($name . '_trigger')) {
                wp_clear_scheduled_hook($name . '_trigger');
            }
        }
    }

    /**
     * Get cron jobs events.
     *
     * @return array
     */
    public function getEvents()
    {
        $exports = get_option('wpae_cron_scheduler_exports');

        if (!$exports) {
            return [];
        }

        $events = [];

        foreach ($exports as $export_id) {
            $events[$export_id] = WPAE::getExportNameByID($export_id);
        }

        return $events;
    }
}
new CronJobs;