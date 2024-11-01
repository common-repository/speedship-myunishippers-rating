<?php

/**
 * WWE LTL Admin Filters
 * 
 * @package     WWE LTL Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check WooCommerce Exist
 */
function speed_ltl_freight_woocommrec_avaibility_error()
{

    $class = "error";
    $message = "WooCommerce LTL Freight is enabled, but not effective. It requires WooCommerce in order to work , Please <a target='_blank' href='https://wordpress.org/plugins/woocommerce/installation/'>Install</a> WooCommerce Plugin. Reactivate WooCommerce LTL Freight plugin to create LTL shipping class.";
    echo "<div class=\"$class\"> <p>$message</p></div>";
}

/**
 * Add Tab For Speedfreight In Woo Settings
 * @param $settings
 */
function speed_ltl_shipping_sections($settings)
{

    include('ltl_tab_class_woocommrece.php');
    return $settings;
}

/**
 * Check WooCommerce Version And Throw Error If Less Than 2.6
 */
function speed_ltl_check_woo_version()
{

    $wcPluginVersion = new speed_ltl_shipping_get_quotes();
    $woo_version = $wcPluginVersion->ltl_get_woo_version_number();

    $version = '2.6';
    if (!version_compare($woo_version["woocommerce_plugin_version"], $version, ">=")) {

        add_action('admin_notices', 'speed_ltl_admin_notice_failure');
    }
}

/**
 * Admin Notices
 */
function speed_ltl_admin_notice_failure()
{
?>
    <div class="notice notice-error">
        <p><?php _e('WWE LTL plugin requires WooCommerce version 2.6 or higher to work. Functionality may not work properly.', 'wwe-woo-version-failure'); ?></p>
    </div>
<?php
}

/**
 * Hide Shipping Methods If Not From Eniture
 * @param $available_methods
 */
function speed_ltl_hide_shipping_based_on_class($available_methods)
{

    $forceShowMethods = apply_filters('force_show_methods', []);

    if (get_option('wc_settings_wwe_allow_other_plugins') == 'no' && (!empty($forceShowMethods)) && (!in_array("valid_third_party", $forceShowMethods))) {
        if (count($available_methods) > 0) {
            $plugins_array = [];
            $eniture_plugins = get_option('EN_Plugins');
            if ($eniture_plugins) {
                $plugins_array = json_decode($eniture_plugins);
            }

            foreach ($available_methods as $index => $method) {
                if (!($method->method_id == 'speedship' || $method->method_id == 'ltl_shipping_method' || in_array($method->method_id, $plugins_array))) {
                    unset($available_methods[$index]);
                }
            }
        }
    }
    return $available_methods;
}

/**
 * Save WWE LTL Freight Carriers
 * @param  $post_id
 */
function speed_ltl_save_carrier_status($post_id)
{

    if (isset($_POST['action']) && sanitize_text_field($_POST['action']) == 'save_carrier_status') {

        global $wpdb;
        $all_freight_array = [];
        $count_carrier = 1;
        $ltl_freight_get = $wpdb->get_results("SELECT * FROM wp_freights order by speed_freight_carrierName ASC");

        foreach ($ltl_freight_get as $ltl_freight_get_value) :

            if (isset($_POST[$ltl_freight_get_value->speed_freight_carrierSCAC . $ltl_freight_get_value->id]) && $_POST[$ltl_freight_get_value->speed_freight_carrierSCAC . $ltl_freight_get_value->id] == 'on') {

                $wpdb->query($wpdb->prepare("UPDATE wp_freights SET carrier_status = '%s' WHERE speed_freight_carrierSCAC = '$ltl_freight_get_value->speed_freight_carrierSCAC'", '1'));
            } else {

                $wpdb->query($wpdb->prepare("UPDATE wp_freights SET carrier_status = '%s' WHERE speed_freight_carrierSCAC = '$ltl_freight_get_value->speed_freight_carrierSCAC' ", '0'));
            }
        endforeach;
    }
}

/**
 * Add WWE LTL Shipping Method
 * @param $methods
 */
function speed_ltl_add_LTL_shipping_method($methods)
{

    $methods['ltl_shipping_method'] = 'WC_speedfreight_Shipping_Method';
    return $methods;
}

/**
 * Remove Label If Free Shipping
 * @param $full_label
 * @param $method
 * @return string
 */
function speed_ltl_remove_free_label($full_label, $method)
{

    $full_label = str_replace("(Free)", "", $full_label);
    return $full_label;
}

/**
 * Shipping Message On Cart If No Method Available 
 */
