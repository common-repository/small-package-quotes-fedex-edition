<?php
/**
 * FedEx Small DATABASE
 *
 * @package     FedEx Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create warehouse database table
 */

function create_fedex_small_wh_db($network_wide = null)
{
    if ( is_multisite() && $network_wide ) {
        foreach (get_sites(['fields'=>'ids']) as $blog_id) {
            switch_to_blog($blog_id);
            global $wpdb;
            $warehouse_table = $wpdb->prefix . "warehouse";
            if ($wpdb->query("SHOW TABLES LIKE '" . $warehouse_table . "'") === 0) {
                $origin = 'CREATE TABLE ' . $warehouse_table . '(
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            city varchar(200) NOT NULL,
            state varchar(200) NOT NULL,
            address varchar(255) NOT NULL,
            phone_instore varchar(255) NOT NULL,
            zip varchar(200) NOT NULL,
            country varchar(200) NOT NULL,
            location varchar(200) NOT NULL,
            nickname varchar(200) NOT NULL,
            enable_store_pickup VARCHAR(255) NOT NULL,
            miles_store_pickup VARCHAR(255) NOT NULL ,
            match_postal_store_pickup VARCHAR(255) NOT NULL ,
            checkout_desc_store_pickup VARCHAR(255) NOT NULL ,
            enable_local_delivery VARCHAR(255) NOT NULL ,
            miles_local_delivery VARCHAR(255) NOT NULL ,
            match_postal_local_delivery VARCHAR(255) NOT NULL ,
            checkout_desc_local_delivery VARCHAR(255) NOT NULL ,
            fee_local_delivery VARCHAR(255) NOT NULL ,
            suppress_local_delivery VARCHAR(255) NOT NULL,
            origin_markup VARCHAR(255),
            PRIMARY KEY  (id) )';
                dbDelta($origin);
            }

            $myCustomer = $wpdb->get_row("SHOW COLUMNS FROM " . $warehouse_table . " LIKE 'enable_store_pickup'");
            if (!(isset($myCustomer->Field) && $myCustomer->Field == 'enable_store_pickup')) {
                $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN enable_store_pickup VARCHAR(255) NOT NULL , "
                    . "ADD COLUMN miles_store_pickup VARCHAR(255) NOT NULL , "
                    . "ADD COLUMN match_postal_store_pickup VARCHAR(255) NOT NULL , "
                    . "ADD COLUMN checkout_desc_store_pickup VARCHAR(255) NOT NULL , "
                    . "ADD COLUMN enable_local_delivery VARCHAR(255) NOT NULL , "
                    . "ADD COLUMN miles_local_delivery VARCHAR(255) NOT NULL , "
                    . "ADD COLUMN match_postal_local_delivery VARCHAR(255) NOT NULL , "
                    . "ADD COLUMN checkout_desc_local_delivery VARCHAR(255) NOT NULL , "
                    . "ADD COLUMN fee_local_delivery VARCHAR(255) NOT NULL , "
                    . "ADD COLUMN suppress_local_delivery VARCHAR(255) NOT NULL", $warehouse_table));

            }

            $fedex_spq_origin_markup = $wpdb->get_row("SHOW COLUMNS FROM " . $warehouse_table . " LIKE 'origin_markup'");
            if (!(isset($fedex_spq_origin_markup->Field) && $fedex_spq_origin_markup->Field == 'origin_markup')) {
                $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN origin_markup VARCHAR(255) NOT NULL", $warehouse_table));
            }    

            // Origin terminal address
            fedex_small_update_warehouse();
            add_option('fedex_small_db_version', '1.0');
            restore_current_blog();
        }

    } else {
        global $wpdb;
        $warehouse_table = $wpdb->prefix . "warehouse";
        if ($wpdb->query("SHOW TABLES LIKE '" . $warehouse_table . "'") === 0) {
            $origin = 'CREATE TABLE ' . $warehouse_table . '(
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            city varchar(200) NOT NULL,
            state varchar(200) NOT NULL,
            address varchar(255) NOT NULL,
            phone_instore varchar(255) NOT NULL,
            zip varchar(200) NOT NULL,
            country varchar(200) NOT NULL,
            location varchar(200) NOT NULL,
            nickname varchar(200) NOT NULL,
            enable_store_pickup VARCHAR(255) NOT NULL,
            miles_store_pickup VARCHAR(255) NOT NULL ,
            match_postal_store_pickup VARCHAR(255) NOT NULL ,
            checkout_desc_store_pickup VARCHAR(255) NOT NULL ,
            enable_local_delivery VARCHAR(255) NOT NULL ,
            miles_local_delivery VARCHAR(255) NOT NULL ,
            match_postal_local_delivery VARCHAR(255) NOT NULL ,
            checkout_desc_local_delivery VARCHAR(255) NOT NULL ,
            fee_local_delivery VARCHAR(255) NOT NULL ,
            suppress_local_delivery VARCHAR(255) NOT NULL,
            origin_markup VARCHAR(255),
            PRIMARY KEY  (id) )';
            dbDelta($origin);
        }

        $myCustomer = $wpdb->get_row("SHOW COLUMNS FROM " . $warehouse_table . " LIKE 'enable_store_pickup'");
        if (!(isset($myCustomer->Field) && $myCustomer->Field == 'enable_store_pickup')) {
            $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN enable_store_pickup VARCHAR(255) NOT NULL , "
                . "ADD COLUMN miles_store_pickup VARCHAR(255) NOT NULL , "
                . "ADD COLUMN match_postal_store_pickup VARCHAR(255) NOT NULL , "
                . "ADD COLUMN checkout_desc_store_pickup VARCHAR(255) NOT NULL , "
                . "ADD COLUMN enable_local_delivery VARCHAR(255) NOT NULL , "
                . "ADD COLUMN miles_local_delivery VARCHAR(255) NOT NULL , "
                . "ADD COLUMN match_postal_local_delivery VARCHAR(255) NOT NULL , "
                . "ADD COLUMN checkout_desc_local_delivery VARCHAR(255) NOT NULL , "
                . "ADD COLUMN fee_local_delivery VARCHAR(255) NOT NULL , "
                . "ADD COLUMN suppress_local_delivery VARCHAR(255) NOT NULL", $warehouse_table));

        }

        $fedex_spq_origin_markup = $wpdb->get_row("SHOW COLUMNS FROM " . $warehouse_table . " LIKE 'origin_markup'");
        if (!(isset($fedex_spq_origin_markup->Field) && $fedex_spq_origin_markup->Field == 'origin_markup')) {
            $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN origin_markup VARCHAR(255) NOT NULL", $warehouse_table));
        } 

        // Origin terminal address
        fedex_small_update_warehouse();
        add_option('fedex_small_db_version', '1.0');
    }

}
/**
 * Update warehouse
 */
