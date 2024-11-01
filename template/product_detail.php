<?php

/**
 * WWE Small Product Detail Template
 *
 * @package     WWE Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Drop Ship For Shipping Section In Product Detail Page
 * @param $loop
 * @param $variation_data
 * @param $variation
 * @global $wpdb
 */
function speed_wwe_sm_dropship($loop, $variation_data = [], $variation = [])
{

    global $wpdb;
    $dropship_list = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "warehouse WHERE location = 'dropship'");
    if (!empty($dropship_list)) {
        (isset($variation->ID) && $variation->ID > 0) ? $variationID = $variation->ID : $variationID = get_the_ID();
        /*
         * create enable dropship checkbox.
         */
        woocommerce_wp_checkbox(
            array(
                'id' => '_enable_dropship[' . $variationID . ']',
                'label' => __('Enable drop ship location', 'woocommerce'),
                'value' => get_post_meta($variationID, '_enable_dropship', true),
            )
        );
        $attributes = array(
            'id' => '_dropship_location[' . $variationID . ']',
            'class' => 'p_ds_location',
        );
        $get_loc = maybe_unserialize(get_post_meta($variationID, '_dropship_location', true));


        $valuesArr = [];

        foreach ($dropship_list as $list) {

            (isset($list->nickname) && $list->nickname == '') ? $nickname = '' : $nickname = $list->nickname . ' - ';
            (isset($list->country) && $list->country == '') ? $country = '' : $country = '(' . $list->country . ')';
            $location = $nickname . $list->zip . ', ' . $list->city . ', ' . $list->state . ' ' . $country;
            $finalValue['option_id'] = $list->id;
            $finalValue['option_value'] = $list->id;
            $finalValue['option_label'] = $location;
            $valuesArr[] = $finalValue;
        }
        $aFields[] = array(
            'attributes' => $attributes,
            'label' => 'Drop ship location',
            'value' => $valuesArr,
            'name' => '_dropship_location[' . $variationID . '][]',
            'type' => 'select',
            'selected_value' => $get_loc,
            'variant_id' => $variationID
        );
        $aFields = apply_filters('before_wwe_ltl_product_detail_fields', $aFields);
        apply_filters('En_Plugins_dropship_filter', $aFields, $get_loc, $variationID);
    }
}

/**
 * Dropship Filter
 */
if (!has_filter('En_Plugins_dropship_filter')) {
    add_action('woocommerce_product_options_shipping', 'speed_wwe_sm_dropship');
    add_action('woocommerce_product_after_variable_attributes', 'speed_wwe_sm_dropship', 10, 3);
    add_filter('En_Plugins_dropship_filter', 'speed_wwe_sm_dropship_filter', 10, 3);
}

/**
 * Dropship Dropdown
 * @param $aFields
 * @param $get_loc
 * @param $variationID
 */
function speed_wwe_sm_dropship_filter($aFields, $get_loc, $variationID)
{

    $fieldsHtml = '';
    foreach ($aFields as $key => $sField) {
        $sField = apply_filters('wwe_ltl_product_detail_fields', $sField);
        $fieldsHtml = speed_wwe_sm_dropship_html($sField, $fieldsHtml, $get_loc, $variationID);
    }
    $fieldsHtml = apply_filters('after_wwe_ltl_product_detail_fields', $fieldsHtml);
    // echo $fieldsHtml;
}

/**
 * Attribute For Drop Ship Dropdown
 * @param $attributes
 * @return string
 */
function speed_wwe_small_attributes_string($attributes)
{

    $str = '';
    foreach ($attributes as $key => $sAttribute) {
        $str .= ' ' . $key . ' ="' . $sAttribute . '" ';
    }
    return $str;
}


/**
 * Drop Ship Dropdown Select
 * @param $sField
 * @param $fieldsHtml
 * @return string
 */
