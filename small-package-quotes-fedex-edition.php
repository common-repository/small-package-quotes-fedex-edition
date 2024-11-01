<?php
/*
  Plugin Name: Small Package Quotes - For FedEx Customers
  Plugin URI: https://eniture.com/products/
  Description: Dynamically retrieves your negotiated shipping rates from FedEx and displays the results in the WooCommerce shopping cart.
  Version: 4.3.1
  Author: Eniture Technology
  Author URI: https://eniture.com/
  Text Domain: eniture-technology
  License: GPLv2 or later
  WC requires at least: 6.4
  WC tested up to: 9.1.4
 */
/**
 * FedEx Small Plugin
 *
 * @package     FedEx Small Quotes
 * @author      Eniture-Technology
 */

if (!defined('ABSPATH')) {
    exit;
}

define('FEDEX_SPQ_MAIN_DOMAIN', 'https://ws026.eniture.com');
define('FEDEX_DOMAIN_HITTING_URL', 'https://ws026.eniture.com');
define('FEDEX_FDO_HITTING_URL', 'https://freightdesk.online/api/updatedWoocomData');

add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});

// Define reference
function en_fedex_small_freight_plugin($plugins)
{
    $plugins['spq'] = (isset($plugins['spq'])) ? array_merge($plugins['spq'], ['fedex_small' => 'WC_fedex_small']) : ['fedex_small' => 'WC_fedex_small'];
    return $plugins;
}

add_filter('en_plugins', 'en_fedex_small_freight_plugin');

if (!function_exists('en_woo_plans_notification_PD')) {

    function en_woo_plans_notification_PD($product_detail_options)
    {
        $eniture_plugins_id = 'eniture_plugin_';

        for ($en = 1; $en <= 25; $en++) {
            $settings = get_option($eniture_plugins_id . $en);
            if (isset($settings) && (!empty($settings)) && (is_array($settings))) {
                $plugin_detail = current($settings);
                $plugin_name = (isset($plugin_detail['plugin_name'])) ? $plugin_detail['plugin_name'] : "";

                foreach ($plugin_detail as $key => $value) {
                    if ($key != 'plugin_name') {
                        $action = $value === 1 ? 'enable_plugins' : 'disable_plugins';
                        $product_detail_options[$key][$action] = (isset($product_detail_options[$key][$action]) && strlen($product_detail_options[$key][$action]) > 0) ? $product_detail_options[$key][$action] . ", $plugin_name" : "$plugin_name";
                    }
                }
            }
        }

        return $product_detail_options;
    }

    add_filter('en_woo_plans_notification_action', 'en_woo_plans_notification_PD', 10, 1);
}

/**
 * Load scripts for FedEx Freight json tree view
 */
if (!function_exists('en_jtv_script')) {
    function en_jtv_script()
    {
        wp_register_style('json_tree_view_style', plugin_dir_url(__FILE__) . 'logs/en-json-tree-view/en-jtv-style.css');
        wp_register_script('json_tree_view_script', plugin_dir_url(__FILE__) . 'logs/en-json-tree-view/en-jtv-script.js', ['jquery'], '1.0.0');

        wp_enqueue_style('json_tree_view_style');
        wp_enqueue_script('json_tree_view_script', [
            'en_tree_view_url' => plugins_url(),
        ]);
    }

    add_action('admin_init', 'en_jtv_script');
}

if (!function_exists('en_woo_plans_notification_message')) {

    function en_woo_plans_notification_message($enable_plugins, $disable_plugins)
    {
        $enable_plugins = (strlen($enable_plugins) > 0) ? "$enable_plugins: <b> Enabled</b>. " : "";
        $disable_plugins = (strlen($disable_plugins) > 0) ? " $disable_plugins: Upgrade to <b>Standard Plan to enable</b>." : "";
        return $enable_plugins . "<br>" . $disable_plugins;
    }

    add_filter('en_woo_plans_notification_message_action', 'en_woo_plans_notification_message', 10, 2);
}

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


