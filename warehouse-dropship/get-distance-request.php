<?php

/**
 * WWE Small Get Distance
 *
 * @package     WWE Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Distance Request Class
 */
class Get_fedex_small_distance
{

    function __construct()
    {
        add_filter("en_wd_get_address", array($this, "sm_address"), 10, 2);
    }

    /**
     * Get Address Upon Access Level
     * @param $map_address
     * @param $accessLevel
     */
    function fedex_small_address($map_address, $accessLevel, $destinationZip = array())
    {

        $domain = fedex_small_get_domain();
        $postData = array(
            'acessLevel' => $accessLevel,
            'address' => $map_address,
            'originAddresses' => (isset($map_address)) ? $map_address : "",
            'destinationAddress' => (isset($destinationZip)) ? $destinationZip : "",
            'eniureLicenceKey' => get_option('fedex_small_licence_key'),
            'ServerName' => $domain,
        );
        $FedEx_Small_Curl_Request = new FedEx_Small_Curl_Request();
        $output = $FedEx_Small_Curl_Request->fedex_small_get_curl_response(FEDEX_DOMAIN_HITTING_URL . '/addon/google-location.php', $postData);

        return $output;
    }

}
