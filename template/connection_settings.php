<?php
/**
 * FedEx Small Connection Settings
 *
 * @package     FedEx Small Quotes
 * @author      Eniture-Technology
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * FedEx Small Connection Settings Tab Class
 */
class FedEx_Small_Connection_Settings
{

    /**
     * Connection Settings Fields
     */

    public function fedex_small_con_setting()
    {
        echo '<div class="fedex_small_connection_section">';
        $settings = array(
            'section_title_fedex_small' => array(
                'name' => __('', 'woocommerce-settings-fedex_small'),
                'type' => 'title',
                'desc' => '<br> ',
                'id' => 'fedex_small_connection_title',
            ),

             'api_selection_fedex_small' => array(
                'name' => __('Which API Will You Connect To?', 'woocommerce-settings-fedex_small'),
                'type' => 'select',
                'desc' => __('', 'woocommerce-settings-fedex_small'),
                'default' => 'legacy_api',
                'options' => [
                    'legacy_api' => __('Legacy API', 'woocommerce-settings-fedex_small'),
                    'new_api' => __('New API', 'woocommerce-settings-fedex_small'),
                ],
                'id' => 'api_selection_fedex_small',
              ),

              
             // New API
            'client_id_fedex_small' => array(
                'name' => __('API Key ', 'woocommerce-settings-fedex_small'),
                'type' => 'text',
                'desc' => __('', 'woocommerce-settings-fedex_small'),
                'id' => 'fedex_small_client_id',
                'class' => 'fedex_small_new_api'
            ),
            'client_secret_fedex_small' => array(
                'name' => __('Secret Key ', 'woocommerce-settings-fedex_small'),
                'type' => 'text',
                'desc' => __('', 'woocommerce-settings-fedex_small'),
                'id' => 'fedex_small_client_secret',
                'class' => 'fedex_small_new_api'
            ),
            'fedex_small_new_api_acc_number' => array(
                'name' => __('Account Number ', 'woocommerce-settings-fedex_small'),
                'type' => 'text',
                'desc' => __('', 'woocommerce-settings-fedex_small'),
                'id' => 'fedex_small_new_api_acc_number',
                'class' => 'fedex_small_new_api'
            ),

            'acc_number_fedex_small' => array(
                'name' => __('Account Number ', 'woocommerce-settings-fedex_small'),
                'type' => 'text',
                'desc' => __('', 'woocommerce-settings-fedex_small'),
                'id' => 'fedex_small_account_number'
            ),

            'password_fedex_small' => array(
                'name' => __('Production Password ', 'woocommerce-settings-fedex_small'),
                'type' => 'text',
                'desc' => __('', 'woocommerce-settings-fedex_small'),
                'id' => 'fedex_small_password'
            ),

            'meter_number_fedex_small' => array(
                'name' => __('Meter Number ', 'woocommerce-settings-fedex_small'),
                'type' => 'text',
                'desc' => __('', 'woocommerce-settings-fedex_small'),
                'id' => 'fedex_small_meter_number'
            ),

            'auth_key_fedex_small' => array(
                'name' => __('Authentication Key ', 'woocommerce-settings-fedex_small'),
                'type' => 'text',
                'desc' => __('', 'woocommerce-settings-fedex_small'),
                'id' => 'fedex_small_auth_key'
            ),

            'licence_key_fedex_small' => array(
                'name' => __('Eniture API Key ', 'woocommerce-settings-fedex_small'),
                'type' => 'text',
                'desc' => __('obtain a Eniture API Key from <a href="https://eniture.com/woocommerce-fedex-small-package-plugin/" target="_blank" >eniture.com </a>', 'woocommerce-settings-fedex_small'),
                'id' => 'fedex_small_licence_key'
            ),

            'hub_id_fedex_small' => array(
                'name' => __('Hub Id ', 'woocommerce-settings-fedex_small_quotes'),
                'type' => 'select',
                'default' => '3',
                'desc' => __('', 'woocommerce-settings-fedex_small_quotes'),
                'id' => 'hub_id_fedex_small',
                'options' => array(
                    '0' => __('Select', 'Select'),
                    '5185' => __('5185 ALPA Allentown', '5185 ALPA Allentown'),
                    '5303' => __('5303 ATGA Atlanta', '5303 ATGA Atlanta'),
                    '5281' => __('5281 CHNC Charlotte', '5281 CHNC Charlotte'),
                    '5929' => __('5929 COCA Chino', '5929 COCA Chino'),
                    '5751' => __('5751 DLTX Dallas', '5751 DLTX Dallas'),
                    '5802' => __('5802 DNCO Denver', '5802 DNCO Denver'),
                    '5481' => __('5481 DTMI Detroit', '5481 DTMI Detroit'),
                    '5087' => __('5087 EDNJ Edison', '5087 EDNJ Edison'),
                    '5431' => __('5431 GCOH Grove City', '5431 GCOH Grove City'),
                    '5771' => __('5771 HOTX Houston', '5771 HOTX Houston'),
                    '5436' => __('5436 GPOH Groveport Ohio', '5436 GPOH Groveport Ohio'),
                    '5902' => __('5902 LACA Los Angeles', '5902 LACA Los Angeles'),
                    '5465' => __('5465 ININ Indianapolis', '5465 ININ Indianapolis'),
                    '5648' => __('5648 KCKS Kansas City', '5648 KCKS Kansas City'),
                    '5254' => __('5254 MAWV Martinsburg', '5254 MAWV Martinsburg'),
                    '5379' => __('5379 METN Memphis', '5379 METN Memphis'),
                    '5552' => __('5552 MPMN Minneapolis', '5552 MPMN Minneapolis'),
                    '5531' => __('5531 NBWI New Berlin', '5531 NBWI New Berlin'),
                    '5110' => __('5110 NENY Newburgh', '5110 NENY Newburgh'),
                    '5015' => __('5015 NOMA Northborough', '5015 NOMA Northborough'),
                    '5327' => __('5327 ORFL Orlando', '5327 ORFL Orlando'),
                    '5194' => __('5194 PHPA Philadelphia', '5194 PHPA Philadelphia'),
                    '5854' => __('5854 PHAZ Phoenix', '5854 PHAZ Phoenix'),
                    '5150' => __('5150 PTPA Pittsburgh', '5150 PTPA Pittsburgh'),
                    '5958' => __('5958 SACA Sacramento', '5958 SACA Sacramento'),
                    '5843' => __('5843 SCUT Salt Lake City', '5843 SCUT Salt Lake City'),
                    '5983' => __('5983 SEWA Seattle', '5983 SEWA Seattle'),
                    '5631' => __('5631 STMO St. Louis', '5631 STMO St. Louis'),
                    '5893' => __('5893 RENV Reno', '5893 RENV Reno'),
                )
            ),

            'section_end_fedex_small' => array(
                'type' => 'sectionend',
                'id' => 'fedex_small_licence_key'
            ),
        );

        return $settings;
    }
}