if (!function_exists('is_plugin_active')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

/**
 * Check woocommerce installlation
 */
if (!is_plugin_active('woocommerce/woocommerce.php')) {
    add_action('admin_notices', 'fedex_small_wc_avaibility_err');
}

/**
 * Woo Availability error
 */
function fedex_small_wc_avaibility_err()
{
    $class = "error";
    $message = "Small Package Quotes For FedEx is enabled but not effective. It requires WooCommerce to work, please <a target='_blank' href='https://wordpress.org/plugins/woocommerce/installation/'>Install</a> WooCommerce Plugin.";
    echo "<div class=\"$class\"> <p>$message</p></div>";
}

/**
 * Check woocommerce version compatibility
 */
add_action('admin_init', 'fedex_small_check_woo_version');

/**
 * Check Woo Version
 */
function fedex_small_check_woo_version()
{
    $woo_version = fedex_small_wc_version_number();
    $version = '2.6';
    if (!version_compare($woo_version, $version, ">=")) {
        add_action('admin_notices', 'fedex_small_wc_version_failure');
    }
}

/**
 * Woo Version Failure
 */
function fedex_small_wc_version_failure()
{
    ?>
    <div class="notice notice-error">
        <p>
            <?php
            _e('Fedex Small plugin requires WooCommerce version 2.6 or higher to work. Functionality may not work properly.', 'wwe-woo-version-failure');
            ?>
        </p>
    </div>
    <?php
}

/**
 * Return woocomerce version
 */
function fedex_small_wc_version_number()
{
    $plugin_folder = get_plugins('/' . 'woocommerce');
    $plugin_file = 'woocommerce.php';

    if (isset($plugin_folder[$plugin_file]['Version'])) {
        return $plugin_folder[$plugin_file]['Version'];
    } else {
        return NULL;
    }
}

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) || is_plugin_active_for_network('woocommerce/woocommerce.php')) {

    /**
     * Load scripts for FedEx Small
     */
    add_action('admin_enqueue_scripts', 'fedex_small_admin_script');

    /**
     * Admin Script
     */
    function fedex_small_admin_script()
    {

        wp_register_style('fedex_small_style', plugin_dir_url(__FILE__) . '/css/fedex_small_style.css', false, '3.0.8');
        wp_register_style('fedex_small_wickedpicker_style', 'https://cdn.jsdelivr.net/npm/wickedpicker@0.4.3/dist/wickedpicker.min.css', false, '2.0.3');
        wp_enqueue_style('fedex_small_style');
        wp_enqueue_style('fedex_small_wickedpicker_style');
        wp_register_script('fedex_small_wickedpicker_style', plugin_dir_url(__FILE__) . '/js/wickedpicker.js', false, '2.0.3');
        wp_enqueue_script('fedex_small_wickedpicker_style');

        if (is_admin() && (!empty( $_GET['page']) && 'wc-orders' == $_GET['page'] ) && (!empty( $_GET['action']) && 'new' == $_GET['action'] )) {
            if (!wp_script_is('eniture_calculate_shipping_admin', 'enqueued')) {
                wp_enqueue_script('eniture_calculate_shipping_admin', plugin_dir_url(__FILE__) . 'js/eniture-calculate-shipping-admin.js', array(), '1.0.0' );
            }
        }
    }

    /**
     * FedEx Small action links
     */
    add_filter('plugin_action_links', 'fedex_small_add_action_plugin', 10, 5);

    /**
     * Add Plugin Actions
     * @staticvar $plugin
     * @param $actions
     * @param $plugin_file
     * @return array
     */
    function fedex_small_add_action_plugin($actions, $plugin_file)
    {
        static $plugin;
        if (!isset($plugin))
            $plugin = plugin_basename(__FILE__);
        if ($plugin == $plugin_file) {
            $settings = array('settings' => '<a href="admin.php?page=wc-settings&tab=fedex_small">' . __('Settings', 'General') . '</a>');
            $site_link = array('support' => '<a href="https://support.eniture.com/" target="_blank">Support</a>');
            $actions = array_merge($settings, $actions);
            $actions = array_merge($site_link, $actions);
        }
        return $actions;
    }

    /**
     * Get Host
     * @param type $url
     * @return type
     */
    if (!function_exists('getHost')) {

        function getHost($url)
        {
            $parseUrl = parse_url(trim($url));
            if (isset($parseUrl['host'])) {
                $host = $parseUrl['host'];
            } else {
                $path = explode('/', $parseUrl['path']);
                $host = $path[0];
            }
            return trim($host);
        }

    }

    /**
     * Get Domain Name
     */
    if (!function_exists('fedex_small_get_domain')) {

        function fedex_small_get_domain()
        {
            global $wp;
            $url = home_url($wp->request);
            return getHost($url);
        }
    }

    // Product detail set plans notification message for nested checkbox
    if (!function_exists('en_woo_plans_nested_notification_message')) {

        function en_woo_plans_nested_notification_message($enable_plugins, $disable_plugins, $feature)
        {
            $enable_plugins = (strlen($enable_plugins) > 0) ? "$enable_plugins: <b> Enabled</b>. " : "";
            $disable_plugins = (strlen($disable_plugins) > 0 && $feature == 'nested_material') ? " $disable_plugins: Upgrade to <b>Advance Plan to enable</b>." : "";
            return $enable_plugins . "<br>" . $disable_plugins;
        }

        add_filter('en_woo_plans_nested_notification_message_action', 'en_woo_plans_nested_notification_message', 10, 3);
    }

    add_action('admin_enqueue_scripts', 'en_fedex_small_script');

    /**
     * Load Front-end scripts for fedex
     */
    function en_fedex_small_script()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('en_fedex_small_script', plugin_dir_url(__FILE__) . 'js/en-fedex-small.js', array(), '1.0.7');
        wp_localize_script('en_fedex_small_script', 'en_fedex_small_admin_script', array(
            'plugins_url' => plugins_url(),
            'allow_proceed_checkout_eniture' => trim(get_option("allow_proceed_checkout_eniture")),
            'prevent_proceed_checkout_eniture' => trim(get_option("prevent_proceed_checkout_eniture")),
            'fedex_small_order_cutoff_time' => get_option("fedex_small_orderCutoffTime"),
            'fedex_small_packaging_type' => get_option("fedex_small_packaging_type"),
        ));
    }

    /**
     * Inlude Plugin Files
     */
    require_once('warehouse-dropship/wild-delivery.php');
    require_once('warehouse-dropship/get-distance-request.php');

    require_once 'template/csv-export.php';

    require_once 'product/en-common-product-detail.php';
    require_once 'product/en-product-detail.php';

    require_once('template/products-nested-options.php');
    require_once 'template/csv-export.php';

    require_once('standard-package-addon/standard-package-addon.php');

    require_once 'update-plan.php';
    require_once 'fdo/en-fdo.php';
    require_once 'fdo/en-sbs.php';

    require_once 'helper/en_helper_class.php';
    require_once('db/fedex_small_db.php');
    require_once('fedex-samll-carriers.php');
    require_once('fedex_small_admin_filter.php');
    require_once('fedex-small-curl-class.php');
    require_once('fedex-small-auto-residential.php');
    require_once('fedex_small_shipping_class.php');
    require_once('template/connection_settings.php');
    require_once('template/quote_settings.php');

    require_once('fedex_small_test_connection.php');
    require_once('fedex_small_carrier_service.php');
    require_once('fedex_small_group_package.php');
    require_once('fedex_small_wc_update_change.php');
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    require_once('order-details/en-order-export.php');
    require_once('order-details/en-order-widget.php');
    require_once('order-details/rates/order-rates.php');

    require_once('fedex_small_version_compact.php');

    // Origin terminal address
    add_action('admin_init', 'fedex_small_update_warehouse');


    /**
     * FedEx Small Activation Hook
     */
    register_activation_hook(__FILE__, 'create_fedex_small_wh_db');
    register_activation_hook(__FILE__, 'create_fedex_small_option');
    register_activation_hook(__FILE__, 'old_store_fedex_sm_dropship_status');

    register_activation_hook(__FILE__, 'en_fedex_small_activate_hit_to_update_plan');
    register_deactivation_hook(__FILE__, 'en_fedex_small_deactivate_hit_to_update_plan');
    register_deactivation_hook(__FILE__, 'en_fedex_small_deactivate_plugin');
    /**
     * fedex small plugin update now
     * @param array type $upgrader_object
     * @param array type $options
     */
    function en_fedex_small_update_now()
    {
        $index = 'small-package-quotes-fedex-edition/small-package-quotes-fedex-edition.php';
        $plugin_info = get_plugins();
        $plugin_version = (isset($plugin_info[$index]['Version'])) ? $plugin_info[$index]['Version'] : '';
        $update_now = get_option('en_fedex_small_update_now');

        if ($update_now != $plugin_version) {
            if (!function_exists('en_fedex_small_activate_hit_to_update_plan')) {
                require_once(__DIR__ . '/update-plan.php');
            }

            create_fedex_small_wh_db();
            create_fedex_small_option();
            old_store_fedex_sm_dropship_status();
            en_fedex_small_activate_hit_to_update_plan();

            update_option('en_fedex_small_update_now', $plugin_version);
        }
    }

    add_action('init', 'en_fedex_small_update_now');

    /**
     * FedEx Small Action And Filters
     */
    add_filter('woocommerce_shipping_methods', 'add_fedex_small');
    add_filter('woocommerce_get_settings_pages', 'fedex_small_shipping_sections');
    add_action('woocommerce_shipping_init', 'fedex_small_init');
    add_filter('woocommerce_package_rates', 'fedex_small_hide_shipping');
    add_filter('woocommerce_shipping_calculator_enable_city', '__return_true');
    add_filter('woocommerce_cart_no_shipping_available_html', 'fedex_small_default_error_message', 999, 1);
    add_action('init', 'fedex_small_no_method_available');
    add_action('init', 'fedex_small_default_error_message_selection');
}

