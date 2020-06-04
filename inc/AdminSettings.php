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
            __('WPAE Cron Scheduler', WPAE_CRSCH_TD), // Page title
            __('WPAE Cron Scheduler', WPAE_CRSCH_TD), // Menu title
            'manage_options', // Capability
            'wpae-cron-scheduler', // Menu slug
            [$this, 'adminSubpageCallback'] // Callback function
        );
    }

    /**
     * Admin settings callback
     */
    public function adminSubpageCallback()
    {
        ?>

        <div class="wrap">
            <h1><?php _e('WPAE Cron Scheduler', WPAE_CRSCH_TD) ?></h1>

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
        $is_woocommerce_active = is_plugin_active('woocommerce/woocommerce.php');

        $event_types = [
            'processing' => 'Processing',
            'trigger' => 'Trigger'
        ];
        ?>

        <ul id="wpae-crsch-exports-list">
            <?php foreach ($exports as $key => $export): ?>
                <li class="wpae-crsch-export-item">
                    <table class="form-table">
                        <tbody>
                            <tr class="wpae-crsch-bdb">
                                <th>
                                    <label for="exportId">
                                        <?php _e('Export', WPAE_CRSCH_TD) ?>
                                    </label>
                                </th>

                                <td>
                                    <select class="wpae-crsch-input" id="exportId" name="wpae_cron_scheduler_exports[<?php echo $key ?>][id]" required>
                                        <option value="">-- <?php _e('Select an export from the list', WPAE_CRSCH_TD) ?> --</option>

                                        <?php foreach ($wpae_exports as $wpae_export): ?>
                                            <option value="<?php echo $wpae_export->id ?>" <?php echo !empty($export['id']) && $wpae_export->id == $export['id'] ? 'selected' : '' ?>>
                                                <?php echo "#ID:$wpae_export->id - $wpae_export->friendly_name" ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </td>
                            </tr>

                            <?php foreach ($event_types as $type_key => $type_name): ?>
                                <tr>
                                    <th><?php echo $type_name ?> <?php _e('Next run', WPAE_CRSCH_TD) ?></th>

                                    <td>
                                        <label>
                                            <input type="text" class="wpae-crsch-input regular-text js-wpae-crsch-uncheck" name="wpae_cron_scheduler_exports[<?php echo $key ?>][<?php echo $type_key ?>][next_run]" value="<?php echo !empty($export[$type_key]['next_run']) ? $export[$type_key]['next_run'] : '' ?>" required>
                                        </label>

                                        <p class="description">
                                            <?php printf(
                                                __('Format: %1$s or anything accepted by %2$s', WPAE_CRSCH_TD),
                                                '<code>YYYY-MM-DD HH:MM:SS</code>',
                                                '<a href="https://www.php.net/manual/function.strtotime.php" target="_blank"><code>strtotime()</code></a>'
                                            ) ?>
                                        </p>
                                    </td>
                                </tr>

                                <tr class="wpae-crsch-bdb">
                                    <th>
                                        <label for="recurrence">
                                            <?php echo $type_name ?> <?php _e('Recurrence', WPAE_CRSCH_TD) ?>
                                        </label>
                                    </th>

                                    <td>
                                        <select class="wpae-crsch-input" id="recurrence" name="wpae_cron_scheduler_exports[<?php echo $key ?>][<?php echo $type_key ?>][recurrence]">
                                            <option value="non-repeating"><?php _e('Non-repeating', WPAE_CRSCH_TD) ?></option>

                                            <?php foreach (wp_get_schedules() as $schedule_key => $schedule): ?>
                                                <option value="<?php echo $schedule_key ?>" <?php echo !empty($export[$type_key]['recurrence']) && $schedule_key == $export[$type_key]['recurrence'] ? 'selected' : '' ?>>
                                                    <?php echo $schedule['display'] ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach ?>

                            <?php if ($is_woocommerce_active): ?>
                                <tr>
                                    <th>WooCommerce</th>

                                    <td>
                                        <label>
                                            <input class="wpae-crsch-input" type="checkbox" name="wpae_cron_scheduler_exports[<?php echo $key ?>][is_wc_products]" value="1" <?php echo !empty($export['is_wc_products']) ? 'checked="checked"' : '' ?>> <?php _e('Check if is the WooCommerce products export', WPAE_CRSCH_TD) ?>
                                        </label>
                                    </td>
                                </tr>
                            <?php endif ?>

                        </tbody>
                    </table>

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