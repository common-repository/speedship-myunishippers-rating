<?php

/**
 * WWE LTL Warehouse Template
 * 
 * @package     WWE LTL Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$warehous_list = $wpdb->get_results(
    "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE location = 'warehouse'"
);
?>
<script type="text/javascript">
    jQuery(document).ready(function() {

        function setLTLCity($this) {
            var city = jQuery($this).val();
            jQuery('#ltl_origin_city').val(city);
        }

        window.location.href = jQuery('.close').attr('href');
        jQuery('.hide_val').click(function() {

            jQuery('#edit_form_id').val('');
            jQuery("#ltl_origin_zip").val('');
            jQuery('.city_select').hide();
            jQuery('.city_input').show();
            jQuery('#ltl_origin_city').css('background', 'none');
            jQuery("#ltl_origin_city").val('');
            jQuery("#ltl_origin_state").val('');
            jQuery("#ltl_origin_country").val('');
            jQuery('.ltl_zip_validation_err').hide();
            jQuery('.ltl_city_validation_err').hide();
            jQuery('.ltl_state_validation_err').hide();
            jQuery('.ltl_country_validation_err').hide();
            jQuery('.not_allowed').hide();
            jQuery('.wrng_credential').hide();
        });

        jQuery('.ltl_add_warehouse_btn').click(function() {

            setTimeout(function() {

                if (jQuery('.ltl_add_warehouse_popup').is(':visible')) {
                    jQuery('.ltl_add_warehouse_input > input').eq(0).focus();
                }
            }, 500);
        });

        jQuery("#ltl_origin_zip").keypress(function(e) {

            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {

                return false;
            }
        });

        jQuery("#ltl_origin_zip").on('change', function() {

            if (jQuery("#ltl_origin_zip").val() == '') {

                return false;
            }

            var postForm = {
                'action': 'ltl_get_address',
                'origin_zip': jQuery('#ltl_origin_zip').val(),
            };

            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: postForm,
                dataType: 'json',
                beforeSend: function() {
                    jQuery('.ltl_zip_validation_err').hide();
                    jQuery('.ltl_city_validation_err').hide();
                    jQuery('.ltl_state_validation_err').hide();
                    jQuery('.ltl_country_validation_err').hide();
                },
                success: function(data) {

                    if (data) {

                        if (data.country === 'US') {

                            if (data.postcode_localities == 1) {
                                jQuery('.city_select').show();
                                jQuery('#actname').replaceWith(data.city_option);
                                jQuery('.ltl_multi_state').replaceWith(data.city_option);
                                jQuery('.city-multiselect').change(function() {
                                    setLTLCity(this);
                                });
                                jQuery('#ltl_origin_city').val(data.first_city);
                                jQuery('#ltl_origin_state').val(data.state);
                                jQuery('#ltl_origin_country').val(data.country);
                                jQuery('#ltl_origin_state').css('background', 'none');
                                jQuery('.city_select_css').css('background', 'none');
                                jQuery('#ltl_origin_country').css('background', 'none');
                                jQuery('.city_input').hide();
                            } else {
                                jQuery('.city_input').show();
                                jQuery('#_city').removeAttr('value');
                                jQuery('.city_select').hide();
                                jQuery('#ltl_origin_city').val(data.city);
                                jQuery('#ltl_origin_state').val(data.state);
                                jQuery('#ltl_origin_country').val(data.country);
                                jQuery('#ltl_origin_city').css('background', 'none');
                                jQuery('#ltl_origin_state').css('background', 'none');
                                jQuery('#ltl_origin_country').css('background', 'none');
                            }
                        } else if (data.result === 'false') {
                            jQuery('#ltl_origin_city').css('background', 'none');
                            jQuery('#ltl_origin_state').css('background', 'none');
                            jQuery('#ltl_origin_country').css('background', 'none');
                        } else if (data.apiResp === 'apiErr') {
                            jQuery('.wrng_credential').show('slow');
                            jQuery('#ltl_origin_city').css('background', 'none');
                            jQuery('#ltl_origin_state').css('background', 'none');
                            jQuery('#ltl_origin_country').css('background', 'none');
                            setTimeout(function() {
                                jQuery('.wrng_credential').hide('slow');
                            }, 5000);
                        } else {
                            jQuery('.not_allowed').show('slow');
                            jQuery('#ltl_origin_city').css('background', 'none');
                            jQuery('#ltl_origin_state').css('background', 'none');
                            jQuery('#ltl_origin_country').css('background', 'none');
                            setTimeout(function() {
                                jQuery('.not_allowed').hide('slow');
                            }, 5000);
                        }
                    }
                },
            });
            return false;
        });
    });

    jQuery(function() {

        jQuery('input.alphaonly').keyup(function() {

            if (this.value.match(/[^a-zA-Z ]/g)) {

                this.value = this.value.replace(/[^a-zA-Z ]/g, '');
            }
        });
    });
</script>
<div class="ltl_setting_section">
    <h1>Warehouses</h1><br>
    <a href="#ltl_add_warehouse_btn" title="Add Warehouse" class="ltl_add_warehouse_btn hide_val" name="avc">Add</a>
    <br>
    <div class="warehouse_text">
        <p>Warehouses that inventory all products not otherwise identified as drop shipped items. The warehouse with the lowest shipping cost to the destination is used for quoting purposes.</p>
    </div>
    <div id="message" class="updated inline warehouse_deleted">
        <p><strong>Success! Warehouse deleted successfully.</strong></p>
    </div>
    <div id="message" class="updated inline warehouse_created">
        <p><strong>Success! New warehouse added successfully.</strong></p>
    </div>
    <div id="message" class="updated inline warehouse_updated">
        <p><strong>Success! Warehouse updated successfully.</strong></p>
    </div>
    <table class="ltl_warehouse_list" id="append_warehouse">
        <thead>
            <tr>
                <th class="ltl_warehouse_list_heading">City</th>
                <th class="ltl_warehouse_list_heading">State</th>
                <th class="ltl_warehouse_list_heading">Zip</th>
                <th class="ltl_warehouse_list_heading">Country</th>
                <th class="ltl_warehouse_list_heading">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (count($warehous_list) > 0) {

                foreach ($warehous_list as $list) {
            ?>
                    <tr id="row_<?php echo esc_attr($list->id); ?>" data-id="<?php echo esc_attr($list->id); ?>">
                        <td class="ltl_warehouse_list_data"><?php echo esc_html($list->city); ?></td>
                        <td class="ltl_warehouse_list_data"><?php echo esc_html($list->state); ?></td>
                        <td class="ltl_warehouse_list_data"><?php echo esc_html($list->zip); ?></td>
                        <td class="ltl_warehouse_list_data"><?php echo esc_html($list->country); ?></td>
                        <td class="ltl_warehouse_list_data">
                            <a href="javascript(0)" onclick="return edit_ltl_warehouse(<?php echo esc_attr($list->id); ?>);"><img src="<?php echo plugins_url('warehouse-dropship/wild/assets/images/edit.png', __FILE__); ?>" title="Edit"></a>
                            <a href="javascript(0)" onclick="return delete_ltl_current_warehouse(<?php echo esc_attr($list->id); ?>);"><img src="<?php echo plugins_url('warehouse-dropship/wild/assets/images/delete.png', __FILE__); ?>" title="Delete"></a>
                        </td>
                    </tr>
                <?php
                }
            } else {
                ?>
                <tr class="new_warehouse_add" data-id=0></tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Add Popup for new warehouse -->
    <div id="ltl_add_warehouse_btn" class="ltl_warehouse_overlay">
        <div class="ltl_add_warehouse_popup">
            <h2 class="warehouse_heading">Warehouse</h2>
            <a class="close" href="#">&times;</a>
            <div class="content">
                <div class="already_exist">
                    <strong>Error!</strong> Zip code already exists.
                </div>
                <div class="not_allowed">
                    <p><strong>Error!</strong> Please enter US zip code.</p>
                </div>
                <div class="wrng_credential">
                    <p><strong>Error!</strong> Please verify credentials at connection settings panel.</p>
                </div>
                <form method="post">
                    <input type="hidden" name="edit_form_id" value="" id="edit_form_id">
                    <div class="ltl_add_warehouse_input">
                        <label for="ltl_origin_zip">Zip</label>
                        <input type="text" title="Zip" maxlength="5" value="" name="ltl_origin_zip" placeholder="30214" id="ltl_origin_zip">
                    </div>

                    <div class="ltl_add_warehouse_input city_input">
                        <label for="ltl_origin_city">City</label>
                        <input type="text" class="alphaonly" title="City" value="" name="ltl_origin_city" placeholder="Fayetteville" id="ltl_origin_city">
                    </div>

                    <div class="ltl_add_warehouse_input city_select">
                        <label for="ltl_origin_city">City</label>
                        <select id="actname"></select>
                    </div>

                    <div class="ltl_add_warehouse_input">
                        <label for="ltl_origin_state">State</label>
                        <input type="text" class="alphaonly" maxlength="2" title="State" value="" name="ltl_origin_state" placeholder="GA" id="ltl_origin_state">
                    </div>

                    <div class="ltl_add_warehouse_input">
                        <label for="ltl_origin_country">Country</label>
                        <input type="text" class="alphaonly" maxlength="2" title="Country" name="ltl_origin_country" value="" placeholder="US" id="ltl_origin_country">
                        <input type="hidden" name="ltl_location" value="warehouse" id="ltl_location">
                    </div>

                    <input type="submit" name="ltl_submit_warehouse" value="Save" class="save_warehouse_form" onclick="return save_ltl_warehouse();">
                </form>
            </div>
        </div>
    </div>