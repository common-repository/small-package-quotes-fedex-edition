<?php

/**
 * FedEx Small Carrier Service
 *
 * @package     FedEx Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get Quotes For FedEx Small
 */
class FedEx_Get_Shipping_Quotes extends EnFedExSmallFdo
{

    public $en_wd_origin_array;
    public $forcefully_residential_delivery = FALSE;
    public $forcefully_always_residential_delivery = FALSE;

    /** $fedex_sm_errors */
    public $fedex_sm_errors = array();

    /** $no_services_select */
    public $no_services_select = array();
    public $product_detail = array();
    public $hazardous_status;
    public $simple_quotes;
    public $end_point_url = FEDEX_DOMAIN_HITTING_URL . '/s/fedex/fedex_shipment_rates.php';
    public $is_minus_100_percent_exists = false;

    /**
     * Array For Getting Quotes
     * @param $packages
     * @param $content
     * @return array
     */
    function fedex_Small_shipping_array($packages, $content, $services_list, $package_plugin = "")
    {
        // FDO
        $en_fdo_meta_data = $post_data = array();

        $residential = "";
        $destinationAddressFedexSmall = $this->destinationAddressFedexSmall();

        $exceedWeight = get_option('wc_settings_wwe_return_LTL_quotes');
        (get_option('fedex_small_quote_as_residential_delivery') == 'yes') ? $residential = 'on' : $residential = 'off';
        $en_shipments = (isset($content['en_shipments'])) ? $content['en_shipments'] : [];

        $Pweight = 0;
        $findLtl = 0;
        //threshold
        $weight_threshold = get_option('en_weight_threshold_lfq');
        $weight_threshold = isset($weight_threshold) && $weight_threshold > 0 ? $weight_threshold : 150;
        // check plan for nested material
        $nested_plan = apply_filters('fedex_small_quotes_plans_suscription_and_features', 'nested_material');

        foreach ($packages as $package) {
            $productName = array();
            $productQty = array();
            $productPrice = array();
            $productWeight = array();
            $productLength = array();
            $productWidth = array();
            $productHeight = array();
            $product_name = array();
            $productMarkup = array();
            $shipmentWeekDays = "";
            $products = array();
            $total_weight = 0;
            $total_girth = 0;
            $orderCutoffTime = "";
            $shipmentOffsetDays = "";
            $modifyShipmentDateTime = "";
            $storeDateTime = "";
            $doNesting = 0;
            $product_markup_shipment = 0;

            $ship_item_alone = $nestingPercentage = $nestedDimension = $nestedItems = $stakingProperty = [];

            $this->en_wd_origin_array = (isset($package['origin'])) ? $package['origin'] : array();
            $package_zip = (isset($package['origin']['zip'])) ? $package['origin']['zip'] : '';
            if (!($exceedWeight == 'yes' && $Pweight > $weight_threshold) &&
                (empty($en_shipments) || (!empty($en_shipments) && isset($en_shipments[$package_zip]))) &&
                (!isset($package['is_shipment']) || (isset($package['is_shipment']) && $package['is_shipment'] != 'ltl'))) {
                if (isset($package['items'])) {
                    $productIdCount = 0;
                    foreach ($package['items'] as $item) {
                        $Pweight = $item['productWeight'];
                        $productName[$productIdCount] = $item['productName'];
                        $productWeight[$productIdCount] = $item['productWeight'];
                        $productLength[$productIdCount] = $item['productLength'];
                        $productWidth[$productIdCount] = $item['productWidth'];
                        $productHeight[$productIdCount] = $item['productHeight'];
                        $productQty[$productIdCount] = $item['productQty'];
                        $productPrice[$productIdCount] = $item['productPrice'];
                        $ship_item_alone[$productIdCount] = $item['ship_item_alone'];

                        $productMarkup[$productIdCount] = (isset($item['product_markup'])) ? $item['product_markup'] : [];

                        $product_name[] = isset($item['product_name']) ? $item['product_name'] : '';
                        $products[] = isset($item['products']) ? $item['products'] : '';
                        $total_weight += $productWeight[$productIdCount] * $productQty[$productIdCount];
                        $total_girth += $productLength[$productIdCount] + ((2 * $productWidth[$productIdCount]) + (2 * $productHeight[$productIdCount]));

                        // Do Nesting
                        $nestingPercentage[$productIdCount] = $item['nestedPercentage'];
                        $nestedDimension[$productIdCount] = $item['nestedDimension'];
                        $nestedItems[$productIdCount] = $item['nestedItems'];
                        $stakingProperty[$productIdCount] = $item['stakingProperty'];
                        isset($item['nestedMaterial']) && !empty($item['nestedMaterial']) &&
                        $item['nestedMaterial'] == 'yes' && !is_array($nested_plan) ? $doNesting = 1 : "";

                        $productIdCount++;

                        if(!empty($item['markup']) && is_numeric($item['markup'])){
                            $product_markup_shipment += $item['markup'];
                        }
                    }
                }

                $domain = fedex_small_get_domain();

                $getVersion = $this->fedexSmpkgWcVersionNumber();

                if (isset($package['origin'], $package['origin']['sender_origin'])) {
                    $sender_origin = $package['origin']['sender_origin'];
                } else {
                    $sender_origin = '';
                }

                //**Start: Cut Off Time & Ship Date Offset
                $fedex_small_delivery_estimates = get_option('fedex_small_delivery_estimates');
                // Shipment days of a week
                $shipmentWeekDays = $this->fedex_small_shipment_week_days();

                if ($fedex_small_delivery_estimates == 'delivery_days' || $fedex_small_delivery_estimates == 'delivery_date') {
                    $orderCutoffTime = get_option('fedex_small_orderCutoffTime');
                    $shipmentOffsetDays = get_option('fedex_small_shipmentOffsetDays');
                    $modifyShipmentDateTime = ($orderCutoffTime != '' || $shipmentOffsetDays != '' || (is_array($shipmentWeekDays) && count($shipmentWeekDays) > 0)) ? 1 : 0;
                    $storeDateTime = date('Y-m-d H:i:s', current_time('timestamp'));
                }

                $package_type = get_option('fedex_small_packaging_method');
                $per_package_weight = '';
                if('ship_one_package_70' == $package_type){
                    $package_type = 'ship_as_one';
                    $per_package_weight = '70';
                }elseif('ship_one_package_150' == $package_type){
                    $package_type = 'ship_as_one';
                    $per_package_weight = '150';
                }

                //**End: Cut Off Time & Ship Date Offset
                // FDO
                $en_fdo_meta_data = $this->en_cart_package($package);
                $location_origin = isset($package['origin'], $package['origin']['location']) ? $package['origin']['location'] : '';
                $location_city = isset($package['origin'], $package['origin']['city']) ? $package['origin']['city'] : '';
                $location_state = isset($package['origin'], $package['origin']['state']) ? $package['origin']['state'] : '';
                $location_zip = isset($package['origin'], $package['origin']['zip']) ? $package['origin']['zip'] : '';

                $s_post_data = array(
                    'platform' => 'WordPress',
                    'plugin_version' => $getVersion["fedexSmpkg_plugin_version"],
                    'wordpress_version' => get_bloginfo('version'),
                    'woocommerce_version' => $getVersion["woocommerce_plugin_version"],
                    'carrierName' => 'fedexSmall',
                    'carrier_mode' => 'pro', // use test / pro
                    'key' => get_option('fedex_small_auth_key'),
                    'password' => get_option('fedex_small_password'),
                    'MeterNumber' => get_option('fedex_small_meter_number'),
                    'AccountNumber' => get_option('fedex_small_account_number'),
                    'licence_key' => get_option('fedex_small_licence_key'),
                    'sever_name' => $this->fedex_small_parse_url($domain),
                    'modifyShipmentDateTime' => $modifyShipmentDateTime,
                    'OrderCutoffTime' => $orderCutoffTime,
                    'shipmentOffsetDays' => $shipmentOffsetDays,
                    'storeDateTime' => $storeDateTime,
                    'shipmentWeekDays' => $shipmentWeekDays,
                    'receiverCity' => $destinationAddressFedexSmall['city'],
                    'receiverState' => $destinationAddressFedexSmall['state'],
                    'receiverZip' => $destinationAddressFedexSmall['zip'],
                    'receiverCountry' => $destinationAddressFedexSmall['country'],
                    'senderCity' => isset($package['origin'], $package['origin']['city']) ? $package['origin']['city'] : '',
                    'senderState' => isset($package['origin'], $package['origin']['state']) ? $package['origin']['state'] : '',
                    'senderZip' => isset($package['origin'], $package['origin']['zip']) ? $package['origin']['zip'] : '',
                    'senderCountry' => isset($package['origin'], $package['origin']['country']) ? $package['origin']['country'] : '',
                    'total_weight' => $total_weight,
                    'total_girth' => $total_girth,
                    'sender_origin' => $sender_origin,
                    'sender_origin' => $location_origin . ": " . $location_city . ", " . $location_state . " " . $location_zip,
                    'product_name' => $product_name,
                    'products' => $products,
                    'residential_delivery' => $residential,
                    'pkg_type' => '00',
                    // Product Information
                    'width' => $productWidth,
                    'height' => $productHeight,
                    'length' => $productLength,
                    'weight' => $productWeight,
                    'count' => $productQty,
                    'price' => $productPrice,
                    'markup' => $productMarkup,
                    // FDO
                    'en_fdo_meta_data' => $en_fdo_meta_data,
                    // Nested indexes
                    'doNesting' => $doNesting,
                    'nesting_percentage' => $nestingPercentage,
                    'nesting_dimension' => $nestedDimension,
                    'nested_max_limit' => $nestedItems,
                    'nested_stack_property' => $stakingProperty,
                    // Shippable item
                    'ship_item_alone' => $ship_item_alone,
                    'packagesType' => $package_type,
                    'perPackageWeight' => $per_package_weight,
                    // Sbs optimization mode
                    'sbsMode' => get_option('box_sizing_optimization_mode'),
                    'origin_markup' => (isset($package['origin']['origin_markup'])) ? $package['origin']['origin_markup'] : 0,
                    'product_level_markup' => $product_markup_shipment,

                     // New API Parameters
                     'requestForNewAPI' => get_option('api_selection_fedex_small') == 'new_api' ? '1' : '0',
                     'clientId' => get_option('api_selection_fedex_small') == 'new_api' ? get_option('fedex_small_client_id') : '',
                     'clientSecret' => get_option('api_selection_fedex_small') == 'new_api' ? get_option('fedex_small_client_secret') : '',
                     'accountNumber' => get_option('api_selection_fedex_small') == 'new_api' ? get_option('fedex_small_new_api_acc_number') : '',
                );

                $s_post_data = apply_filters('en_update_the_request_according_to_accessorial', $s_post_data);
                $s_post_data = apply_filters('en_update_the_request_step_to_accessorials', $s_post_data);

                // Insurance Fee
                $action_insurance = apply_filters('fedex_small_quotes_plans_suscription_and_features', 'insurance_fee');
                if (!is_array($action_insurance)) {
                    $s_post_data['includeDeclaredValue'] = 1;
                    $s_post_data['prefferedCurrency'] = 'USD';
                }

                // Hazardous Material
                $hazardous_material = apply_filters('fedex_small_quotes_plans_suscription_and_features', 'hazardous_material');

                if (!is_array($hazardous_material)) {
                    (isset($package['hazardous_material'])) ? $this->hazardous_status = 'yes' : $this->hazardous_status = '';
                    (isset($package['hazardous_material'])) ? $s_post_data['hazardous_status'] = 'yes' : '';
                    // FDO
                    $s_post_data['en_fdo_meta_data'] = array_merge($s_post_data['en_fdo_meta_data'], $this->en_package_hazardous($package, $en_fdo_meta_data));
                }

                //Except Ground Transit Restriction
                $exempt_ground_restriction_plan = apply_filters('fedex_small_quotes_plans_suscription_and_features', 'transit_days');
                if (!is_array($exempt_ground_restriction_plan)) {
                    (isset($package['exempt_ground_transit_restriction'])) ? $s_post_data['exempt_ground_transit_restriction'] = 'yes' : '';
                }

                // In-store pickup and local delivery
                $instore_pickup_local_devlivery_action = apply_filters('fedex_small_quotes_plans_suscription_and_features', 'instore_pickup_local_devlivery');

                if (!is_array($instore_pickup_local_devlivery_action)) {
                    $s_post_data = apply_filters('en_fedex_small_wd_standard_plans', $s_post_data, $s_post_data['receiverZip'], $this->en_wd_origin_array, $package_plugin);
                }

                $s_post_data = $this->_is_smart_post_enable($s_post_data);
                if(isset($package['origin']['zip'])) {
                    $post_data[$package['origin']['zip']] = apply_filters("en_woo_addons_carrier_service_quotes_request", $s_post_data, en_woo_plugin_fedex_small);

                }
            }

            if(isset($package['origin']['zip'])){
                $post_data = apply_filters(
                    'enit_box_sizes_post_array_filter', $post_data, $package, $package['origin']['zip'], $services_list
                );
            }

            // Compatability with OLD SBS Addon
            $zip_code = (isset($package['origin']['zip'])) ? $package['origin']['zip'] : 0;
            if (isset($post_data[$zip_code]['vertical_rotation'], $post_data[$zip_code]['length']) &&
                count($post_data[$zip_code]['length']) == count($post_data[$zip_code]['vertical_rotation']) &&
                !empty($post_data[$zip_code]['vertical_rotation'])) {
                $post_data[$zip_code]['vertical_rotation'] = array_combine(array_keys($post_data[$zip_code]['length']), $post_data[$zip_code]['vertical_rotation']);
            }
            if (isset($post_data[$zip_code]['shipBinAlone'], $post_data[$zip_code]['length']) &&
                count($post_data[$zip_code]['length']) == count($post_data[$zip_code]['shipBinAlone']) &&
                !empty($post_data[$zip_code]['shipBinAlone'])) {
                $post_data[$zip_code]['shipBinAlone'] = array_combine(array_keys($post_data[$zip_code]['length']), $post_data[$zip_code]['shipBinAlone']);
            }
        }

        $post_data = apply_filters('en_mutiple_packages_valid_request', $post_data);
        // Eniture debug mood
        do_action("eniture_debug_mood", "Fedex small Features", get_option('eniture_plugin_3'));
        do_action("eniture_debug_mood", "Request (Fedex small)", $post_data);
        do_action("eniture_debug_mood", "Build Query (Fedex small)", http_build_query(!empty($post_data) ? $post_data : []));
        return $post_data;
    }