function fedex_small_update_warehouse()
{
    // Origin terminal address
    global $wpdb;
    $warehouse_table = $wpdb->prefix . "warehouse";
    $warehouse_address = $wpdb->get_row("SHOW COLUMNS FROM " . $warehouse_table . " LIKE 'phone_instore'");
    if (!(isset($warehouse_address->Field) && $warehouse_address->Field == 'phone_instore')) {
        $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN address VARCHAR(255) NOT NULL", $warehouse_table));
        $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN phone_instore VARCHAR(255) NOT NULL", $warehouse_table));
    }
}

/**
 * Create plugin option
 */

function create_fedex_small_option($network_wide = null)
{
    if ( is_multisite() && $network_wide ) {

        foreach (get_sites(['fields'=>'ids']) as $blog_id) {
            switch_to_blog($blog_id);
            $eniture_plugins = get_option('EN_Plugins');
            if (!$eniture_plugins) {
                add_option('EN_Plugins', json_encode(array('fedex_small')));
            } else {
                $plugins_array = json_decode($eniture_plugins, true);
                if (!in_array('fedex_small', $plugins_array)) {
                    array_push($plugins_array, 'fedex_small');
                    update_option('EN_Plugins', json_encode($plugins_array));
                }
            }
            restore_current_blog();
        }

    } else {
        $eniture_plugins = get_option('EN_Plugins');
        if (!$eniture_plugins) {
            add_option('EN_Plugins', json_encode(array('fedex_small')));
        } else {
            $plugins_array = json_decode($eniture_plugins, true);
            if (!in_array('fedex_small', $plugins_array)) {
                array_push($plugins_array, 'fedex_small');
                update_option('EN_Plugins', json_encode($plugins_array));
            }
        }
    }

}

/**
 * Remove plugin option
 */
function en_fedex_small_deactivate_plugin($network_wide = null)
{
    if ( is_multisite() && $network_wide ) {
        foreach (get_sites(['fields'=>'ids']) as $blog_id) {
            switch_to_blog($blog_id);
            $eniture_plugins = get_option('EN_Plugins');
            $plugins_array = json_decode($eniture_plugins, true);
            $plugins_array = !empty($plugins_array) && is_array($plugins_array) ? $plugins_array : array();
            $key = array_search('fedex_small', $plugins_array);
            if ($key !== false) {
                unset($plugins_array[$key]);
            }
            update_option('EN_Plugins', json_encode($plugins_array));
            restore_current_blog();
        }
    } else {
        $eniture_plugins = get_option('EN_Plugins');
        $plugins_array = json_decode($eniture_plugins, true);
        $plugins_array = !empty($plugins_array) && is_array($plugins_array) ? $plugins_array : array();
        $key = array_search('fedex_small', $plugins_array);
        if ($key !== false) {
            unset($plugins_array[$key]);
        }
        update_option('EN_Plugins', json_encode($plugins_array));
    }
}