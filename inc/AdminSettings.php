<?php

namespace WpaeCrsch;

class AdminSettings
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'addAdminSubpage']);
        add_action('admin_init', [$this, 'registerSettings']);
    }

    /**
     * Add admin settings subpage
     */
    public function addAdminSubpage()
    {
        add_submenu_page(
            'options-general.php', // Parent slug
            __('WPAE Cron Sheduler', WPAE_CRSCH_TD), // Page title
            __('WPAE Cron Sheduler', WPAE_CRSCH_TD), // Menu title
            'manage_options', // Capability
            'wpae-cron-scheduler', // Menu slug
            [$this, 'adminSubpageCallback'], // Callback function
        );
    }

    /**
     * Admin settings callback
     */
    public function adminSubpageCallback()
    {
        ?>

        <div class="wrap">
            <h1><?php _e('WPAE Cron Sheduler', WPAE_CRSCH_TD) ?></h1>

            <form action='options.php' method='post'>
                <?php
                    settings_fields('wpae_crsch_settings');
                    do_settings_sections('wpae_crsch_settings');
                    submit_button();
                ?>
            </form>
        </div>

        <?php
    }

    /**
     * Register settings and sections
     */
    public function registerSettings()
    {
        // Register setting
        register_setting('wpae_crsch_settings', 'wpae_cron_scheduler_exports');

        // Settings section
        add_settings_section(
            'wpae_crsch_section',
            __('Exports list', WPAE_CRSCH_TD),
            [$this, 'registerSettingsCallback'],
            'wpae_crsch_settings'
        );
    }

    /**
     * Register settings callback
     */
    public function registerSettingsCallback()
    {}
}
new AdminSettings;