    /**
     * @return shipment days of a week
     */
    public function fedex_small_shipment_week_days()
    {

        $shipment_days_of_week = array();

        if (get_option('all_shipment_days_fedex_small') == 'yes') {
            return $shipment_days_of_week;
        }
        if (get_option('monday_shipment_day_fedex_small') == 'yes') {
            $shipment_days_of_week[] = 1;
        }
        if (get_option('tuesday_shipment_day_fedex_small') == 'yes') {
            $shipment_days_of_week[] = 2;
        }
        if (get_option('wednesday_shipment_day_fedex_small') == 'yes') {
            $shipment_days_of_week[] = 3;
        }
        if (get_option('thursday_shipment_day_fedex_small') == 'yes') {
            $shipment_days_of_week[] = 4;
        }
        if (get_option('friday_shipment_day_fedex_small') == 'yes') {
            $shipment_days_of_week[] = 5;
        }

        return $shipment_days_of_week;
    }

    /**
     * @param $_12hourTime
     * @param $string
     * @return false|string
     */
    function convert_12_hour_to_24_hour_formate($_12hourTime, $string)
    {
        $time_in_24_hour_format = date("G:i", strtotime($_12hourTime . ' ' . $string));
        return $time_in_24_hour_format;
    }

    /**
     * @param $post_data
     * @return mixed
     */
    function _is_smart_post_enable($post_data)
    {
        $hub_id = get_option('hub_id_fedex_small');
        $hub_post = get_option('fedex_small_smart_post');

        $valid_weight = (isset($post_data['total_weight']) && ($post_data['total_weight'] <= 70)) ? TRUE : FALSE;
        $total_girth = (isset($post_data['total_girth']) && ($post_data['total_girth'] <= 130)) ? TRUE : FALSE;

        if ($total_girth && $valid_weight && $hub_id > 0 && $hub_post == "yes" && !empty($post_data)) {
            $post_data['smartPOST'] = array(
                'hubId' => $hub_id,
                'indicia' => 'PARCEL_SELECT'
            );
        }

        return $post_data;
    }