function speed_wwe_sm_dropship_html($sField, $fieldsHtml)
{

    $description = "";
    $disable_me = FALSE;

    $plan_notifi = apply_filters('en_woo_plans_notification_action', []);

    $dropship_flag = count($sField['value']);

    $dropship_flag = isset($dropship_flag) && ($dropship_flag > 1) ? true : false;

    if (!empty($plan_notifi) && (isset($plan_notifi['multi_dropship']))) {
        $enable_plugins = (isset($plan_notifi['multi_dropship']['enable_plugins'])) ? $plan_notifi['multi_dropship']['enable_plugins'] : "";
        $disable_plugins = (isset($plan_notifi['multi_dropship']['disable_plugins'])) ? $plan_notifi['multi_dropship']['disable_plugins'] : "";
        if (strlen($disable_plugins) > 0) {
            if (strlen($enable_plugins) > 0) {
                $description = "<br><br>" . apply_filters('en_woo_plans_notification_message_action', $enable_plugins, $disable_plugins);
            } else {
                if ($dropship_flag && get_option('wwe_small_packages_quotes_store_type') == "1") {
                    // $description = apply_filters('speed_wwe_small_packages_quotes_plans_notification_link', array(2));
                    // $disable_me = TRUE;
                } elseif (get_option('wwe_small_packages_quotes_store_type') == "0" && get_option('en_old_user_dropship_status') == "1") {
                    //old user and single dropship then show msg standard required
                    // $description = apply_filters('speed_wwe_small_packages_quotes_plans_notification_link', array(2));
                    // $disable_me = TRUE;
                }
            }
        }
    }

    $disable_dropship_flage = true;
    if (get_option('wwe_small_packages_quotes_store_type') == "0" && get_option('en_old_user_dropship_status') == "0") {
        $disable_dropship_flage = false;
    }
    //      Multi Dropship
    $multi_dropship = apply_filters('speed_wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'multi_dropship');
    $str = speed_wwe_small_attributes_string($sField['attributes']);
    $fieldsHtml .= '<p class="form-field _dropship_location">';
    $fieldsHtml .= '<label for="_dropship_location">' . $sField['label'] . '</label>';
    if ($sField['type'] == 'select') {
        $fieldsHtml .= '<select name="' . $sField['name'] . '" ' . $str . '>';
        if ($sField['value']) {
            $count = 0;
            foreach ($sField['value'] as $option) {

                $disabled_option = isset($disable_dropship_flage) && ($disable_dropship_flage == true && $count > 0 && (is_array($multi_dropship))) ? 'disabled' : '';

                $selected_option = speed_wwe_sm_product_ds_selected_option($sField['selected_value'], $option['option_value']);
                $fieldsHtml .= '<option ' . $disabled_option . ' value="' . esc_attr($option['option_value']) . '" ' . $selected_option . '>' . esc_html($option['option_label']) . ' </option>';

                $count++;
            }
        }

        $fieldsHtml .= '</select>';
        $fieldsHtml .= $description;
    }
    $fieldsHtml .= '</p>';
    return $fieldsHtml;
}

/**
 * Drop Ship Dropdown Selected Options
 * @param $get_loc
 * @param $option_val
 */
function speed_wwe_sm_product_ds_selected_option($get_loc, $option_val)
{

    $selected = '';
    if (is_array($get_loc)) {
        if (in_array($option_val, $get_loc)) {
            $selected = 'selected="selected"';
        }
    } else {
        $selected = selected($get_loc, $option_val, false);
    }
    return $selected;
}

/**
 * Drop Ship Save For Variations
 */
add_action('woocommerce_save_product_variation', 'speed_wwe_sm_save_variable_fields', 10, 1);

/**
 * Product Variable custom fields save
 * @param $post_id
 */
function speed_wwe_sm_save_variable_fields($post_id)
{

    if (isset($post_id) && $post_id > 0) :
        $enable_ds = (isset($_POST['_enable_dropship'][$post_id]) ? sanitize_text_field($_POST['_enable_dropship'][$post_id]) : "");
        $ds_locaton = sanitize_text_field($_POST['_dropship_location'][$post_id]);
        $ds_location_val = isset($ds_locaton) && is_array($ds_locaton) ? array_map('intval', $ds_locaton) : $ds_locaton;
        update_post_meta($post_id, '_enable_dropship', esc_attr($enable_ds));
        update_post_meta($post_id, '_dropship_location', maybe_serialize($ds_location_val));
    endif;
}

/**
 * Save Product Custom Shipping Options
 */
add_action('woocommerce_process_product_meta', 'speed_wwe_small_product_fields_save');

/**
 * Product custom fields save
 * @param $post_id
 */
function speed_wwe_small_product_fields_save($post_id)
{

    $woocommerce_checkbox = (isset($_POST['_enable_dropship'][$post_id])) ? sanitize_text_field($_POST['_enable_dropship'][$post_id]) : "";
    $dropship_location = sanitize_text_field($_POST['_dropship_location'][$post_id]);
    $dropship_location_val = isset($dropship_location) && is_array($dropship_location) ? array_map('intval', $dropship_location) : $dropship_location;
    update_post_meta($post_id, '_enable_dropship', esc_attr($woocommerce_checkbox));
    update_post_meta($post_id, '_dropship_location', maybe_serialize($dropship_location_val));
}
