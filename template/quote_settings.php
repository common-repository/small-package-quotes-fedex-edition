<?php

/**
 * FedEx Small Quote Settings
 *
 * @package     FedEx Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class For Quote Settings Tab
 */
class FedEx_Small_Quote_Settings
{

    /**
     * Quote Setting Fields
     */
    function fedex_small_quote_settings_tab()
    {

        $disable_transit = "";
        $transit_package_required = "";

        $disable_hazardous = "";
        $hazardous_package_required = "";


        $action_transit = apply_filters('fedex_small_quotes_plans_suscription_and_features', 'transit_days');
        if (is_array($action_transit)) {
            $disable_transit = "disabled_me";
            $transit_package_required = apply_filters('fedex_small_plans_notification_link', $action_transit);
        }

        $action_hazardous = apply_filters('fedex_small_quotes_plans_suscription_and_features', 'hazardous_material');
        if (is_array($action_hazardous)) {
            $disable_hazardous = "disabled_me";
            $hazardous_package_required = apply_filters('fedex_small_plans_notification_link', $action_hazardous);
        }

        //**Plan_Validation: Cut Off Time & Ship Date Offset
        $disable_cutOffTime_shipDateOffset = "";
        $cutOffTime_shipDateOffset_package_required = "";
        $action_cutOffTime_shipDateOffset = apply_filters('fedex_small_quotes_plans_suscription_and_features', 'cutOffTime_shipDateOffset');
        if (is_array($action_cutOffTime_shipDateOffset)) {
            $disable_cutOffTime_shipDateOffset = "disabled_me";
            $cutOffTime_shipDateOffset_package_required = apply_filters('fedex_small_plans_notification_link', $action_cutOffTime_shipDateOffset);
        }
        //**End: Cut Off Time & Ship Date Offset

        $package_type_options = [
            'ship_alone' => __('Quote each item as shipping as its own package', 'woocommerce-settings-fedex_small_quotes'),
            'ship_combine_and_alone' => __('Combine the weight of all items without dimensions and quote them as one package while quoting each item with dimensions as shipping as its own package', 'woocommerce-settings-fedex_small_quotes'),
            'ship_one_package_70' => __('Quote shipping as if all items ship as one package up to 70 LB each', 'woocommerce-settings-fedex_small_quotes'),
            'ship_one_package_150' => __('Quote shipping as if all items ship as one package up to 150 LB each', 'woocommerce-settings-fedex_small_quotes'),
        ];
        $package_type_default = 'ship_alone';
        $fedex_small_packaging_type = get_option("fedex_small_packaging_type");
        if(!empty($fedex_small_packaging_type) && $fedex_small_packaging_type == 'old'){
            $package_type_default = 'eniture_packaging';
            $package_type_options['eniture_packaging'] = __('Use the default Eniture packaging algorithm', 'woocommerce-settings-fedex_small_quotes');
        }

        echo '<div class="fedex_small_quote_section">';

        $settings = array(
            'fedex_small_services' => array(
                'name' => __('Quote Service Options ', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'title',
                'desc' => '',
                'id' => 'fedex_small_quote_hdng'
            ),
            'fedex_small_domastic_srvcs' => array(
                'name' => __('Domestic Services', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'id' => 'fedex_small_dom_srvc_hdng',
                'class' => 'dom_int_srvc_hdng'
            ),
            'fedex_small_int_srvcs' => array(
                'name' => __('International Services', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'id' => 'fedex_small_int_srvc_hdng',
                'class' => 'dom_int_srvc_hdng'
            ),
            'fedex_small_select_all_services' => array(
                'name' => __('Select All', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'id' => 'wc_settings_select_all_',
                'class' => 'fedex_small_all_services',
            ),
            'fedex_small_select_all_int_services' => array(
                'name' => __('Select All', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'id' => 'wc_settings_select_int_all',
                'class' => 'fedex_small_all_int_services',
            ),
            'fedex_small_home_dlvry' => array(
                'name' => __('FedEx Home Delivery', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_home_delivery',
                'class' => 'fedex_small_quotes_services',
            ),
            'fedex_small_int_gnd' => array(
                'name' => __('FedEx International Ground', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_int_ground',
                'class' => 'fedex_small_int_quotes_services',
            ),
            'fedex_small_home_dlvry_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_home_delivery_markup',
                'class' => 'fedex_small_quotes_services_markup',
            ),
            'fedex_small_int_gnd_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_int_ground_markup',
                'class' => 'fedex_small_int_quotes_services_markup',
            ),
            'fedex_small_gnd' => array(
                'name' => __('FedEx Ground', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_ground',
                'class' => 'fedex_small_quotes_services',
            ),
            'fedex_small_int_eco' => array(
                'name' => __('FedEx International Economy', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_int_economy',
                'class' => 'fedex_small_int_quotes_services',
            ),
            'fedex_small_gnd_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_ground_markup',
                'class' => 'fedex_small_quotes_services_markup',
            ),
            'fedex_small_int_eco_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_int_economy_markup',
                'class' => 'fedex_small_int_quotes_services_markup',
            ),
            'fedex_small_saver' => array(
                'name' => __('FedEx Express Saver', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_express_saver',
                'class' => 'fedex_small_quotes_services',
            ),
            'fedex_small_eco_dist' => array(
                'name' => __('FedEx International Economy Distribution', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_int_eco_distribution',
                'class' => 'fedex_small_int_quotes_services',
            ),
            'fedex_small_saver_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_express_saver_markup',
                'class' => 'fedex_small_quotes_services_markup',
            ),
            'fedex_small_eco_dist_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_int_eco_distribution_markup',
                'class' => 'fedex_small_int_quotes_services_markup',
            ),
            'fedex_small_2day_srvc' => array(
                'name' => __('FedEx 2Day', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_2_day',
                'class' => 'fedex_small_quotes_services',
            ),
            'fedex_small_eco_freight' => array(
                'name' => __('FedEx International Economy Freight', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_int_eco_freight',
                'class' => 'fedex_small_int_quotes_services',
            ),
            'fedex_small_2day_srvc_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_2_day_markup',
                'class' => 'fedex_small_quotes_services_markup',
            ),
            'fedex_small_eco_freight_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_int_eco_freight_markup',
                'class' => 'fedex_small_int_quotes_services_markup',
            ),
            'fedex_small_2day_am' => array(
                'name' => __('FedEx 2Day AM', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_2_day_AM',
                'class' => 'fedex_small_quotes_services',
            ),
            'fedex_small_first' => array(
                'name' => __('FedEx International First', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_int_first',
                'class' => 'fedex_small_int_quotes_services',
            ),
            'fedex_small_2day_am_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_2_day_AM_markup',
                'class' => 'fedex_small_quotes_services_markup',
            ),
            'fedex_small_first_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_int_first_markup',
                'class' => 'fedex_small_int_quotes_services_markup',
            ),
            'fedex_small_st_overnight' => array(
                'name' => __('FedEx Standard Overnight', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_standard_overnight',
                'class' => 'fedex_small_quotes_services',
            ),
            'fedex_small_priority' => array(
                'name' => __('FedEx International Priority', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_int_priority',
                'class' => 'fedex_small_int_quotes_services',
            ),
            'fedex_small_st_overnight_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_standard_overnight_markup',
                'class' => 'fedex_small_quotes_services_markup',
            ),
            'fedex_small_priority_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_int_priority_markup',
                'class' => 'fedex_small_int_quotes_services_markup',
            ),
            'fedex_small_pr_overnight' => array(
                'name' => __('FedEx Priority Overnight', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_priority_overnight',
                'class' => 'fedex_small_quotes_services',
            ),
            'fedex_small_pr_dist' => array(
                'name' => __('FedEx International Priority Distribution', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_int_priority_distribution',
                'class' => 'fedex_small_int_quotes_services',
            ),
            'fedex_small_pr_overnight_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%) ', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_priority_overnight_markup',
                'class' => 'fedex_small_quotes_services_markup',
            ),
            'fedex_small_pr_dist_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%) ', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_int_priority_distribution_markup',
                'class' => 'fedex_small_int_quotes_services_markup',
            ),
            'fedex_small_fst_overnight' => array(
                'name' => __('FedEx First Overnight', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_first_overnight',
                'class' => 'fedex_small_quotes_services',
            ),
            'fedex_small_pr_fr' => array(
                'name' => __('FedEx International Priority Freight', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_int_priority_freight',
                'class' => 'fedex_small_int_quotes_services',
            ),
            'fedex_small_fst_overnight_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_first_overnight_markup',
                'class' => 'fedex_small_quotes_services_markup',
            ),
            'fedex_small_pr_fr_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_int_priority_freight_markup',
                'class' => 'fedex_small_int_quotes_services_markup',
            ),
            'fedex_small_smart_post' => array(
                'name' => __('FedEx SmartPost', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_smart_post',
                'class' => 'fedex_small_quotes_services',
            ),
            'fedex_small_int_distribution' => array(
                'name' => __('FedEx International Distribution Freight', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_int_distribution_freight',
                'class' => 'fedex_small_int_quotes_services',
            ),
            'fedex_small_smart_post_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%) ', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_smart_post_markup',
                'class' => 'fedex_small_quotes_services_markup',
            ),
            'fedex_small_int_distribution_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_int_distribution_freight_markup',
                'class' => 'fedex_small_int_quotes_services_markup',
            ),
            'fedex_small_one_rate_domastic_srvcs' => array(
                'name' => __('One Rate Services', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'id' => 'fedex_small_dom_srvc_hdngggg',
                'class' => 'dom_int_srvc_hdng'
            ),
            'fedex_small_one_rate_domastic_srvcs_after' => array(
                'name' => __('', 'fedex_small_one_rate_domastic_srvcs_after'),
                'type' => 'text',
                'class' => 'hidden fedex_small_one_rate_hide_me',
            ),
            'fedex_small_one_rate_select_all_services' => array(
                'name' => __('Select All', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'id' => 'wc_settings_one_rate_select_all_',
                'class' => 'fedex_small_one_rate_all_services fedex_small_one_rate_quotes_services one_rate_click',
            ),
            'fedex_small_one_rate_select_all_services_after' => array(
                'name' => __('', 'fedex_small_one_rate_select_all_services_after'),
                'type' => 'text',
                'class' => 'hidden fedex_small_one_rate_hide_me',
            ),
            'fedex_small_one_rate_saver' => array(
                'name' => __('FedEx Express Saver - One Rate', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_one_rate_express_saver',
                'class' => 'fedex_small_one_rate_quotes_services one_rate_checkbox one_rate_click',
            ),
            'fedex_small_one_rate_saver_after' => array(
                'name' => __('', 'fedex_small_one_rate_saver_after'),
                'type' => 'text',
                'class' => 'hidden fedex_small_one_rate_hide_me',
            ),
            'fedex_small_saver_onerate_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_saver_onerate_markup',
                'class' => 'fedex_small_quotes_services_markup ',
            ),
            'fedex_small_saver_onerate_markup_after' => array(
                'name' => __('', 'fedex_small_saver_onerate_markup_after'),
                'type' => 'text',
                'class' => 'hidden fedex_small_one_rate_hide_me',
            ),
            'fedex_smallone_rate_2day_srvc' => array(
                'name' => __('FedEx 2Day - One Rate', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_one_rate_2_day',
                'class' => 'fedex_small_one_rate_quotes_services one_rate_checkbox one_rate_click',
            ),
            'fedex_smallone_rate_2day_srvc_after' => array(
                'name' => __('', 'fedex_smallone_rate_2day_srvc_after'),
                'type' => 'text',
                'class' => 'hidden fedex_small_one_rate_hide_me',
            ),
            'fedex_small_2day_srvc_onerate_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_2day_srvc_onerate_markup',
                'class' => 'fedex_small_quotes_services_markup',
            ),
            'fedex_small_2day_srvc_onerate_markup_after' => array(
                'name' => __('', 'fedex_small_2day_srvc_onerate_markup_after'),
                'type' => 'text',
                'class' => 'hidden fedex_small_one_rate_hide_me',
            ),
            'fedex_small_one_rate_2day_am' => array(
                'name' => __('FedEx 2Day AM - One Rate', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_one_rate_2_day_AM',
                'class' => 'fedex_small_one_rate_quotes_services one_rate_checkbox one_rate_click',
            ),
            'fedex_small_one_rate_2day_am_after' => array(
                'name' => __('', 'fedex_small_one_rate_2day_am_after'),
                'type' => 'text',
                'class' => 'hidden fedex_small_one_rate_hide_me',
            ),
            'fedex_small_2day_am_onerate_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_2day_am_onerate_markup',
                'class' => 'fedex_small_quotes_services_markup',
            ),
            'fedex_small_2day_am_onerate_markup_after' => array(
                'name' => __('', 'fedex_small_2day_am_onerate_markup_after'),
                'type' => 'text',
                'class' => 'hidden fedex_small_one_rate_hide_me',
            ),
            'fedex_small_one_rate_st_overnight' => array(
                'name' => __('FedEx Standard Overnight - One Rate', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_one_rate_standard_overnight',
                'class' => 'fedex_small_one_rate_quotes_services one_rate_checkbox one_rate_click',
            ),
            'fedex_small_one_rate_st_overnight_after' => array(
                'name' => __('', 'fedex_small_one_rate_st_overnight_after'),
                'type' => 'text',
                'class' => 'hidden fedex_small_one_rate_hide_me',
            ),
            'fedex_small_st_overnight_onerate_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_st_overnight_onerate_markup',
                'class' => 'fedex_small_quotes_services_markup',
            ),
            'fedex_small_st_overnight_onerate_markup_after' => array(
                'name' => __('', 'fedex_small_st_overnight_onerate_markup_after'),
                'type' => 'text',
                'class' => 'hidden fedex_small_one_rate_hide_me',
            ),
            'fedex_small_one_rate_pr_overnight' => array(
                'name' => __('FedEx Priority Overnight - One Rate', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_one_rate_priority_overnight',
                'class' => 'fedex_small_one_rate_quotes_services one_rate_checkbox one_rate_click',
            ),
            'fedex_small_one_rate_pr_overnight_after' => array(
                'name' => __('', 'fedex_small_one_rate_pr_overnight_after'),
                'type' => 'text',
                'class' => 'hidden fedex_small_one_rate_hide_me',
            ),
            'fedex_small_pr_overnight_onerate_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_pr_overnight_onerate_markup',
                'class' => 'fedex_small_quotes_services_markup',
            ),
            'fedex_small_pr_overnight_onerate_markup_after' => array(
                'name' => __('', 'fedex_small_pr_overnight_onerate_markup_after'),
                'type' => 'text',
                'class' => 'hidden fedex_small_one_rate_hide_me',
            ),
            'fedex_small_one_rate_fst_overnight' => array(
                'name' => __('FedEx First Overnight - One Rate', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_one_rate_first_overnight',
                'class' => 'fedex_small_one_rate_quotes_services one_rate_checkbox one_rate_click',
            ),
            'fedex_small_one_rate_fst_overnight_after' => array(
                'name' => __('', 'fedex_small_one_rate_fst_overnight_after'),
                'type' => 'text',
                'class' => 'hidden fedex_small_one_rate_hide_me',
            ),
            'fedex_small_fst_overnight_onerate_markup' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes_markup'),
                'type' => 'text',
                'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-fedex_small_quotes_markup'),
                'placeholder' => 'Markup',
                'id' => 'fedex_small_fst_overnight_onerate_markup',
                'class' => 'fedex_small_quotes_services_markup',
            ),
            'fedex_small_fst_overnight_onerate_markup_after' => array(
                'name' => __('', 'fedex_small_fst_overnight_onerate_markup_after'),
                'type' => 'text',
                'class' => 'hidden fedex_small_one_rate_hide_me',
            ),
            'price_sort_fedex_small' => array(
                'name' => __("Don't sort shipping methods by price", 'woocommerce-settings-ups_small_quotes'),
                'type' => 'checkbox',
                'desc' => 'By default, the plugin will sort all shipping methods by price in ascending order.',
                'id' => 'shipping_methods_do_not_sort_by_price'
            ),

            // Package rating method when Standard Box Sizes isn't in use
            'fedex_small_packaging_method_label' => array(
                'name' => __('Package rating method when Standard Box Sizes isn\'t in use', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'text',
                'id' => 'fedex_small_packaging_method_label'
            ),
            'fedex_small_packaging_method' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'radio',
                'default' => $package_type_default,
                'options' => $package_type_options,
                'id' => 'fedex_small_packaging_method',
            ),

            'service_fedex_small_estimates_title' => array(
                'name' => __('Delivery Estimate Options ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                'type' => 'text',
                'desc' => '',
                'id' => 'service_fedex_small_estimates_title'
            ),
            'dont_show_estimates_fedex_small' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'radio',
                'class' => "",
                'default' => "dont_show_estimates",
                'options' => array(
                    'dont_show_estimates' => __("Don't display delivery estimates.", 'woocommerce'),
                    'delivery_days' => __('Display estimated number of days until delivery.', 'woocommerce'),
                    'delivery_date' => __('Display estimated delivery date.', 'woocommerce'),
                ),
                'id' => 'fedex_small_delivery_estimates',
            ),
            //**Start: Cut Off Time & Ship Date Offset
            'cutOffTime_shipDateOffset_fedex_small' => array(
                'name' => __('Cut Off Time & Ship Date Offset ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                'type' => 'text',
                'class' => 'hidden',
                'desc' => $cutOffTime_shipDateOffset_package_required,
                'id' => 'fedex_small_cutOffTime_shipDateOffset'
            ),
            'orderCutoffTime_fedex_small' => array(
                'name' => __('Order Cut Off Time ', 'woocommerce-settings-fedex_small_freight_orderCutoffTime'),
                'type' => 'text',
                'placeholder' => '--:-- --',
                'desc' => 'Enter the cut off time (e.g. 2.00) for the orders. Orders placed after this time will be quoted as shipping the next business day.',
                'id' => 'fedex_small_orderCutoffTime',
                'class' => $disable_cutOffTime_shipDateOffset,
            ),
            'shipmentOffsetDays_fedex_small' => array(
                'name' => __('Fulfilment Offset Days ', 'woocommerce-settings-fedex_small_shipmentOffsetDays'),
                'type' => 'text',
                'desc' => 'The number of days the ship date needs to be moved to allow the processing of the order.',
                'placeholder' => 'Fulfilment Offset Days, e.g. 2',
                'id' => 'fedex_small_shipmentOffsetDays',
                'class' => $disable_cutOffTime_shipDateOffset,
            ),
            'all_shipment_days_fedex_small' => array(
                'name' => __("What days do you ship orders?", 'woocommerce-settings-ups_small_quotes'),
                'type' => 'checkbox',
                'desc' => 'Select All',
                'class' => "all_shipment_days_fedex_small $disable_cutOffTime_shipDateOffset",
                'id' => 'all_shipment_days_fedex_small'
            ),
            'monday_shipment_day_fedex_small' => array(
                'name' => __("", 'woocommerce-settings-ups_small_quotes'),
                'type' => 'checkbox',
                'desc' => 'Monday',
                'class' => "fedex_small_shipment_day $disable_cutOffTime_shipDateOffset",
                'id' => 'monday_shipment_day_fedex_small'
            ),
            'tuesday_shipment_day_fedex_small' => array(
                'name' => __("", 'woocommerce-settings-ups_small_quotes'),
                'type' => 'checkbox',
                'desc' => 'Tuesday',
                'class' => "fedex_small_shipment_day $disable_cutOffTime_shipDateOffset",
                'id' => 'tuesday_shipment_day_fedex_small'
            ),
            'wednesday_shipment_day_fedex_small' => array(
                'name' => __("", 'woocommerce-settings-ups_small_quotes'),
                'type' => 'checkbox',
                'desc' => 'Wednesday',
                'class' => "fedex_small_shipment_day $disable_cutOffTime_shipDateOffset",
                'id' => 'wednesday_shipment_day_fedex_small'
            ),
            'thursday_shipment_day_fedex_small' => array(
                'name' => __("", 'woocommerce-settings-ups_small_quotes'),
                'type' => 'checkbox',
                'desc' => 'Thursday',
                'class' => "fedex_small_shipment_day $disable_cutOffTime_shipDateOffset",
                'id' => 'thursday_shipment_day_fedex_small'
            ),
            'friday_shipment_day_fedex_small' => array(
                'name' => __("", 'woocommerce-settings-ups_small_quotes'),
                'type' => 'checkbox',
                'desc' => 'Friday',
                'class' => "fedex_small_shipment_day $disable_cutOffTime_shipDateOffset",
                'id' => 'friday_shipment_day_fedex_small'
            ),
            // Start Transit days            
            'fedex_sm_ground_transit_label' => array(
                'name' => __('Ground transit time restriction', 'woocommerce-settings-fedex_small'),
                'type' => 'text',
                'class' => 'hidden',
                'desc' => $transit_package_required,
                'id' => 'fedex_sm_ground_transit_label'
            ),
            'restrict_days_transit_package_fedex_small' => array(
                'name' => __('Enter the number of transit days to restrict ground service to. Leave blank to disable this feature.', 'ground-transit-settings-ground_transit'),
                'type' => 'text',
                'class' => $disable_transit,
                'id' => 'restrict_days_transit_package_fedex_small'
            ),
            'restrict_transit_fedex_small_packages' => array(
                'name' => __('', 'woocommerce-settings-fedex_small'),
                'type' => 'radio',
                'id' => 'restrict_transit_fedex_small_packages',
                'class' => "$disable_transit restrict_by_calendar_days_in_transit_1st_option",
                'options' => array(
                    'TransitTimeInDays' => __('Restrict by the carrier\'s in transit days metric.', 'woocommerce'),
                    'CalenderDaysInTransit' => __('Restrict by the calendar days in transit.', 'woocommerce'),
                ),
                'id' => 'restrict_radio_btn_transit_fedex_small',
            ),
            // End Transit days 
            /*
             * FedEx Residentail Delivery, Handeling Fee And Hazardous Fee
             */
            'residential_delivery_options_label' => array(
                'name' => __('Residential Delivery', 'woocommerce-settings-fedex_small'),
                'type' => 'text',
                'class' => 'hidden',
                'id' => 'residential_delivery_options_label'
            ),
            'fedex_small_residential_delivery' => array(
                'name' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'checkbox',
                'desc' => __('Always quote as residential delivery.', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_quote_as_residential_delivery'
            ),
            // Auto-detect residential addresses notification
            'avaibility_auto_residential' => array(
                'name' => __('', 'woocommerce-settings-fedex_small'),
                'type' => 'text',
                'class' => 'hidden',
                'desc' => "Click <a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/'>here</a> to add the Auto-detect residential addresses module. (<a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/#documentation'>Learn more</a>)",
                'id' => 'avaibility_auto_residential'
            ),
            // Use my standard box sizes notification
            'avaibility_box_sizing' => array(
                'name' => __('Use my standard box sizes', 'woocommerce-settings-fedex_small'),
                'type' => 'text',
                'class' => 'hidden',
                'desc' => "Click <a target='_blank' href='https://eniture.com/woocommerce-standard-box-sizes/'>here</a> to add the Standard Box Sizes module. (<a target='_blank' href='https://eniture.com/woocommerce-standard-box-sizes/#documentation'>Learn more</a>)",
                'id' => 'avaibility_box_sizing'
            ),
            //                       Start Hazardous Material
            'fedex_small_hazardous_fee' => array(
                'name' => __('Hazardous material settings', 'woocommerce-settings-fedex_small'),
                'type' => 'text',
                'class' => 'hidden',
                'desc' => $hazardous_package_required,
                'id' => 'fedex_small_hazardous_fee'
            ),
            'fedex_small_hazardous_materials_shipments' => array(
                'name' => __('', 'woocommerce-settings-fedex_small'),
                'type' => 'checkbox',
                'desc' => 'Only quote ground service for hazardous materials shipments.',
                'class' => $disable_hazardous,
                'id' => 'fedex_small_hazardous_materials_shipments',
            ),
            'en_fedex_small_ground_hazardous_material_fee' => array(
                'name' => __('Ground Hazardous Material Fee', 'ground-transit-settings-ground_transit'),
                'type' => 'text',
                'desc' => 'Enter an amount, e.g 20. or Leave blank to disable.',
                'class' => $disable_hazardous,
                'id' => 'en_fedex_small_ground_hazardous_material_fee'
            ),
            'en_fedex_small_air_hazardous_material_fee' => array(
                'name' => __('Air Hazardous Material Fee', 'ground-transit-settings-ground_transit'),
                'type' => 'text',
                'desc' => 'Enter an amount, e.g 20. or Leave blank to disable.',
                'class' => $disable_hazardous,
                'id' => 'en_fedex_small_air_hazardous_material_fee'
            ),
            // End Hazardous Material
            'fedex_small_hand_free' => array(
                'name' => __('Handling Fee / Markup ', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'text',
                'desc' => '<span class="desc_text_style">Amount excluding tax. Enter an amount, e.g 3.75, or a percentage, e.g, 5%. Leave blank to disable.</span>',
                'id' => 'fedex_small_hand_fee_mark_up'
            ),
            'publish_negotiated_fedex_small_rates' => array(
                'name' => __('Rate source', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'radio',
                'options' => array(
                    'negotiated' => __('Use my negotiated rates.', 'woocommerce'),
                    'publish' => __('Use retail (list) rates.', 'woocommerce'),
                ),
                'id' => 'wc_pulish_negotiate_fedex_small',
            ),
            'en_fedex_spq_enable_logs' => [
                'name' => __("Enable Logs  ", 'woocommerce-settings-fedex_ltl_quotes'),
                'type' => 'checkbox',
                'desc' => 'When checked, the Logs page will contain up to 25 of the most recent transactions.',
                'id' => 'en_fedex_spq_enable_logs'
            ],
            // Ignore items with the following Shipping Class(es) By (K)
            'en_ignore_items_through_freight_classification' => array(
                'name' => __('Ignore items with the following Shipping Class(es)', 'woocommerce-settings-wwe_quetes'),
                'type' => 'text',
                'desc' => "Enter the <a target='_blank' href = '" . get_admin_url() . "admin.php?page=wc-settings&tab=shipping&section=classes'>Shipping Slug</a> you'd like the plugin to ignore. Use commas to separate multiple Shipping Slug.",
                'id' => 'en_ignore_items_through_freight_classification'
            ),
            'allow_other_plugins_fedex_small' => array(
                'name' => __('Allow other plugins to show quotes ', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'select',
                'default' => '3',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'fedex_small_allow_other_plugins_option',
                'options' => array(
                    'no' => __('NO', 'NO'),
                    'yes' => __('YES', 'YES')
                )
            ),
            'unable_retrieve_shipping_clear_fedex_small' => array(
                'title' => __('', 'woocommerce'),
                'name' => __('', 'woocommerce-settings-fedex-small-quotes'),
                'desc' => '',
                'id' => 'wc_unable_retrieve_shipping_clear_fedex_small',
                'css' => '',
                'default' => '',
                'type' => 'title',
            ),
            'unable_retrieve_shipping_fedex_small' => array(
                'name' => __('Checkout options if the plugin fails to return a rate ', 'woocommerce-settings-ups-small-quotes'),
                'type' => 'title',
                'desc' => 'When the plugin is unable to retrieve shipping quotes and no other shipping options are provided by an alternative source:',
                'id' => 'wc_settings_unable_retrieve_shipping_fedex_small'
            ),
            'pervent_checkout_proceed_fedex_small' => array(
                'name' => __('', 'woocommerce-settings-fedex-small-quotes'),
                'type' => 'radio',
                'id' => 'pervent_checkout_proceed_fedex_small_packages',
                'options' => array(
                    'allow' => __('', 'woocommerce'),
                    'prevent' => __('', 'woocommerce'),
                ),
                'id' => 'wc_pervent_proceed_checkout_eniture',
            ),
            'section_end_quote' => array(
                'type' => 'sectionend',
                'id' => 'wc_settings_quote_section_end'
            )
        );
        return $settings;
    }

}