    /**
     * URL Rewriting
     * @param $domain
     * @return url
     */
    function fedex_small_parse_url($domain)
    {
        $domain = trim($domain);
        $parsed = parse_url($domain);
        if (empty($parsed['scheme'])) {
            $domain = 'http://' . ltrim($domain, '/');
        }
        $parse = parse_url($domain);
        $refinded_domain_name = $parse['host'];
        $domain_array = explode('.', $refinded_domain_name);
        if (in_array('www', $domain_array)) {
            $key = array_search('www', $domain_array);
            unset($domain_array[$key]);
            if(phpversion() < 8) {
                $refinded_domain_name = implode('.', $domain_array);
             }else {
                $refinded_domain_name = implode('.', $domain_array);
             }
        }
        return $refinded_domain_name;
    }

    /**
     * Get Nearest Address If Multiple Warehouses
     * @param $warehous_list
     * @param $receiverZipCode
     * @return array
     */
    function fedex_Small_multi_warehouse($warehous_list, $receiverZipCode)
    {
        if (count($warehous_list) == 1) {
            $warehous_list = reset($warehous_list);
            return $this->fedex_small_origin_array($warehous_list);
        }

        if(empty($receiverZipCode)){
            $receiver_country = (strlen(WC()->customer->get_shipping_country()) > 0) ? WC()->customer->get_shipping_country() : $fedex_small_woo_obj->fedex_small_getCountry();
            if($receiver_country == 'AE'){
                $warehous_list = reset($warehous_list);
                return $this->fedex_small_origin_array($warehous_list);
            }
        }

        $fedex_Small_distance_request = new Get_fedex_small_distance();
        $accessLevel = "MultiDistance";
        $response_json = $fedex_Small_distance_request->fedex_small_address($warehous_list, $accessLevel, $this->destinationAddressFedexSmall());

        $response_json = json_decode($response_json);
        return $this->fedex_small_origin_array((isset($response_json->origin_with_min_dist)) ? $response_json->origin_with_min_dist : "");
    }

    /**
     * destinationAddressFedexSmall
     * @return array type
     */
    function destinationAddressFedexSmall()
    {
        $en_order_accessories = apply_filters('en_order_accessories', []);
        if (isset($en_order_accessories) && !empty($en_order_accessories)) {
            return $en_order_accessories;
        }

        $fedex_small_woo_obj = new Fedex_Small_Woo_Update_Changes();
        $freight_zipcode = (strlen(WC()->customer->get_shipping_postcode()) > 0) ? WC()->customer->get_shipping_postcode() : $fedex_small_woo_obj->fedex_small_postcode();
        $freight_state = (strlen(WC()->customer->get_shipping_state()) > 0) ? WC()->customer->get_shipping_state() : $fedex_small_woo_obj->fedex_small_getState();
        $freight_country = (strlen(WC()->customer->get_shipping_country()) > 0) ? WC()->customer->get_shipping_country() : $fedex_small_woo_obj->fedex_small_getCountry();
        $freight_city = (strlen(WC()->customer->get_shipping_city()) > 0) ? WC()->customer->get_shipping_city() : $fedex_small_woo_obj->fedex_small_getCity();
        $address = $fedex_small_woo_obj->fedex_small_getAddress1();

        if(empty($freight_zipcode)){
            if($freight_country == 'AE'){
                $freight_zipcode = '00000';
            }
        }

        return array(
            'city' => $freight_city,
            'state' => $freight_state,
            'zip' => $freight_zipcode,
            'country' => $freight_country,
            'address' => $address,
        );
    }

    /**
     * Create Origin Array
     * @param $origin
     * @return array
     */
    function fedex_small_origin_array($origin)
    {
        // In-store pickup and local delivery
        if (has_filter("en_fedex_small_wd_origin_array_set")) {
            return apply_filters("en_fedex_small_wd_origin_array_set", $origin);
        }

        $zip = (isset($origin->zip)) ? $origin->zip : "";
        $city = (isset($origin->city)) ? $origin->city : "";
        $state = (isset($origin->state)) ? $origin->state : "";
        $country = (isset($origin->country)) ? $origin->country : "";
        $country = ($country == "CN") ? "CA" : $country;
        $location = (isset($origin->location)) ? $origin->location : "";
        $locationId = (isset($origin->id)) ? $origin->id : "";
        return array(
            'locationId' => $locationId,
            'zip' => $zip,
            'city' => $city,
            'state' => $state,
            'location' => $location,
            'country' => $country,
            'sender_origin' => $location . ", " . $zip . ", " . $city . ", " . $state . ", " . $country,
        );
    }

