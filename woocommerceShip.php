<?php

/**
 * Plugin Name: SpeedShip/myUnishippers Rating 
 * Plugin URI: http://wwex.com/
 * Description: POS Parcel and LTL Rating from Worldwide Express / Unishippers
 * Version: 1.0
 * Author: WWEX Speedship
 * Author URI: http://www.wwex.com
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /lang
 * Text Domain: wwex
 */

if (!defined('ABSPATH')) {
    exit;
}

define('SPEED_WWE_DOMAIN_HITTING_URL', 'https://wwex.com');
define('SPEED_WWE_FDO_HITTING_URL', 'https://wwex.com');

include dirname(__FILE__) . '/standard-box-sizes/en-standard-box-sizes.php';
include dirname(__FILE__) . '/residential-address-detection/residential-address-detection.php';

include dirname(__FILE__) . '/ltl-freight-quotes-worldwide-express-edition-speedship/woocommercefrieght.php';

include dirname(__FILE__) . '/pallet-packaging-for-woocommerce/pallet-packaging-for-woocommerce.php';

// Define reference
function speed_wwe_small_plugin($plugins)
{
    $plugins['spq'] = (isset($plugins['spq'])) ? array_merge($plugins['spq'], ['speedship' => 'WC_speedship']) : ['speedship' => 'WC_speedship'];
    return $plugins;
}

add_filter('en_plugins', 'speed_wwe_small_plugin');

if (!function_exists('en_woo_plans_notification_PD')) {

    function en_woo_plans_notification_PD($product_detail_options)
    {
        $eniture_plugins_id = 'eniture_plugin_';

        for ($e = 1; $e <= 25; $e++) {
            $settings = get_option($eniture_plugins_id . $e);
            if (isset($settings) && (!empty($settings)) && (is_array($settings))) {
                $plugin_detail = current($settings);
                $plugin_name = (isset($plugin_detail['plugin_name'])) ? $plugin_detail['plugin_name'] : "";

                foreach ($plugin_detail as $key => $value) {
                    if ($key != 'plugin_name') {
                        $action = $value === 1 ? 'enable_plugins' : 'disable_plugins';
                        $product_detail_options[$key][$action] = (isset($product_detail_options[$key][$action]) && strlen($product_detail_options[$key][$action]) > 0) ? $product_detail_options[$key][$action] . ", $plugin_name" : "$plugin_name";
                    }
                }
            }
        }

        return $product_detail_options;
    }

    add_filter('en_woo_plans_notification_action', 'en_woo_plans_notification_PD', 10, 1);
}

if (!function_exists('en_woo_plans_notification_message')) {

    function en_woo_plans_notification_message($enable_plugins, $disable_plugins)
    {
        $enable_plugins = (strlen($enable_plugins) > 0) ? "$enable_plugins: <b> Enabled</b>. " : "";
        $disable_plugins = (strlen($disable_plugins) > 0) ? " $disable_plugins: Upgrade to <b>Standard Plan to enable</b>." : "";
        return $enable_plugins . "<br>" . $disable_plugins;
    }

    add_filter('en_woo_plans_notification_message_action', 'en_woo_plans_notification_message', 10, 2);
}

// Nesting Material
if (!function_exists('en_woo_plans_nested_notification_message')) {

    function en_woo_plans_nested_notification_message($enable_plugins, $disable_plugins, $feature)
    {
        $enable_plugins = (strlen($enable_plugins) > 0) ? "$enable_plugins: <b> Enabled</b>. " : "";
        $disable_plugins = (strlen($disable_plugins) > 0 && $feature == 'nested_material') ? " $disable_plugins: Upgrade to <b>Advance Plan to enable</b>." : "";
        return $enable_plugins . "<br>" . $disable_plugins;
    }

    add_filter('en_woo_plans_nested_notification_message_action', 'en_woo_plans_nested_notification_message', 10, 3);
}

