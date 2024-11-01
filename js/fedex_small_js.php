<?php
/**
 * FedEx Small JS
 *
 * @package     FedEx Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Connection Section Fields Validation
 *
 * Quote Setting Section Fields Validation
 *
 * Test Connection AJAX
 */
add_action('admin_footer', 'fedex_small_connection_setting');

/**
 * JS
 */
function fedex_small_connection_setting()
{
    ?>
    <script>

        // Update plan
        if (typeof en_update_plan != 'function') {
            function en_update_plan(input) {
                let action = jQuery(input).attr('data-action');
                jQuery.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: {action: action},
                    success: function (data_response) {
                        window.location.reload(true);
                    }
                });
            }
        }

        jQuery(document).ready(function () {
            jQuery(".fedex_small_int_quotes_services_markup, .fedex_small_quotes_services_markup, #restrict_days_transit_package_fedex_small , #en_fedex_small_ground_hazardous_material_fee , #en_fedex_small_air_hazardous_material_fee , #fedex_small_hand_fee_mark_up").focus(function (e) {
                jQuery("#" + this.id).css({'border-color': '#ddd'});
            });
            jQuery("#fedex_small_hand_fee_mark_up").closest('tr').addClass("fedex_small_hand_fee_mark_up");
            jQuery("#fedex_small_allow_other_plugins_option").closest('tr').addClass("fedex_small_allow_other_plugins_option");

            jQuery(".fedex_small_one_rate_hide_me").closest('tr').addClass("fedex_small_one_rate_hide_me");
            jQuery("#wc_settings_one_rate_select_all_").closest('tr').addClass("wc_settings_one_rate_select_all_");

            jQuery("#fedex_sm_ground_transit_label").closest('tr').addClass("fedex_sm_ground_transit_label");
            jQuery("#fedex_small_hazardous_fee").closest('tr').addClass("fedex_small_hazardous_fee");
            jQuery("#restrict_days_transit_package_fedex_small").closest('tr').addClass("restrict_days_transit_package_fedex_small");
            jQuery("input[name*='restrict_radio_btn_transit_fedex_small']").closest('tr').addClass('restrict_radio_btn_transit_fedex_small');
            jQuery("input[name*='fedex_small_hazardous_materials_shipments']").closest('tr').addClass('fedex_small_hazardous_materials_shipments');
            jQuery("input[name*='en_fedex_small_ground_hazardous_material_fee']").closest('tr').addClass('en_fedex_small_ground_hazardous_material_fee');
            jQuery("input[name*='en_fedex_small_air_hazardous_material_fee']").closest('tr').addClass('en_fedex_small_air_hazardous_material_fee');

            jQuery("#fedex_small_quote_as_residential_delivery").closest('tr').addClass("fedex_small_quote_as_residential_delivery");
            jQuery("#avaibility_auto_residential").closest('tr').addClass("avaibility_auto_residential");


            jQuery(".fedex_small_quotes_services_markup").closest('tr').addClass('markup_text_fields_tr');
            jQuery(".fedex_small_int_quotes_services_markup").closest('tr').addClass('markup_text_fields_tr');

            jQuery("#fedex_small_int_srvc_hdng").closest('tr').addClass('fedex_small_int_srvc_hdng_tr');
            jQuery('#fedex_small_dom_srvc_hdngggg').closest('tr').addClass('fedex_small_one_rate_tr');
            jQuery("#fedex_small_cutOffTime_shipDateOffset").closest('tr').addClass("fedex_small_cutOffTime_shipDateOffset_required_label");
            jQuery("#fedex_small_orderCutoffTime").closest('tr').addClass("fedex_small_cutOffTime_shipDateOffset");
            jQuery("#fedex_small_shipmentOffsetDays").closest('tr').addClass("fedex_small_cutOffTime_shipDateOffset");
            jQuery("#fedex_small_timeformate").closest('tr').addClass("fedex_small_timeformate");

            jQuery(".fedex_small_dont_show_estimate_option").closest('tr').addClass("fedex_small_dont_show_estimate_option_tr");
            jQuery("#service_small_estimates_title").closest('tr').addClass("service_small_estimates_title_tr");
            jQuery("input[name=fedex_small_delivery_estimates]").closest('tr').addClass("fedex_small_delivery_estimates_tr");
            jQuery("#service_fedex_small_estimates_title").closest('tr').addClass("service_fedex_small_estimates_title_tr");
            jQuery("#shipping_methods_do_not_sort_by_price").closest('tr').addClass("fedex_small_shipping_methods_do_not_sort_by_price_tr");
            jQuery(".fedex_small_shipment_day").closest('tr').addClass("fedex_small_shipment_day_tr");
            jQuery("#all_shipment_days_fedex_small").closest('tr').addClass("all_shipment_days_fedex_small_tr");

            jQuery('#fedex_small_shipmentOffsetDays').attr('min', 1);
            jQuery('#en_fedex_small_ground_hazardous_material_fee').attr('min', 1);
            jQuery('#en_fedex_small_air_hazardous_material_fee').attr('min', 1);

            jQuery("#order_shipping_line_items .shipping .display_meta").css('display', 'none');

            jQuery('.fedex_small_connection_section').before('<div class="warning-msg"><p> <b>Note!</b> You must have a FedEx account to use this application. If you do not have one, contact FedEx at 800-463-3339 or <a target="_blank" href="https://www.fedex.com/en-us/create-account.html">register online.</a></p>');

            var fedexSmallCurrentTime = '<?php echo get_option("fedex_small_orderCutoffTime"); ?>';
            if (fedexSmallCurrentTime != '') {
                jQuery('#fedex_small_orderCutoffTime').wickedpicker({
                    now: fedexSmallCurrentTime,
                    title: 'Cut Off Time'
                });
            } else {
                jQuery('#fedex_small_orderCutoffTime').wickedpicker({
                    now: '',
                    title: 'Cut Off Time'
                });
            }

            var url = getUrlVarsFedExSmall()["tab"];
            if (url === 'fedex_small') {
                jQuery('#footer-left').attr('id', 'wc-footer-left');
            }

            /*
             * Add Title To Connection Setting Fields
             */

            jQuery('#fedex_small_auth_key').attr('title', 'Authentication Key');
            jQuery('#fedex_small_password').attr('title', 'Production Password');
            jQuery('#fedex_small_account_number').attr('title', 'Account Number');
            jQuery('#fedex_small_meter_number').attr('title', 'Meter Number');
            jQuery('#fedex_small_licence_key').attr('title', 'Eniture API Key');
            jQuery('#fedex_small_hazardous_fee').attr('title', 'Hazardous Material Fee');
            jQuery('#fedex_small_hand_fee_mark_up').attr('title', 'Handling Fee / Markup');

            /*
             * Add CSS Class To Quote Services
             */

            jQuery('.bold-text').closest('tr').addClass('fedex_small_quotes_services_tr');
            jQuery('.fedex_small_quotes_services').closest('tr').addClass('fedex_small_quotes_services_tr');
            jQuery('.fedex_small_quotes_services').closest('td').addClass('fedex_small_quotes_services_td');

            jQuery('.fedex_small_one_rate_quotes_services').closest('tr').addClass('fedex_small_one_rate_quotes_services_tr');
            jQuery('.fedex_small_one_rate_quotes_services').closest('td').addClass('fedex_small_one_rate_quotes_services_td');

            jQuery('.fedex_small_int_quotes_services').closest('tr').addClass('fedex_small_quotes_services_tr');
            jQuery('.fedex_small_int_quotes_services').closest('td').addClass('fedex_small_quotes_services_td');


            jQuery('.fedex_small_int_quotes_services').closest('tr').addClass('int_quotes_services_markup_text_fields_tr');
            jQuery('#fedex_small_saver_onerate_markup').closest('tr').addClass('fedex_small_saver_onerate_markup_tr');
            jQuery('#fedex_small_2day_srvc_onerate_markup_hidden').closest('tr').addClass('fedex_small_2day_srvc_onerate_markup_hidden_tr');
            jQuery('#fedex_small_eco_freight_onerate_markup').closest('tr').addClass('fedex_small_eco_freight_onerate_markup_tr');

            jQuery('#fedex_small_saver_onerate_markup_label').closest('tr').addClass('onerate_labels_class');
            jQuery('#fedex_small_2day_srvc_onerate_markup_label').closest('tr').addClass('onerate_labels_class');
            jQuery('#fedex_small_2day_am_onerate_markup_label').closest('tr').addClass('onerate_labels_class');
            jQuery('#fedex_small_st_overnight_onerate_markup_label').closest('tr').addClass('onerate_labels_class');
            jQuery('#fedex_small_pr_overnight_onerate_markup_label').closest('tr').addClass('onerate_labels_class');
            jQuery('#fedex_small_fst_overnight_onerate_markup_label').closest('tr').addClass('onerate_labels_class');

            jQuery('#fedex_small_home_dlvry_markup_label').closest('tr').addClass('domestic_service_labels_tr_class');
            jQuery('#fedex_small_gnd_markup_label').closest('tr').addClass('domestic_service_labels_tr_class');
            jQuery('#fedex_small_saver_markup_label').closest('tr').addClass('domestic_service_labels_tr_class');
            jQuery('#fedex_small_2day_srvc_markup_label').closest('tr').addClass('domestic_service_labels_tr_class');
            jQuery('#fedex_small_2day_am_markup_label').closest('tr').addClass('domestic_service_labels_tr_class');
            jQuery('#fedex_small_st_overnight_markup_label').closest('tr').addClass('domestic_service_labels_tr_class');
            jQuery('#fedex_small_pr_overnight_markup_label').closest('tr').addClass('domestic_service_labels_tr_class');
            jQuery('#fedex_small_fst_overnight_markup_label').closest('tr').addClass('domestic_service_labels_tr_class');
            jQuery('#fedex_small_smart_post_markup_label').closest('tr').addClass('domestic_service_labels_tr_class');

            jQuery('#fedex_small_int_gnd_label').closest('tr').addClass('international_service_labels_tr_class');
            jQuery('#fedex_small_int_distribution_label').closest('tr').addClass('international_service_labels_tr_class');
            jQuery('#fedex_small_pr_fr_label').closest('tr').addClass('international_service_labels_tr_class');
            jQuery('#fedex_small_pr_dist_label').closest('tr').addClass('international_service_labels_tr_class');
            jQuery('#fedex_small_priority_label').closest('tr').addClass('international_service_labels_tr_class');
            jQuery('#fedex_small_first_label').closest('tr').addClass('international_service_labels_tr_class');
            jQuery('#fedex_small_eco_freight_label').closest('tr').addClass('international_service_labels_tr_class');
            jQuery('#fedex_small_eco_dist_label').closest('tr').addClass('international_service_labels_tr_class');
            jQuery('#fedex_small_int_eco_labal').closest('tr').addClass('international_service_labels_tr_class');
            jQuery('#fedex_small_int_gnd_label').closest('tr').addClass('international_service_labels_tr_class');
            jQuery('.onerate_markup_hidden_fields').css('visibility', 'hidden');

            if (jQuery('input[name=wc_pulish_negotiate_fedex_small][value=publish]').is(":checked")) {
            } else {
                jQuery('input[name=wc_pulish_negotiate_fedex_small][value=negotiated]').attr('checked', 'checked');

            }

            //** Start: Validation for domestic service level markup

            jQuery(".fedex_small_quotes_services_markup").keydown(function (e) {
                // Allow: backspace, delete, tab, escape, enter and .
                if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 53, 189]) !== -1 ||
                    // Allow: Ctrl+A, Command+A
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    // Allow: home, end, left, right, down, up
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                    // let it happen, don't do anything
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }

                if ((jQuery(this).val().indexOf('.') != -1) && (jQuery(this).val().substring(jQuery(this).val().indexOf('.'), jQuery(this).val().indexOf('.').length).length > 2)) {
                    if (e.keyCode !== 8 && e.keyCode !== 46) { //exception
                        e.preventDefault();
                    }
                }

            });
            jQuery(".fedex_small_quotes_services_markup").keyup(function (e) {

                var selected_domestic_id = jQuery(this).attr("id");
                jQuery("#" + selected_domestic_id).css({"border": "1px solid #ddd"});

                var val = jQuery("#" + selected_domestic_id).val();
                if (val.split('.').length - 1 > 1) {
                    var newval = val.substring(0, val.length - 1);
                    var countDots = newval.substring(newval.indexOf('.') + 1).length;
                    newval = newval.substring(0, val.length - countDots - 1);
                    jQuery("#" + selected_domestic_id).val(newval);

                }
                if (val.split('%').length - 1 > 1) {
                    var newval = val.substring(0, val.length - 1);
                    var countPercentages = newval.substring(newval.indexOf('%') + 1).length;
                    newval = newval.substring(0, val.length - countPercentages - 1);
                    jQuery("#" + selected_domestic_id).val(newval);
                }
                if (val.split('>').length - 1 > 0) {
                    var newval = val.substring(0, val.length - 1);
                    var countGreaterThan = newval.substring(newval.indexOf('>') + 1).length;
                    newval = newval.substring(newval, newval.length - countGreaterThan - 1);
                    jQuery("#" + selected_domestic_id).val(newval);
                }
                if (val.split('_').length - 1 > 0) {
                    var newval = val.substring(0, val.length - 1);
                    var countUnderScore = newval.substring(newval.indexOf('_') + 1).length;
                    newval = newval.substring(newval, newval.length - countUnderScore - 1);
                    jQuery("#" + selected_domestic_id).val(newval);
                }
                if (val.split('-').length - 1 > 1) {
                    var newval = val.substring(0, val.length - 1);
                    var countPercentages = newval.substring(newval.indexOf('-') + 1).length;
                    newval = newval.substring(0, val.length - countPercentages - 1);
                    jQuery("#" + selected_domestic_id).val(newval);
                }
            });

            //** END: Validation for domestic service level markup

            //** Start: Validation for International service level markup

            jQuery(".fedex_small_int_quotes_services_markup").keydown(function (e) {
                // Allow: backspace, delete, tab, escape, enter and .
                if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 53, 189]) !== -1 ||
                    // Allow: Ctrl+A, Command+A
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    // Allow: home, end, left, right, down, up
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                    // let it happen, don't do anything
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }

                if ((jQuery(this).val().indexOf('.') != -1) && (jQuery(this).val().substring(jQuery(this).val().indexOf('.'), jQuery(this).val().indexOf('.').length).length > 2)) {
                    if (e.keyCode !== 8 && e.keyCode !== 46) { //exception
                        e.preventDefault();
                    }
                }

            });
            jQuery(".fedex_small_int_quotes_services_markup").keyup(function (e) {

                var selected_international_id = jQuery(this).attr("id");
                jQuery("#" + selected_international_id).css({"border": "1px solid #ddd"});

                var val = jQuery("#" + selected_international_id).val();
                if (val.split('.').length - 1 > 1) {

                    var newval = val.substring(0, val.length - 1);
                    var countDots = newval.substring(newval.indexOf('.') + 1).length;
                    newval = newval.substring(0, val.length - countDots - 1);
                    jQuery("#" + selected_international_id).val(newval);

                }

                if (val.split('%').length - 1 > 1) {
                    var newval = val.substring(0, val.length - 1);
                    var countPercentages = newval.substring(newval.indexOf('%') + 1).length;
                    newval = newval.substring(0, val.length - countPercentages - 1);
                    jQuery("#" + selected_international_id).val(newval);
                }
                if (val.split('>').length - 1 > 0) {
                    var newval = val.substring(0, val.length - 1);
                    var countGreaterThan = newval.substring(newval.indexOf('>') + 1).length;
                    newval = newval.substring(newval, newval.length - countGreaterThan - 1);
                    jQuery("#" + selected_international_id).val(newval);
                }
                if (val.split('_').length - 1 > 0) {
                    var newval = val.substring(0, val.length - 1);
                    var countUnderScore = newval.substring(newval.indexOf('_') + 1).length;
                    newval = newval.substring(newval, newval.length - countUnderScore - 1);
                    jQuery("#" + selected_international_id).val(newval);
                }
                if (val.split('-').length - 1 > 1) {
                    var newval = val.substring(0, val.length - 1);
                    var countPercentages = newval.substring(newval.indexOf('-') + 1).length;
                    newval = newval.substring(0, val.length - countPercentages - 1);
                    jQuery("#" + selected_international_id).val(newval);
                }

            });
            //** END: Validation for International service level markup

            //** Start: Order Cut Off Time
            jQuery("#fedex_small_orderCutoffTime").keydown(function (e) {
                // Allow: backspace, delete, tab, escape, enter and .
                if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 53, 189]) !== -1 ||
                    // Allow: Ctrl+A, Command+A
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    // Allow: home, end, left, right, down, up
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                    // let it happen, don't do anything
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105) && e.keyCode != 186) {
                    e.preventDefault();
                }
                if (e.keyCode == 186) {
                    return;
                }
            });

            jQuery("#fedex_small_orderCutoffTime").keyup(function (e) {
                if (e.keyCode == 189 || e.keyCode == 53 || e.keyCode == 190) {
                    e.preventDefault();
                    jQuery("#fedex_small_orderCutoffTime").val('');
                }
            });


            //** End: Order Cut Off Time

            //** Start: Validat Shipment Offset Days
            jQuery("#fedex_small_shipmentOffsetDays").keydown(function (e) {
                if (e.keyCode == 8)
                    return;

                var val = jQuery("#fedex_small_shipmentOffsetDays").val();
                if (val.length > 1 || e.keyCode == 190) {
                    e.preventDefault();
                }
                // Allow: backspace, delete, tab, escape, enter and .
                if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 53, 189]) !== -1 ||
                    // Allow: Ctrl+A, Command+A
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    // Allow: home, end, left, right, down, up
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                    // let it happen, don't do anything
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }

            });
            // Allow: only positive numbers
            jQuery("#fedex_small_shipmentOffsetDays").keyup(function (e) {
                if (e.keyCode == 189) {
                    e.preventDefault();
                    jQuery("#fedex_small_shipmentOffsetDays").val('');
                }

            });
            //** End: Validat Shipment Offset Days

            jQuery("#fedex_small_hand_fee_mark_up").keydown(function (e) {

                // Allow: backspace, delete, tab, escape, enter and .
                if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 53, 189]) !== -1 ||
                    // Allow: Ctrl+A, Command+A
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    // Allow: home, end, left, right, down, up
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                    // let it happen, don't do anything
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }

                if ((jQuery(this).val().indexOf('.') != -1) && (jQuery(this).val().substring(jQuery(this).val().indexOf('.'), jQuery(this).val().indexOf('.').length).length > 2)) {
                    if (e.keyCode !== 8 && e.keyCode !== 46) { //exception
                        e.preventDefault();
                    }
                }

            });
            jQuery("#fedex_small_hand_fee_mark_up").keyup(function (e) {

                var val = jQuery("#fedex_small_hand_fee_mark_up").val();

                if (val.split('.').length - 1 > 1) {

                    var newval = val.substring(0, val.length - 1);
                    var countDots = newval.substring(newval.indexOf('.') + 1).length;
                    newval = newval.substring(0, val.length - countDots - 1);
                    jQuery("#fedex_small_hand_fee_mark_up").val(newval);
                }

                if (val.split('%').length - 1 > 1) {
                    var newval = val.substring(0, val.length - 1);
                    var countPercentages = newval.substring(newval.indexOf('%') + 1).length;
                    newval = newval.substring(0, val.length - countPercentages - 1);
                    jQuery("#fedex_small_hand_fee_mark_up").val(newval);
                }
                if (val.split('>').length - 1 > 0) {
                    var newval = val.substring(0, val.length - 1);
                    var countGreaterThan = newval.substring(newval.indexOf('>') + 1).length;
                    newval = newval.substring(newval, newval.length - countGreaterThan - 1);
                    jQuery("#fedex_small_hand_fee_mark_up").val(newval);
                }
                if (val.split('_').length - 1 > 0) {
                    var newval = val.substring(0, val.length - 1);
                    var countUnderScore = newval.substring(newval.indexOf('_') + 1).length;
                    newval = newval.substring(newval, newval.length - countUnderScore - 1);
                    jQuery("#fedex_small_hand_fee_mark_up").val(newval);
                }
            });

            //**Start: Ground Hazardous field validation
            jQuery("#en_fedex_small_ground_hazardous_material_fee").keydown(function (e) {

                // Allow: backspace, delete, tab, escape, enter and .
                if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 53, 189]) !== -1 ||
                    // Allow: Ctrl+A, Command+A
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    // Allow: home, end, left, right, down, up
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                    // let it happen, don't do anything
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }

                if ((jQuery(this).val().indexOf('.') != -1) && (jQuery(this).val().substring(jQuery(this).val().indexOf('.'), jQuery(this).val().indexOf('.').length).length > 2)) {
                    if (e.keyCode !== 8 && e.keyCode !== 46) { //exception
                        e.preventDefault();
                    }
                }

            });
            jQuery("#en_fedex_small_ground_hazardous_material_fee").keyup(function (e) {

                var val = jQuery("#en_fedex_small_ground_hazardous_material_fee").val();

                if (val.split('.').length - 1 > 1) {

                    var newval = val.substring(0, val.length - 1);
                    var countDots = newval.substring(newval.indexOf('.') + 1).length;
                    newval = newval.substring(0, val.length - countDots - 1);
                    jQuery("#en_fedex_small_ground_hazardous_material_fee").val(newval);
                }

                if (val.split('%').length - 1 > 1) {
                    var newval = val.substring(0, val.length - 1);
                    var countPercentages = newval.substring(newval.indexOf('%') + 1).length;
                    newval = newval.substring(0, val.length - countPercentages - 1);
                    jQuery("#en_fedex_small_ground_hazardous_material_fee").val(newval);
                }
                if (val.split('>').length - 1 > 0) {
                    var newval = val.substring(0, val.length - 1);
                    var countGreaterThan = newval.substring(newval.indexOf('>') + 1).length;
                    newval = newval.substring(newval, newval.length - countGreaterThan - 1);
                    jQuery("#en_fedex_small_ground_hazardous_material_fee").val(newval);
                }
                if (val.split('_').length - 1 > 0) {
                    var newval = val.substring(0, val.length - 1);
                    var countUnderScore = newval.substring(newval.indexOf('_') + 1).length;
                    newval = newval.substring(newval, newval.length - countUnderScore - 1);
                    jQuery("#en_fedex_small_ground_hazardous_material_fee").val(newval);
                }
            });
            //**End: Ground Hazardous field validation

            //**Start: Air Hazardous field validation
            jQuery("#en_fedex_small_air_hazardous_material_fee").keydown(function (e) {

                // Allow: backspace, delete, tab, escape, enter and .
                if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 53, 189]) !== -1 ||
                    // Allow: Ctrl+A, Command+A
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    // Allow: home, end, left, right, down, up
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                    // let it happen, don't do anything
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }

                if ((jQuery(this).val().indexOf('.') != -1) && (jQuery(this).val().substring(jQuery(this).val().indexOf('.'), jQuery(this).val().indexOf('.').length).length > 2)) {
                    if (e.keyCode !== 8 && e.keyCode !== 46) { //exception
                        e.preventDefault();
                    }
                }

            });
            jQuery("#en_fedex_small_air_hazardous_material_fee").keyup(function (e) {

                var val = jQuery("#en_fedex_small_air_hazardous_material_fee").val();

                if (val.split('.').length - 1 > 1) {

                    var newval = val.substring(0, val.length - 1);
                    var countDots = newval.substring(newval.indexOf('.') + 1).length;
                    newval = newval.substring(0, val.length - countDots - 1);
                    jQuery("#en_fedex_small_air_hazardous_material_fee").val(newval);
                }

                if (val.split('%').length - 1 > 1) {
                    var newval = val.substring(0, val.length - 1);
                    var countPercentages = newval.substring(newval.indexOf('%') + 1).length;
                    newval = newval.substring(0, val.length - countPercentages - 1);
                    jQuery("#en_fedex_small_air_hazardous_material_fee").val(newval);
                }
                if (val.split('>').length - 1 > 0) {
                    var newval = val.substring(0, val.length - 1);
                    var countGreaterThan = newval.substring(newval.indexOf('>') + 1).length;
                    newval = newval.substring(newval, newval.length - countGreaterThan - 1);
                    jQuery("#en_fedex_small_air_hazardous_material_fee").val(newval);
                }
                if (val.split('_').length - 1 > 0) {
                    var newval = val.substring(0, val.length - 1);
                    var countUnderScore = newval.substring(newval.indexOf('_') + 1).length;
                    newval = newval.substring(newval, newval.length - countUnderScore - 1);
                    jQuery("#en_fedex_small_air_hazardous_material_fee").val(newval);
                }
            });
            //**End: Air Hazardous field validation


            /*
             * Uncheck Select All Checkbox
             */

            jQuery(".fedex_small_quotes_services").on('change load', function () {
                var checkboxes = jQuery('.fedex_small_quotes_services:checked').size();
                var un_checkboxes = jQuery('.fedex_small_quotes_services').size();
                if (checkboxes === un_checkboxes) {
                    jQuery('.fedex_small_all_services').attr('checked', true);
                } else {
                    jQuery('.fedex_small_all_services').attr('checked', false);
                }
            });

            /*
             * Uncheck Week days Select All Checkbox
             */

            jQuery(".fedex_small_shipment_day").on('change load', function () {
                console.log();
                var checkboxes = jQuery('.fedex_small_shipment_day:checked').size();
                var un_checkboxes = jQuery('.fedex_small_shipment_day').size();
                if (checkboxes === un_checkboxes) {
                    jQuery('.all_shipment_days_fedex_small').attr('checked', true);
                } else {
                    jQuery('.all_shipment_days_fedex_small').attr('checked', false);
                }
            });

            /*
             * Uncheck One Rate Services Select All Checkbox
             */

            jQuery(".one_rate_checkbox").on('change load', function () {
                var int_checkboxes = jQuery('.one_rate_checkbox:checked').size();
                var int_un_checkboxes = jQuery('.one_rate_checkbox').size();
                if (int_checkboxes === int_un_checkboxes) {
                    jQuery('.fedex_small_one_rate_all_services').attr('checked', true);
                } else {
                    jQuery('.fedex_small_one_rate_all_services').attr('checked', false);
                }
            });

            /*
             * Uncheck International Services Select All Checkbox
             */

            jQuery(".fedex_small_int_quotes_services").on('change load', function () {
                var int_checkboxes = jQuery('.fedex_small_int_quotes_services:checked').size();
                var int_un_checkboxes = jQuery('.fedex_small_int_quotes_services').size();
                if (int_checkboxes === int_un_checkboxes) {
                    jQuery('.fedex_small_all_int_services').attr('checked', true);
                } else {
                    jQuery('.fedex_small_all_int_services').attr('checked', false);
                }
            });

            /*
             * Save Changes Action
             */

            jQuery('.fedex_small_quote_section .button-primary, .fedex_small_quote_section .is-primary').on('click', function (e) {

                jQuery('.error').remove();
                jQuery('.updated').remove();
                if (!enFedexSmallHandlingFeeValidation()) {
                    return false;
                } else if (!fedex_small_palletshipclass()) {
                    return false;
                } else if (!enFedexSmallAirHazardousMaterialFeeValidation()) {
                    return false;
                } else if (!enFedexSmallGroundHazardousMaterialFeeValidation()) {
                    return false;
                } else if (!enFedexSmallGroundTransitValidation()) {
                    return false;
                }

                let fedex_small_quotes_services_markup = jQuery('.fedex_small_quotes_services_markup ');
                jQuery(fedex_small_quotes_services_markup).each(function () {

                    if (jQuery('#' + this.id).val() != '' && !en_fedex_small_domestic_markup_service(this.id)) {
                        e.preventDefault();
                        return false;
                    }
                });

                let fedex_small_int_quotes_services_markup = jQuery('.fedex_small_int_quotes_services_markup');
                jQuery(fedex_small_int_quotes_services_markup).each(function () {
                    if (jQuery('#' + this.id).val() != '' && !en_fedex_small_international_markup_service(this.id)) {
                        e.preventDefault();
                        return false;
                    }
                });

                var num_of_checkboxes = jQuery('.fedex_small_quotes_services:checked').size();
                var num_of_int_checkboxes = jQuery('.fedex_small_int_quotes_services:checked').size();
                var num_of_one_rate_checkboxes = jQuery('.fedex_small_one_rate_quotes_services:checked').size();
                var handling_fee = jQuery('#fedex_small_hand_fee_mark_up').val();
                var hazardous_fee = jQuery('#fedex_small_hazardous_fee').val();
                var no_of_trandit_days = jQuery('#restrict_days_transit_package_fedex_small').val();
                var Error = true;

                var numberOnlyRegex = /^[0-9]{1,2}?$/;

                /*
                 * Check Number of Selected Services
                 */


                if (num_of_checkboxes < 1 && num_of_int_checkboxes < 1 && num_of_one_rate_checkboxes < 1) {
                    jQuery("#mainform .fedex_small_quote_section").prepend('<div id="message" class="error inline no_srvc_select"><p><strong>Error! </strong>Please select at least one carrier service.</p></div>');
                    jQuery('html, body').animate({
                        'scrollTop': jQuery('.no_srvc_select').position().top
                    });
                    return false
                }


                var fedex_small_orderCutoffTime = jQuery("#fedex_small_orderCutoffTime").val();

                const timeFormatRegex = /^(?:^([1-9]|0[1-9]|1[0-2])( : |\.)[0-5][0-9] (AM|PM)$)/;
                if (fedex_small_orderCutoffTime != "" && !timeFormatRegex.test(fedex_small_orderCutoffTime)) {

                    jQuery("#mainform .fedex_small_quote_section").prepend('<div id="message" class="error inline fedex_small_fedex_small_orderCutoffTime_error"><p><strong>Error! </strong>Time format should be HH:MM and only minutes less than 60 are allowed.</p></div>');
                    jQuery('html, body').animate({
                        'scrollTop': jQuery('.fedex_small_fedex_small_orderCutoffTime_error').position().top
                    });

                    return false
                }


                var fedex_small_shipmentOffsetDays = jQuery("#fedex_small_shipmentOffsetDays").val();
                if (fedex_small_shipmentOffsetDays != "" && fedex_small_shipmentOffsetDays < 1) {

                    jQuery("#mainform .fedex_small_quote_section").prepend('<div id="message" class="error inline fedex_small_orderCutoffTime_error"><p><strong>Error! </strong>Days should not be less than 1.</p></div>');
                    jQuery('html, body').animate({
                        'scrollTop': jQuery('.fedex_small_orderCutoffTime_error').position().top
                    });
                    jQuery("#fedex_small_shipmentOffsetDays").css({'border-color': '#e81123'});
                    return false
                }
                if (fedex_small_shipmentOffsetDays != "" && fedex_small_shipmentOffsetDays > 8) {

                    jQuery("#mainform .fedex_small_quote_section").prepend('<div id="message" class="error inline fedex_small_orderCutoffTime_error"><p><strong>Error! </strong>Days should be less than or equal to 8.</p></div>');
                    jQuery('html, body').animate({
                        'scrollTop': jQuery('.fedex_small_orderCutoffTime_error').position().top
                    });
                    jQuery("#fedex_small_shipmentOffsetDays").css({'border-color': '#e81123'});
                    return false
                }

                var numberOnlyRegex = /^[0-9]+$/;

                if (fedex_small_shipmentOffsetDays != "" && !numberOnlyRegex.test(fedex_small_shipmentOffsetDays)) {

                    jQuery("#mainform .fedex_small_quote_section").prepend('<div id="message" class="error inline fedex_small_orderCutoffTime_error"><p><strong>Error! </strong>Entered Days are not valid.</p></div>');
                    jQuery('html, body').animate({
                        'scrollTop': jQuery('.fedex_small_orderCutoffTime_error').position().top
                    });
                    jQuery("#fedex_small_shipmentOffsetDays").css({'border-color': '#e81123'});
                    return false
                }

                /*Custom Error Message Validation*/
                var enFedexSmallCheckedValCustomMsg = jQuery("input[name='wc_pervent_proceed_checkout_eniture']:checked").val();
                var allow_proceed_checkout_eniture = jQuery("textarea[name=allow_proceed_checkout_eniture]").val();
                var prevent_proceed_checkout_eniture = jQuery("textarea[name=prevent_proceed_checkout_eniture]").val();

                if (enFedexSmallCheckedValCustomMsg == 'allow' && allow_proceed_checkout_eniture == '') {
                    jQuery("#mainform .fedex_small_quote_section").prepend('<div id="message" class="error inline fedex_small_custom_error_message"><p><strong>Error! </strong>Custom message field is empty.</p></div>');
                    jQuery('html, body').animate({
                        'scrollTop': jQuery('.fedex_small_custom_error_message').position().top
                    });
                    jQuery("textarea[name=allow_proceed_checkout_eniture]").css({'border-color': '#e81123'});
                    return false
                } else if (enFedexSmallCheckedValCustomMsg == 'prevent' && prevent_proceed_checkout_eniture == '') {
                    jQuery("#mainform .fedex_small_quote_section").prepend('<div id="message" class="error inline fedex_small_custom_error_message"><p><strong>Error! </strong>Custom message field is empty.</p></div>');
                    jQuery('html, body').animate({
                        'scrollTop': jQuery('.fedex_small_custom_error_message').position().top
                    });
                    jQuery("textarea[name=prevent_proceed_checkout_eniture]").css({'border-color': '#e81123'});
                    return false
                }
                return Error;

            });

            /*
             * Select All Services
             */

            var sm_all_checkboxes = jQuery('.fedex_small_quotes_services');
            if (sm_all_checkboxes.length === sm_all_checkboxes.filter(":checked").length) {
                jQuery('.fedex_small_all_services').prop('checked', true);
            }

            jQuery(".fedex_small_all_services").change(function () {
                if (this.checked) {
                    jQuery(".fedex_small_quotes_services").each(function () {
                        this.checked = true;
                    });
                } else {
                    jQuery(".fedex_small_quotes_services").each(function () {
                        this.checked = false;
                    });
                }
            });

            /*
             * Select One Rate All Services
             */

            var sm_all_checkboxes = jQuery('.fedex_small_one_rate_quotes_services');
            if (sm_all_checkboxes.length === sm_all_checkboxes.filter(":checked").length) {
                jQuery('.fedex_small_one_rate_all_services').prop('checked', true);
            }

            jQuery(".fedex_small_one_rate_all_services").change(function () {
                if (this.checked) {
                    jQuery(".fedex_small_one_rate_quotes_services").each(function () {
                        this.checked = true;
                    });
                } else {
                    jQuery(".fedex_small_one_rate_quotes_services").each(function () {
                        this.checked = false;
                    });
                }
            });

            /* One Rate checkbox */
            if (jQuery(".one_rate_error").length > 0) {
                jQuery(".one_rate_click").on('change', function () {

                    var one_rate_checkbox = false;
                    jQuery(".one_rate_click").each(function () {

                        this.checked ? one_rate_checkbox = true : "";

                    });

                    var display = one_rate_checkbox == true ? "block" : "none";
                    jQuery(".one_rate_error").css("display", display);

                });
            }

            if (jQuery(".one_rate_error").length > 1) {
                jQuery(".one_rate_error :first").remove();
            }

        });

        /*
         * Select All Services International
         */

        var all_int_checkboxes = jQuery('.fedex_small_int_quotes_services');
        if (all_int_checkboxes.length === all_int_checkboxes.filter(":checked").length) {
            jQuery('.fedex_small_all_int_services').prop('checked', true);
        }

        jQuery(".fedex_small_all_int_services").change(function () {
            if (this.checked) {
                jQuery(".fedex_small_int_quotes_services").each(function () {
                    this.checked = true;
                });
            } else {
                jQuery(".fedex_small_int_quotes_services").each(function () {
                    this.checked = false;
                });
            }
        });

        /*
         * Select All Shipment Week days
         */

        var all_int_checkboxes = jQuery('.all_shipment_days_fedex_small');
        if (all_int_checkboxes.length === all_int_checkboxes.filter(":checked").length) {
            jQuery('.all_shipment_days_fedex_small').prop('checked', true);
        }

        jQuery(".all_shipment_days_fedex_small").change(function () {
            if (this.checked) {
                jQuery(".fedex_small_shipment_day").each(function () {
                    this.checked = true;
                });
            } else {
                jQuery(".fedex_small_shipment_day").each(function () {
                    this.checked = false;
                });
            }
        });

        /**
         * Read a page's GET URL variables and return them as an associative array.
         */
        function getUrlVarsFedExSmall() {
            var vars = [], hash;
            var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for (var i = 0; i < hashes.length; i++) {
                hash = hashes[i].split('=');
                vars.push(hash[0]);
                vars[hash[0]] = hash[1];
            }
            return vars;
        }

        function en_fedex_small_domestic_markup_service(id) {

            var en_fedex_small_domestic_markup_service = jQuery('#' + id).val();
            var en_fedex_small_domestic_markup_service_regex = /^(-?[0-9]{1,4}%?)$|(\.[0-9]{1,2})%?$/;

            if (!en_fedex_small_domestic_markup_service_regex.test(en_fedex_small_domestic_markup_service)) {
                jQuery("#mainform .fedex_small_quote_section").prepend('<div id="message" class="error inline fedex_small_dom_markup_service_error"><p><strong>Error! </strong>Service Level Markup fee format should be 100.20 or 10%.</p></div>');
                jQuery('html, body').animate({
                    'scrollTop': jQuery('.fedex_small_dom_markup_service_error').position().top
                });
                jQuery("#" + id).css({'border-color': '#e81123'});
                return false;
            } else {
                return true;
            }
        }

        function en_fedex_small_international_markup_service(id) {

            var en_fedex_small_international_markup_service = jQuery('#' + id).val();
            var en_fedex_small_international_markup_service_regex = /^(-?[0-9]{1,4}%?)$|(\.[0-9]{1,2})%?$/;

            if (!en_fedex_small_international_markup_service_regex.test(en_fedex_small_international_markup_service)) {
                jQuery("#mainform .fedex_small_quote_section").prepend('<div id="message" class="error inline fedex_small_int_markup_service_error"><p><strong>Error! </strong>Service Level Markup fee format should be 100.20 or 10%.</p></div>');
                jQuery('html, body').animate({
                    'scrollTop': jQuery('.fedex_small_int_markup_service_error').position().top
                });
                jQuery("#" + id).css({'border-color': '#e81123'});
                return false;
            } else {
                return true;
            }
        }

        function enFedexSmallHandlingFeeValidation() {

            var handling_fee = jQuery('#fedex_small_hand_fee_mark_up').val();

            var handling_fee_regex = /^(-?[0-9]{1,4}%?)$|(\.[0-9]{1,2})%?$/;
            var numeric_values_regex = /^[0-9]{1,7}$/;
            if (handling_fee != '' && numeric_values_regex.test(handling_fee)) {
                return true;
            } else if (handling_fee != '' && !handling_fee_regex.test(handling_fee)) {
                jQuery("#mainform .fedex_small_quote_section").prepend('<div id="message" class="error inline fedex_small_handlng_fee_error"><p><strong>Error! </strong>Handling fee format should be 100.20 or 10%.</p></div>');
                jQuery('html, body').animate({
                    'scrollTop': jQuery('.fedex_small_handlng_fee_error').position().top
                });
                return false;
            } else {
                return true;
            }
        }

        function enFedexSmallAirHazardousMaterialFeeValidation() {

            var air_hazardous_fee = jQuery('#en_fedex_small_air_hazardous_material_fee').val();
            var air_hazardous_fee_regex = /^([0-9]{1,4}%?)$|(\.[0-9]{1,2})%?$/;
            if (air_hazardous_fee != '' && !air_hazardous_fee_regex.test(air_hazardous_fee) || air_hazardous_fee.split('.').length - 1 > 1) {
                jQuery("#mainform .fedex_small_quote_section").prepend('<div id="message" class="error inline fedex_small_air_hazardous_fee_error"><p><strong>Error! </strong>Air hazardous material fee format should be 100.20 or 10%.</p></div>');
                jQuery('html, body').animate({
                    'scrollTop': jQuery('.fedex_small_air_hazardous_fee_error').position().top
                });
                jQuery("#en_fedex_small_air_hazardous_material_fee").css({'border-color': '#e81123'});
                return false;
            } else {
                return true;
            }
        }

        function enFedexSmallGroundHazardousMaterialFeeValidation() {

            var ground_hazardous_fee = jQuery('#en_fedex_small_ground_hazardous_material_fee').val();
            var ground_hazardous_regex = /^([0-9]{1,4}%?)$|(\.[0-9]{1,2})%?$/;
            if (ground_hazardous_fee != '' && !ground_hazardous_regex.test(ground_hazardous_fee) || ground_hazardous_fee.split('.').length - 1 > 1) {
                jQuery("#mainform .fedex_small_quote_section").prepend('<div id="message" class="error inline fedex_small_ground_hazardous_fee_error"><p><strong>Error! </strong>Ground  hazardous material  fee format should be 100.20 or 10%.</p></div>');
                jQuery('html, body').animate({
                    'scrollTop': jQuery('.fedex_small_ground_hazardous_fee_error').position().top
                });
                jQuery("#en_fedex_small_ground_hazardous_material_fee").css({'border-color': '#e81123'});
                return false;
            } else {
                return true;
            }
        }

        function enFedexSmallGroundTransitValidation() {
            var ground_transit_value = jQuery('#restrict_days_transit_package_fedex_small').val();
            var ground_transit_regex = /^[0-9]{1,2}$/;
            if (ground_transit_value != '' && !ground_transit_regex.test(ground_transit_value)) {
                jQuery("#mainform .fedex_small_quote_section").prepend('<div id="message" class="error inline fedex_ground_transit_error"><p><strong>Error! </strong>Maximum 2 numeric characters are allowed for transit day field.</p></div>');
                jQuery('html, body').animate({
                    'scrollTop': jQuery('.fedex_ground_transit_error').position().top
                });
                jQuery("#restrict_days_transit_package_fedex_small").css({'border-color': '#e81123'});
                return false;
            } else {
                return true;
            }
        }


        /*
         * Validate Input If Empty or Invalid
         */

        function validateInput(form_id) {
            var has_err = true;
            jQuery(form_id + " input[type='text']").each(function () {
                var input = jQuery(this).val();
                var response = validateString(input);
                if (jQuery(this).parent().find('.err').length < 1) {
                    jQuery(this).after('<span class="err"></span>');
                }
                var errorElement = jQuery(this).parent().find('.err');
                jQuery(errorElement).html('');
                var errorText = jQuery(this).attr('title');
                var optional = jQuery(this).data('optional');
                optional = (optional === undefined) ? 0 : 1;
                errorText = (errorText != undefined) ? errorText : '';
                if ((optional == 0) && (response == false || response == 'empty')) {
                    errorText = (response == 'empty') ? errorText + ' is required.' : 'Invalid input.';
                    jQuery(errorElement).html(errorText);
                }
                has_err = (response != true && optional == 0) ? false : has_err;
            });
            return has_err;
        }

        /*
         * Check Valid Time
         */
        function isValidTime(time) {
        }

        /*
         * Check Input Value Is Not String
         */

        function isValidNumber(value, noNegative) {
            if (typeof (noNegative) === 'undefined')
                noNegative = false;
            var isValidNumber = false;
            var validNumber = (noNegative == true) ? parseFloat(value) >= 0 : true;
            if ((value == parseInt(value) || value == parseFloat(value)) && (validNumber)) {
                if (value.indexOf(".") >= 0) {
                    var n = value.split(".");
                    if (n[n.length - 1].length <= 2) {
                        isValidNumber = true;
                    } else {
                        isValidNumber = 'decimal_point_err';
                    }
                } else {
                    isValidNumber = true;
                }
            }
            return isValidNumber;
        }

        /*
         * Validate Input String 
         */

        function validateString(string) {
            if (string == '') {
                return 'empty';
            } else {
                return true;
            }
        }

        /*
         * Connection Settings Input Validation On Save 
         */

        jQuery(".fedex_small_connection_section .button-primary, .fedex_small_connection_section .is-primary").click(function () {
            var input = validateInput('.fedex_small_connection_section');
            if (input === false) {
                return false;
            }
        });


        /*
         * Test Connection 
         */

        jQuery(".fedex_small_connection_section .woocommerce-save-button").before('<a href="javascript:void(0)" class="button-primary fedex_small_test_connection">Test Connection</a>');
        jQuery('.fedex_small_test_connection').click(function (e) {
            var input = validateInput('.fedex_small_connection_section');
            if (input === false) {
                return false;
            }
            var postForm = {
                'action': 'fedex_small_test_connection',
                'fedex_small_auth': jQuery('#fedex_small_auth_key').val(),
                'fedex_small_password': jQuery('#fedex_small_password').val(),
                'fedex_small_acc_number': jQuery('#fedex_small_account_number').val(),
                'fedex_small_meter': jQuery('#fedex_small_meter_number').val(),
                'fedex_small_license': jQuery('#fedex_small_licence_key').val(),
            };
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: postForm,
                dataType: 'json',
                beforeSend: function () {
                    jQuery('#fedex_small_auth_key').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/small-package-quotes-fedex-edition/asset/processing.gif") no-repeat scroll 50% 50%');
                    jQuery('#fedex_small_password').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/small-package-quotes-fedex-edition/asset/processing.gif") no-repeat scroll 50% 50%');
                    jQuery('#fedex_small_account_number').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/small-package-quotes-fedex-edition/asset/processing.gif") no-repeat scroll 50% 50%');
                    jQuery('#fedex_small_meter_number').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/small-package-quotes-fedex-edition/asset/processing.gif") no-repeat scroll 50% 50%');
                    jQuery('#fedex_small_licence_key').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/small-package-quotes-fedex-edition/asset/processing.gif") no-repeat scroll 50% 50%');
                },
                success: function (data) {
                    if (data.success === 1) {
                        jQuery(".updated").hide();
                        jQuery('#fedex_small_auth_key').css('background', '#fff');
                        jQuery('#fedex_small_password').css('background', '#fff');
                        jQuery('#fedex_small_account_number').css('background', '#fff');
                        jQuery('#fedex_small_meter_number').css('background', '#fff');
                        jQuery('#fedex_small_licence_key').css('background', '#fff');
                        jQuery(".fedex_small_success_message").remove();
                        jQuery(".fedex_small_error_message").remove();
                        jQuery('.warning-msg').before('<div class="notice notice-success fedex_small_success_message"><p><strong>Success! </strong>The test resulted in a successful connection.</p></div>');
                    } else {
                        jQuery(".updated").hide();
                        jQuery(".fedex_small_error_message").remove();
                        jQuery('#fedex_small_auth_key').css('background', '#fff');
                        jQuery('#fedex_small_password').css('background', '#fff');
                        jQuery('#fedex_small_account_number').css('background', '#fff');
                        jQuery('#fedex_small_meter_number').css('background', '#fff');
                        jQuery('#fedex_small_licence_key').css('background', '#fff');
                        jQuery(".fedex_small_success_message").remove();
                        if (data.error != 1) {
                            jQuery('.warning-msg').before('<div class="notice notice-error fedex_small_error_message"><p>Error! ' + data.error + '</p></div>');
                        } else {
                            jQuery('.warning-msg').before('<div class="notice notice-error fedex_small_error_message"><p>Error! Please verify credentials and try again.</p></div>');
                        }
                    }
                }
            });
            e.preventDefault();
        });

        /*Custom Error Message*/
        jQuery(document).ready(function () {
            var prevent_text_box = jQuery('.prevent_text_box').length;
            if (!prevent_text_box > 0) {
                jQuery("input[name*='wc_pervent_proceed_checkout_eniture']").closest('tr').addClass('wc_pervent_proceed_checkout_eniture');
                jQuery(".wc_pervent_proceed_checkout_eniture input[value*='allow']").after('Allow user to continue to check out and display this message <br> <textarea  name="allow_proceed_checkout_eniture" class="prevent_text_box" title="Message" maxlength="250"><?php echo trim(get_option("allow_proceed_checkout_eniture")); ?></textarea> <br> <span class="description"> Enter a maximum of 250 characters.</span> <br><br>');
                jQuery(".wc_pervent_proceed_checkout_eniture input[value*='prevent']").after('Prevent user from checking out and display this message <br> <textarea name="prevent_proceed_checkout_eniture" class="prevent_text_box" title="Message" maxlength="250"><?php echo trim(get_option("prevent_proceed_checkout_eniture")); ?></textarea> <br> <span class="description"> Enter a maximum of 250 characters.</span>');
            }

            var delivery_estimate = jQuery('input[name=fedex_small_delivery_estimates]:checked').val();
            if (delivery_estimate == undefined) {
                jQuery('.fedex_small_dont_show_estimate_option').prop("checked", true);
            }

            var delivery_estimate_val = jQuery('input[name=fedex_small_delivery_estimates]:checked').val();
            if (delivery_estimate_val == 'dont_show_estimates') {
                jQuery("#fedex_small_orderCutoffTime").prop('disabled', true);
                jQuery("#fedex_small_shipmentOffsetDays").prop('disabled', true);
            } else {
                jQuery("#fedex_small_orderCutoffTime").prop('disabled', false);
                jQuery("#fedex_small_shipmentOffsetDays").prop('disabled', false);
            }

        });

        jQuery("input[name=fedex_small_delivery_estimates]").change(function () {
            var delivery_estimate_val = jQuery('input[name=fedex_small_delivery_estimates]:checked').val();
            if (delivery_estimate_val == 'dont_show_estimates') {
                jQuery("#fedex_small_orderCutoffTime").prop('disabled', true);
                jQuery("#fedex_small_shipmentOffsetDays").prop('disabled', true);
            } else {
                jQuery("#fedex_small_orderCutoffTime").prop('disabled', false);
                jQuery("#fedex_small_shipmentOffsetDays").prop('disabled', false);
            }
        });

        function fedex_small_palletshipclass() {
            var en_ship_class = jQuery('#en_ignore_items_through_freight_classification').val();
            var en_ship_class_arr = en_ship_class.split(',');
            var en_ship_class_trim_arr = en_ship_class_arr.map(Function.prototype.call, String.prototype.trim);
            if (en_ship_class_trim_arr.indexOf('ltl_freight') != -1) {
                jQuery("#mainform .fedex_small_quote_section").prepend('<div id="message" class="error inline fedex_small_pallet_weight_error"><p><strong>Error! </strong>Shipping Slug of <b>ltl_freight</b> can not be ignored.</p></div>');
                jQuery('html, body').animate({
                    'scrollTop': jQuery('.fedex_small_pallet_weight_error').position().top
                });
                jQuery("#en_ignore_items_through_freight_classification").css({'border-color': '#e81123'});
                return false;
            } else {
                return true;
            }
        }

        //      jquery check current plugin
        var FedExSmallgetUrlParameter = function FedExSmallgetUrlParameter(sParam) {
            var sPageURL = window.location.search.substring(1),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;
            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                }
            }
        };
        //      jquery check current plugin and implement the css
        var tab = FedExSmallgetUrlParameter('tab');
        if (typeof (tab) !== 'undefined' && tab === 'fedex_small') {
            jQuery('.sm_add_warehouse_popup').css('height', 'auto');
        }

        // Nested Material
        jQuery(document).ready(function () {
            // JS for edit product nested fields
            jQuery("._nestedMaterials").closest('p').addClass("_nestedMaterials_tr");
            jQuery("._nestedPercentage").closest('p').addClass("_nestedPercentage_tr");
            jQuery("._maxNestedItems").closest('p').addClass("_maxNestedItems_tr");
            jQuery("._nestedDimension").closest('p').addClass("_nestedDimension_tr");
            jQuery("._nestedStakingProperty").closest('p').addClass("_nestedStakingProperty_tr");

            if (!jQuery('._nestedMaterials').is(":checked")) {
                jQuery('._nestedPercentage_tr').hide();
                jQuery('._nestedDimension_tr').hide();
                jQuery('._maxNestedItems_tr').hide();
                jQuery('._nestedDimension_tr').hide();
                jQuery('._nestedStakingProperty_tr').hide();
            } else {
                jQuery('._nestedPercentage_tr').show();
                jQuery('._nestedDimension_tr').show();
                jQuery('._maxNestedItems_tr').show();
                jQuery('._nestedDimension_tr').show();
                jQuery('._nestedStakingProperty_tr').show();
            }

            jQuery("input[name=_nestedPercentage]").attr('min', '0');
            jQuery("input[name=_maxNestedItems]").attr('min', '0');
            jQuery("input[name=_nestedPercentage]").attr('max', '100');
            jQuery("input[name=_nestedPercentage]").attr('maxlength', '3');
            jQuery("input[name=_maxNestedItems]").attr('maxlength', '4');

            if (jQuery("input[name=_nestedPercentage]").val() == '') {
                jQuery("input[name=_nestedPercentage]").val(0);
            }

            // Nested fields validation on product details
            jQuery("._nestedPercentage").keydown(function (eve) {
                FedEx_SPQ_StopSpecialCharacters(eve);
                var nestedPercentage = jQuery('._nestedPercentage').val();
                if (nestedPercentage.length == 2) {
                    var newValue = nestedPercentage + '' + eve.key;
                    if (newValue > 100) {
                        return false;
                    }
                }
            });

            jQuery("._maxNestedItems").keydown(function (eve) {
                FedEx_SPQ_StopSpecialCharacters(eve);
            });

            jQuery("input[name=_nestedMaterials]").change(function () {
                if (!jQuery('._nestedMaterials').is(":checked")) {
                    jQuery('._nestedPercentage_tr').hide();
                    jQuery('._nestedDimension_tr').hide();
                    jQuery('._maxNestedItems_tr').hide();
                    jQuery('._nestedDimension_tr').hide();
                    jQuery('._nestedStakingProperty_tr').hide();
                } else {
                    jQuery('._nestedPercentage_tr').show();
                    jQuery('._nestedDimension_tr').show();
                    jQuery('._maxNestedItems_tr').show();
                    jQuery('._nestedDimension_tr').show();
                    jQuery('._nestedStakingProperty_tr').show();
                }
            });
        });

        function FedEx_SPQ_StopSpecialCharacters(e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if (jQuery.inArray(e.keyCode, [46, 9, 27, 13, 110, 190, 189]) !== -1 ||
                // Allow: Ctrl+A, Command+A
                (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: home, end, left, right, down, up
                (e.keyCode >= 35 && e.keyCode <= 40)) {
                // let it happen, don't do anything
                e.preventDefault();
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 90)) && (e.keyCode < 96 || e.keyCode > 105) && e.keyCode != 186 && e.keyCode != 8) {
                e.preventDefault();
            }
            if (e.keyCode == 186 || e.keyCode == 190 || e.keyCode == 189 || (e.keyCode > 64 && e.keyCode < 91)) {
                e.preventDefault();
                return;
            }
        }

    </script>
    <?php
}