    /**
     * Get FedEx Small Web Quotes
     * @param $request_data
     * @return array
     */
    function fedex_small_get_quotes($request_data, $package_plugin = "")
    {
        $this->simple_quotes = true;

        // Multi Curl SmartPost Request
        $valid_weight = (isset($request_data['total_weight']) && ($request_data['total_weight'] <= 70)) ? TRUE : FALSE;
        if (isset($request_data['smartPOST']) && !empty($request_data['smartPOST']) && $valid_weight) {
            $request_data['isSPAvailable'] = 1;
            // Eniture debug mood
            do_action("eniture_debug_mood", "SmartPost Request (Fedex Small)", $request_data);
            do_action("eniture_debug_mood", "SmartPost Build Query (Fedex Small)", http_build_query($request_data));
        }

        // Check response from session
        $currentData = md5(json_encode($request_data));
        $requestFromSession = WC()->session->get('previousRequestData');
        $requestFromSession = ((is_array($requestFromSession)) && (!empty($requestFromSession))) ? $requestFromSession : array();

        if (isset($requestFromSession[$currentData]) && (!empty($requestFromSession[$currentData]))) {
            $requestFromSession = json_decode($requestFromSession[$currentData]);
            // Eniture debug mood
            do_action("eniture_debug_mood", " Fedex Small Features", get_option('eniture_plugin_3'));
            do_action("eniture_debug_mood", "Quotes session Response (Fedex Small)", $requestFromSession);
            return $requestFromSession;
        }

        if (is_array($request_data) && count($request_data) > 0) {
            $FedEx_Small_Curl_Request = new FedEx_Small_Curl_Request();
            // requestKeySBS
            if (isset($request_data['requestKeySBS']) && strlen($request_data['requestKeySBS']) > 0) {
                $request_data['requestKey'] = $request_data['requestKeySBS'];
            } else {
                $request_data['requestKey'] = (isset($request_data['requestKey'])) ? $request_data['requestKey'] : md5(microtime() . rand());
            }

            $output = $FedEx_Small_Curl_Request->fedex_small_get_curl_response($this->end_point_url, $request_data);

            // Set response in session
            $response = json_decode($output, TRUE);

            if (isset($response['q']) ||
                isset($response['weight_based_pricing']) ||
                isset($response['one_rate_pricing']) ||
                isset($response['home_ground_pricing']) ||
                isset($response['RateReplyDetails'])) {
                if (isset($response['autoResidentialSubscriptionExpired']) &&
                    ($response['autoResidentialSubscriptionExpired'] == 1)) {
                    $flag_api_response = "no";
                    $request_data['residential_detecion_flag'] = $flag_api_response;
                    $currentData = md5(json_encode($request_data));
                }

                $requestFromSession[$currentData] = $output;
                WC()->session->set('previousRequestData', $requestFromSession);
            }

            // Eniture debug mood
            do_action("eniture_debug_mood", " Fedex Small Features", get_option('eniture_plugin_3'));
            do_action("eniture_debug_mood", "Quotes Response (Fedex Small)", json_decode($output));

            return json_decode($output);
        }
    }

    /**
     * Return the array
     * @param object $result
     * @return object
     */
    public function quote_detail($result)
    {
        return isset($result->RateReplyDetails) ? $result->RateReplyDetails : ((isset($result->q)) ? $result->q : [1]);
    }

    /**
     * Return the array
     * @param object $result
     * @return object
     */
    public function en_bin_packaging_detail($result)
    {
        return isset($result->binPackaging->response) ? $result->binPackaging->response : [];
    }

