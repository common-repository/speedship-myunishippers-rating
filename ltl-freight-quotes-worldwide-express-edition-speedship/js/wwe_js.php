<?php

/**
 * WWE LTL JS
 *
 * @package     WWE LTL Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_footer', 'ltl_ajax_carrrier_button');

/**
 * JS Function
 */
function ltl_ajax_carrrier_button()
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
                    data: {
                        action: action
                    },
                    success: function(data_response) {
                        window.location.reload(true);
                    }
                });
            }
        }

        jQuery(document).ready(function() {
            jQuery("#wwe_quests_notify_delivery_as_option").closest('tr').addClass("wwe_quests_notify_delivery_as_option");
            //          JS for nested fields on product details
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

            jQuery("#wc_settings_wwe_label_as , #wwe_ltl_hold_at_terminal_fee , #wwe_freight_handling_weight , #wc_settings_wwe_hand_free_mark_up").focus(function(e) {
                jQuery("#" + this.id).css({
                    'border-color': '#ddd'
                });
            });

            var prevent_text_box = jQuery('.prevent_text_box').length;
            if (!prevent_text_box > 0) {
                jQuery("input[name*='wc_pervent_proceed_checkout_eniture']").closest('tr').addClass('wc_pervent_proceed_checkout_eniture');
                jQuery(".wc_pervent_proceed_checkout_eniture input[value*='allow']").after('Allow user to continue to check out and display this message<br><textarea  name="allow_proceed_checkout_eniture" class="prevent_text_box" title="Message" maxlength="250"><?php echo trim(get_option("allow_proceed_checkout_eniture")); ?></textarea></br><span class="description"> Enter a maximum of 250 characters.</span>');
                jQuery(".wc_pervent_proceed_checkout_eniture input[value*='prevent']").after('Prevent user from checking out and display this message <br><textarea name="prevent_proceed_checkout_eniture" class="prevent_text_box" title="Message" maxlength="250"><?php echo trim(get_option("prevent_proceed_checkout_eniture")); ?></textarea></br><span class="description"> Enter a maximum of 250 characters.</span>');
            }

            jQuery("#wwe_freight_handling_weight").closest('tr').addClass("wwe_freight_handling_weight_tr");
            jQuery("#wwe_freight_maximum_handling_weight").closest('tr').addClass("wwe_freight_maximum_handling_weight_tr");
            jQuery("#wc_settings_wwe_residential_delivery").closest('tr').addClass("wc_settings_wwe_residential_delivery");
            jQuery("#avaibility_auto_residential").closest('tr').addClass("avaibility_auto_residential");
            jQuery("#avaibility_lift_gate").closest('tr').addClass("avaibility_lift_gate");
            jQuery("#wc_settings_wwe_lift_gate_delivery").closest('tr').addClass("wc_settings_wwe_lift_gate_delivery");
            jQuery("#wwe_quests_liftgate_delivery_as_option").closest('tr').addClass("wwe_quests_liftgate_delivery_as_option");
            jQuery("#residential_delivery_options_label").closest('tr').addClass("residential_delivery_options_label");
            jQuery("#liftgate_delivery_options_label").closest('tr').addClass("liftgate_delivery_options_label");
            jQuery("#wwe_ltl_hold_at_terminal_fee").closest('tr').addClass("wwe_ltl_hold_at_terminal_fee_tr");
            jQuery("#wc_settings_wwe_hand_free_mark_up").closest('tr').addClass("wc_settings_wwe_hand_free_mark_up_tr");
            jQuery("#wc_settings_wwe_allow_other_plugins").closest('tr').addClass("wc_settings_wwe_allow_other_plugins_tr");


            jQuery("#wwe_ltl_hold_at_terminal_checkbox_status").closest('tr').addClass("wwe_ltl_hold_at_terminal_checkbox_status");

            jQuery("#order_shipping_line_items .shipping .display_meta").css('display', 'none');

            //** START: Validation for Quote_setting Hold_a_terminal fee

            jQuery("#wwe_ltl_hold_at_terminal_fee").keydown(function(e) {

                if ((jQuery(this).val().indexOf('.') != -1) && (jQuery(this).val().substring(jQuery(this).val().indexOf('.'), jQuery(this).val().indexOf('.').length).length > 2)) {
                    if (e.keyCode !== 8 && e.keyCode !== 46) { //exception
                        e.preventDefault();
                    }
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

            jQuery("#wwe_ltl_hold_at_terminal_fee").keyup(function(e) {

                var val = jQuery("#wwe_ltl_hold_at_terminal_fee").val();

                if (val.split('.').length - 1 > 1) {

                    var newval = val.substring(0, val.length - 1);
                    var countDots = newval.substring(newval.indexOf('.') + 1).length;
                    newval = newval.substring(0, val.length - countDots - 1);
                    jQuery("#wwe_ltl_hold_at_terminal_fee").val(newval);
                }

                if (val.split('%').length - 1 > 1) {
                    var newval = val.substring(0, val.length - 1);
                    var countPercentages = newval.substring(newval.indexOf('%') + 1).length;
                    newval = newval.substring(0, val.length - countPercentages - 1);
                    jQuery("#wwe_ltl_hold_at_terminal_fee").val(newval);
                }
                if (val.split('>').length - 1 > 0) {
                    var newval = val.substring(0, val.length - 1);
                    var countGreaterThan = newval.substring(newval.indexOf('>') + 1).length;
                    newval = newval.substring(newval, newval.length - countGreaterThan - 1);
                    jQuery("#wwe_ltl_hold_at_terminal_fee").val(newval);
                }
                if (val.split('_').length - 1 > 0) {
                    var newval = val.substring(0, val.length - 1);
                    var countUnderScore = newval.substring(newval.indexOf('_') + 1).length;
                    newval = newval.substring(newval, newval.length - countUnderScore - 1);
                    jQuery("#wwe_ltl_hold_at_terminal_fee").val(newval);
                }

            });

            /**
             * Offer lift gate delivery as an option and Always include residential delivery fee
             * @returns {undefined}
             */

            jQuery(".checkbox_fr_add").on("click", function() {
                var id = jQuery(this).attr("id");
                if (id == "wc_settings_wwe_lift_gate_delivery") {
                    jQuery("#wwe_quests_liftgate_delivery_as_option").prop({
                        checked: false
                    });
                    jQuery("#en_woo_addons_liftgate_with_auto_residential").prop({
                        checked: false
                    });

                } else if (id == "wwe_quests_liftgate_delivery_as_option" ||
                    id == "en_woo_addons_liftgate_with_auto_residential") {
                    jQuery("#wc_settings_wwe_lift_gate_delivery").prop({
                        checked: false
                    });
                }
            });

            var url = getUrlVarsWWELTL()["tab"];
            if (url === 'wwe_quests') {
                jQuery('#footer-left').attr('id', 'wc-footer-left');
            }

            /*
             * Restrict Handling Fee with 8 digits limit
             */

            jQuery("#wc_settings_wwe_hand_free_mark_up").attr('maxlength', '8');

        });

        //      Nested fields validation on product details
        jQuery("._nestedPercentage").keydown(function(eve) {
            stopSpecialCharacters(eve);
            var nestedPercentage = jQuery('._nestedPercentage').val();
            if (nestedPercentage.length == 2) {
                var newValue = nestedPercentage + '' + eve.key;
                if (newValue > 100) {
                    return false;
                }
            }
        });

        jQuery("._maxNestedItems").keydown(function(eve) {
            stopSpecialCharacters(eve);
        });

        jQuery("input[name=_nestedMaterials]").change(function() {
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

        function wwe_freight_palletshipclass() {
            var en_ship_class = jQuery('#en_ignore_items_through_freight_classification').val();
            var en_ship_class_arr = en_ship_class.split(',');
            var en_ship_class_trim_arr = en_ship_class_arr.map(Function.prototype.call, String.prototype.trim);
            if (en_ship_class_trim_arr.indexOf('ltl_freight') != -1) {
                jQuery("#mainform .quote_section_class_ltl").prepend('<div id="message" class="error inline wwe_freight_pallet_weight_error"><p><strong>Error! </strong>Shipping Slug of <b>ltl_freight</b> can not be ignored.</p></div>');
                jQuery('html, body').animate({
                    'scrollTop': jQuery('.wwe_freight_pallet_weight_error').position().top
                });
                jQuery("#en_ignore_items_through_freight_classification").css({
                    'border-color': '#e81123'
                });
                return false;
            } else {
                return true;
            }
        }

        function stopSpecialCharacters(e) {
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

        jQuery(".ltl_connection_section_class .button-primary").click(function() {
            var input = validateInput('.ltl_connection_section_class');
            if (input === false) {
                return false;
            }
        });
        jQuery(".ltl_connection_section_class .woocommerce-save-button").before('<a href="javascript:void(0)" class="button-primary ltl_test_connection">Test Connection</a>');
        jQuery('.ltl_test_connection').click(function(e) {
            var input = validateInput('.ltl_connection_section_class');
            if (input === false) {
                return false;
            }

            var postForm = {
                'world_wide_express_account_number': jQuery('#wc_settings_wwe_world_wide_express_account_number').val(),
                'speed_freight_username': jQuery('#wc_settings_wwe_speed_freight_username').val(),
                'speed_freight_password': jQuery('#wc_settings_wwe_speed_freight_password').val(),
                'speed_freight_licence_key': jQuery('#wc_settings_wwe_licence_key').val(),
                'authentication_key': jQuery('#wc_settings_wwe_authentication_key').val(),
                'action': 'ltl_validate_keys'
            };
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: postForm,
                dataType: 'json',
                beforeSend: function() {
                    jQuery(".ltl_test_connection").css("color", "#fff");
                    jQuery(".ltl_connection_section_class .button-primary").css("cursor", "pointer");
                },
                success: function(data) {

                    if (data.success) {
                        jQuery(".updated").hide();
                        jQuery('#wc_settings_wwe_world_wide_express_account_number').css('background', '#fff');
                        jQuery('#wc_settings_wwe_speed_freight_username').css('background', '#fff');
                        jQuery('#wc_settings_wwe_speed_freight_password').css('background', '#fff');
                        jQuery('#wc_settings_wwe_licence_key').css('background', '#fff');
                        jQuery('#wc_settings_wwe_authentication_key').css('background', '#fff');
                        jQuery(".class_success_message").remove();
                        jQuery(".class_error_message").remove();
                        jQuery(".ltl_connection_section_class .button-primary").attr("disabled", false);
                        jQuery('.warning-msg-ltl').before('<p class="class_success_message" ><b> Success! The test resulted in a successful connection. </b></p>');
                    } else {
                        jQuery(".updated").hide();
                        jQuery(".class_error_message").remove();
                        jQuery('#wc_settings_wwe_world_wide_express_account_number').css('background', '#fff');
                        jQuery('#wc_settings_wwe_speed_freight_username').css('background', '#fff');
                        jQuery('#wc_settings_wwe_speed_freight_password').css('background', '#fff');
                        jQuery('#wc_settings_wwe_licence_key').css('background', '#fff');
                        jQuery('#wc_settings_wwe_authentication_key').css('background', '#fff');
                        jQuery(".class_success_message").remove();
                        jQuery(".ltl_connection_section_class .button-primary").attr("disabled", false);
                        if (data.error_desc) {
                            var error_message = data.error_desc;
                            if (error_message == 'Connection failed due to license expired. Please upgrade / renew you license from eniture.com dashboard.') {
                                jQuery('.warning-msg-ltl').before('<p class="class_error_message" ><b>Error! Connection failed due to invalid license key. </b></p>');
                            } else if (error_message == 'Invalid authentication info') {
                                jQuery('.warning-msg-ltl').before('<p class="class_error_message" ><b>Error! please verify your credentials and try again. </b></p>');
                            } else {
                                jQuery('.warning-msg-ltl').before('<p class="class_error_message" ><b>Error! ' + data.error_desc + ' </b></p>');
                            }
                        } else {
                            jQuery('.warning-msg-ltl').before('<p class="class_error_message" ><b>Error! The credentials entered did not result in a successful test. Confirm your credentials and try again. </b></p>');
                        }
                    }
                }
            });
            e.preventDefault();
        })

        /**
         * Read a page's GET URL variables and return them as an associative array.
         */
        function getUrlVarsWWELTL() {
            var vars = [],
                hash;
            var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for (var i = 0; i < hashes.length; i++) {
                hash = hashes[i].split('=');
                vars.push(hash[0]);
                vars[hash[0]] = hash[1];
            }
            return vars;
        }

        function labelValidation() {
            var label_value = jQuery('#wc_settings_wwe_label_as').val();
            var labelRegex = /^[a-zA-Z0-9\-\s]+$/;
            if (typeof label_value != 'undefined' && label_value.length > 25) {
                jQuery("#mainform .quote_section_class_ltl").prepend('<div id="message" class="error inline wwe_freight_label_error"><p><strong>Maximum 25 alpha characters are allowed for label field.</strong></p></div>');
                jQuery('html, body').animate({
                    'scrollTop': jQuery('.wwe_freight_label_error').position().top
                });
                jQuery("#wc_settings_wwe_label_as").css({
                    'border-color': '#e81123'
                });
                return false;
            } else if (typeof label_value != 'undefined' && label_value != '' && !labelRegex.test(label_value)) {
                jQuery("#mainform .quote_section_class_ltl").prepend('<div id="message" class="error inline wwe_freight_spec_label_error"><p><strong>No special characters allowed for label field.</strong></p></div>');
                jQuery('html, body').animate({
                    'scrollTop': jQuery('.wwe_freight_spec_label_error').position().top
                });
                jQuery("#wc_settings_wwe_label_as").css({
                    'border-color': '#e81123'
                });
                return false;
            } else {
                return true;
            }
        }

        function palletWeightValidation() {
            var weight_of_handling_unit = jQuery('#wwe_freight_handling_weight').val();
            if (typeof weight_of_handling_unit != 'undefined' && weight_of_handling_unit.length > 0) {
                var validResponse = isValidDecimal(weight_of_handling_unit, 'wwe_freight_handling_weight');
            } else {
                validResponse = true;
            }
            if (validResponse) {
                return true;
            } else {
                jQuery("#mainform .quote_section_class_ltl").prepend('<div id="message" class="error inline wwe_freight_pallet_weight_error"><p><strong>Error! </strong>Weight of Handling Unit format should be like, e.g. 48.5 and only 3 digits are allowed after decimal point. The value can be up to 20,000.</p></div>');
                jQuery('html, body').animate({
                    'scrollTop': jQuery('.wwe_freight_pallet_weight_error').position().top
                });
                jQuery("#wwe_freight_handling_weight").css({
                    'border-color': '#e81123'
                });
                return false;
            }
        }

        function palletMaxWeightValidation() {
            var max_weight_of_handling_unit = jQuery('#wwe_freight_maximum_handling_weight').val();
            if (typeof max_weight_of_handling_unit != 'undefined' && max_weight_of_handling_unit.length > 0) {
                var validResponse = isValidDecimal(max_weight_of_handling_unit, 'wwe_freight_maximum_handling_weight');
            } else {
                validResponse = true;
            }
            if (validResponse) {
                return true;
            } else {
                jQuery("#mainform .quote_section_class_ltl").prepend('<div id="message" class="error inline wwe_freight_pallet_max_weight_error"><p><strong>Error! </strong>Maximum Weight per Handling Unit format should be like, e.g. 48.5 and only 3 digits are allowed after decimal point. The value can be up to 20,000.</p></div>');
                jQuery('html, body').animate({
                    'scrollTop': jQuery('.wwe_freight_pallet_max_weight_error').position().top
                });
                jQuery("#wwe_freight_maximum_handling_weight").css({
                    'border-color': '#e81123'
                });
                return false;
            }
        }

        /**
         * Check is valid number
         * @param num
         * @param selector
         * @param limit | LTL weight limit 20K
         * @returns {boolean}
         */
        function isValidDecimal(num, selector, limit = 20000) {
            // validate the number:
            // positive and negative numbers allowed
            // just - sign is not allowed,
            // -0 is also not allowed.
            if (parseFloat(num) === 0) {
                // Change the value to zero
                return false;
            }

            const reg = /^(-?[0-9]{1,5}(\.\d{1,4})?|[0-9]{1,5}(\.\d{1,4})?)$/;
            let isValid = false;
            if (reg.test(num)) {
                isValid = inRange(parseFloat(num), -limit, limit);
            }
            if (isValid === true) {
                return true;
            }
            return isValid;
        }

        /**
         * Check is the number is in given range
         *
         * @param num
         * @param min
         * @param max
         * @returns {boolean}
         */
        function inRange(num, min, max) {
            return ((num - min) * (num - max) <= 0);
        }

        function holdAtTerminalFeeValidation() {
            var abf_hold_at_terminal_fee = jQuery('#wwe_ltl_hold_at_terminal_fee').val();
            var abf_hold_at_terminal_fee_regex = /^(-?[0-9]{1,4}%?)$|(\.[0-9]{1,2})%?$/;
            if (typeof abf_hold_at_terminal_fee_regex != 'undefined' && abf_hold_at_terminal_fee != '' && !abf_hold_at_terminal_fee_regex.test(abf_hold_at_terminal_fee) || abf_hold_at_terminal_fee.split('.').length - 1 > 1) {
                jQuery("#mainform .quote_section_class_ltl").prepend('<div id="message" class="error inline wwe_freight_hold_at_terminal_fee_error"><p><strong>Hold at terminal fee format should be 100.20 or 10%.</strong></p></div>');
                jQuery('html, body').animate({
                    'scrollTop': jQuery('.wwe_freight_hold_at_terminal_fee_error').position().top
                });
                jQuery("#wwe_ltl_hold_at_terminal_fee").css({
                    'border-color': '#e81123'
                });
                return false;
            } else {
                return true;
            }
        }

        function handlingFeeValidation() {
            var handling_fee = jQuery('#wc_settings_wwe_hand_free_mark_up').val();
            var handling_fee_regex = /^(-?[0-9]{1,4}%?)$|(\.[0-9]{1,2})%?$/;
            if (typeof handling_fee != 'undefined' && handling_fee != '' && !handling_fee_regex.test(handling_fee) || handling_fee.split('.').length - 1 > 1) {
                jQuery("#mainform .quote_section_class_ltl").prepend('<div id="message" class="error inline wwe_freight_handlng_fee_error"><p><strong>Handling fee format should be 100.20 or 10%.</strong></p></div>');
                jQuery('html, body').animate({
                    'scrollTop': jQuery('.wwe_freight_handlng_fee_error').position().top
                });
                jQuery("#wc_settings_wwe_hand_free_mark_up").css({
                    'border-color': '#e81123'
                });
                return false;
            } else {
                return true;
            }
        }

        function validateInput(form_id) {
            var has_err = true;
            jQuery(form_id + " input[type='text']").each(function() {
                var input = jQuery(this).val();
                var response = validateString(input);

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

        function validateString(string) {
            if (string == '') {
                return 'empty';
            } else {
                return true;
            }
        }
    </script>
<?php
}

add_action('admin_footer', 'ltl_no_carrier_select');

/**
 * No CArrier Select JS
 */
function ltl_no_carrier_select()
{
?>
    <script>
        jQuery(document).ready(function() {
            jQuery('.ltl_connection_section_class .form-table').before('<div class="warning-msg-ltl"><p> <b>Note!</b> You must have a Worldwide Express account to use this application. If you do not have one, click <a href="https://wwex.com/request-worldwide-express-account-number/" target="_blank">here</a> to access the new account request form. </p>');

            jQuery('.carrier_section_class .button-primary').on('click', function() {
                jQuery(".updated").hide();
                var num_of_checkboxes = jQuery('.carrier_check:checked').size();
                if (num_of_checkboxes < 1) {
                    jQuery(".carrier_section_class:first-child").before('<div id="message" class="error inline no_srvc_select"><p><strong>Please select at least one carrier service.</strong></p></div>');

                    jQuery('html, body').animate({
                        'scrollTop': jQuery('.no_srvc_select').position().top
                    });
                    return false;
                }
            });

            jQuery('.quote_section_class_ltl .button-primary').on('click', function() {
                jQuery(".updated").hide();
                jQuery('.error').remove();
            });
        });
    </script>
<?php
}

add_action('admin_footer', 'ltl_check_all');

/**
 * Check all JS
 */
function ltl_check_all()
{
?>
    <script>
        var all_checkboxes = jQuery('.carrier_check');
        if (all_checkboxes.length === all_checkboxes.filter(":checked").length) {
            jQuery('.include_all').prop('checked', true);
        }

        jQuery(".include_all").change(function() {
            if (this.checked) {
                jQuery(".carrier_check").each(function() {
                    this.checked = true;
                })
            } else {
                jQuery(".carrier_check").each(function() {
                    this.checked = false;
                })
            }
        });

        /*
         * Uncheck Select All Checkbox
         */

        jQuery(".carrier_check").on('change load', function() {
            var int_checkboxes = jQuery('.carrier_check:checked').size();
            var int_un_checkboxes = jQuery('.carrier_check').size();
            if (int_checkboxes === int_un_checkboxes) {
                jQuery('.include_all').attr('checked', true);
            } else {
                jQuery('.include_all').attr('checked', false);
            }
        });
    </script>
<?php
}

add_action('admin_footer', 'ltl_admin_quote_setting_input');

/**
 * Quote settings JS
 */
function ltl_admin_quote_setting_input()
{
?>
    <input type="hidden" id="show_wwe_saved_method" value="<?php echo get_option('wc_settings_wwe_rate_method'); ?>" />
    <script>
        jQuery(window).load(function() {
            var saved_mehod_value = jQuery('#show_wwe_saved_method').val();
            if (saved_mehod_value == 'Cheapest') {
                jQuery(".wwe_delivery_estimate").removeAttr('style');
                jQuery(".wwe_Number_of_label_as").removeAttr('style');
                jQuery(".wwe_Number_of_options_class").removeAttr('style');

                jQuery("#wc_settings_wwe_Number_of_options").closest('tr').addClass("wwe_Number_of_options_class");
                jQuery("#wc_settings_wwe_Number_of_options").closest('tr').css("display", "none");
                jQuery("#wc_settings_wwe_label_as").closest('tr').addClass("wwe_Number_of_label_as");
                jQuery("#wc_settings_wwe_delivery_estimate").closest('tr').addClass("wwe_delivery_estimate");
                jQuery("#wc_settings_wwe_rate_method").closest('tr').addClass("wwe_rate_mehod");

                jQuery('.wwe_rate_mehod td span').html('Displays only the cheapest returned Rate.');
                jQuery('.wwe_Number_of_label_as td span').html('What the user sees during checkout, e.g. "Freight". Leave blank to display the carrier name.');
            }
            if (saved_mehod_value == 'cheapest_options') {

                jQuery(".wwe_delivery_estimate").removeAttr('style');
                jQuery(".wwe_Number_of_label_as").removeAttr('style');
                jQuery(".wwe_Number_of_options_class").removeAttr('style');

                jQuery("#wc_settings_wwe_delivery_estimate").closest('tr').addClass("wwe_delivery_estimate");
                jQuery("#wc_settings_wwe_label_as").closest('tr').addClass("wwe_Number_of_label_as");
                jQuery("#wc_settings_wwe_label_as").closest('tr').css("display", "none");
                jQuery("#wc_settings_wwe_Number_of_options").closest('tr').addClass("wwe_Number_of_options_class");
                jQuery("#wc_settings_wwe_rate_method").closest('tr').addClass("wwe_rate_mehod");

                jQuery('.wwe_rate_mehod td span').html('Displays a list of a specified number of least expensive options.');
                jQuery('.wwe_Number_of_options_class td span').html('Number of options to display in the shopping cart.');
            }
            if (saved_mehod_value == 'average_rate') {

                jQuery(".wwe_delivery_estimate").removeAttr('style');
                jQuery(".wwe_Number_of_label_as").removeAttr('style');
                jQuery(".wwe_Number_of_options_class").removeAttr('style');

                jQuery("#wc_settings_wwe_delivery_estimate").closest('tr').addClass("wwe_delivery_estimate");
                jQuery("#wc_settings_wwe_delivery_estimate").closest('tr').css("display", "none");
                jQuery("#wc_settings_wwe_label_as").closest('tr').addClass("wwe_Number_of_label_as");
                jQuery("#wc_settings_wwe_Number_of_options").closest('tr').addClass("wwe_Number_of_options_class");
                jQuery("#wc_settings_wwe_rate_method").closest('tr').addClass("wwe_rate_mehod");

                jQuery('.wwe_rate_mehod td span').html('Displays a single rate based on an average of a specified number of least expensive options.');
                jQuery('.wwe_Number_of_options_class td span').html('Number of options to include in the calculation of the average.');
                jQuery('.wwe_Number_of_label_as td span').html('What the user sees during checkout, e.g. "Freight". If left blank will default to "Freight".');

            }

        });

        //        changed
        var wc_settings_wwe_rate_method = jQuery("#wc_settings_wwe_rate_method").val();
        if (wc_settings_wwe_rate_method == 'Cheapest') {
            jQuery("#wc_settings_wwe_Number_of_options").closest('tr').addClass("wwe_Number_of_options_class");
            jQuery("#wc_settings_wwe_Number_of_options").closest('tr').css("display", "none");
        }

        jQuery("#wc_settings_wwe_rate_method").change(function() {
            var rating_method = jQuery(this).val();
            if (rating_method == 'Cheapest') {

                jQuery(".wwe_delivery_estimate").removeAttr('style');
                jQuery(".wwe_Number_of_label_as").removeAttr('style');
                jQuery(".wwe_Number_of_options_class").removeAttr('style');

                jQuery("#wc_settings_wwe_Number_of_options").closest('tr').addClass("wwe_Number_of_options_class");
                jQuery("#wc_settings_wwe_Number_of_options").closest('tr').css("display", "none");
                jQuery("#wc_settings_wwe_label_as").closest('tr').addClass("wwe_Number_of_label_as");
                jQuery("#wc_settings_wwe_delivery_estimate").closest('tr').addClass("wwe_delivery_estimate");
                jQuery("#wc_settings_wwe_rate_method").closest('tr').addClass("wwe_rate_mehod");

                jQuery('.wwe_rate_mehod td span').html('Displays only the cheapest returned Rate.');
                jQuery('.wwe_Number_of_label_as td span').html('What the user sees during checkout, e.g. "Freight". Leave blank to display the carrier name.');

            }
            if (rating_method == 'cheapest_options') {

                jQuery(".wwe_delivery_estimate").removeAttr('style');
                jQuery(".wwe_Number_of_label_as").removeAttr('style');
                jQuery(".wwe_Number_of_options_class").removeAttr('style');

                jQuery("#wc_settings_wwe_delivery_estimate").closest('tr').addClass("wwe_delivery_estimate");
                jQuery("#wc_settings_wwe_label_as").closest('tr').addClass("wwe_Number_of_label_as");
                jQuery("#wc_settings_wwe_label_as").closest('tr').css("display", "none");
                jQuery("#wc_settings_wwe_Number_of_options").closest('tr').addClass("wwe_Number_of_options_class");
                jQuery("#wc_settings_wwe_rate_method").closest('tr').addClass("wwe_rate_mehod");

                jQuery('.wwe_rate_mehod td span').html('Displays a list of a specified number of least expensive options.');
                jQuery('.wwe_Number_of_options_class td span').html('Number of options to display in the shopping cart.');
            }
            if (rating_method == 'average_rate') {

                jQuery(".wwe_delivery_estimate").removeAttr('style');
                jQuery(".wwe_Number_of_label_as").removeAttr('style');
                jQuery(".wwe_Number_of_options_class").removeAttr('style');

                jQuery("#wc_settings_wwe_delivery_estimate").closest('tr').addClass("wwe_delivery_estimate");
                jQuery("#wc_settings_wwe_delivery_estimate").closest('tr').css("display", "none");
                jQuery("#wc_settings_wwe_label_as").closest('tr').addClass("wwe_Number_of_label_as");
                jQuery("#wc_settings_wwe_Number_of_options").closest('tr').addClass("wwe_Number_of_options_class");
                jQuery("#wc_settings_wwe_rate_method").closest('tr').addClass("wwe_rate_mehod");

                jQuery('.wwe_rate_mehod td span').html('Displays a single rate based on an average of a specified number of least expensive options.');
                jQuery('.wwe_Number_of_options_class td span').html('Number of options to include in the calculation of the average.');
                jQuery('.wwe_Number_of_label_as td span').html('What the user sees during checkout, e.g. "Freight". If left blank will default to "Freight".');
            }
        });

        jQuery(document).ready(function() {

            jQuery('.ltl_connection_section_class input[type="text"]').each(function() {
                if (jQuery(this).parent().find('.err').length < 1) {
                    jQuery(this).after('<span class="err"></span>');
                }
            });

            jQuery('#wc_settings_wwe_world_wide_express_account_number').attr('title', 'Account Number');
            jQuery('#wc_settings_wwe_speed_freight_username').attr('title', 'Username');
            jQuery('#wc_settings_wwe_speed_freight_password').attr('title', 'Password');
            jQuery('#wc_settings_wwe_licence_key').attr('title', 'Plugin License Key');
            jQuery('#wc_settings_wwe_authentication_key').attr('title', 'Authentication Key');
            jQuery('#wc_settings_wwe_text_for_own_arrangment').attr('title', 'Text For Own Arrangement');
            jQuery('#wc_settings_wwe_hand_free_mark_up').attr('title', 'Handling Fee / Markup');
            jQuery('#wc_settings_wwe_label_as').attr('title', 'Label As');
        })


        function isValidNumber(value, noNegative) {
            if (typeof(noNegative) === 'undefined')
                noNegative = false;
            var isValidNumber = false;
            var validNumber = (noNegative == true) ? parseFloat(value) >= 0 : true;
            if ((value == parseInt(value) || value == parseFloat(value)) && (validNumber)) {
                if (value.indexOf(".") >= 0) {
                    var n = value.split(".");
                    if (n[n.length - 1].length <= 4) {
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

        jQuery(document).ready(function() {

            jQuery('.quote_section_class_ltl .button-primary').on('click', function() {

                var Error = true;

                if (!labelValidation()) {
                    return false;
                } else if (!palletWeightValidation()) {
                    return false;
                } else if (!palletMaxWeightValidation()) {
                    return false;
                } else if (!handlingFeeValidation()) {
                    return false;
                } else if (!holdAtTerminalFeeValidation()) {
                    return false;
                } else if (!wwe_freight_palletshipclass()) {
                    return false;
                }
                /*Custom Error Message Validation*/
                var checkedValCustomMsg = jQuery("input[name='wc_pervent_proceed_checkout_eniture']:checked").val();
                var allow_proceed_checkout_eniture = jQuery("textarea[name=allow_proceed_checkout_eniture]").val();
                var prevent_proceed_checkout_eniture = jQuery("textarea[name=prevent_proceed_checkout_eniture]").val();

                if (checkedValCustomMsg == 'allow' && allow_proceed_checkout_eniture == '') {
                    jQuery("#mainform .quote_section_class_ltl").prepend('<div id="message" class="error inline wwe_ltl_custom_error_message"><p><strong>Custom message field is empty.</strong></p></div>');
                    jQuery('html, body').animate({
                        'scrollTop': jQuery('.wwe_ltl_custom_error_message').position().top
                    });
                    return false;
                } else if (checkedValCustomMsg == 'prevent' && prevent_proceed_checkout_eniture == '') {
                    jQuery("#mainform .quote_section_class_ltl").prepend('<div id="message" class="error inline wwe_ltl_custom_error_message"><p><strong>Custom message field is empty.</strong></p></div>');
                    jQuery('html, body').animate({
                        'scrollTop': jQuery('.wwe_ltl_custom_error_message').position().top
                    });
                    return false;
                }


                var handling_weight = jQuery('#wwe_freight_handling_weight').val();
                var handling_weight_array = handling_weight.split('.');
                if (handling_weight != '' && handling_weight_array[1] == '') {
                    jQuery("#mainform .quote_section_class_ltl").prepend('<div id="message" class="error inline wwe_freight_weight_fee_error"><p><strong>Weight of Handling Unit format should be 100.20.</strong></p></div>');
                    jQuery('html, body').animate({
                        'scrollTop': jQuery('.wwe_freight_weight_fee_error').position().top
                    });
                    return false;
                }

                if (handling_weight != '' && handling_weight_array[1] != undefined && handling_weight_array[1].length > 2) {
                    jQuery("#mainform .quote_section_class_ltl").prepend('<div id="message" class="error inline wwe_freight_weight_fee_error"><p><strong>Weight of Handling Unit format should be 100.20.</strong></p></div>');
                    jQuery('html, body').animate({
                        'scrollTop': jQuery('.wwe_freight_weight_fee_error').position().top
                    });
                    return false;
                }
                if ((handling_weight) == '.') {
                    jQuery("#mainform .quote_section_class_ltl").prepend('<div id="message" class="error inline wwe_freight_weight_fee_error"><p><strong>Weight of Handling Unit format should be 100.20.</strong></p></div>');
                    jQuery('html, body').animate({
                        'scrollTop': jQuery('.wwe_freight_weight_fee_error').position().top
                    });
                    return false;
                }
                var numberOnlyRegex = /^\d*\.?\d*$/;
                if (handling_weight != "" && !numberOnlyRegex.test(handling_weight)) {
                    jQuery("#mainform .quote_section_class_ltl").prepend('<div id="message" class="error inline wwe_freight_weight_fee_error"><p><strong>Weight of Handling Unit format should be 100.20.</strong></p></div>');
                    jQuery('html, body').animate({
                        'scrollTop': jQuery('.wwe_freight_weight_fee_error').position().top
                    });
                    return false;
                }
                return Error;

            });

            jQuery("#wwe_freight_handling_weight,#wwe_freight_maximum_handling_weight").keydown(function(e) {
                // Allow: backspace, delete, tab, escape, enter and .
                if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                    // Allow: Ctrl+A, Command+A
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    // Allow: home, end, left, right, down, up
                    (e.keyCode >= 35 && e.keyCode <= 40) || (e.target.id == 'wwe_freight_handling_weight' && (e.keyCode == 109)) || (e.target.id == 'wwe_freight_handling_weight' && (e.keyCode == 189))) {
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

            jQuery("#wwe_freight_handling_weight").keyup(function(e) {

                var val = jQuery("#wwe_freight_handling_weight").val();

                if (val.split('.').length - 1 > 1) {

                    var newval = val.substring(0, val.length - 1);
                    var countDots = newval.substring(newval.indexOf('.') + 1).length;
                    newval = newval.substring(0, val.length - countDots - 1);
                    jQuery("#wwe_freight_handling_weight").val(newval);
                }

                if (val.split('%').length - 1 > 1) {
                    var newval = val.substring(0, val.length - 1);
                    var countPercentages = newval.substring(newval.indexOf('%') + 1).length;
                    newval = newval.substring(0, val.length - countPercentages - 1);
                    jQuery("#wwe_freight_handling_weight").val(newval);
                }
                if (val.split('>').length - 1 > 0) {
                    var newval = val.substring(0, val.length - 1);
                    var countGreaterThan = newval.substring(newval.indexOf('>') + 1).length;
                    newval = newval.substring(newval, newval.length - countGreaterThan - 1);
                    jQuery("#wwe_freight_handling_weight").val(newval);
                }
                if (val.split('_').length - 1 > 0) {
                    var newval = val.substring(0, val.length - 1);
                    var countUnderScore = newval.substring(newval.indexOf('_') + 1).length;
                    newval = newval.substring(newval, newval.length - countUnderScore - 1);
                    jQuery("#wwe_freight_handling_weight").val(newval);
                }
            });
        });
    </script>
<?php
}
