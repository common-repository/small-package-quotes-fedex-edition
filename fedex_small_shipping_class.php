<?php

/**
 * FedEx Small ShippingCtrl Class
 *
 * @package     FedEx Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialization Function
 */
function fedex_small_init()
{
    if (!class_exists('WC_fedex_small')) {

        /**
         * FedEx Small ShippingCtrl Calculation Class
         */
        class WC_fedex_small extends WC_Shipping_Method
        {

            /** $smpkgFoundErr */
            public $smpkgFoundErr = array();

            /** $smpkgQuoteErr */
            public $smpkgQuoteErr = array();
            public $order_detail;
            public $is_autoresid;
            public $accessorials;
            public $helper_obj;
            public $fedex_small_res_inst;
            public $api_response_fedex_bins;
            public $instore_pickup_and_local_delivery;
            public $group_small_shipments;
            public $web_service_inst;
            public $package_plugin;
            public $InstorPickupLocalDelivery;
            public $woocommerce_package_rates;
            public $quote_settings;
            public $shipment_type;
            public $eniture_rates;
            public $VersionCompat;
            public $en_not_returned_the_quotes = FALSE;
            public $minPrices = [];
            // Virtual Products
            public $en_fdo_meta_data_third_party = [];
            // FDO
            public $en_fdo_meta_data = [];

            /**
             * Woocommerce ShippingCtrl Field Attributes
             * @param $instance_id
             */
            public function __construct($instance_id = 0)
            {
                $title = get_option('wc_settings_fedex_small_label_as');
                (!$title) ? $title = "Small Package Quotes For FedEx" : '';
                $this->id = 'fedex_small';
                $this->helper_obj = new En_Fed_Sml_Helper_Class();
                $this->instance_id = absint($instance_id);
                $this->method_title = __('Small Package Quotes for FedEx');
                $this->method_description = __('This Small Package Quotes plugin by Eniture Technology retrieves parcel quotes for FedEx customers.');
                $this->supports = array(
                    'shipping-zones',
                    'instance-settings',
                    'instance-settings-modal',
                );
                $this->enabled = "yes";
                $this->title = "Small Package Quotes For FedEx";
                $this->init();
                add_action('woocommerce_checkout_update_order_review', array($this, 'calculate_shipping'));
            }

            /**
             * Update Fedex Small Woocommerce ShippingCtrl Settings
             */
            function init()
            {
                $this->init_form_fields();
                $this->init_settings();
                add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
            }

            /**
             * Enable Woocommerce ShippingCtrl For Fedex Small
             */
            function init_form_fields()
            {
                $this->instance_form_fields = array(
                    'enabled' => array(
                        'title' => __('Enable / Disable', 'fedex_small'),
                        'type' => 'checkbox',
                        'label' => __('Rates retrieved by the Fedex Small Package Quotes plugin from Eniture Technology.', 'fedex_small'),
                        'default' => 'no',
                        'id' => 'fedex_small_enable_disable_shipping'
                    )
                );
            }

            /**
             * Multi shipment query
             * @param array $en_rates
             * @param string $accessorial
             */
            public function en_multi_shipment($en_rates, $accessorial, $origin)
            {
                $accessorial .= '_fedex_small';
                $en_rates = (isset($en_rates) && (is_array($en_rates))) ? array_slice($en_rates, 0, 1) : [];
                $total_cost = array_sum($this->VersionCompat->enArrayColumn($en_rates, 'cost'));

                !$total_cost > 0 ? $this->en_not_returned_the_quotes = TRUE : '';

                $en_rates = !empty($en_rates) ? reset($en_rates) : [];
                $this->minPrices[$origin] = $en_rates;
                // FDO
                $this->en_fdo_meta_data[$origin] = (isset($en_rates['meta_data']['en_fdo_meta_data'])) ? $en_rates['meta_data']['en_fdo_meta_data'] : [];

                if (isset($this->eniture_rates[$accessorial])) {
                    $this->eniture_rates[$accessorial]['cost'] += $total_cost;
                } else {
                    $this->eniture_rates[$accessorial] = [
                        'id' => $accessorial,
                        'label' => 'Shipping',
                        'cost' => $total_cost,
                        'label_sufex' => str_split($accessorial),
                        'plugin_name' => 'fedexSmall',
                        'plugin_type' => 'small',
                        'owned_by' => 'eniture'
                    ];
                }
            }

            /**
             * Virtual Products
             */
            public function en_virtual_products()
            {
                global $woocommerce;
                $products = $woocommerce->cart->get_cart();
                $items = $product_name = [];
                foreach ($products as $key => $product_obj) {
                    $product = $product_obj['data'];
                    $is_virtual = $product->get_virtual();

                    if ($is_virtual == 'yes') {
                        $attributes = $product->get_attributes();
                        $product_qty = $product_obj['quantity'];
                        $product_title = str_replace(array("'", '"'), '', $product->get_title());
                        $product_name[] = $product_qty . " x " . $product_title;

                        $meta_data = [];
                        if (!empty($attributes)) {
                            foreach ($attributes as $attr_key => $attr_value) {
                                $meta_data[] = [
                                    'key' => $attr_key,
                                    'value' => $attr_value,
                                ];
                            }
                        }

                        $items[] = [
                            'id' => $product_obj['product_id'],
                            'name' => $product_title,
                            'quantity' => $product_qty,
                            'price' => $product->get_price(),
                            'weight' => 0,
                            'length' => 0,
                            'width' => 0,
                            'height' => 0,
                            'type' => 'virtual',
                            'product' => 'virtual',
                            'sku' => $product->get_sku(),
                            'attributes' => $attributes,
                            'variant_id' => 0,
                            'meta_data' => $meta_data,
                        ];
                    }
                }

                $virtual_rate = [];

                if (!empty($items)) {
                    $virtual_rate = [
                        'id' => 'en_virtual_rate',
                        'label' => 'Virtual Quote',
                        'cost' => 0,
                    ];

                    $virtual_fdo = [
                        'plugin_type' => 'small',
                        'plugin_name' => 'fedex_small',
                        'accessorials' => '',
                        'items' => $items,
                        'address' => '',
                        'handling_unit_details' => '',
                        'rate' => $virtual_rate,
                    ];

                    $meta_data = [
                        'sender_origin' => 'Virtual Product',
                        'product_name' => wp_json_encode($product_name),
                        'en_fdo_meta_data' => $virtual_fdo,
                    ];

                    $virtual_rate['meta_data'] = $meta_data;

                }

                return $virtual_rate;
            }

            /**
             * Single shipment query
             * @param array $en_rates
             * @param string $accessorial
             */
            public function en_single_shipment($en_rates, $accessorial, $origin)
            {
                $this->eniture_rates = array_merge($this->eniture_rates, $en_rates);
            }

            /**
             * Calculate ShippingCtrl Rates For Fedex Small
             * @param $package
             * @return array
             */
            public function calculate_shipping($package = array(), $eniture_admin_order_action = false)
            {
                if (is_admin() && !wp_doing_ajax() && !$eniture_admin_order_action) {
                    return [];
                }

                $package = apply_filters('en_show_rates_when_click_on_update_btn', $package);
                $this->package_plugin = get_option('fedex_small_package');

                $label_sufex_arr = array();
                $Fedex_Small_Auto_Residential_Detection = new Fedex_Small_Auto_Residential_Detection();

                $coupn = WC()->cart->get_coupons();
                if (isset($coupn) && !empty($coupn)) {
                    $freeShipping = $this->fedexSmpkgFreeShipping($coupn);
                    if ($freeShipping == 'y')
                        return FALSE;
                }

                // -100% handling fee
                $handling_fee = get_option('fedex_small_hand_fee_mark_up');
                if ($handling_fee == '-100%') {
                    $rates = array(
                        'id' => 'fedex_small:' . 'free',
                        'label' => 'Free Shipping',
                        'cost' => 0,
                        'plugin_name' => 'fedexSmall',
                        'plugin_type' => 'small',
                        'owned_by' => 'eniture'
                    );
                    $this->add_rate($rates);
                    
                    return [];
                }

                $fedex_small_woo_obj = new Fedex_Small_Woo_Update_Changes();
                $dest_zipcode = (strlen(WC()->customer->get_shipping_postcode()) > 0) ? WC()->customer->get_shipping_postcode() : $fedex_small_woo_obj->fedex_small_postcode();

                if(empty($dest_zipcode)){
                    $receiver_country = (strlen(WC()->customer->get_shipping_country()) > 0) ? WC()->customer->get_shipping_country() : $fedex_small_woo_obj->fedex_small_getCountry();
                    if($receiver_country == 'AE'){
                        $dest_zipcode = '00000';
                    }
                }

                $get_packg_obj = new FedEx_Small_Shipping_Get_Package();
                $fedex_small_res_inst = new FedEx_Get_Shipping_Quotes();
                $this->VersionCompat = new VersionCompat();

                $this->fedex_small_res_inst = $fedex_small_res_inst;
                $this->web_service_inst = $fedex_small_res_inst;

                $this->get_hazardous_fields();

                $this->instore_pickup_and_local_delivery = FALSE;

                $rates = array();
                $rateArray = array();

                $quotesArray = array();
                $quotes = array();
                $SmPkgWebServiceArr = array();
                $fedex_small_package = "";
                $fedex_small_package = $get_packg_obj->group_fedex_small_shipment($package, $fedex_small_res_inst, $dest_zipcode);
                $no_param_multi_ship = 0;
                $services = array();
                $services_list = array();

                // Suppress small rates when weight threshold is met
                $supress_parcel_rates = apply_filters('en_suppress_parcel_rates_hook', '');
                if (!empty($fedex_small_package) && is_array($fedex_small_package) && $supress_parcel_rates) {
                    foreach ($fedex_small_package as $org_id => $pckg) {
                        $total_shipment_weight = 0;

                        $shipment_items = !empty($pckg['items']) ? $pckg['items'] : []; 
                        foreach ($shipment_items as $item) {
                            $total_shipment_weight += (floatval($item['productWeight']) * $item['productQty']);
                        }

                        $fedex_small_package[$org_id]['shipment_weight'] = $total_shipment_weight;
                        $weight_threshold = get_option('en_weight_threshold_lfq');
                        $weight_threshold = isset($weight_threshold) && $weight_threshold > 0 ? $weight_threshold : 150;
                        
                        if ($total_shipment_weight >= $weight_threshold) {
                            $fedex_small_package[$org_id]['is_shipment'] = 'ltl';
                            $fedex_small_package[$org_id]['origin']['ptype'] = 'ltl';
                        }
                    }
                }

                $domestic_services = apply_filters('fedex_small_domestic_services', array());
                $one_rate_services = apply_filters('fedex_small_one_rate_services', array());
                $intrntal_services = apply_filters('fedex_small_international_services', array());

                $service_flag = (!empty($domestic_services) || (!empty($one_rate_services)) || (!empty($intrntal_services))) ? TRUE : FALSE;

                if (isset($fedex_small_package) && !empty($fedex_small_package) && $service_flag) {

                    $services_list['domestic_services'] = $domestic_services;
                    $services_list['one_rate_services'] = $one_rate_services;
                    $services_list['intrntal_services'] = $intrntal_services;

                    $SmPkgWebServiceArr = $fedex_small_res_inst->fedex_Small_shipping_array($fedex_small_package, $package, $services_list, $this->package_plugin);

                    $counter = 0;
                    $domestic_international = [];
                    foreach ($SmPkgWebServiceArr as $locId => $sPackage) {

                        if ($sPackage != 'ltl') {

                            $services = [];

                            $EnFedExSmallTransitDays = new EnFedExSmallTransitDays();

                            $markup = (isset($sPackage['markup'])) ? $sPackage['markup'] : [];
                            $package_bins = (isset($sPackage['bins'])) ? $sPackage['bins'] : [];
                            $en_box_fee = (isset($sPackage['en_box_fee'])) ? $sPackage['en_box_fee'] : [];
                            $en_multi_box_qty = (isset($sPackage['count'])) ? $sPackage['count'] : [];
                            $fedex_bins = (isset($sPackage['fedex_bins'])) ? $sPackage['fedex_bins'] : [];
                            $hazardous_status = (isset($sPackage['hazardous_status'])) ? $sPackage['hazardous_status'] : '';
                            $package_bins = !empty($fedex_bins) ? $package_bins + $fedex_bins : $package_bins;
                            if (!isset($sPackage['senderZip'])) {
                                continue;
                            }

                            $this->fedex_small_res_inst->product_detail[$sPackage['senderZip']]['product_name'] = json_encode($sPackage['product_name']);
                            $this->fedex_small_res_inst->product_detail[$sPackage['senderZip']]['products'] = $sPackage['products'];
                            $this->fedex_small_res_inst->product_detail[$sPackage['senderZip']]['sender_origin'] = $sPackage['sender_origin'];
                            $this->fedex_small_res_inst->product_detail[$sPackage['senderZip']]['package_bins'] = $package_bins;
                            $this->fedex_small_res_inst->product_detail[$sPackage['senderZip']]['en_box_fee'] = $en_box_fee;
                            $this->fedex_small_res_inst->product_detail[$sPackage['senderZip']]['en_multi_box_qty'] = $en_multi_box_qty;
                            $this->fedex_small_res_inst->product_detail[$sPackage['senderZip']]['hazardous_status'] = $hazardous_status;
                            $this->fedex_small_res_inst->product_detail[$sPackage['senderZip']]['markup'] = $markup;
                            $this->fedex_small_res_inst->product_detail[$sPackage['senderZip']]['exempt_ground_transit_restriction'] = (isset($sPackage['exempt_ground_transit_restriction'])) ? $sPackage['exempt_ground_transit_restriction'] : '';
                            $this->fedex_small_res_inst->product_detail[$sPackage['senderZip']]['origin_markup'] = $sPackage['origin_markup'];
                            $this->fedex_small_res_inst->product_detail[$sPackage['senderZip']]['product_level_markup'] = $sPackage['product_level_markup'];

                            // FDO
                            $en_fdo_meta_data = (isset($sPackage['en_fdo_meta_data'])) ? $sPackage['en_fdo_meta_data'] : '';
                            $this->fedex_small_res_inst->product_detail[$sPackage['senderZip']]['en_fdo_meta_data'] = $en_fdo_meta_data;

                            if (isset($sPackage['receiverCountry']) && $sPackage['receiverCountry'] != $sPackage['senderCountry']) {
                                $services['international'] = $intrntal_services;
                            }

                            if (isset($sPackage['receiverCountry']) && $sPackage['receiverCountry'] == $sPackage['senderCountry']) {
                                $services['domestic'] = $domestic_services;
                            }

                            $domestic_international[$locId] = $services;

                            if (isset($sPackage['forcefully_residential_delivery']) && $sPackage['forcefully_residential_delivery'] == 'on') {
                                $this->web_service_inst->forcefully_residential_delivery = TRUE;
                            }

                            if (isset($sPackage['forcefully_always_residential_delivery']) && $sPackage['forcefully_always_residential_delivery'] == 'on') {
                                $this->web_service_inst->forcefully_always_residential_delivery = TRUE;
                            }

                            $quotes[$locId] = $fedex_small_res_inst->fedex_small_get_quotes($sPackage, $this->package_plugin);

                            (isset($sPackage['hazardous_material'])) ? $quotes[$locId]->hazardous_material = TRUE : "";

                            $Fedex_Small_Auto_Residential_Detection = new Fedex_Small_Auto_Residential_Detection();
                            $label_sfx_rtrn = $Fedex_Small_Auto_Residential_Detection->filter_label_sufex_array_fedex_small($quotes[$locId]);
                            $label_sufex_arr = array_merge($label_sufex_arr, $label_sfx_rtrn);
                        }

                        $counter++;
                    }
                    if(isset($locId)) {
                        (isset($quotes[$locId]->InstorPickupLocalDelivery)) ? $this->InstorPickupLocalDelivery = $quotes[$locId]->InstorPickupLocalDelivery : '';
                        (isset($quotes[$locId]->one_rate_pricing->InstorPickupLocalDelivery)) ? $this->InstorPickupLocalDelivery = $quotes[$locId]->one_rate_pricing->InstorPickupLocalDelivery : '';
                        (isset($quotes[$locId]->home_ground_pricing->InstorPickupLocalDelivery)) ? $this->InstorPickupLocalDelivery = $quotes[$locId]->home_ground_pricing->InstorPickupLocalDelivery : '';
                        (isset($quotes[$locId]->weight_based_pricing->InstorPickupLocalDelivery)) ? $this->InstorPickupLocalDelivery = $quotes[$locId]->weight_based_pricing->InstorPickupLocalDelivery : '';
                        (isset($quotes[$locId]->weight_based_pricing_smart_post->InstorPickupLocalDelivery)) ? $this->InstorPickupLocalDelivery = $quotes[$locId]->weight_based_pricing_smart_post->InstorPickupLocalDelivery : '';
                    }
                }
                // Virtual products
                $virtual_rate = $this->en_virtual_products();
                $en_is_shipment = (count($quotes) > 1 || $no_param_multi_ship == 1) || $no_param_multi_ship == 1 || !empty($virtual_rate) ? 'en_multi_shipment' : 'en_single_shipment';
                $this->quote_settings['shipment'] = $en_is_shipment;
                $this->eniture_rates = [];

                $en_rates = $quotes;
                $one_rate['one_rate'] = $one_rate_services;

                foreach ($en_rates as $origin => $step_for_rates) {

                    $product_detail = (isset($this->fedex_small_res_inst->product_detail[$origin])) ? $this->fedex_small_res_inst->product_detail[$origin] : array();
                    (isset($domestic_international[$origin])) ? $services = $domestic_international[$origin] : '';
                    $filterd_rates = $fedex_small_res_inst->parse_fedex_small_output($step_for_rates, $services, $one_rate, $product_detail, $this->quote_settings);

                    $en_sorting_rates = (isset($filterd_rates['en_sorting_rates'])) ? $filterd_rates['en_sorting_rates'] : "";
                    if (isset($filterd_rates['en_sorting_rates']))
                        unset($filterd_rates['en_sorting_rates']);

                    if (is_array($filterd_rates) && !empty($filterd_rates)) {
                        foreach ($filterd_rates as $accessorial => $service) {
                            (!empty($filterd_rates[$accessorial])) ? array_multisort($en_sorting_rates[$accessorial], SORT_ASC, $filterd_rates[$accessorial]) : $en_sorting_rates[$accessorial] = [];
                            $this->$en_is_shipment($filterd_rates[$accessorial], $accessorial, $origin);
                        }
                    } else {
                        $this->en_not_returned_the_quotes = TRUE;
                    }
                }

                if ($this->en_not_returned_the_quotes) {
                    return [];
                }

                if ($en_is_shipment == 'en_single_shipment') {

                    // In-store pickup and local delivery
                    $instore_pickup_local_devlivery_action = apply_filters('fedex_small_quotes_plans_suscription_and_features', 'instore_pickup_local_devlivery');
                    if (isset($this->web_service_inst->en_wd_origin_array['suppress_local_delivery']) && $this->web_service_inst->en_wd_origin_array['suppress_local_delivery'] == "1" && (!is_array($instore_pickup_local_devlivery_action))) {
                        $this->eniture_rates = apply_filters('suppress_local_delivery', $this->eniture_rates, $this->web_service_inst->en_wd_origin_array, $this->package_plugin, $this->InstorPickupLocalDelivery);
                    }
                }
                $rad_status = true;
                $all_plugins = apply_filters('active_plugins', get_option('active_plugins'));
                if (stripos(implode($all_plugins), 'residential-address-detection.php') || is_plugin_active_for_network('residential-address-detection/residential-address-detection.php')) {
                    if(get_option('suspend_automatic_detection_of_residential_addresses') != 'yes') {
                        $rad_status = get_option('residential_delivery_options_disclosure_types_to') != 'not_show_r_checkout';
                    }
                }
                $accessorials = $rad_status == true ? ['R' => 'residential delivery'] : [];
                add_filter('woocommerce_package_rates', array($this, 'en_sort_woocommerce_available_shipping_methods'), 10, 2);

                $en_rates = $this->eniture_rates;

                // Images for FDO
                $image_urls = apply_filters('en_fdo_image_urls_merge', []);
                // Virtual products
                if (!empty($virtual_rate)) {
                    $en_virtual_fdo_meta_data[] = $virtual_rate['meta_data']['en_fdo_meta_data'];
                    $this->en_fdo_meta_data_third_party = !empty($this->en_fdo_meta_data_third_party) ? array_merge($this->en_fdo_meta_data_third_party, $en_virtual_fdo_meta_data) : $en_virtual_fdo_meta_data;
                }
                foreach ($en_rates as $accessorial => $rate) {

                    if ($en_is_shipment == 'en_single_shipment') {
                        $fedex_small_delivey_estimate = get_option('fedex_small_delivery_estimates');
                        if (isset($fedex_small_delivey_estimate) && !empty($fedex_small_delivey_estimate) && $fedex_small_delivey_estimate != 'dont_show_estimates') {

                            if ($fedex_small_delivey_estimate == 'delivery_date') {
                                $rate['label'] .= ' (Expected delivery by ' . date('m-d-Y', strtotime($rate['transit_time'])) . ' ' . date('h:i A', strtotime($rate['transit_time'])) . ')';
                            } else if ($fedex_small_delivey_estimate == 'delivery_days') {
                                $correct_word = ($rate['delivery_days'] == 1) ? 'is' : 'are';
                                $rate['label'] .= ' (Intransit days: ' . $rate['delivery_days'] . ')';
                            }
                        }
                    }

                    if (isset($rate['label_sufex']) && !empty($rate['label_sufex'])) {
                        // Custom work mgs4u ref ticket #46864896
                        if (has_filter('en_update_rate_through_cart_enhancement')) {
                            $rate = apply_filters('en_update_rate_through_cart_enhancement', $rate);
                        } else {
                            $label_sufex = array_intersect_key($accessorials, array_flip($rate['label_sufex']));
                            $rate['label'] .= (!empty($label_sufex)) ? ' with ' . implode(' and ', $label_sufex) : '';
                        }

                        // Order widget detail set
                        // FDO
                        if (isset($this->minPrices) && !empty($this->minPrices)) {
                            $rate['minPrices'] = $this->minPrices;
                            $rate['meta_data']['min_prices'] = wp_json_encode($this->minPrices);
                            $rate['meta_data']['en_fdo_meta_data']['data'] = array_values($this->en_fdo_meta_data);
                            // Virtual Products
                            (!empty($this->en_fdo_meta_data_third_party)) ? $rate['meta_data']['en_fdo_meta_data']['data'] = array_merge($rate['meta_data']['en_fdo_meta_data']['data'], $this->en_fdo_meta_data_third_party) : '';
                            $rate['meta_data']['en_fdo_meta_data']['shipment'] = 'multiple';
                            $rate['meta_data']['en_fdo_meta_data'] = wp_json_encode($rate['meta_data']['en_fdo_meta_data']);
                        } else {
                            $en_set_fdo_meta_data['data'] = [$rate['meta_data']['en_fdo_meta_data']];
                            $en_set_fdo_meta_data['shipment'] = 'sinlge';
                            $rate['meta_data']['en_fdo_meta_data'] = wp_json_encode($en_set_fdo_meta_data);
                        }

                        // Images for FDO
                        $rate['meta_data']['en_fdo_image_urls'] = wp_json_encode($image_urls);
                    }

                    if (isset($rate['cost']) && $rate['cost'] > 0) {
                        $rate['id'] = isset($rate['id']) && is_string($rate['id']) ? 'fedex_small:' . $rate['id'] : '';
                        $this->add_rate($rate);
                        $en_rates[$accessorial] = array_merge($en_rates[$accessorial], $rate);
                    }else if(is_numeric($rate['cost']) && $rate['cost'] == 0 && $fedex_small_res_inst->is_minus_100_percent_exists){
                        $rate['id'] = 'fedex_small:' . 'en_free';
                        $rate['title'] = 'Free';
                        $rate['label'] = 'Free';
                        $this->add_rate($rate);
                        $en_rates[$accessorial] = array_merge($en_rates[$accessorial], $rate);
                    }
                }
                // Origin terminal address
                if ($en_is_shipment == 'en_single_shipment') {
                    (isset($this->InstorPickupLocalDelivery->localDelivery) && ($this->InstorPickupLocalDelivery->localDelivery->status == 1)) ? $this->local_delivery($this->web_service_inst->en_wd_origin_array['fee_local_delivery'], $this->web_service_inst->en_wd_origin_array['checkout_desc_local_delivery'], $this->web_service_inst->en_wd_origin_array) : "";
                    (isset($this->InstorPickupLocalDelivery->inStorePickup) && ($this->InstorPickupLocalDelivery->inStorePickup->status == 1)) ? $this->pickup_delivery($this->web_service_inst->en_wd_origin_array['checkout_desc_store_pickup'], $this->web_service_inst->en_wd_origin_array, $this->InstorPickupLocalDelivery->totalDistance) : "";
                }

                return $en_rates;
            }

            /**
             * Append label in quote
             * @param array type $rate
             * @return string type
             */
            public function set_label_in_quote($rate)
            {
                $rate_label = "";

                $rate_label = (isset($rate['transit_label'])) ? $rate['transit_label'] : "";

                return $rate_label;
            }

            /**
             * Add Hazardous Fee
             * @param string $service_code
             * @param array $quote_settings
             * @return string
             */
            function add_hazardous_material($service_code)
            {
                $hazardous_fee = get_option('en_fedex_small_ground_hazardous_material_fee');

                $air_hazardous_fee = get_option('en_fedex_small_air_hazardous_material_fee');
                return ($service_code == "FEDEX_GROUND" || $service_code == "GROUND_HOME_DELIVERY") ? $hazardous_fee : $air_hazardous_fee;
            }

            /**
             * Hazardouds values quote settings
             */
            function get_hazardous_fields()
            {
                $this->quote_settings = [];
                $this->quote_settings['hazardous_materials_shipments'] = get_option('fedex_small_hazardous_materials_shipments');
                $this->quote_settings['ground_hazardous_material_fee'] = get_option('en_fedex_small_ground_hazardous_material_fee');
                $this->quote_settings['air_hazardous_material_fee'] = get_option('en_fedex_small_air_hazardous_material_fee');
                $this->quote_settings['residential_delivery'] = get_option('fedex_small_quote_as_residential_delivery');
                $this->quote_settings['dont_sort'] = get_option('shipping_methods_do_not_sort_by_price');
                $this->quote_settings['handling_fee'] = get_option('fedex_small_hand_fee_mark_up');
                $this->quote_settings['services'] = [
                    'domestic' => apply_filters('fedex_small_domestic_services', []),
                    'one_rate' => apply_filters('fedex_small_one_rate_services', []),
                    'international' => apply_filters('fedex_small_international_services', [])
                ];
            }

            /**
             * final rates sorting
             * @param array type $rates
             * @param array type $package
             * @return array type
             */
            function en_sort_woocommerce_available_shipping_methods($rates, $package)
            {
//              if there are no rates don't do anything

                if (!$rates) {
                    return [];
                }

//              check the option to sort shipping methods by price on quote settings 
                if (get_option('shipping_methods_do_not_sort_by_price') != 'yes') {

                    $local_delivery = isset($rates['local-delivery']) ? $rates['local-delivery'] : '';
                    $in_store_pick_up = isset($rates['in-store-pick-up']) ? $rates['in-store-pick-up'] : '';
//                  get an array of prices
                    $prices = array();
                    foreach ($rates as $rate) {
                        $prices[] = $rate->cost;
                    }

//                  use the prices to sort the rates
                    array_multisort($prices, $rates);

//                  unset instore-pickup & local delivery and set at the end of quotes array
                    if (isset($in_store_pick_up) && !empty($in_store_pick_up)) {
                        unset($rates['in-store-pick-up']);
                        $rates['in-store-pick-up'] = $in_store_pick_up;
                    }
                    if (isset($local_delivery) && !empty($local_delivery)) {
                        unset($rates['local-delivery']);
                        $rates['local-delivery'] = $local_delivery;
                    }
                }
//              return the rates
                return $rates;
            }

            /**
             * Pickup delivery quote
             * @return array type
             */
            function pickup_delivery($label, $en_wd_origin_array, $total_distance)
            {
                $this->woocommerce_package_rates = 1;
                $this->instore_pickup_and_local_delivery = TRUE;

                $label = (isset($label) && (strlen($label) > 0)) ? $label : 'In-store pick up';
                // Origin terminal address
                $address = (isset($en_wd_origin_array['address'])) ? $en_wd_origin_array['address'] : '';
                $city = (isset($en_wd_origin_array['city'])) ? $en_wd_origin_array['city'] : '';
                $state = (isset($en_wd_origin_array['state'])) ? $en_wd_origin_array['state'] : '';
                $zip = (isset($en_wd_origin_array['zip'])) ? $en_wd_origin_array['zip'] : '';
                $phone_instore = (isset($en_wd_origin_array['phone_instore'])) ? $en_wd_origin_array['phone_instore'] : '';
                strlen($total_distance) > 0 ? $label .= ': Free | ' . str_replace("mi", "miles", $total_distance) . ' away' : '';
                strlen($address) > 0 ? $label .= ' | ' . $address : '';
                strlen($city) > 0 ? $label .= ', ' . $city : '';
                strlen($state) > 0 ? $label .= ' ' . $state : '';
                strlen($zip) > 0 ? $label .= ' ' . $zip : '';
                strlen($phone_instore) > 0 ? $label .= ' | ' . $phone_instore : '';

                $pickup_delivery = array(
                    'id' => 'fedex_small:' . 'in-store-pick-up',
                    'cost' => 0,
                    'label' => $label,
                    'plugin_name' => 'fedexSmall',
                    'plugin_type' => 'small',
                    'owned_by' => 'eniture'
                );

                add_filter('woocommerce_package_rates', array($this, 'en_sort_woocommerce_available_shipping_methods'), 10, 2);
                $this->add_rate($pickup_delivery);
            }

            /**
             * Local delivery quote
             * @param string type $cost
             * @return array type
             */
            function local_delivery($cost, $label, $en_wd_origin_array)
            {
                $this->woocommerce_package_rates = 1;
                $this->instore_pickup_and_local_delivery = TRUE;
                $label = (isset($label) && (strlen($label) > 0)) ? $label : 'Local Delivery';
                $local_delivery = array(
                    'id' => 'fedex_small:' . 'local-delivery',
                    'cost' => !empty($cost) ? $cost : 0,
                    'label' => $label,
                    'plugin_name' => 'fedexSmall',
                    'plugin_type' => 'small',
                    'owned_by' => 'eniture'
                );

                add_filter('woocommerce_package_rates', array($this, 'en_sort_woocommerce_available_shipping_methods'), 10, 2);
                $this->add_rate($local_delivery);
            }

            /**
             * Check is free shipping or not
             * @param $coupon
             * @return string
             */
            function fedexSmpkgFreeShipping($coupon)
            {
                foreach ($coupon as $key => $value) {
                    if ($value->get_free_shipping() == 1) {
                        $rates = array(
                            'id' => 'free',
                            'label' => 'Free ShippingCtrl',
                            'cost' => 0
                        );
                        return 'y';
                    }
                }
            }

        }

    }
}