    /**
     * Get Shipping Array For Single Shipment
     * @param $result
     * @param $serviceType
     * @return array
     */
    function parse_fedex_small_output($result, $id_services, $one_rate_services, $product_detail, $quote_settings)
    {
        $en_box_fee = 0;
        $this->hazardous_status = (isset($product_detail['hazardous_status'])) ? $product_detail['hazardous_status'] : FALSE;
        $hazardous_material = isset($this->hazardous_status) && ($this->hazardous_status == 'yes') ? true : false;

        $all_services_array = array();
        $transit_time = 0;
        $hazardous_fee = 0;
        $en_count_rates = 0;
        $meta_data = array();
        $accessorials = array();

        $en_rates = [];
        $en_sorting_rates = [];

        $EnFedExSmallTransitDays = new EnFedExSmallTransitDays();

        $WC_fedex_small = new WC_fedex_small;

        $en_always_accessorial = [];
        $multiple_accessorials[] = ['S'];

        $this->forcefully_residential_delivery ? $multiple_accessorials[] = ['R'] : '';

        (isset($quote_settings['residential_delivery']) && $quote_settings['residential_delivery'] == 'yes') ? $en_always_accessorial[] = 'R' : '';
        ($hazardous_material) ? $en_always_accessorial[] = 'H' : '';
        $en_auto_residential_status = !in_array('R', $en_always_accessorial) && isset($result->residentialStatus) && $result->residentialStatus == 'r' ? 'r' : '';

        $meta_data['accessorials'] = json_encode($en_always_accessorial);
        $meta_data['sender_origin'] = (isset($product_detail['sender_origin'])) ? $product_detail['sender_origin'] : '';
        $meta_data['product_name'] = (isset($product_detail['product_name'])) ? $product_detail['product_name'] : '';
        $meta_data['plugin_name'] = "fedex_small";

        $markup = (isset($product_detail['markup'])) ? $product_detail['markup'] : [];

        // FDO
        $en_fdo_meta_data = (isset($product_detail['en_fdo_meta_data'])) ? $product_detail['en_fdo_meta_data'] : [];
        if (!empty($en_fdo_meta_data) && !is_array($en_fdo_meta_data)) {
            $en_fdo_meta_data = json_decode($en_fdo_meta_data, true);
        }

        $en_auto_residential_status == 'r' ? $en_fdo_meta_data['accessorials']['residential'] = true : '';

        $package_bins = (isset($product_detail['package_bins'])) ? $product_detail['package_bins'] : [];
        $en_box_fee_arr = (isset($product_detail['en_box_fee']) && !empty($product_detail['en_box_fee'])) ? $product_detail['en_box_fee'] : [];
        $en_multi_box_qty = (isset($product_detail['en_multi_box_qty']) && !empty($product_detail['en_multi_box_qty'])) ? $product_detail['en_multi_box_qty'] : [];
        $products = (isset($product_detail['products'])) ? $product_detail['products'] : [];

        if (isset($en_box_fee_arr) && is_array($en_box_fee_arr) && !empty($en_box_fee_arr)) {
            foreach ($en_box_fee_arr as $en_box_fee_key => $en_box_fee_value) {
                $en_multi_box_quantity = (isset($en_multi_box_qty[$en_box_fee_key])) ? $en_multi_box_qty[$en_box_fee_key] : 0;
                $en_box_fee += $en_box_fee_value * $en_multi_box_quantity;
            }
        }

        $quote = [];
        $bin_packaging = [];
        $handling_fee = get_option('fedex_small_hand_fee_mark_up');

        $en_with_residential_delivery = $this->forcefully_always_residential_delivery && $en_auto_residential_status != 'r' ? ' with residential delivery.' : '';

        $home_ground_pricing_flag = $no_quotes = false;
        if((isset($result->HighestSeverity) && ($result->HighestSeverity == 'ERROR' || $result->HighestSeverity == 'WARNING' || $result->HighestSeverity == 'FAILURE')) || (isset($result->error) && $result->error == 1) || (isset($result->severity) && in_array($result->severity, ['ERROR', 'WARNING', 'FAILURE']))) {
            return [];
        }elseif(empty($result->q) && empty($result->weight_based_pricing) && empty($result->home_ground_pricing) && empty($result->one_rate_pricing) && empty($result->smart_post_pricing)){
            return [];
        }else if (isset($result->weight_based_pricing) || isset($result->home_ground_pricing) || isset($result->one_rate_pricing) || isset($result->normal_pricing) || isset($result->smart_post_pricing)) {
            (isset($result->weight_based_pricing->HighestSeverity) && strtolower($result->weight_based_pricing->HighestSeverity) == 'error') ? $no_quotes = true : '';
            (isset($result->weight_based_pricing->severity) && strtolower($result->weight_based_pricing->severity) == 'error') ? $no_quotes = true : '';
        } elseif (!isset($result->q)) {
            $result = (object)['q' => (object)[1]];
            $no_quotes = true;
        }

        if (isset($result->q) || isset($result->RateReplyDetails)) {
            $quote['weight_based_pricing'] = $this->quote_detail($result);
            $bin_packaging['weight_based_pricing'] = $this->en_bin_packaging_detail($result);
            $this->simple_quotes = false;
        } else {
            if (isset($result->one_rate_pricing)) {
                $quote['one_rate_pricing'] = $this->quote_detail($result->one_rate_pricing);
                $bin_packaging['one_rate_pricing'] = $this->en_bin_packaging_detail($result->one_rate_pricing);
                !in_array('R', $en_always_accessorial) && isset($result->one_rate_pricing->residentialStatus) && $result->one_rate_pricing->residentialStatus == 'r' ? $en_auto_residential_status = 'r' : '';
            }

            if (isset($result->home_ground_pricing)) {
                $home_ground_pricing_flag = true;
                $quote['home_ground_pricing'] = $this->quote_detail($result->home_ground_pricing);
                $bin_packaging['home_ground_pricing'] = $this->en_bin_packaging_detail($result->home_ground_pricing);
                !in_array('R', $en_always_accessorial) && isset($result->home_ground_pricing->residentialStatus) && $result->home_ground_pricing->residentialStatus == 'r' ? $en_auto_residential_status = 'r' : '';
            }

            if (isset($result->weight_based_pricing)) {
                $quote['weight_based_pricing'] = $this->quote_detail($result->weight_based_pricing);
                $bin_packaging['weight_based_pricing'] = $this->en_bin_packaging_detail($result->weight_based_pricing);
                !in_array('R', $en_always_accessorial) && isset($result->weight_based_pricing->residentialStatus) && $result->weight_based_pricing->residentialStatus == 'r' ? $en_auto_residential_status = 'r' : '';
            } elseif (isset($result->normal_pricing)) {
                $quote['weight_based_pricing'] = $this->quote_detail($result->normal_pricing);
                $bin_packaging['weight_based_pricing'] = $this->en_bin_packaging_detail($result->normal_pricing);
                !in_array('R', $en_always_accessorial) && isset($result->normal_pricing->residentialStatus) && $result->normal_pricing->residentialStatus == 'r' ? $en_auto_residential_status = 'r' : '';
            }

            if (isset($result->smart_post_pricing)) {
                $quote['weight_based_pricing_smart_post'] = $this->quote_detail($result->smart_post_pricing);
                $bin_packaging['weight_based_pricing_smart_post'] = $this->en_bin_packaging_detail($result->smart_post_pricing);
                !in_array('R', $en_always_accessorial) && isset($result->smart_post_pricing->residentialStatus) && $result->smart_post_pricing->residentialStatus == 'r' ? $en_auto_residential_status = 'r' : '';
            }
        }

        $new_api_enabled = get_option('api_selection_fedex_small') == 'new_api';
        if ($new_api_enabled) {
          // convert stdClass object to array for each service quotes to match with old api
          foreach ($quote as $service_name => $services_list) {
              if (empty($services_list)) continue;
              $quote[$service_name] = array_values((array)$services_list);
          }
        }

        foreach ($quote as $service_name => $services_list) {
            if ($new_api_enabled) $services_list = $this->formatServiceQuotes($services_list, $service_name);

            // Bin Packaging Box Fee|Product Title Start
            $bin_packaging_filtered = (isset($bin_packaging[$service_name])) ? json_decode(json_encode($bin_packaging[$service_name]), TRUE) : [];
            $en_box_total_price = 0;
            if (isset($bin_packaging_filtered['bins_packed']) && !empty($bin_packaging_filtered['bins_packed'])) {
                foreach ($bin_packaging_filtered['bins_packed'] as $bins_packed_key => $bins_packed_value) {
                    $bin_data = (isset($bins_packed_value['bin_data'])) ? $bins_packed_value['bin_data'] : [];
                    $bin_items = (isset($bins_packed_value['items'])) ? $bins_packed_value['items'] : [];
                    $bin_id = (isset($bin_data['id'])) ? $bin_data['id'] : '';
                    $bin_type = (isset($bin_data['type'])) ? $bin_data['type'] : '';
                    $bins_detail = (isset($package_bins[$bin_id])) ? $package_bins[$bin_id] : [];

                    $en_box_price = (isset($bins_detail['box_price'])) ? $bins_detail['box_price'] : 0;
                    $en_box_total_price += $en_box_price;

                    foreach ($bin_items as $bin_items_key => $bin_items_value) {
                        $bin_item_id = (isset($bin_items_value['id'])) ? $bin_items_value['id'] : '';
                        $get_product_name = (isset($products[$bin_item_id])) ? $products[$bin_item_id] : '';
                        if ($bin_type == 'item') {
                            $bin_packaging_filtered['bins_packed'][$bins_packed_key]['bin_data']['product_name'] = $get_product_name;
                        }

                        if (isset($bin_packaging_filtered['bins_packed'][$bins_packed_key]['items'][$bin_items_key])) {
                            $bin_packaging_filtered['bins_packed'][$bins_packed_key]['items'][$bin_items_key]['product_name'] = $get_product_name;
                        }
                    }
                }
            }

            $en_box_total_price += $en_box_fee;

            $meta_data['bin_packaging'] = wp_json_encode($bin_packaging_filtered);
            // FDO
            $en_fdo_meta_data['bin_packaging'] = $bin_packaging_filtered;
            $en_fdo_meta_data['bins'] = $package_bins;
            // Bin Packaging Box Fee|Product Title End

            if(!(isset($product_detail['exempt_ground_transit_restriction']) && $product_detail['exempt_ground_transit_restriction'] == 'yes')){
                $services_list = $EnFedExSmallTransitDays->fedex_enable_disable_service_ground($services_list);
            }
            if (isset($services_list->ServiceType, $services_list->RatedShipmentDetails) && $services_list->ServiceType == "SMART_POST") {
                $transit_time = (isset($services_list->DeliveryTimestamp)) ? $services_list->DeliveryTimestamp : '';
                $delivery_days = (isset($services_list->totalTransitTimeInDays)) ? $services_list->totalTransitTimeInDays : '';
                $smartpost_service = (isset($services_list->smartPostService)) ? $services_list->smartPostService : '';
                $RatedShipmentDetails = $services_list->RatedShipmentDetails;
                $service_type = $services_list->ServiceType;
                $services = $id_services;
                $service_key_name = key($services);
                $service = $this->RatedShipmentDetails($RatedShipmentDetails);

                $service_title = (isset($services[$service_key_name][$service_type]['name'])) ? $services[$service_key_name][$service_type]['name'] : "";
                switch ($smartpost_service) {
                    case 'PRESORTED_STANDARD':
                        $service_title = 'Fedex SmartPost parcel select lightweight';
                        break;
                    default:
                        $service_title = 'Fedex SmartPost parcel select';
                        break;
                }

                $services_id = (isset($services[$service_key_name][$service_type]['services_id'])) ? $services[$service_key_name][$service_type]['services_id'] : "";
                $service = (!empty($service)) ? reset($service) : array();
                $total_charge = $service->ShipmentRateDetail->TotalNetCharge->Amount;
                $total_charge = apply_filters('en_product_markup_with_cost', $total_charge, $markup);

                // product level markup
                if (!empty($product_detail['product_level_markup'])) {
                    $total_charge = $this->calculate_service_level_markup($total_charge, $product_detail['product_level_markup']);
                }

                // origin level markup
                if (!empty($product_detail['origin_markup'])) {
                    $total_charge = $this->calculate_service_level_markup($total_charge, $product_detail['origin_markup']);
                }

                //**Start: Adding Serive Level Markup Fee
                $smartpost_service_markup = $services[$service_key_name][$service_type]['markup'];
                $total_charge = $this->calculate_service_level_markup($total_charge, $smartpost_service_markup);
                //**End: Adding Serive Level Markup Fee

                $grand_total = !empty($handling_fee) ? $this->calculate_handeling_fee($handling_fee, $total_charge, true) : $total_charge;

                $hazardous_fee = 0;
                $hazardous = apply_filters('fedex_small_quotes_plans_suscription_and_features', 'hazardous_material');

                if (!is_array($hazardous)) {
                    $hazardous_fee = get_option('fedex_small_hazardous_fee');
                }

                $meta_data['service_type'] = $service_type;
                $meta_data['service_name'] = $service_name;
                $meta_data['services_id'] = $services_id;

                $hazardous_material_fee = ($hazardous_material) ? $WC_fedex_small->add_hazardous_material($meta_data['service_type']) : 0;

                $service_cost = $grand_total > 0 ? (float)$grand_total + (float)$hazardous_material_fee + (float)$en_box_total_price : 0;
                $smart_post_rate = array(
                    'id' => $service_type . "_" . $service_name,
                    'service_type' => $service_type . "_" . $service_name,
                    'cost' => $service_cost,
                    'services_id' => $services_id,
                    'rate' => $service_cost,
                    'transit_time' => $transit_time,
                    'delivery_days' => $delivery_days,
                    'title' => $service_title,
                    'label' => $service_title,
                    'label_as' => $service_title,
                    'service_name' => $service_name,
                    'meta_data' => $meta_data,
                    'plugin_name' => 'fedexSmall',
                    'plugin_type' => 'small',
                    'owned_by' => 'eniture'
                );

                // FDO
                $en_fdo_meta_data['rate'] = $smart_post_rate;
                if (isset($en_fdo_meta_data['rate']['meta_data'])) {
                    unset($en_fdo_meta_data['rate']['meta_data']);
                }
                $en_fdo_meta_data['quote_settings'] = $quote_settings;
                $smart_post_rate['meta_data']['en_fdo_meta_data'] = $en_fdo_meta_data;

                $all_services_array[$service_type] = $smart_post_rate;

                if ($hazardous_material && $service_type != "FEDEX_GROUND") {
                    if (isset($quote_settings['hazardous_materials_shipments']) && ($quote_settings['hazardous_materials_shipments'] == "yes")) {
                        unset($all_services_array[$service_type]);
                    }
                }

                $en_accessorial_type = (!$this->forcefully_residential_delivery && $en_auto_residential_status == 'r') ? 'SR' : 'S';
                $en_rates[$en_accessorial_type][$en_count_rates] = $all_services_array[$service_type];
                $en_rates[$en_accessorial_type][$en_count_rates]['label_sufex'] = ['S'];
                $en_sorting_rates[$en_accessorial_type][$en_count_rates]['cost'] = $service_cost;
            } elseif (isset($services_list) && (!empty($services_list)) || $no_quotes) {
                $services = ($service_name == "one_rate_pricing") ? $one_rate_services : $id_services;
                $service_key_name = key($services);

                if ($service_key_name != "international" && ($this->simple_quotes)) {
                    if ($service_name == "home_ground_pricing") {
                        $home_grd_services = array();
                        (isset($services[$service_key_name]['GROUND_HOME_DELIVERY']['name'])) ? $home_grd_services[$service_key_name]['GROUND_HOME_DELIVERY']['name'] = 'FedEx Home Delivery' : "";
                        (isset($services[$service_key_name]['FEDEX_GROUND']['name'])) ? $home_grd_services[$service_key_name]['FEDEX_GROUND']['name'] = 'FedEx Ground' : "";
                        (isset($services[$service_key_name]['FEDEX_GROUND']['markup'])) ? $home_grd_services[$service_key_name]['FEDEX_GROUND']['markup'] = $services[$service_key_name]['FEDEX_GROUND']['markup'] : 0;
                        (isset($services[$service_key_name]['GROUND_HOME_DELIVERY']['markup'])) ? $home_grd_services[$service_key_name]['GROUND_HOME_DELIVERY']['markup'] = $services[$service_key_name]['GROUND_HOME_DELIVERY']['markup'] : 0;
                        (isset($services[$service_key_name]['GROUND_HOME_DELIVERY']['services_id'])) ? $home_grd_services[$service_key_name]['GROUND_HOME_DELIVERY']['services_id'] = 1 : "";
                        (isset($services[$service_key_name]['FEDEX_GROUND']['services_id'])) ? $home_grd_services[$service_key_name]['FEDEX_GROUND']['services_id'] = 2 : "";
                        $services = $home_grd_services;
                    } elseif ($home_ground_pricing_flag && $service_name == "weight_based_pricing") {
                        if (isset($services[$service_key_name]['GROUND_HOME_DELIVERY']['name']))
                            unset($services[$service_key_name]['GROUND_HOME_DELIVERY']['name']);
                        if (isset($services[$service_key_name]['FEDEX_GROUND']['name']))
                            unset($services[$service_key_name]['FEDEX_GROUND']['name']);
                    }
                }

                if (isset($services_list->ServiceType)) {
                    $services_list_extend[] = (array)$services_list;
                    $services_list = json_decode(json_encode($services_list_extend));
                }
                /*discount shipping start*/
                $discount_result = [];
                $discount_result = apply_filters('en_discount_shipping', $discount_result);
                /*discount shipping end*/

                if (is_int($services_list)) $services_list = [];

                foreach ($services_list as $service_key => $service) {
                    $EnServiceType = (isset($service->ServiceType)) ? $service->ServiceType : '';
                    if ($service_title = (isset($services[$service_key_name][$EnServiceType]['name'])) ? $services[$service_key_name][$EnServiceType]['name'] : "" || $no_quotes) {
                        $no_quotes ? $service_title = 'Shipping' : '';
                        if (($service_key_name == "international" && $service_name != "home_ground_pricing") || $service_key_name != "international" || $no_quotes) {
                            $transit_time = (isset($service->DeliveryTimestamp)) ? $service->DeliveryTimestamp : '';
                            $delivery_days = (isset($service->totalTransitTimeInDays)) ? $service->totalTransitTimeInDays : '';

                            $service_type = $EnServiceType;

                            $service = $this->RatedShipmentDetails((isset($service->RatedShipmentDetails)) ? $service->RatedShipmentDetails : []);

                            $reset_service = reset($service);

                            $total_charge = isset($reset_service->ShipmentRateDetail) ? $reset_service->ShipmentRateDetail->TotalNetCharge->Amount : 0;
                            $transportation_charge = isset($reset_service->ShipmentRateDetail) ? $reset_service->ShipmentRateDetail->TotalNetFreight->Amount : 0;

                            /*discount shipping start*/
                            $origin_add = isset($product_detail['en_fdo_meta_data']['address']['country']) ? $product_detail['en_fdo_meta_data']['address']['country'] : '';
                            $discount_info = apply_filters('en_discount_shipping_total', $discount_result, $reset_service, $service_type, $total_charge, $transportation_charge, $product_detail);

                            $total_discount = '';
                            if(!empty($discount_info)) {
                                $total_charge = $discount_info[0];
                                $total_discount = $discount_info[1];
                            }

                            /*discount shipping end*/
                            $total_charge = apply_filters('en_product_markup_with_cost', $total_charge, $markup);

                            // product level markup
                            if (!empty($product_detail['product_level_markup'])) {
                                $total_charge = $this->calculate_service_level_markup($total_charge, $product_detail['product_level_markup']);
                            }

                            // origin level markup
                            if (!empty($product_detail['origin_markup'])) {
                                $total_charge = $this->calculate_service_level_markup($total_charge, $product_detail['origin_markup']);
                            }

                            //Start: Adding Service level markup fee
                            $service_markup_fee = (isset($services[$service_key_name][$service_type]['markup'])) ? $services[$service_key_name][$service_type]['markup'] : 0;
                            $total_charge = $this->calculate_service_level_markup($total_charge, $service_markup_fee);
                            //End: Adding Service level markup fee

                            $grand_total = strlen($handling_fee) > 0 ? $this->calculate_handeling_fee($handling_fee, $total_charge, true) : $total_charge;

                            $services_id = (isset($services[$service_key_name][$EnServiceType]['services_id'])) ? $services[$service_key_name][$EnServiceType]['services_id'] : "";
                            $meta_data['service_type'] = $service_type;
                            $meta_data['service_name'] = $service_name;
                            $meta_data['services_id'] = $services_id;
                            $hazardous_material_fee = ($hazardous_material) ? $WC_fedex_small->add_hazardous_material($meta_data['service_type']) : 0;

                            $service_cost = (float)$grand_total + (float)$hazardous_material_fee + (float)$en_box_total_price;
                            $en_service_cost = $grand_total > 0 ? $grand_total + (float)$hazardous_material_fee + (float)$en_box_total_price : 0;
                            $en_service_rate = $grand_total > 0 ? $grand_total + (float)$hazardous_fee + (float)$en_box_total_price : 0;
                            if (($hazardous_material) && ($service_type != "FEDEX_GROUND") && ($service_type != "GROUND_HOME_DELIVERY")) {
                                if (isset($quote_settings['hazardous_materials_shipments']) && ($quote_settings['hazardous_materials_shipments'] == "yes")) {
                                    continue;
                                }
                            }
                            $surcharges = (isset($reset_service->ShipmentRateDetail->Surcharges)) ? $reset_service->ShipmentRateDetail->Surcharges : [];
                            $en_service = array(
                                'id' => $service_type . "_" . $service_name,
                                'fedex_id' => $service_type,
                                'services_id' => $services_id,
                                'discount_services' => $total_discount,
                                'service_type' => $service_type . "_" . $service_name,
                                'cost' => $en_service_cost,
                                'rate' => $en_service_rate,
                                'transit_time' => $transit_time,
                                'delivery_days' => $delivery_days,
                                'title' => $service_title,
                                'label' => $service_title,
                                'label_as' => $service_title,
                                'service_name' => $service_name,
                                'meta_data' => $meta_data,
                                'surcharges' => $this->en_get_accessorials_prices($surcharges, $en_always_accessorial, $en_auto_residential_status, $grand_total),
                                'plugin_name' => 'fedexSmall',
                                'plugin_type' => 'small',
                                'owned_by' => 'eniture'
                            );

                            foreach ($multiple_accessorials as $multiple_accessorials_key => $accessorial) {
                                $en_fliped_accessorial = array_flip($accessorial);
                                // When auto-rad detected
                                (!$this->forcefully_residential_delivery && $en_auto_residential_status == 'r') ? $accessorial[] = 'R' : '';
                                ($this->forcefully_always_residential_delivery && !in_array('R', $accessorial)) ? $accessorial[] = 'R' : '';
                                $en_extra_charges = array_diff_key((isset($en_service['surcharges']) ? $en_service['surcharges'] : []), $en_fliped_accessorial);
                                $en_accessorial_type = implode('', $accessorial);
                                $en_rates[$en_accessorial_type][$en_count_rates] = $en_service;

                                // Service name changed GROUND HOME DELIVERY to FEDEX GROUND
                                if ((isset($en_service['service_type'], $en_service['title'], $en_service['label']) &&
                                        $service_type == 'GROUND_HOME_DELIVERY') &&
                                    $this->forcefully_residential_delivery &&
                                    !in_array('R', $accessorial)) {
                                    $en_rates[$en_accessorial_type][$en_count_rates]['service_type'] = 'FEDEX_GROUND_home_ground_pricing';
                                    $en_rates[$en_accessorial_type][$en_count_rates]['title'] = 'FedEx Ground';
                                    $en_rates[$en_accessorial_type][$en_count_rates]['label'] = 'FedEx Ground';
                                }
                                // Cost of the rates
                                $en_sorting_rates
                                [$en_accessorial_type]
                                [$en_count_rates]['cost'] = // Used for sorting of rates
                                $en_rates
                                [$en_accessorial_type]
                                [$en_count_rates]['cost'] = (isset($en_service['cost']) ? $en_service['cost'] : 0) - array_sum($en_extra_charges);
                                /*discount shipping start*/
                                if(!empty($total_discount)) {
                                    $en_rates[$en_accessorial_type][$en_count_rates]['meta_data']['discount_services'] = $total_discount;
                                }
                                /*discount shipping end*/
                                $en_rates[$en_accessorial_type][$en_count_rates]['meta_data']['label_sufex'] = wp_json_encode($accessorial);
                                $en_rates[$en_accessorial_type][$en_count_rates]['meta_data']['accessorial_charges'] = wp_json_encode($en_service['surcharges']);
                                $en_rates[$en_accessorial_type][$en_count_rates]['label_sufex'] = $accessorial;
                                if (isset($en_rates[$en_accessorial_type][$en_count_rates]['id']) && strlen($en_accessorial_type) > 0) {
                                    $en_rates[$en_accessorial_type][$en_count_rates]['id'] .= '_' . $en_accessorial_type;
                                } else {
                                    $alphabets = 'abcdefghijklmnopqrstuvwxyz';
                                    $rand_string = substr(str_shuffle(str_repeat($alphabets, mt_rand(1, 10))), 1, 10);
                                    $en_rates[$en_accessorial_type][$en_count_rates]['id'] = $rand_string;
                                }

                                if (in_array('R', $accessorial)) {
                                    $en_fdo_meta_data['accessorials']['residential'] = true;
                                }

                                // FDO
                                $en_fdo_meta_data['rate'] = $en_rates[$en_accessorial_type][$en_count_rates];
                                if (isset($en_fdo_meta_data['rate']['meta_data'])) {
                                    unset($en_fdo_meta_data['rate']['meta_data']);
                                }
                                $en_fdo_meta_data['quote_settings'] = $quote_settings;
                                $en_rates[$en_accessorial_type][$en_count_rates]['meta_data']['en_fdo_meta_data'] = $en_fdo_meta_data;
                                $en_count_rates++;
                            }
                        }
                    }
                }
            }
        }

        $en_rates['en_sorting_rates'] = $en_sorting_rates;
        return $en_rates;
    }