if (is_admin()) {
    require_once('warehouse-dropship/wwe-small-wild-delivery.php');
    require_once 'template/wwe-small-products-insurance-option.php';
    require_once('quoteSpeedShipShipment.php');
    require_once('template/products-nested-options.php');

    // Micro Warehouse
    $all_plugins = apply_filters('active_plugins', get_option('active_plugins'));
    if (!stripos(implode($all_plugins), 'micro-warehouse-shipping.php')) {
        require_once 'template/product_detail.php';
        require_once 'template/wwe-small-products-options.php';
    }
}

require_once 'template/csv-export.php';
require_once('standard-package-addon/standard-package-addon.php');
require_once('warehouse-dropship/get-distance-request.php');
require_once 'helper/en_helper_class.php';
require_once('wwe-small-curl-class.php');
require_once 'update-plan.php';
require_once 'fdo/en-fdo.php';
require_once 'fdo/en-sbs.php';
require_once 'carrier_service.php';
require_once 'db/wwesmall_db.php';
require_once 'small_packages_shipping_class.php';

add_action('admin_enqueue_scripts', 'speed_speedship_script');

/**
 * Load Front-end scripts for speedship
 */
function speed_speedship_script()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('speed_speedship_script', plugin_dir_url(__FILE__) . 'js/en-speedship.js', [], '1.0.1');
    wp_localize_script('speed_speedship_script', 'en_speedship_admin_script', array(
        'plugins_url' => plugins_url(),
        'allow_proceed_checkout_eniture' => trim(get_option("allow_proceed_checkout_eniture")),
        'prevent_proceed_checkout_eniture' => trim(get_option("prevent_proceed_checkout_eniture")),
        'wwe_small_order_cutoff_time' => get_option("wwe_small_orderCutoffTime"),
    ));
}

require_once 'speed_group_small_shipment.php';
require_once 'wwe_small_wc_update_change.php';
require_once 'wwe-small-packages-quotes-auto-residential-detection.php';

require_once 'orders/orders.php';

require_once('wwe_small_version_compact.php');

/**
 * Get Host
 * @param type $url
 * @return type
 */
if (!function_exists('getHost')) {

    function getHost($url)
    {
        $parseUrl = parse_url(trim($url));
        if (isset($parseUrl['host'])) {
            $host = $parseUrl['host'];
        } else {
            $path = explode('/', $parseUrl['path']);
            $host = $path[0];
        }
        return trim($host);
    }
}

/**
 * Get Domain Name
 */
if (!function_exists('wwe_small_get_domain')) {

    function wwe_small_get_domain()
    {
        global $wp;
        $wp_request = (isset($wp->request)) ? $wp->request : '';
        $url = home_url($wp_request);
        return getHost($url);
    }
}

/**
 * Admin Scripts
 */
function speed_smpkg_admin_script()
{
    wp_register_style('small_packges_style', plugin_dir_url(__FILE__) . '/css/small_packges_style.css', false, '2.1.8');
    wp_enqueue_style('small_packges_style');

    wp_register_style('wwe_small_wickedpicker_style', plugin_dir_url(__FILE__) . '/css/wickedpicker.min.css', false, '2.0.3');
    wp_enqueue_style('wwe_small_wickedpicker_style');
    wp_register_script('wwe_small_wickedpicker_style', plugin_dir_url(__FILE__) . '/js/wickedpicker.js', false, '2.0.3');
    wp_enqueue_script('wwe_small_wickedpicker_style');
}

add_action('admin_enqueue_scripts', 'speed_smpkg_admin_script');

