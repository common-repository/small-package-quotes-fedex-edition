<?php

/**
 * WWE Small Save Warehouse
 * 
 * @package     WWE Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_sm_get_address', 'get_address_api_ajax');
add_action('wp_ajax_nopriv_sm_get_address', 'get_address_api_ajax');

/**
 * Get Address From ZipCode Using API
 */
function get_address_api_ajax() {

    if (isset($_POST['origin_zip'])) {
        require_once 'get-distance-request.php';
        $map_address = $_POST['origin_zip'];
        $zipCode = str_replace(' ', '', $map_address);
        $accessLevel = 'address';
        $resp_json = Get_sm_distance::sm_address($zipCode, $accessLevel);
        $map_result = json_decode($resp_json, true);
        $city = "";
        $state = "";
        $country = "";
        $postcode_localities = 0;
        $address_type = $city_name = $city_option = '';
        if (isset($map_result['error']) && !empty($map_result['error'])) {
            echo json_encode(array('apiResp' => 'apiErr'));
            exit;
        }
        if (count($map_result['results']) == 0) {
            echo json_encode(array('result' => 'false'));
            exit;
        }
        $first_city = '';
        if (count($map_result['results']) > 0) {
            $arrComponents = $map_result['results'][0]['address_components'];
            if (isset($map_result['results'][0]['postcode_localities']) && $map_result['results'][0]['postcode_localities']) {
                foreach ($map_result['results'][0]['postcode_localities'] as $index => $component) {
                    $first_city = ($index == 0) ? $component : $first_city;
                    $city_option .= '<option value="' . trim($component) . ' "> ' . $component . ' </option>';
                }
                $city = '<select id="' . $address_type . '_city" class="city-multiselect select sm_multi_state city_select_css" name="' . $address_type . '_city" aria-required="true" aria-invalid="false">
                            ' . $city_option . '</select>';
                $postcode_localities = 1;
            } elseif ($arrComponents) {
                foreach ($arrComponents as $index => $component) {
                    $type = $component['types'][0];
                    if ($city == "" && ( $type == "sublocality_level_1" || $type == "locality" )) {
                        $city_name = trim($component['long_name']);
                    }
                }
            }
            if ($arrComponents) {
                foreach ($arrComponents as $index => $state_app) {
                    $type = $state_app['types'][0];
                    if ($state == "" && ( $type == "administrative_area_level_1" )) {
                        $state_name = trim($state_app['short_name']);
                        $state = $state_name;
                    }
                    if ($country == "" && ( $type == "country" )) {
                        $country_name = trim($state_app['short_name']);
                        $country = $country_name;
                    }
                }
            }
            echo json_encode(array('first_city' => $first_city, 'city' => $city_name, 'city_option' => $city, 'state' => $state, 'country' => $country, 'postcode_localities' => $postcode_localities));
            exit;
        }
    }
}

/**
 * Validate Input Fields
 * @param type $sPostData
 * @return string
 */
function smpkgValidatePostData($sPostData) {

    foreach ($sPostData as $key => &$tag) {
        $check_characters = preg_match('/[#$%@^&!_*()+=\-\[\]\';,.\/{}|":<>?~\\\\]/', $tag);
        if ($check_characters != 1) {
            $data[$key] = sanitize_text_field($tag);
        } else {
            $data[$key] = 'Error';
        }
    }
    return $data;
}

/**
 * Filtered Data Array
 * @param $validateData
 * @return array
 */
function wwe_small_filtered_data($validateData) {

    return array(
        'city' => $validateData["city"],
        'state' => $validateData["state"],
        'zip' => preg_replace('/\s+/', '', $validateData["zip"]),
        'country' => $validateData["country"],
        'location' => $validateData["location"],
        'nickname' => ( isset($validateData["nickname"]) ) ? $validateData["nickname"] : "",
    );
}

add_action('wp_ajax_sm_save_warehouse', 'save_warehouse_ajax');
add_action('wp_ajax_nopriv_sm_save_warehouse', 'save_warehouse_ajax');

/**
 * Save Warehouse Function
 * @global $wpdb
 */
function save_warehouse_ajax() {

    global $wpdb;
    $input_data_arr = array(
        'city' => $_POST['origin_city'],
        'state' => $_POST['origin_state'],
        'zip' => $_POST['origin_zip'],
        'country' => $_POST['origin_country'],
        'location' => $_POST['location'],
    );

    $validateData = smpkgValidatePostData($input_data_arr);
    $get_warehouse = $wpdb->get_results(
            "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE city = '" . $validateData["city"] . "' && state = '" . $validateData["state"] . "' && zip = '" . $validateData["zip"] . "'");
    if ($validateData["city"] != 'Error') {
        $data = wwe_small_filtered_data($validateData);
        if (isset($validateData["city"])) {
            $get_warehouse_id = ( isset($_POST['origin_id']) && intval($_POST['origin_id']) ) ? $_POST['origin_id'] : "";
            if ($get_warehouse_id && empty($get_warehouse)) {
                $update_qry = $wpdb->update(
                        $wpdb->prefix . 'warehouse', $data, array('id' => $get_warehouse_id)
                );
            } else {
                if (empty($get_warehouse)) {
                    $insert_qry = $wpdb->insert(
                            $wpdb->prefix . 'warehouse', $data
                    );
                }
            }
        }
        $lastid = $wpdb->insert_id;
        if ($lastid == 0) {
            $lastid = $get_warehouse_id;
        }
        $warehous_list = array('origin_city' => $data["city"], 'origin_state' => $data["state"], 'origin_zip' => $data["zip"], 'origin_country' => $data["country"], 'insert_qry' => $insert_qry, 'update_qry' => $update_qry, 'id' => $lastid);
        echo json_encode($warehous_list);
        exit;
    } else {
        echo "false";
        exit;
    }
}