    /**
     * Get accessorials prices from api response
     * @param array $accessorials
     * @return array
     */
    public function en_get_accessorials_prices($accessorials, $en_always_accessorial, $en_auto_residential_status, $total_price)
    {
        $surcharges = [];
        $fuel_surcharges = 0;
        $mapp_surcharges = [
            'RESIDENTIAL_DELIVERY' => 'R',
        ];

        if (isset($accessorials->SurchargeType) && $accessorials->SurchargeType == 'FUEL') {
            $fuel_surcharges = $accessorials->Amount->Amount;
        }

        foreach ($accessorials as $key => $accessorial) {
            $key = (isset($accessorial->SurchargeType)) ? $accessorial->SurchargeType : '';
            ($key == 'FUEL') ? $fuel_surcharges = $accessorial->Amount->Amount : '';

            if (isset($mapp_surcharges[$key])) {
                $accessorial = (isset($accessorial->Amount->Amount)) ? $accessorial->Amount->Amount : 0;
                in_array($mapp_surcharges[$key], $en_always_accessorial) && !$this->forcefully_residential_delivery ?
                    $accessorial = 0 : '';
                $en_auto_residential_status == 'r' && $mapp_surcharges[$key] == 'R' && !$this->forcefully_residential_delivery ?
                    $accessorial = 0 : '';
                $surcharges[$mapp_surcharges[$key]] = $accessorial;
            }
        }

        if (isset($surcharges['R']) && $surcharges['R'] > 0) {
            $residential_surcharges = $surcharges['R'];
            $fuel_percentage = ($fuel_surcharges * 100) / ($total_price - $fuel_surcharges);
            $surcharges['R'] = $residential_surcharges + ($residential_surcharges * $fuel_percentage / 100);
        }

        return $surcharges;
    }