if (!function_exists('is_plugin_active')) {

    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

add_filter('plugin_action_links', 'speed_smallpkg_add_action_plugin', 10, 5);

/**
 * Add plugin Actions
 * @staticvar $plugin
 * @param $actions
 * @param $plugin_file
 */
function speed_smallpkg_add_action_plugin($actions, $plugin_file)
{

    static $plugin;
    if (!isset($plugin))
        $plugin = plugin_basename(__FILE__);
    if ($plugin == $plugin_file) {
        $settings = array('settings' => '<a href="admin.php?page=wc-settings&tab=wwe_small_packages_quotes">' . __('Settings', 'General') . '</a>');
        $actions = array_merge($settings, $actions);
    }
    return $actions;
}

add_action('admin_init', 'speed_check_woo_version');
/**
 * Check Woo Version
 */
function speed_check_woo_version()
{

    $woo_version = speed_sm_get_woo_version_number();
    $version = '2.6';
    if (!version_compare($woo_version, $version, ">=")) {
        add_action('admin_notices', 'speed_admin_notice_failure');
    }
}

/**
 * Failure Notices
 */
function speed_admin_notice_failure()
{
?>
    <div class="notice notice-error">
        <p><?php
            _e('WWE Small plugin requires WooCommerce version 2.6 or higher to work. Functionality may not work properly.', 'wwe-woo-version-failure');
            ?></p>
    </div>
<?php
}

/**
 * Woo Version
 */
function speed_sm_get_woo_version_number()
{
    if (!function_exists('get_plugins'))
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    $plugin_folder = get_plugins('/' . 'woocommerce');
    $plugin_file = 'woocommerce.php';

    if (isset($plugin_folder[$plugin_file]['Version'])) {

        return $plugin_folder[$plugin_file]['Version'];
    } else {

        return NULL;
    }
}

if (!is_plugin_active('woocommerce/woocommerce.php')) {
    add_action('admin_notices', 'speed_smallpkg_woocommerce_avaibility_error');
} else {
    add_filter('woocommerce_get_settings_pages', 'speed_smallpkg_shipping_sections');
}

/**
 * Sections
 * @param $settings
 */
function speed_smallpkg_shipping_sections($settings)
{

    include('small_packages_tab_class_woocommrece.php');
    return $settings;
}

/**
 * Woo Availability Error
 */
function speed_smallpkg_woocommerce_avaibility_error()
{

    $class = "error";
    $message = "WooCommerce WWE Small Package is enabled but not effective. It requires WooCommerce in order to work , Please <a target='_blank' href='https://wordpress.org/plugins/woocommerce/installation/'>Install</a> WooCommerce Plugin.";
    echo "<div class=\"$class\"> <p>$message</p></div>";
}

add_action('woocommerce_shipping_init', 'speed_smallpkg_shipping_method_init');
add_filter('woocommerce_shipping_methods', 'speed_smallpkg_add_shipping_method');
add_filter('woocommerce_cart_no_shipping_available_html', 'speed_small_default_error_message', 999, 1);
add_action('init', 'speed_small_no_method_available');

add_action('init', 'speed_small_default_error_message_selection');

/**
 * Update Default custom error message selection
 */
function speed_small_default_error_message_selection()
{
    $custom_error_selection = get_option('wc_pervent_proceed_checkout_eniture');
    if (empty($custom_error_selection)) {
        update_option('wc_pervent_proceed_checkout_eniture', 'prevent', true);
        update_option('prevent_proceed_checkout_eniture', 'There are no shipping methods available for the address provided. Please check the address.', true);
    }
}

/**
 * @param $message
 * @return string
 */
if (!function_exists("speed_small_default_error_message")) {

    function speed_small_default_error_message($message)
    {
        if (get_option('wc_pervent_proceed_checkout_eniture') == 'prevent') {
            remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20, 2);
            return __(get_option('prevent_proceed_checkout_eniture'));
        } else if (get_option('wc_pervent_proceed_checkout_eniture') == 'allow') {
            add_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20, 2);
            return __(get_option('allow_proceed_checkout_eniture'));
        }
    }
}
/**
 * Shipping Message On Cart If No Method Available
 */