/**
 * Update Default custom error message selection
 */
function fedex_small_default_error_message_selection()
{
    $custom_error_selection = get_option('wc_pervent_proceed_checkout_eniture');
    if (empty($custom_error_selection)) {
        update_option('wc_pervent_proceed_checkout_eniture', 'prevent', true);
        update_option('prevent_proceed_checkout_eniture', 'There are no shipping methods available for the address provided. Please check the address.', true);
    }
}

/**
 * @param $message
 * @return string
 */
if (!function_exists("fedex_small_default_error_message")) {

    function fedex_small_default_error_message($message)
    {
        if (get_option('wc_pervent_proceed_checkout_eniture') == 'prevent') {
            remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20, 2);
            return __(get_option('prevent_proceed_checkout_eniture'));
        } else if (get_option('wc_pervent_proceed_checkout_eniture') == 'allow') {
            add_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20, 2);
            return __(get_option('allow_proceed_checkout_eniture'));
        }
    }

}
define("en_woo_plugin_fedex_small", "fedex_small");

add_action('wp_enqueue_scripts', 'en_fedex_small_frontend_checkout_script');

/**
 * Load Frontend scripts for FedEx Small
 */
function en_fedex_small_frontend_checkout_script()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('en_fedex_small_frontend_checkout_script', plugin_dir_url(__FILE__) . 'front/js/en-fedex-small-checkout.js', array(), '1.0.0');
    wp_localize_script('en_fedex_small_frontend_checkout_script', 'frontend_script', array(
        'pluginsUrl' => plugins_url(),
    ));
}

