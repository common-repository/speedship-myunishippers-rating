<?php

/**
 * WWE LTL Drop Ship Template
 * 
 * @package     WWE LTL Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

$dropship_list = $wpdb->get_results(
    "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE location = 'dropship'"
);
?>
<script type="text/javascript">
    jQuery(document).ready(function() {

        function setLtlDsCity($this) {
            var city = jQuery($this).val();
            jQuery('#ltl_dropship_city').val(city);
        }

        jQuery('.hide_drop_val').click(function() {
            jQuery('#edit_dropship_form_id').val('');
            jQuery("#ltl_dropship_zip").val('');
            jQuery('.city_select').hide();
            jQuery('.city_input').show();
            jQuery('#ltl_dropship_city').css('background', 'none');
            jQuery("#ltl_dropship_nickname").val('');
            jQuery("#ltl_dropship_city").val('');
            jQuery('.ltl_multi_state').empty();
            jQuery("#ltl_dropship_state").val('');
            jQuery("#ltl_dropship_country").val('');
            jQuery('.ltl_zip_validation_err').hide();
            jQuery('.ltl_city_validation_err').hide();
            jQuery('.ltl_state_validation_err').hide();
            jQuery('.ltl_country_validation_err').hide();
            jQuery('.not_allowed').hide();
            jQuery('.already_exist').hide();
            jQuery('.wrng_credential').hide();
        });

        jQuery('.ltl_add_dropship_btn').click(function() {

            setTimeout(function() {

                if (jQuery('.ds_popup').is(':visible')) {
                    jQuery('.ds_input > input').eq(0).focus();
                }
            }, 500);
        });

        jQuery("#ltl_dropship_zip").keypress(function(e) {

            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });

        jQuery("#ltl_dropship_zip").on('change', function() {

            if (jQuery("#ltl_dropship_zip").val() == '') {
                return false;
            }

            var postForm = {
                'action': 'ltl_get_address',
                'origin_zip': jQuery('#ltl_dropship_zip').val(),
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
                                jQuery('#dropship_actname').replaceWith(data.city_option);
                                jQuery('.en_wd_multi_state').replaceWith(data.city_option);
                                jQuery('#ltl_dropship_state').val(data.state);
                                jQuery('#ltl_dropship_country').val(data.country);
                                jQuery('.city-multiselect').change(function() {
                                    setLtlDsCity(this);
                                });
                                jQuery('#ltl_dropship_city').val(data.first_city);
                                jQuery('#ltl_dropship_state').css('background', 'none');
                                jQuery('.city_select_css').css('background', 'none');
                                jQuery('#ltl_dropship_country').css('background', 'none');
                                jQuery('.city_input').hide();

                            } else {

                                jQuery('.city_input').show();
                                jQuery('#_city').removeAttr('value');
                                jQuery('.city_select').hide();
                                jQuery('#ltl_dropship_city').val(data.city);
                                jQuery('#ltl_dropship_state').val(data.state);
                                jQuery('#ltl_dropship_country').val(data.country);
                                jQuery('#ltl_dropship_city').css('background', 'none');
                                jQuery('#ltl_dropship_state').css('background', 'none');
                                jQuery('#ltl_dropship_country').css('background', 'none');
                            }
                        } else if (data.result === 'false') {

                            jQuery('#ltl_dropship_city').css('background', 'none');
                            jQuery('#ltl_dropship_state').css('background', 'none');
                            jQuery('#ltl_dropship_country').css('background', 'none');

                        } else if (data.apiResp === 'apiErr') {
                            jQuery('.wrng_credential').show('slow');
                            jQuery('#ltl_dropship_city').css('background', 'none');
                            jQuery('#ltl_dropship_state').css('background', 'none');
                            jQuery('#ltl_dropship_country').css('background', 'none');
                            setTimeout(function() {
                                jQuery('.wrng_credential').hide('slow');
                            }, 5000);
                        } else {

                            jQuery('.not_allowed').show('slow');
                            jQuery('#ltl_dropship_city').css('background', 'none');
                            jQuery('#ltl_dropship_state').css('background', 'none');
                            jQuery('#ltl_dropship_country').css('background', 'none');
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

            var location_field_id = jQuery(this).attr("id");
            var location_regex = location_field_id == 'en_wd_origin_city' || location_field_id == 'en_wd_dropship_city' ? /[^a-zA-Z-]/g : /[^a-zA-Z]/g;
            if (this.value.match(location_regex)) {
                this.value = this.value.replace(location_regex, '');
            }
        });
    });
</script>

<div class="ltl_setting_section">
    <a href="#delete_ltl_dropship_btn" class="delete_ltl_dropship_btn hide_drop_val"></a>
    <div id="delete_ltl_dropship_btn" class="ltl_warehouse_overlay">
        <div class="ltl_add_warehouse_popup">
            <h2 class="del_hdng">
                Warning!
            </h2>
            <p class="delete_p">
                Warning! If you delete this location, Drop ship location settings will be disable against products if any.
            </p>
            <div class="del_btns">
                <a href="#" class="cancel_delete">Cancel</a>
                <a href="#" class="confirm_delete">OK</a>
            </div>
        </div>
    </div>

    <h1>Drop ships</h1><br>
    <a href="#add_ltl_dropship_btn" title="Add Drop Ship" class="ltl_add_dropship_btn hide_drop_val">Add</a>
    <br>
    <div class="warehouse_text">
        <p>Locations that inventory specific items that are drop shipped to the destination. Use the product's settings page to identify it as a drop shipped item and its associated drop ship location. Orders that include drop shipped items will display a single figure for the shipping rate estimate that is equal to the sum of the cheapest option of each shipment required to fulfill the order.</p>
    </div>
    <div id="message" class="updated inline dropship_created">
        <p><strong>Success! New drop ship added successfully.</strong></p>
    </div>
    <div id="message" class="updated inline dropship_updated">
        <p><strong>Success! Drop ship updated successfully.</strong></p>
    </div>
    <div id="message" class="updated inline dropship_deleted">
        <p><strong>Success! Drop ship deleted successfully.</strong></p>
    </div>
    <table class="ltl_dropship_list" id="append_dropship">
        <thead>
            <tr>
                <th class="ltl_dropship_list_heading">Nickname</th>
                <th class="ltl_dropship_list_heading">City</th>
                <th class="ltl_dropship_list_heading">State</th>
                <th class="ltl_dropship_list_heading">Zip</th>
                <th class="ltl_dropship_list_heading">Country</th>
                <th class="ltl_dropship_list_heading">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (count($dropship_list) > 0) {
                foreach ($dropship_list as $list) {
            ?>
                    <tr id="row_<?php echo esc_html($list->id); ?>">
                        <td class="ltl_dropship_list_data"><?php echo esc_html($list->nickname); ?></td>
                        <td class="ltl_dropship_list_data"><?php echo esc_html($list->city); ?></td>
                        <td class="ltl_dropship_list_data"><?php echo esc_html($list->state); ?></td>
                        <td class="ltl_dropship_list_data"><?php echo esc_html($list->zip); ?></td>
                        <td class="ltl_dropship_list_data"><?php echo esc_html($list->country); ?></td>
                        <td class="ltl_dropship_list_data">
                            <a href="javascript(0)" onclick="return edit_ltl_dropship(<?php echo esc_html($list->id); ?>);"><img src="<?php echo plugins_url('warehouse-dropship/wild/assets/images/edit.png', __FILE__); ?>" title="Edit"></a>
                            <a href="javascript(0)" onclick="return delete_ltl_current_dropship(<?php echo esc_html($list->id); ?>);"><img src="<?php echo plugins_url('warehouse-dropship/wild/assets/images/delete.png', __FILE__); ?>" title="Delete"></a>
                        </td>
                    </tr>
                <?php
                }
            } else {
                ?>
                <tr class="new_dropship_add" data-id=0></tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Add Popup for new dropship -->
    <div id="add_ltl_dropship_btn" class="ltl_warehouse_overlay">
        <div class="ltl_add_warehouse_popup ds_popup">
            <h2 class="dropship_heading">Drop Ship</h2>
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
                    <input type="hidden" name="edit_dropship_form_id" value="" id="edit_dropship_form_id">
                    <div class="ltl_add_warehouse_input ds_input">
                        <label for="ltl_dropship_nickname">Nickname</label>
                        <input type="text" title="Nickname" value="" name="ltl_dropship_nickname" placeholder="Nickname" id="ltl_dropship_nickname">
                    </div>
                    <div class="ltl_add_warehouse_input">
                        <label for="ltl_origin_zip">Zip</label>
                        <input title="Zip" type="text" value="" name="ltl_dropship_zip" placeholder="30214" id="ltl_dropship_zip">
                    </div>

                    <div class="ltl_add_warehouse_input city_input">
                        <label for="ltl_origin_city">City</label>
                        <input type="text" class="alphaonly" title="City" value="" name="ltl_dropship_city" placeholder="Fayetteville" id="ltl_dropship_city">
                    </div>

                    <div class="ltl_add_warehouse_input city_select">
                        <label for="ltl_origin_city">City</label>
                        <select id="dropship_actname"></select>
                    </div>

                    <div class="ltl_add_warehouse_input">
                        <label for="ltl_origin_state">State</label>
                        <input type="text" class="alphaonly" maxlength="2" title="State" value="" name="ltl_dropship_state" placeholder="GA" id="ltl_dropship_state">
                    </div>

                    <div class="ltl_add_warehouse_input">
                        <label for="ltl_origin_country">Country</label>
                        <input type="text" class="alphaonly" maxlength="2" title="Country" name="ltl_dropship_country" value="" placeholder="US" id="ltl_dropship_country">
                        <input type="hidden" name="ltl_dropship_location" value="dropship" id="ltl_dropship_location">
                    </div>

                    <input type="submit" name="ltl_submit_dropship" value="Save" class="save_warehouse_form" onclick="return save_ltl_dropship();">
                </form>
            </div>
        </div>
    </div>