if (!function_exists("speed_small_no_method_available")) {

    function speed_small_no_method_available()
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
 * Load shipping method
 * @param array $methods
 * @return string
 */
function speed_smallpkg_add_shipping_method($methods)
{

    $methods['speedship'] = 'WC_speedship';
    return $methods;
}

add_filter('woocommerce_package_rates', 'speed_speedship_hide_shipping');

/**
 * Hide Other plugins
 * @param $available_methods
 */
function speed_speedship_hide_shipping($available_methods)
{
    $en_eniture_apps = apply_filters('en_shipping_applications', []);
    $shipment_id = speed_wwe_small_return_shipment_id($available_methods);
    $rmThirdPartyArr = [];
    $rmThirdPartyArr['wc_settings_wwe_small_allow_other_plugins'] = get_option('wc_settings_wwe_small_allow_other_plugins');
    $rmThirdPartyArr['shipment_id'] = $shipment_id;
    $rmThirdParty = apply_filters('decide_rm_third_party_quotes', false, $rmThirdPartyArr, $available_methods);
    if ($rmThirdParty) {
        if (count($available_methods) > 0) {
            $plugins_array = [];
            $speedship_rates = [];
            $eniture_plugins = get_option('EN_Plugins');
            if ($eniture_plugins) {
                $plugins_array = json_decode($eniture_plugins);
            }

            foreach ($available_methods as $index => $method) {
                if (!($method->method_id == 'speedship' || $method->method_id == 'ltl_shipping_method' || in_array($method->method_id, $plugins_array) || in_array($method->method_id, $en_eniture_apps))) {
                    unset($available_methods[$index]);
                }
            }
        }
    }
    return $available_methods;
}

/**
 * Return the shipment method.
 */
function speed_wwe_small_return_shipment_id($available_methods)
{

    foreach ($available_methods as $method) {
        if ($method->method_id == "speedship") {
            return $method->method_id;
        }
    }
    return false;
}

add_filter('woocommerce_cart_shipping_method_full_label', 'speed_smallpkg_remove_free_label', 10, 2);

/**
 * Remove Label
 * @param $full_label
 * @param $method
 */
function speed_smallpkg_remove_free_label($full_label, $method)
{

    $full_label = str_replace("(Free)", "", $full_label);
    return $full_label;
}

add_action('admin_init', 'speed_wwe_small_update', 10, 2);
register_activation_hook(__FILE__, 'speed_create_sm_wh_db');
register_activation_hook(__FILE__, 'speed_en_wwe_small_activate_hit_to_update_plan');
register_activation_hook(__FILE__, 'speed_old_store_wwe_sm_dropship_status');
register_deactivation_hook(__FILE__, 'speed_en_wwe_small_deactivate_hit_to_update_plan');
register_activation_hook(__FILE__, 'speed_wwe_small_get_all_warehouse_dropship');

/**
 * WWE small plugin update now
 * @param array type $upgrader_object
 * @param array type $options
 */
function speed_wwe_small_update_now()
{
    $index = 'speedship-myunishippers-rating/woocommerceShip.php';
    $plugin_info = get_plugins();
    $plugin_version = (isset($plugin_info[$index]['Version'])) ? $plugin_info[$index]['Version'] : '';
    $update_now = get_option('speed_wwe_small_update_now');

    if ($update_now != $plugin_version) {
        if (!function_exists('speed_en_wwe_small_activate_hit_to_update_plan')) {
            require_once(__DIR__ . '/update-plan.php');
        }

        speed_create_sm_wh_db();
        speed_en_wwe_small_activate_hit_to_update_plan();
        speed_old_store_wwe_sm_dropship_status();
        speed_wwe_small_get_all_warehouse_dropship();

        update_option('speed_wwe_small_update_now', $plugin_version);
    }
}

add_action('init', 'speed_wwe_small_update_now');

/* Auto-residential hook */
define("speed_en_woo_plugin_wwe_small_packages_quotes", "wwe_small_packages_quotes");


add_action('wp_enqueue_scripts', 'speed_wwe_small_frontend_checkout_script');

/**
 * Load Frontend scripts for ODFL
 */
function speed_wwe_small_frontend_checkout_script()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('speed_wwe_small_frontend_checkout_script', plugin_dir_url(__FILE__) . 'front/js/en-wwe-small-checkout.js', [], '1.0.0');
    wp_localize_script('speed_wwe_small_frontend_checkout_script', 'frontend_script', array(
        'pluginsUrl' => plugins_url(),
    ));
}

