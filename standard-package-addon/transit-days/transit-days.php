<?php

/**
 * transit days 
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnFedExSmallTransitDays")) {

    class EnFedExSmallTransitDays {

        public function __construct() {
        }

        /**
         * 
         * @param array type $result
         * @return json_encode type
         */
        public function wwe_small_enable_disable_ups_ground($result) {

            $transit_day_type = get_option('restrict_radio_btn_transit_fedex_small');
            $response = (isset($result->q)) ? $result->q : array();
            $days_to_restrict = get_option('ground_transit_wwe_small_packages');

            $package = apply_filters('fedex_small_quotes_plans_suscription_and_features', 'transit_days');
            if (!is_array($package) && strlen($days_to_restrict) > 0 && strlen($transit_day_type) > 0) {
                foreach ($response as $row => $service) {
                    if ($service->serviceCode == "GND" &&
                            (isset($service->$transit_day_type)) &&
                            ($service->$transit_day_type >= $days_to_restrict))
                        unset($result->q[$row]);
                }
            }

            return json_encode($result);
        }

        public function fedex_enable_disable_service_ground($response) {
            $transit_day_type = get_option('restrict_radio_btn_transit_fedex_small'); //get value of check box to see which one is checked 
            $days_to_restrict = get_option('restrict_days_transit_package_fedex_small');
            $transit_days = apply_filters('fedex_small_quotes_plans_suscription_and_features', 'transit_days');
            if (!is_array($transit_days) && strlen($days_to_restrict) > 0 && strlen($transit_day_type) > 0) {
                foreach ($response as $row => $service) {
                    if ( isset($service->ServiceType) && ($service->ServiceType == "FEDEX_GROUND" || $service->ServiceType == "GROUND_HOME_DELIVERY") 
                            && isset($service->$transit_day_type) && 
                            ($service->$transit_day_type > $days_to_restrict))
                    {
                        unset($response[$row]);
                    }
                }
            }

            return $response;  //return the encoded json response 
        }

    }

}
        