/**
 * Plans Common Hooks
 */
add_filter('fedex_small_quotes_plans_suscription_and_features', 'fedex_small_quotes_plans_suscription_and_features', 1);

function fedex_small_quotes_plans_suscription_and_features($feature)
{
    $package = get_option('fedex_small_package');

    $features = array
    (
        'instore_pickup_local_devlivery' => array('3'),
        'transit_days' => array('3'),
        'hazardous_material' => array('2', '3'),
        'insurance_fee' => array('2', '3'),
        'cutOffTime_shipDateOffset' => array('2', '3'),
        'nested_material' => array('3'),
    );
    if (get_option('fedex_small_quotes_store_type') == "1") {
        $features['multi_warehouse'] = array('2', '3');
        $features['multi_dropship'] = array('', '0', '1', '2', '3');
    } else {
        $dropship_status = get_option('en_old_user_dropship_status');
        $warehouse_status = get_option('en_old_user_warehouse_status');

        isset($dropship_status) && ($dropship_status == "0") ? $features['multi_dropship'] = array('', '0', '1', '2', '3') : '';
        isset($warehouse_status) && ($warehouse_status == "0") ? $features['multi_warehouse'] = array('2', '3') : '';
    }

    return (isset($features[$feature]) && (in_array($package, $features[$feature]))) ? TRUE : ((isset($features[$feature])) ? $features[$feature] : '');
}

add_filter('fedex_small_plans_notification_link', 'fedex_small_plans_notification_link', 1);

function fedex_small_plans_notification_link($plans)
{
    $plan = current($plans);
    $plan_to_upgrade = "";
    switch ($plan) {
        case 1:
            $plan_to_upgrade = "<a target='_blank' href='https://eniture.com/woocommerce-fedex-small-package-plugin/'>Basic Plan required</a>";
            break;
        case 2:
            $plan_to_upgrade = "<a target='_blank' href='https://eniture.com/woocommerce-fedex-small-package-plugin/'>Standard Plan required</a>";
            break;
        case 3:
            $plan_to_upgrade = "<a target='_blank' href='https://eniture.com/woocommerce-fedex-small-package-plugin/'>Advanced Plan required</a>";
            break;
    }

    return $plan_to_upgrade;
}

/**
 *
 * old customer check dropship / warehouse status on plugin update
 */