    /**
     * Get Calculate service level markup
     * @param $total_charge
     * @param $international_markup
     */
    function calculate_service_level_markup($total_charge, $international_markup, $is_handeling_fee = false)
    {
        $international_markup = !$total_charge > 0 ? 0 : $international_markup;
        $grandTotal = 0;
        if (floatval($international_markup)) {
            $pos = strpos($international_markup, '%');
            if ($pos > 0) {
                $rest = substr($international_markup, $pos);
                $exp = explode($rest, $international_markup);
                $get = $exp[0];
                ($get == '-100') && $is_handeling_fee ? $this->is_minus_100_percent_exists = true : '';
                $percnt = $get / 100 * $total_charge;
                $grandTotal += $total_charge + $percnt;
            } else {
                $grandTotal += $total_charge + $international_markup;
            }
        } else {
            $grandTotal += $total_charge;
        }
        return $grandTotal;
    }

    /**
     * Calculate Handeling Fee For Each Shipment
     * @param $handeling_fee
     * @param $total
     * @return int
     */
    function calculate_handeling_fee($handeling_fee, $total, $is_handeling_fee = false)
    {
        $handeling_fee = !$total > 0 ? 0 : $handeling_fee;
        $grandTotal = 0;
        if (floatval($handeling_fee)) {
            $pos = strpos($handeling_fee, '%');
            if ($pos > 0) {
                $rest = substr($handeling_fee, $pos);
                $exp = explode($rest, $handeling_fee);
                $get = $exp[0];
                ($get == '-100') && $is_handeling_fee ? $this->is_minus_100_percent_exists = true : '';
                $percnt = $get / 100 * $total;
                $grandTotal += $total + $percnt;
            } else {
                $grandTotal += $total + $handeling_fee;
            }
        } else {
            $grandTotal += $total;
        }
        return $grandTotal;
    }

