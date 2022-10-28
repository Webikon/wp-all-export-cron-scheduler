<?php

namespace Webikon\WpAllExport\Scheduler;

use PMXE_Plugin;
use PMXE_Export_Record;

class WPAE
{
    /**
     * Get WPAE exports from database
     *
     * @return array
     */
    public static function getExports()
    {
        global $wpdb;

        // WPAE db table exports name
        $db_wpae_table_name = PMXE_Plugin::getInstance()->getTablePrefix() . 'exports';

        return $wpdb->get_results("SELECT * FROM $db_wpae_table_name WHERE canceled = 0");
    }

    /**
     * Get single export name by ID
     *
     * @param integer $export_id
     * @return string
     */
    public static function getExportNameByID($export_id)
    {
        $wpae_export = new PMXE_Export_Record();
        $export = $wpae_export->getById($export_id);

        if (!empty($export->friendly_name)) {
            return str_replace('-', '_', sanitize_title($export->friendly_name));
        }

        return;
    }
}