add_action('wp_ajax_sm_edit_warehouse', 'edit_warehouse_ajax');
add_action('wp_ajax_nopriv_sm_edit_warehouse', 'edit_warehouse_ajax');

/**
 * Edit Warehouse Function
 * @global $wpdb
 */
function edit_warehouse_ajax() {

    global $wpdb;
    $get_warehouse_id = ( isset($_POST['edit_id']) && intval($_POST['edit_id']) ) ? $_POST['edit_id'] : "";
    $warehous_list = $wpdb->get_results(
            "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE id=$get_warehouse_id"
    );
    echo json_encode($warehous_list);
    exit;
}

add_action('wp_ajax_sm_delete_warehouse', 'delete_warehouse_ajax');
add_action('wp_ajax_nopriv_sm_delete_warehouse', 'delete_warehouse_ajax');

/**
 * Delete Warehouse Function
 * @global $wpdb
 */
function delete_warehouse_ajax() {

    global $wpdb;
    $get_warehouse_id = ( isset($_POST['delete_id']) && intval($_POST['delete_id']) ) ? $_POST['delete_id'] : "";
    $qry = $wpdb->delete($wpdb->prefix . 'warehouse', array('id' => $get_warehouse_id, 'location' => 'warehouse'));
    echo $qry;
    exit;
}

add_action('wp_ajax_sm_save_dropship', 'save_dropship_ajax');
add_action('wp_ajax_nopriv_sm_save_dropship', 'save_dropship_ajax');

/**
 * Save Dropship Function
 * @global $wpdb
 */
function save_dropship_ajax() {

    global $wpdb;
    $input_data_arr = array(
        'city' => $_POST['dropship_city'],
        'state' => $_POST['dropship_state'],
        'zip' => $_POST['dropship_zip'],
        'country' => $_POST['dropship_country'],
        'location' => $_POST['location'],
        'nickname' => $_POST['nickname'],
    );
    $validateData = smpkgValidatePostData($input_data_arr);
    $get_warehouse = $wpdb->get_results(
            "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE city = '" . $validateData["city"] . "' && state = '" . $validateData["state"] . "' && zip = '" . $validateData["zip"] . "' && nickname = '" . $validateData["nickname"] . "'");
    if ($validateData["city"] != 'Error' && $validateData["nickname"] != 'Error') {
        $data = wwe_small_filtered_data($validateData);
        if (isset($validateData["city"])) {
            $get_dropship_id = ( isset($_POST['dropship_id']) && intval($_POST['dropship_id']) ) ? $_POST['dropship_id'] : "";
            if ($get_dropship_id != '' && empty($get_warehouse)) {
                $update_qry = $wpdb->update(
                        $wpdb->prefix . 'warehouse', $data, array('id' => $get_dropship_id)
                );
            } else {
                if (empty($get_warehouse)) {
                    $insert_qry = $wpdb->insert(
                            $wpdb->prefix . 'warehouse', $data
                    );
                }
            }
        }
        $lastid = $wpdb->insert_id;
        if ($lastid == 0) {
            $lastid = $get_dropship_id;
        }
        $warehous_list = array('nickname' => $data["nickname"], 'origin_city' => $data["city"], 'origin_state' => $data["state"], 'origin_zip' => $data["zip"], 'origin_country' => $data["country"], 'insert_qry' => $insert_qry, 'update_qry' => $update_qry, 'id' => $lastid);
        echo json_encode($warehous_list);
        exit;
    } else {
        echo "false";
        exit;
    }
}

add_action('wp_ajax_sm_edit_dropship', 'edit_dropship_ajax');
add_action('wp_ajax_nopriv_sm_edit_dropship', 'edit_dropship_ajax');

/**
 * Edit Dropship Function
 * @global $wpdb
 */
function edit_dropship_ajax() {

    global $wpdb;
    $get_dropship_id = ( isset($_POST['dropship_edit_id']) && intval($_POST['dropship_edit_id']) ) ? $_POST['dropship_edit_id'] : "";
    $warehous_list = $wpdb->get_results(
            "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE id=$get_dropship_id"
    );
    echo json_encode($warehous_list);
    exit;
}

add_action('wp_ajax_sm_delete_dropship', 'delete_dropship_ajax');
add_action('wp_ajax_nopriv_sm_delete_dropship', 'delete_dropship_ajax');

/**
 * Delete Dropship Function
 * @global $wpdb
 */
function delete_dropship_ajax() {

    global $wpdb;
    $dropship_id = ( isset($_POST['dropship_delete_id']) && intval($_POST['dropship_delete_id']) ) ? $_POST['dropship_delete_id'] : "";
    $get_dropship_id = array($dropship_id);
    $ser = maybe_serialize($get_dropship_id);
    $get_post_id = $wpdb->get_results("SELECT group_concat(post_id) as post_ids_list FROM `" . $wpdb->prefix . "postmeta` WHERE `meta_key` = '_dropship_location' AND (`meta_value` LIKE '%" . $ser . "%' OR `meta_value` = '" . $dropship_id . "')");
    $post_id = reset($get_post_id)->post_ids_list;
    if (isset($post_id)) {
        $wpdb->query("UPDATE `" . $wpdb->prefix . "postmeta` SET `meta_value` = '' WHERE `meta_key` IN('_enable_dropship','_dropship_location')  AND `post_id` IN ($post_id)");
    }
    $qry = $wpdb->delete($wpdb->prefix . "warehouse", array('id' => $dropship_id, 'location' => 'dropship'));
    echo $qry;
    exit;
}