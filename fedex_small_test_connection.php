<?php
/**
 * FedEx Small Test connection
 * 
 * @package     FedEx Small Quotes
 * @author      Eniture-Technology
 */
    if ( ! defined( 'ABSPATH' ) ) {
	exit; 
    }

/**
 * FedEx Small Test connection AJAX Request
 */

    add_action( 'wp_ajax_nopriv_fedex_small_test_connection', 'fedex_test_submit' );
    add_action( 'wp_ajax_fedex_small_test_connection', 'fedex_test_submit' );
    /**
     * Test connection FUnction
     */
    function fedex_test_submit() 
    {
        $auth   = ( isset( $_POST['fedex_small_auth'] ) )       ? sanitize_text_field($_POST['fedex_small_auth'])       : '';
        $pass   = ( isset( $_POST['fedex_small_password'] ) )   ? sanitize_text_field($_POST['fedex_small_password'])   : ''; 
        $acc    = ( isset( $_POST['fedex_small_acc_number'] ) ) ? sanitize_text_field($_POST['fedex_small_acc_number']) : ''; 
        $meter  = ( isset( $_POST['fedex_small_meter'] ) )      ? sanitize_text_field($_POST['fedex_small_meter'])      : ''; 
        $lcns   = ( isset( $_POST['fedex_small_license'] ) )    ? sanitize_text_field($_POST['fedex_small_license'])    : '';
        $client_id = ( isset( $_POST['fedex_small_client_id'] ) ) ? sanitize_text_field($_POST['fedex_small_client_id']) : '';
        $client_secret = ( isset( $_POST['fedex_small_client_secret'] ) ) ? sanitize_text_field($_POST['fedex_small_client_secret']) : '';
        $new_api_acc = ( isset( $_POST['fedex_small_new_api_acc_number'] ) ) ? sanitize_text_field($_POST['fedex_small_new_api_acc_number']) : '';
        $apiEnabled = ( isset( $_POST['fedex_small_api_selected'] ) ) ? sanitize_text_field($_POST['fedex_small_api_selected']) : '';
        $domain = fedex_small_get_domain();
        
        $data = array(
            'fedex_user_id'         => $auth,
            'fedex_password'        => $pass,
            'fedex_meter_number'    => $meter,
            'fedex_account_number'  => $acc,
            'licence_key'           => $lcns,
            'carrierName'           => 'FedexSmall',
            'plateform'             => 'WordPress',
            'carrier_mode'          => 'test',
            'sever_name'            => $domain,
            // New API Parameters
            'requestForNewAPI'      => $apiEnabled == 'new_api',
            'clientId'              => $client_id,
            'clientSecret'          => $client_secret,
            'accountNumber'         => $new_api_acc
        );
        $url          = FEDEX_DOMAIN_HITTING_URL . '/s/fedex/fedex_shipment_rates_test.php';
        $field_string = http_build_query($data);
        $response = wp_remote_post($url,
            array(
                'method' => 'POST',
                'timeout' => 60,
                'redirection' => 5,
                'blocking' => true,
                'body' => $field_string,
            )
        );
        
        $Response = wp_remote_retrieve_body($response);
        echo $Response;
        exit();
    }
    
    /**
     * Get Host 
     * @param type $url
     * @return type
     */
    if(!function_exists('getHost')){
        function getHost($url) { 
            $parseUrl = parse_url(trim($url)); 
            if(isset($parseUrl['host'])){
                $host = $parseUrl['host'];
            }else{
                 $path = explode('/', $parseUrl['path']);
                 $host = $path[0];
            }
            return trim($host); 
        }
    }
    
    /**
     * Get Domain Name 
     */
    if(!function_exists('fedex_small_get_domain')){
        function fedex_small_get_domain(){
            global $wp;
            $url =  home_url( $wp->request );
            return getHost($url);
        }
    }