add_filter('speed_wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'speed_wwe_small_packages_quotes_quotes_plans_suscription_and_features', 1);

function speed_wwe_small_packages_quotes_quotes_plans_suscription_and_features($feature)
{
    $package = get_option('wwe_small_packages_quotes_package');

    $features = array(
        'instore_pickup_local_devlivery' => array('3'),
        'transit_days' => array('3'),
        'hazardous_material' => array('2', '3'),
        'insurance_fee' => array('2', '3'),
        'wwe_small_cutOffTime_shipDateOffset' => array('2', '3'),
        'wwe_small_show_delivery_estimates' => array('1', '2', '3'),
        'nested_material' => array('3'),
    );

    // if (get_option('wwe_small_packages_quotes_store_type') == "1") {
    // $features['multi_warehouse'] = array('2', '3');
    // $features['multi_dropship'] = array('', '0', '1', '2', '3');
    // }
    // if (get_option('en_old_user_dropship_status') === "0" && get_option('wwe_small_packages_quotes_store_type') == "0") {
    // $features['multi_dropship'] = array('', '0', '1', '2', '3');
    // }
    // if (get_option('en_old_user_warehouse_status') === "0" && get_option('wwe_small_packages_quotes_store_type') == "0") {
    // $features['multi_warehouse'] = array('2', '3');
    // }
    return true;
    // return (isset($features[$feature]) && (in_array($package, $features[$feature]))) ? TRUE : ((isset($features[$feature])) ? $features[$feature] : '');
}

add_filter('speed_wwe_small_packages_quotes_plans_notification_link', 'speed_wwe_small_packages_quotes_plans_notification_link', 1);

function speed_wwe_small_packages_quotes_plans_notification_link($plans)
{
    $plan = current($plans);
    $plan_to_upgrade = "";
    switch ($plan) {
        case 2:
            $plan_to_upgrade = "<a href='https://eniture.com/woocommerce-worldwide-express-small-package-plugin/' target='_blank'>Standard Plan required</a>";
            break;
        case 3:
            $plan_to_upgrade = "<a href='https://eniture.com/woocommerce-worldwide-express-small-package-plugin/' target='_blank'>Advanced Plan required</a>";
            break;
    }

    return $plan_to_upgrade;
}

/**
 *
 * old customer check dropship / warehouse status on plugin update
 */
function speed_old_store_wwe_sm_dropship_status()
{
    global $wpdb;

    // Check total no. of dropships on plugin updation
    $table_name = $wpdb->prefix . 'warehouse';
    $count_query = "select count(*) from $table_name where location = 'dropship' ";
    $num = $wpdb->get_var($count_query);

    if (get_option('en_old_user_dropship_status') == "0" && get_option('wwe_small_packages_quotes_store_type') == "0") {

        $dropship_status = ($num > 1) ? 1 : 0;

        update_option('en_old_user_dropship_status', "$dropship_status");
    } elseif (get_option('en_old_user_dropship_status') == "" && get_option('wwe_small_packages_quotes_store_type') == "0") {
        $dropship_status = ($num == 1) ? 0 : 1;

        update_option('en_old_user_dropship_status', "$dropship_status");
    }

    // Check total no. of warehouses on plugin updation
    $table_name = $wpdb->prefix . 'warehouse';
    $warehouse_count_query = "select count(*) from $table_name where location = 'warehouse' ";
    $warehouse_num = $wpdb->get_var($warehouse_count_query);

    if (get_option('en_old_user_warehouse_status') == "0" && get_option('wwe_small_packages_quotes_store_type') == "0") {

        $warehouse_status = ($warehouse_num > 1) ? 1 : 0;

        update_option('en_old_user_warehouse_status', "$warehouse_status");
    } elseif (get_option('en_old_user_warehouse_status') == "" && get_option('wwe_small_packages_quotes_store_type') == "0") {
        $warehouse_status = ($warehouse_num == 1) ? 0 : 1;

        update_option('en_old_user_warehouse_status', "$warehouse_status");
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
add_action('admin_init', 'speed_wwe_small_update_warehouse');