function old_store_fedex_sm_dropship_status()
{
    global $wpdb;

//  Check total no. of dropships on plugin updation
    $table_name = $wpdb->prefix . 'warehouse';
    $count_query = "select count(*) from $table_name where location = 'dropship' ";
    $num = $wpdb->get_var($count_query);

    if (get_option('en_old_user_dropship_status') == "0" && get_option('fedex_small_quotes_store_type') == "0") {
        $dropship_status = ($num > 1) ? 1 : 0;

        update_option('en_old_user_dropship_status', "$dropship_status");
    } elseif (get_option('en_old_user_dropship_status') == "" && get_option('fedex_small_quotes_store_type') == "0") {
        $dropship_status = ($num == 1) ? 0 : 1;

        update_option('en_old_user_dropship_status', "$dropship_status");
    }

//  Check total no. of warehouses on plugin updation
    $table_name = $wpdb->prefix . 'warehouse';
    $warehouse_count_query = "select count(*) from $table_name where location = 'warehouse' ";
    $warehouse_num = $wpdb->get_var($warehouse_count_query);

    if (get_option('en_old_user_warehouse_status') == "0" && get_option('fedex_small_quotes_store_type') == "0") {
        $warehouse_status = ($warehouse_num > 1) ? 1 : 0;

        update_option('en_old_user_warehouse_status', "$warehouse_status");
    } elseif (get_option('en_old_user_warehouse_status') == "" && get_option('fedex_small_quotes_store_type') == "0") {
        $warehouse_status = ($warehouse_num == 1) ? 0 : 1;

        update_option('en_old_user_warehouse_status', "$warehouse_status");
    }
}
// fdo va
add_action('wp_ajax_nopriv_fedex_s_fd', 'fedex_s_fd_api');
add_action('wp_ajax_fedex_s_fd', 'fedex_s_fd_api');
/**
 * UPS AJAX Request
 */
function fedex_s_fd_api()
{
    $store_name = fedex_small_get_domain();
    $company_id = $_POST['company_id'];
    $data = [
        'plateform'  => 'wp',
        'store_name' => $store_name,
        'company_id' => $company_id,
        'fd_section' => 'tab=fedex_small&section=section-4',
    ];
    if (is_array($data) && count($data) > 0) {
        if($_POST['disconnect'] != 'disconnect') {
            $url =  'https://freightdesk.online/validate-company';
        }else {
            $url = 'https://freightdesk.online/disconnect-woo-connection';
        }
        $response = wp_remote_post($url, [
                'method' => 'POST',
                'timeout' => 60,
                'redirection' => 5,
                'blocking' => true,
                'body' => $data,
            ]
        );
        $response = wp_remote_retrieve_body($response);
    }
    if($_POST['disconnect'] == 'disconnect') {
        $result = json_decode($response);
        if ($result->status == 'SUCCESS') {
            update_option('en_fdo_company_id_status', 0);
        }
    }
    echo $response;
    exit();
}
add_action('rest_api_init', 'en_rest_api_init_status_fedex_s');
function en_rest_api_init_status_fedex_s()
{
    register_rest_route('fdo-company-id', '/update-status', array(
        'methods' => 'POST',
        'callback' => 'en_fedex_s_fdo_data_status',
        'permission_callback' => '__return_true'
    ));
}

/**
 * Update FDO coupon data
 * @param array $request
 * @return array|void
 */
function en_fedex_s_fdo_data_status(WP_REST_Request $request)
{
    $status_data = $request->get_body();
    $status_data_decoded = json_decode($status_data);
    if (isset($status_data_decoded->connection_status)) {
        update_option('en_fdo_company_id_status', $status_data_decoded->connection_status);
        update_option('en_fdo_company_id', $status_data_decoded->fdo_company_id);
    }
    return true;
}

if (!function_exists('en_fedex_check_ground_transit_restrict_status')) {

    function en_fedex_check_ground_transit_restrict_status($ground_transit_statuses)
    {
        $ground_transit_restrict_plan = apply_filters('fedex_small_quotes_plans_suscription_and_features', 'transit_days');
        $ground_restrict_value = (false !== get_option('restrict_days_transit_package_fedex_small')) ? get_option('restrict_days_transit_package_fedex_small') : '';
        if ('' !== $ground_restrict_value && strlen(trim($ground_restrict_value)) && !is_array($ground_transit_restrict_plan)) {
            $ground_transit_statuses['fedex'] = '1';
        }

        return $ground_transit_statuses;
    }

    add_filter('en_check_ground_transit_restrict_status', 'en_fedex_check_ground_transit_restrict_status', 9, 1);
}
