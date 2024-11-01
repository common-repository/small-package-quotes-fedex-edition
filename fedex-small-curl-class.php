<?php

/**
 * Class FedEx_Feight_Curl_Request
 *
 * @package     FedEx Freight Quotes
 * @subpackage  Curl Call
 * @author      Eniture-Technology
 */


if (!defined('ABSPATH')) {
    exit; // exit if direct access
}

/**
 * Class to call curl request
 */
class FedEx_Small_Curl_Request
{
    /**
     * Get Curl Response
     * @param  $url curl hitting url
     * @param  $postData post data to get response
     * @return string
     */

    function fedex_small_get_curl_response($url, $postData)
    {
        if (!empty($url) && !empty($postData)) {
            $field_string = http_build_query($postData);

            $response = wp_remote_post($url,
                array(
                    'method' => 'POST',
                    'timeout' => 60,
                    'redirection' => 5,
                    'blocking' => true,
                    'body' => $field_string,
                )
            );

            $output = wp_remote_retrieve_body($response);
            return $output;
        }
    }

}