    /**
     * FedEx Get Shipment Rated Array
     * @param $locationGroups
     */
    function RatedShipmentDetails($locationGroups)
    {
        $rates_option = get_option('wc_pulish_negotiate_fedex_small');
        ($rates_option == 'negotiated') ? $searchword = 'PAYOR_ACCOUNT' : $searchword = 'PAYOR_LIST';
        if (isset($locationGroups->ShipmentRateDetail)) {
            $locationGroups = (object)[0 => $locationGroups];
            return $locationGroups;
        }
        $allLocations = array_filter($locationGroups, function ($var) use ($searchword) {
            return preg_match("/^$searchword/", $var->ShipmentRateDetail->RateType);
        });

        return $allLocations;
    }

    /**
     * Return woocomerce and abf version
     */
    function fedexSmpkgWcVersionNumber()
    {
        if (!function_exists('get_plugins'))
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');

        $pluginFolder = get_plugins('/' . 'woocommerce');
        $pluginFile = 'woocommerce.php';
        $wwesmpkPluginFolder = get_plugins('/' . 'small-package-quotes-fedex-edition');
        $wwesmpkgPluginFile = 'small-package-quotes-fedex-edition.php';
        $wcPlugin = (isset($pluginFolder[$pluginFile]['Version'])) ? $pluginFolder[$pluginFile]['Version'] : "";
        $wwesmpkgPlugin = (isset($wwesmpkPluginFolder[$wwesmpkgPluginFile]['Version'])) ? $wwesmpkPluginFolder[$wwesmpkgPluginFile]['Version'] : "";

        $pluginVersions = array(
            "woocommerce_plugin_version" => $wcPlugin,
            "fedexSmpkg_plugin_version" => $wwesmpkgPlugin
        );

        return $pluginVersions;
    }

    public function formatServiceQuotes($quotes, $service_name = '')
    {
        if (empty($quotes)) return $quotes;

        if ($service_name == "weight_based_pricing_smart_post" && !empty($quotes[0]) && !is_int($quotes[0])) {
            return $this->getFormattedQuote($quotes[0]);
        }

        foreach ($quotes as $key => $value) {
            $quotes[$key] = $this->getFormattedQuote($value);
        }

        return $quotes;
    }

    public function getFormattedQuote($quote)
    {
        if (empty($quote) || !isset($quote->serviceType)) return $quote;

        if ($quote->serviceType == 'FEDEX_INTERNATIONAL_PRIORITY') {
            $quote->serviceType = 'INTERNATIONAL_PRIORITY';
        }

        $quote->ServiceType = $quote->serviceType;
        if (isset($quote->operationalDetail->totalTransitTimeInDays)) {
            $quote->totalTransitTimeInDays = $quote->operationalDetail->totalTransitTimeInDays;
        }

        if (isset($quote->operationalDetail->deliveryDate)) {
            $quote->DeliveryTimestamp = $quote->operationalDetail->deliveryDate;
        }

        if (isset($quote->ratedShipmentDetails) && !empty($quote->ratedShipmentDetails)) {
            $ShipmentRateDetail = [];
            foreach ($quote->ratedShipmentDetails as $rs_value) {
                if (isset($rs_value->rateType)) {
                    $ShipmentRateDetail[] = (object)['ShipmentRateDetail' => (object)['RateType' => $rs_value->rateType == 'ACCOUNT' ? 'PAYOR_ACCOUNT_PACKAGE' : 'PAYOR_LIST_PACKAGE', 'TotalNetCharge' => (object)['Amount' => isset($rs_value->totalNetCharge) ? $rs_value->totalNetCharge : 0], 'TotalNetFreight' => (object)['Amount' => isset($rs_value->totalBaseCharge) ? $rs_value->totalBaseCharge : 0]], 'RatedPackages' => isset($rs_value->ratedPackages) ? $rs_value->ratedPackages : []];
                }
            }

            $quote->RatedShipmentDetails = $ShipmentRateDetail;
        }

        if (isset($value->ratedShipmentDetails->ACCOUNT->shipmentRateDetail->surCharges)) {
            $quote->surcharges = $value->ratedShipmentDetails->ACCOUNT->shipmentRateDetail->surCharges;
        }

        return $quote;
    }

}
