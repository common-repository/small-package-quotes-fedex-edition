<?php
/**
 * Includes Engine class
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("FedexSmallCarriers")) {
    class FedexSmallCarriers
    {

        /**
         * construct
         */
        public function __construct()
        {
            add_filter('fedex_small_domestic_services', array($this, 'fedex_small_domestic_services'), 10, 1);
            add_filter('fedex_small_one_rate_services', array($this, 'fedex_small_one_rate_services'), 10, 1);
            add_filter('fedex_small_international_services', array($this, 'fedex_small_international'), 10, 1);
        }

        public function fedex_small_domestic_services()
        {

            $services = array();

            if (get_option('fedex_small_home_delivery') == 'yes') {
                $services['GROUND_HOME_DELIVERY'] = ['name' => 'FedEx Home Delivery', 'markup' => get_option('fedex_small_home_delivery_markup'), 'services_id' => 1];
            }

            if (get_option('fedex_small_ground') == 'yes') {
                $services['FEDEX_GROUND'] = 'FedEx Ground';
                $services['FEDEX_GROUND'] = ['name' => 'FedEx Ground', 'markup' => get_option('fedex_small_ground_markup'), 'services_id' => 2];
            }

            if (get_option('fedex_small_express_saver') == 'yes') {
                $services['FEDEX_EXPRESS_SAVER'] = ['name' => 'FedEx Express Saver', 'markup' => get_option('fedex_small_express_saver_markup'), 'services_id' => 3];
            }

            if (get_option('fedex_small_2_day') == 'yes') {
                $services['FEDEX_2_DAY'] = ['name' => 'FedEx 2Day', 'markup' => get_option('fedex_small_2_day_markup'), 'services_id' => 4];
            }

            if (get_option('fedex_small_2_day_AM') == 'yes') {
                $services['FEDEX_2_DAY_AM'] = ['name' => 'FedEx 2Day AM', 'markup' => get_option('fedex_small_2_day_AM_markup'), 'services_id' => 5];
            }

            if (get_option('fedex_small_standard_overnight') == 'yes') {
                $services['STANDARD_OVERNIGHT'] = ['name' => 'FedEx Standard Overnight', 'markup' => get_option('fedex_small_standard_overnight_markup'), 'services_id' => 6];
            }

            if (get_option('fedex_small_priority_overnight') == 'yes') {
                $services['PRIORITY_OVERNIGHT'] = ['name' => 'FedEx Priority Overnight', 'markup' => get_option('fedex_small_priority_overnight_markup'), 'services_id' => 7];
            }

            if (get_option('fedex_small_first_overnight') == 'yes') {
                $services['FIRST_OVERNIGHT'] = ['name' => 'FedEx First Overnight', 'markup' => get_option('fedex_small_first_overnight_markup'), 'services_id' => 8];
            }

            if (get_option('fedex_small_smart_post') == 'yes') {
                $services['SMART_POST'] = ['name' => 'FedEx SmartPost', 'markup' => get_option('fedex_small_smart_post_markup'), 'services_id' => 9];
            }

            return $services;
        }

        public function fedex_small_one_rate_services()
        {
            $services = array();

            if (get_option('fedex_small_one_rate_express_saver') == 'yes') {
                $services['FEDEX_EXPRESS_SAVER'] = ['name' => 'FedEx One Rate Express Saver', 'markup' => get_option('fedex_small_saver_onerate_markup'), 'services_id' => 10];
            }

            if (get_option('fedex_small_one_rate_2_day') == 'yes') {
                $services['FEDEX_2_DAY'] = ['name' => 'FedEx One Rate 2Day', 'markup' => get_option('fedex_small_2day_srvc_onerate_markup'), 'services_id' => 11];
            }

            if (get_option('fedex_small_one_rate_2_day_AM') == 'yes') {
                $services['FEDEX_2_DAY_AM'] = ['name' => 'FedEx One Rate 2Day AM', 'markup' => get_option('fedex_small_2day_am_onerate_markup'), 'services_id' => 12];
            }

            if (get_option('fedex_small_one_rate_standard_overnight') == 'yes') {
                $services['STANDARD_OVERNIGHT'] = ['name' => 'FedEx One Rate Standard Overnight', 'markup' => get_option('fedex_small_st_overnight_onerate_markup'), 'services_id' => 13];
            }

            if (get_option('fedex_small_one_rate_priority_overnight') == 'yes') {
                $services['PRIORITY_OVERNIGHT'] = ['name' => 'FedEx One Rate Priority Overnight', 'markup' => get_option('fedex_small_pr_overnight_onerate_markup'), 'services_id' => 14];
            }

            if (get_option('fedex_small_one_rate_first_overnight') == 'yes') {
                $services['FIRST_OVERNIGHT'] = ['name' => 'FedEx One Rate First Overnight', 'markup' => get_option('fedex_small_fst_overnight_onerate_markup'), 'services_id' => 15];
            }

            return $services;
        }

        public function fedex_small_international()
        {
            $services = array();

            if (get_option('fedex_small_int_ground') == 'yes') {
                $services['FEDEX_GROUND'] = ['name' => 'FedEx International Ground', 'markup' => get_option('fedex_small_int_ground_markup'), 'services_id' => 16];
            }

            if (get_option('fedex_small_int_distribution_freight') == 'yes') {
                $services['INTERNATIONAL_DISTRIBUTION_FREIGHT'] = ['name' => 'FedEx International Distribution Freight', 'markup' => get_option('fedex_small_int_distribution_freight_markup'), 'services_id' => 17];
            }

            if (get_option('fedex_small_int_economy') == 'yes') {
                $services['INTERNATIONAL_ECONOMY'] = ['name' => 'FedEx International Economy', 'markup' => get_option('fedex_small_int_economy_markup'), 'services_id' => 18];
            }

            if (get_option('fedex_small_int_eco_distribution') == 'yes') {
                $services['INTERNATIONAL_ECONOMY_DISTRIBUTION'] = ['name' => 'FedEx International Economy Distribution', 'markup' => get_option('fedex_small_int_eco_distribution_markup'), 'services_id' => 19];
            }

            if (get_option('fedex_small_int_eco_freight') == 'yes') {
                $services['INTERNATIONAL_ECONOMY_FREIGHT'] = ['name' => 'FedEx International Economy Freight', 'markup' => get_option('fedex_small_int_eco_freight_markup'), 'services_id' => 20];
            }

            if (get_option('fedex_small_int_first') == 'yes') {
                $services['INTERNATIONAL_FIRST'] = ['name' => 'FedEx International First', 'markup' => get_option('fedex_small_int_first_markup'), 'services_id' => 21];
            }

            if (get_option('fedex_small_int_priority') == 'yes') {
                $services['INTERNATIONAL_PRIORITY'] = ['name' => 'FedEx International Priority', 'markup' => get_option('fedex_small_int_priority_markup'), 'services_id' => 22];
            }

            if (get_option('fedex_small_int_priority_distribution') == 'yes') {
                $services['INTERNATIONAL_PRIORITY_DISTRIBUTION'] = ['name' => 'FedEx International Priority Distribution', 'markup' => get_option('fedex_small_int_priority_distribution_markup'), 'services_id' => 23];
            }

            if (get_option('fedex_small_int_priority_freight') == 'yes') {
                $services['INTERNATIONAL_PRIORITY_FREIGHT'] = ['name' => 'FedEx Priority Freight', 'markup' => get_option('fedex_small_int_priority_freight_markup'), 'services_id' => 24];
            }

            return $services;
        }
    }

    new FedexSmallCarriers();
}
