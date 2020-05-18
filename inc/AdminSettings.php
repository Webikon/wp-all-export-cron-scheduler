<?php

namespace Webikon\WpAllExport\Scheduler;

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
    {
        $exports = get_option('wpae_cron_scheduler_exports') ?: [''];

        $wpae_exports = WPAE::getExports();
        ?>

        <ul id="wpae-crsch-exports-list">
            <?php foreach ($exports as $export_id): ?>
                <li class="wpae-crsch-export-item">
                    <select name="wpae_cron_scheduler_exports[]">
                        <option value="">-- <?php _e('Select an export from the list', WPAE_CRSCH_TD) ?> --</option>

                        <?php foreach ($wpae_exports as $wpae_export): ?>
                            <option value="<?php echo $wpae_export->id ?>" <?php echo $wpae_export->id == $export_id ? 'selected' : '' ?>>
                                <?php echo "#ID:$wpae_export->id - $wpae_export->friendly_name" ?>
                            </option>
                        <?php endforeach ?>
                    </select>

                    <span id="delete-link">
                        <a href="#" class="delete js-wpae-crsch-remove-item">
                            <?php _e('Remove', WPAE_CRSCH_TD) ?>
                        </a>
                    </span>
                </li>
            <?php endforeach ?>
        </ul>

        <div>
            <button class="button js-wpae-crsch-add-item" type="button">
                + <?php _e('Add export', WPAE_CRSCH_TD) ?>
            </button>
        </div>

        <?php
    }
}
new AdminSettings;