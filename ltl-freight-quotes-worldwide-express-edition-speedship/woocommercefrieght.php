<?php

if (!defined('ABSPATH')) {
    exit;
}

define('SPEED_WWE_FREIGHT_DOMAIN_HITTING_URL', 'https://wwex.com');
define('SPEED_WWE_FREIGHT_FDO_HITTING_URL', 'https://wwex.com');
// Define reference
function speed_en_wwe_freight_plugin($plugins)
{
    $plugins['lfq'] = (isset($plugins['lfq'])) ? array_merge($plugins['lfq'], ['ltl_shipping_method' => 'WC_speedfreight_Shipping_Method']) : ['ltl_shipping_method' => 'WC_speedfreight_Shipping_Method'];
    return $plugins;
}

add_filter('en_plugins', 'speed_en_wwe_freight_plugin');

add_action('admin_enqueue_scripts', 'speed_wwe_ltl_admin_script');

if (!function_exists('is_plugin_active')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

if (!function_exists('en_woo_plans_notification_PD')) {

    function en_woo_plans_notification_PD($product_detail_options)
    {
        $eniture_plugins_id = 'eniture_plugin_';

        for ($en = 1; $en <= 25; $en++) {
            $settings = get_option($eniture_plugins_id . $en);

            if (isset($settings) && (!empty($settings)) && (is_array($settings))) {
                $plugin_detail = current($settings);
                $plugin_name = (isset($plugin_detail['plugin_name'])) ? $plugin_detail['plugin_name'] : "";

                foreach ($plugin_detail as $key => $value) {
                    if ($key != 'plugin_name') {
                        $action = $value === 1 ? 'enable_plugins' : 'disable_plugins';
                        $product_detail_options[$key][$action] = (isset($product_detail_options[$key][$action]) && strlen($product_detail_options[$key][$action]) > 0) ? ", $plugin_name" : "$plugin_name";
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

if (!function_exists('en_woo_plans_nested_notification_message')) {

    function en_woo_plans_nested_notification_message($enable_plugins, $disable_plugins, $feature)
    {
        $enable_plugins = (strlen($enable_plugins) > 0) ? "$enable_plugins: <b> Enabled</b>. " : "";
        $disable_plugins = (strlen($disable_plugins) > 0 && $feature == 'nested_material') ? " $disable_plugins: Upgrade to <b>Advance Plan to enable</b>." : "";
        return $enable_plugins . "<br>" . $disable_plugins;
    }

    add_filter('en_woo_plans_nested_notification_message_action', 'en_woo_plans_nested_notification_message', 10, 3);
}

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
if (!function_exists('wwe_quests_get_domain')) {

    function wwe_quests_get_domain()
    {
        global $wp;
        $url = home_url($wp->request);
        return getHost($url);
    }
}

/**
 * Load Css And Js Scripts
 */
function speed_wwe_ltl_admin_script()
{
    wp_register_style('ltl_style', plugin_dir_url(__FILE__) . '/css/ltl_style.css', [], '1.1.1', 'screen');
    wp_enqueue_style('ltl_style');
}

/**
 * Add Plugin Actions
 */
add_filter('plugin_action_links', 'speed_wwe_ltl_add_action_plugin', 10, 5);

/**
 * Plugin Action
 * @staticvar $plugin
 * @param $actions
 * @param $plugin_file
 * @return array
 */
function speed_wwe_ltl_add_action_plugin($actions, $plugin_file)
{

    static $plugin;
    if (!isset($plugin))
        $plugin = plugin_basename(__FILE__);
    if ($plugin == $plugin_file) {

        $settings = array('settings' => '<a href="admin.php?page=wc-settings&tab=wwe_quests">' . __('Settings', 'General') . '</a>');
        // $site_link = array('support' => '<a href="https://support.eniture.com/home" target="_blank">Support</a>');
        $actions = array_merge($settings, $actions);
        // $actions = array_merge($site_link, $actions);
    }

    return $actions;
}

/**
 * Inlude Plugin Files
 */
require_once 'helper/en_helper_class.php';
require_once('warehouse-dropship/wwe-ltl-wild-delivery.php');
require_once('standard-package-addon/standard-package-addon.php');
require_once('warehouse-dropship/get-distance-request.php');
require_once('template/products-nested-options.php');
require_once('template/wwe-ltl-products-insurance-option.php');
require_once 'template/csv-export.php';

require_once('update-plan.php');

register_activation_hook(__FILE__, 'speed_old_store_wwe_ltl_dropship_status');
register_activation_hook(__FILE__, 'speed_en_wwe_ltl_activate_hit_to_update_plan');
register_deactivation_hook(__FILE__, 'speed_en_wwe_ltl_deactivate_hit_to_update_plan');

require_once 'fdo/en-fdo.php';
require_once(__DIR__ . '/ltl_filter_quotes.php');
require_once 'ltl_version_compact.php';
require_once('quoteSpeedFreightShipment.php');
require_once 'ltl_shipping_class.php';
require_once 'db/woocommrecefreight_db.php';
require_once 'wwe-ltl-liftgate-as-option.php';
require_once 'carrier_service.php';
require_once 'speed_group_ltl_shipments.php';
require_once 'wwe_admin_filter.php';
require_once 'carrier_list.php';
include_once(ABSPATH . 'wp-admin/includes/plugin.php');
require_once 'wc_update_change.php';
require_once 'ltl-curl-class.php';

require_once 'order/rates/order-rates.php';
require_once 'order/en-order-widget.php';

// Micro Warehouse
$all_plugins = apply_filters('active_plugins', get_option('active_plugins'));
if (!stripos(implode($all_plugins), 'micro-warehouse-shipping.php')) {
    require_once 'template/product_detail.php';
    require_once('template/wwe-ltl-products-options.php');
}


/**
 * WWE LTL Action And Filters
 */
if (!is_plugin_active('woocommerce/woocommerce.php')) {

    add_action('admin_notices', 'speed_ltl_freight_woocommrec_avaibility_error');
} else {
    add_filter('woocommerce_get_settings_pages', 'speed_ltl_shipping_sections');
}

add_action('admin_init', 'speed_ltl_check_woo_version');
add_action('woocommerce_shipping_init', 'speed_ltl_shipping_method_init');
add_filter('woocommerce_shipping_methods', 'speed_ltl_add_LTL_shipping_method');
add_filter('woocommerce_package_rates', 'speed_ltl_hide_shipping_based_on_class');
add_action('init', 'speed_ltl_save_carrier_status');
add_filter('woocommerce_cart_shipping_method_full_label', 'speed_ltl_remove_free_label', 10, 2);
add_filter('woocommerce_cart_no_shipping_available_html', 'wwe_ltl_default_error_message', 999, 1);
add_action('init', 'wwe_no_method_available');
add_action('init', 'speed_wwe_ltl_default_error_message_selection');

/**
 * Update Default custom error message selection
 */
function speed_wwe_ltl_default_error_message_selection()
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
if (!function_exists("wwe_ltl_default_error_message")) {

    function wwe_ltl_default_error_message($message)
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
 * WWE LTL Activation Hook
 */
register_activation_hook(__FILE__, 'speed_ltl_freihgt_installation_carrier');
register_activation_hook(__FILE__, 'speed_wwe_ltl_get_all_warehouse_dropship');
register_activation_hook(__FILE__, 'speed_create_ltl_wh_db');

/**
 * WWE LTL plugin update now
 */
function speed_en_wwe_ltl_update_now()
{
    $address = [];
    $index = 'ltl-freight-quotes-worldwide-express-edition-speedship/woocommercefrieght.php';
    $plugin_info = get_plugins();
    $plugin_version = (isset($plugin_info[$index]['Version'])) ? $plugin_info[$index]['Version'] : '';
    $update_now = get_option('speed_en_wwe_ltl_update_now');

    if ($update_now != $plugin_version) {
        if (!function_exists('speed_en_wwe_ltl_activate_hit_to_update_plan')) {
            require_once(__DIR__ . '/update-plan.php');
        }

        speed_en_wwe_ltl_activate_hit_to_update_plan();
        speed_old_store_wwe_ltl_dropship_status();
        speed_create_ltl_wh_db();
        speed_ltl_freihgt_installation_carrier();
        speed_wwe_ltl_get_all_warehouse_dropship($address);

        update_option('speed_en_wwe_ltl_update_now', $plugin_version);
    }
}

add_action('init', 'speed_en_wwe_ltl_update_now');


$arr = [];
apply_filters('product_detail_freight_class', $arr);

define("speed_en_woo_plugin_wwe_quests", "wwe_quests");

add_action('admin_enqueue_scripts', 'speed_en_speedfreight_script');

/**
 * Load Front-end scripts for speedfreight
 */
function speed_en_speedfreight_script()
{
    // Cuttoff Time
    wp_register_style('wwe_lfq_wickedpicker_style', plugin_dir_url(__FILE__) . 'css/wickedpicker.min.css', false, '1.0.0');
    wp_register_script('wwe_lfq_wickedpicker_script', plugin_dir_url(__FILE__) . 'js/wickedpicker.js', false, '1.0.0');
    wp_enqueue_style('wwe_lfq_wickedpicker_style');
    wp_enqueue_script('wwe_lfq_wickedpicker_script');

    wp_enqueue_script('jquery');
    wp_enqueue_script('speed_en_speedfreight_script', plugin_dir_url(__FILE__) . 'js/speedfreight.js', [], '1.0.4');
    wp_localize_script('speed_en_speedfreight_script', 'en_speedfreight_admin_script', array(
        'plugins_url' => plugins_url(),
        'allow_proceed_checkout_eniture' => trim(get_option("allow_proceed_checkout_eniture")),
        'prevent_proceed_checkout_eniture' => trim(get_option("prevent_proceed_checkout_eniture")),
        // Cuttoff Time
        'wwe_lfq_freight_order_cutoff_time' => get_option("wwe_lfq_freight_order_cut_off_time"),
    ));
}

add_action('wp_enqueue_scripts', 'speed_en_wwe_ltl_frontend_checkout_script');

/**
 * Load Frontend scripts for WWE
 */
function speed_en_wwe_ltl_frontend_checkout_script()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('speed_en_wwe_ltl_frontend_checkout_script', plugin_dir_url(__FILE__) . 'front/js/en-wwe-checkout.js', [], '1.0.0');
    wp_localize_script('speed_en_wwe_ltl_frontend_checkout_script', 'frontend_script', array(
        'pluginsUrl' => plugins_url(),
    ));
}

add_filter('speed_wwe_quests_quotes_plans_suscription_and_features', 'speed_wwe_quests_quotes_plans_suscription_and_features', 1);

function speed_wwe_quests_quotes_plans_suscription_and_features($feature)
{

    $package = get_option('wwe_ltl_packages_quotes_package');
    $features = array(
        'instore_pickup_local_devlivery' => array('3'),
        'hazardous_material' => array('2', '3'),
        'hold_at_terminal' => array('3'),
        'nested_material' => array('3'),
        'insurance_fee' => array('2', '3'),
        // Cuttoff Time
        'wwe_lfq_cutt_off_time' => ['2', '3'],
        'wwe_lfq_show_delivery_estimates' => ['1', '2', '3']
    );

    if (get_option('wwe_quests_store_type') == "1") {
        $features['multi_warehouse'] = array('2', '3');
        $features['multi_dropship'] = array('', '0', '1', '2', '3');
        $features['nested_material'] = array('3');
    }

    if (get_option('en_old_user_dropship_status') == "0" && get_option('wwe_quests_store_type') == "0") {
        $features['multi_dropship'] = array('', '0', '1', '2', '3');
    }
    if (get_option('en_old_user_warehouse_status') === "0" && get_option('wwe_quests_store_type') == "0") {
        $features['multi_warehouse'] = array('2', '3');
    }
    return true;
    // return (isset($features[$feature]) && (in_array($package, $features[$feature]))) ? TRUE : ((isset($features[$feature])) ? $features[$feature] : '');
}

add_filter('speed_wwe_quests_plans_notification_link', 'speed_wwe_quests_plans_notification_link', 1);

function speed_wwe_quests_plans_notification_link($plans)
{
    $plan = current($plans);
    $plan_to_upgrade = "";
    switch ($plan) {
        case 2:
            $plan_to_upgrade = "<a target='_blank' class='plan_color' href='https://eniture.com/woocommerce-worldwide-express-ltl-freight/'>Standard Plan required.</a>";
            break;
        case 3:
            $plan_to_upgrade = "<a target='_blank' href='https://eniture.com/woocommerce-worldwide-express-ltl-freight/'>Advanced Plan required.</a>";
            break;
    }

    return $plan_to_upgrade;
}

/**
 *
 * Old customer check dropship / warehouse status on plugin update
 */
function speed_old_store_wwe_ltl_dropship_status()
{
    global $wpdb;

    //      Check total no. of dropships on plugin updation
    $table_name = $wpdb->prefix . 'warehouse';
    $count_query = "select count(*) from $table_name where location = 'dropship' ";
    $num = $wpdb->get_var($count_query);

    if (get_option('en_old_user_dropship_status') == "0" && get_option('wwe_quests_store_type') == "0") {

        $dropship_status = ($num > 1) ? 1 : 0;

        update_option('en_old_user_dropship_status', "$dropship_status");
    } elseif (get_option('en_old_user_dropship_status') == "" && get_option('wwe_quests_store_type') == "0") {
        $dropship_status = ($num == 1) ? 0 : 1;

        update_option('en_old_user_dropship_status', "$dropship_status");
    }

    //      Check total no. of warehouses on plugin updation
    $table_name = $wpdb->prefix . 'warehouse';
    $warehouse_count_query = "select count(*) from $table_name where location = 'warehouse' ";
    $warehouse_num = $wpdb->get_var($warehouse_count_query);

    if (get_option('en_old_user_warehouse_status') == "0" && get_option('wwe_quests_store_type') == "0") {

        $warehouse_status = ($warehouse_num > 1) ? 1 : 0;

        update_option('en_old_user_warehouse_status', "$warehouse_status");
    } elseif (get_option('en_old_user_warehouse_status') == "" && get_option('wwe_quests_store_type') == "0") {
        $warehouse_status = ($warehouse_num == 1) ? 0 : 1;

        update_option('en_old_user_warehouse_status', "$warehouse_status");
    }
}

if (!function_exists('get_all_warehouse_dropship')) {

    function get_all_warehouse_dropship()
    {

        global $wpdb;
        $table_name = $wpdb->prefix . 'warehouse';
        $sql = "select count(*) from $table_name";
        $warehouse_record = $wpdb->get_results($sql);
        return $warehouse_record;
    }
}

add_filter('en_warehouse_dropship', 'get_all_warehouse_dropship', 1);
add_action('admin_init', 'speed_wwe_ltl_update_warehouse');