if (!function_exists("wwe_no_method_available")) {

    function wwe_no_method_available()
    {
        $allow_checkout = (isset($_POST['allow_proceed_checkout_eniture'])) ? sanitize_text_field($_POST['allow_proceed_checkout_eniture']) : get_option('allow_proceed_checkout_eniture');
        $prevent_checkout = (isset($_POST['prevent_proceed_checkout_eniture'])) ? sanitize_text_field($_POST['prevent_proceed_checkout_eniture']) : get_option('prevent_proceed_checkout_eniture');

        if (get_option('allow_proceed_checkout_eniture') !== false) {
            update_option('allow_proceed_checkout_eniture', $allow_checkout);
            update_option('prevent_proceed_checkout_eniture', $prevent_checkout);
        } else {
            $deprecated = null;
            $autoload = 'no';
            add_option('allow_proceed_checkout_eniture', $allow_checkout, $deprecated, $autoload);
            add_option('prevent_proceed_checkout_eniture', $prevent_checkout, $deprecated, $autoload);
        }
    }
}

/**
 * Filter For CSV Import
 */
if (!function_exists('en_import_dropship_location_csv')) {

    /**
     * Import drop ship location CSV
     * @param $data
     * @param $this
     * @return array
     */
    function en_import_dropship_location_csv($data, $parseData)
    {
        $_product_freight_class = $_product_freight_class_variation = '';
        $_dropship_location = $locations = [];
        foreach ($data['meta_data'] as $key => $metaData) {
            $location = explode(',', trim($metaData['value']));
            switch ($metaData['key']) {
                    // Update new columns
                case '_product_freight_class':
                    $_product_freight_class = trim($metaData['value']);
                    unset($data['meta_data'][$key]);
                    break;
                case '_product_freight_class_variation':
                    $_product_freight_class_variation = trim($metaData['value']);
                    unset($data['meta_data'][$key]);
                    break;
                case '_dropship_location_nickname':
                    $locations[0] = $location;
                    unset($data['meta_data'][$key]);
                    break;
                case '_dropship_location_zip_code':
                    $locations[1] = $location;
                    unset($data['meta_data'][$key]);
                    break;
                case '_dropship_location_city':
                    $locations[2] = $location;
                    unset($data['meta_data'][$key]);
                    break;
                case '_dropship_location_state':
                    $locations[3] = $location;
                    unset($data['meta_data'][$key]);
                    break;
                case '_dropship_location_country':
                    $locations[4] = $location;
                    unset($data['meta_data'][$key]);
                    break;
                case '_dropship_location':
                    $_dropship_location = $location;
            }
        }

        // Update new columns
        if (strlen($_product_freight_class) > 0) {
            $data['meta_data'][] = [
                'key' => '_ltl_freight',
                'value' => $_product_freight_class,
            ];
        }

        // Update new columns
        if (strlen($_product_freight_class_variation) > 0) {
            $data['meta_data'][] = [
                'key' => '_ltl_freight_variation',
                'value' => $_product_freight_class_variation,
            ];
        }

        if (!empty($locations) || !empty($_dropship_location)) {
            if (isset($locations[0]) && is_array($locations[0])) {
                foreach ($locations[0] as $key => $location_arr) {
                    $metaValue = [];
                    if (isset($locations[0][$key], $locations[1][$key], $locations[2][$key], $locations[3][$key])) {
                        $metaValue[0] = $locations[0][$key];
                        $metaValue[1] = $locations[1][$key];
                        $metaValue[2] = $locations[2][$key];
                        $metaValue[3] = $locations[3][$key];
                        $metaValue[4] = $locations[4][$key];
                        $dsId[] = en_serialize_dropship($metaValue);
                    }
                }
            } else {
                $dsId[] = en_serialize_dropship($_dropship_location);
            }

            $sereializedLocations = maybe_serialize($dsId);
            $data['meta_data'][] = [
                'key' => '_dropship_location',
                'value' => $sereializedLocations,
            ];
        }
        return $data;
    }

    add_filter('woocommerce_product_importer_parsed_data', 'en_import_dropship_location_csv', '99', '2');
}

/**
 * Serialize drop ship
 * @param $metaValue
 * @return string
 * @global $wpdb
 */

if (!function_exists('en_serialize_dropship')) {
    function en_serialize_dropship($metaValue)
    {
        global $wpdb;
        $dropship = (array)reset($wpdb->get_results(
            "SELECT id
                        FROM " . $wpdb->prefix . "warehouse WHERE nickname='$metaValue[0]' AND zip='$metaValue[1]' AND city='$metaValue[2]' AND state='$metaValue[3]' AND country='$metaValue[4]'"
        ));

        $dropship = array_map('intval', $dropship);

        if (empty($dropship['id'])) {
            $data = en_csv_import_dropship_data($metaValue);
            $wpdb->insert(
                $wpdb->prefix . 'warehouse',
                $data
            );

            $dsId = $wpdb->insert_id;
        } else {
            $dsId = $dropship['id'];
        }

        return $dsId;
    }
}

/**
 * Filtered Data Array
 * @param $metaValue
 * @return array
 */
if (!function_exists('en_csv_import_dropship_data')) {
    function en_csv_import_dropship_data($metaValue)
    {
        return array(
            'city' => $metaValue[2],
            'state' => $metaValue[3],
            'zip' => $metaValue[1],
            'country' => $metaValue[4],
            'location' => 'dropship',
            'nickname' => (isset($metaValue[0])) ? $metaValue[0] : "",
        );
    }
}
