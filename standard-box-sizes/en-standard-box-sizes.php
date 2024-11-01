<?php


if (!defined('ABSPATH')) {
    exit; /* Not allowed to access directly */
}

define('SPEED_SBS_MAIN_FILE', __FILE__);

if (!function_exists('is_plugin_active')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

/**
 * Check woocommerce installlation
 */
if (!function_exists('standard_box_sizes_avaibility_error')) {

    function standard_box_sizes_avaibility_error()
    {

        $class = "error";
        $message = "It requires WooCommerce and Eniture plugins in order to work , Please <a target='_blank' href='https://wordpress.org/plugins/woocommerce/installation/'>Install (Woocommerce)</a> <a target='_blank' href='https://eniture.com/products/'>Install (Eniture Plugins)</a> WooCommerce Plugin.";
        echo "<div class=\"$class\"> <p>$message</p></div>";
    }
}

/**
 * Get Host
 * @param type $url
 * @return type
 */
if (!function_exists('en_sbs_get_host')) {

    function en_sbs_get_host($url)
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
if (!function_exists('en_sbs_get_domain')) {

    function en_sbs_get_domain()
    {
        global $wp;
        $wp_request = (isset($wp->request)) ? $wp->request : '';
        $url = home_url($wp_request);
        return en_sbs_get_host($url);
    }
}


/**
 * Define constants
 */
define('speed_standard_box_sizes_addon_plugin_url', __DIR__);

/**
 * Main include engine class
 */
include_once('helper/en-box-sizing-helper-functions.php');
include_once('includes/en-woo-box-plugin-details.php');
include_once('includes/en-standard-box-sizes-includes.php');
include_once('includes/en-woo-box-addons-curl-request.php');
include_once('admin/templates/en-woo-addon-box-sizing-template.php');
include_once('admin/products/en-addon-products-options.php');
include_once('admin/box-sizes/en-box-sizing-class.php');
include_once('includes/en-woo-box-addons-genrt-request-key.php');
include_once('admin/request-handler/en-box-sizing-request-handler.php');
include_once('admin/order/en-admin-order-class.php');
include_once('admin/order/en-front-order-class.php');
include_once('admin/box-sizes-tab/en-woo-addon-box-sizing-template.php');
include_once('includes/en-woo-box-addons-ajax-request.php');


// One Rate
$en_tab = (isset($_REQUEST['tab'])) ? sanitize_text_field($_REQUEST['tab']) : '';
if ($en_tab == "fedex_small" || $en_tab == 'wwe_small_packages_quotes' || $en_tab == 'ups_small' || $en_tab == 'unishepper_small' || $en_tab == 'trinet') {
    include_once('multi-packaging/multi-packaging.php');
}

if ($en_tab == "fedex_small") {
    include_once('one-rate/one-rate.php');
    include_once('includes/en-woo-box-addons-fedex-one-rate.php');
}

include_once('multi-packaging/multi-packaging-request.php');

/**
 * Load scripts for standard_box_sizes
 */
if (!function_exists('standard_box_sizes_admin_script')) {

    function standard_box_sizes_admin_script()
    {

        wp_enqueue_script('standard_box_sizes_script', plugin_dir_url(__FILE__) . '/admin/assets/js/box-sizing-script.js', array(), '1.1.4');
        wp_localize_script('standard_box_sizes_script', 'sbs', array(
            'en_sbs_plugin_path' => plugin_dir_url(__FILE__),
        ));

        wp_register_style('bootstrap_iso_style', plugin_dir_url(__FILE__) . '/admin/assets/css/bootstrap-iso.css', false, '1.1.1');
        wp_enqueue_style('bootstrap_iso_style');

        wp_register_style('standard_box_sizes_style', plugin_dir_url(__FILE__) . '/admin/assets/css/box-sizing-style.css', false, '1.1.0');
        wp_enqueue_style('standard_box_sizes_style');
    }

    add_action('admin_enqueue_scripts', 'standard_box_sizes_admin_script');
}

/* Activation hook */
register_activation_hook(__FILE__, 'en_3dbin_activation_hook');

/**
 * Activation plugin.
 */
if (!function_exists('en_3dbin_activation_hook')) {

    function en_3dbin_activation_hook()
    {

        en_3dbin_register_post_post_type();
    }
}

/**
 * Register the post type threeDbin.
 */
if (!function_exists('en_3dbin_register_post_post_type')) {

    function en_3dbin_register_post_post_type()
    {

        register_post_type(
            'threedbin',
            array(
                'public' => false,
                'has_archive' => false,
                'rewrite' => array('slug' => 'threedbin'),
            )
        );
    }

    add_action('init', 'en_3dbin_register_post_post_type', 0);
}


/**
 * Will run after Webhook by our eniture to check the
 */
if (!function_exists('en_web_hook_to_update_bin_status')) {

    function en_web_hook_to_update_bin_status()
    {
        $action = isset($_POST['action']) ? sanitize_text_field($_POST['action']) : '';

        if ($action == '') {
            $bin_message = get_option('en_3dbin_message');
            $bin_message_status = get_option('en_3dbin_message_status');
            if (is_admin()) {
                /* if there is any message */
                if (!empty($bin_message) && $bin_message != '') {
                    /* Error case */
                    $fullstop = (substr($bin_message, -1) == '.') ? '' : '.';
                    if ($bin_message_status == 'ERROR') {
                        /* update these notifications after checking flags */
                        echo '<div id="message" class="notice-dismiss-bin notice-error notice is-dismissible "><p><strong>Standard Box Sizes : </strong>' . $bin_message . $fullstop . ' Renewal attempt because of current subscription expired OR hits consumed.</p><span id="bin-del">Delete</span></div>';
                    }
                    /* Success case */
                    if ($bin_message_status == 'SUCCESS') {
                        echo '<div id="message" class="notice-dismiss-bin notice-success notice is-dismissible "><p><strong>Standard Box Sizes : </strong>' . $bin_message . $fullstop . ' Renewal attempt because of current subscription expired OR hits consumed.</p><span id="bin-del">Delete</span></div>';
                    }
                }
            }
        }
    }

    add_action('admin_bar_menu', 'en_web_hook_to_update_bin_status');
}

add_action('wp_ajax_nopriv_en_woo_addons_hide_bin_message', 'en_woo_addons_hide_bin_message_func');
add_action('wp_ajax_en_woo_addons_hide_bin_message', 'en_woo_addons_hide_bin_message_func');


add_action('wp_ajax_nopriv_en_woo_addons_usps_query_function', 'en_woo_addons_hide_bin_message_func');
add_action('wp_ajax_en_woo_addons_usps_query_function', 'en_woo_get_usps_fields_value_query');

/**
 * Ajax request to delete the bin notification.
 */
if (!function_exists('en_woo_addons_hide_bin_message_func')) {

    function en_woo_addons_hide_bin_message_func()
    {
        delete_option('en_3dbin_message');
        delete_option('en_3dbin_message_status');
        echo 'true';
        exit;
    }
}

/**
 * Ajax request to delete the bin notification.
 */
if (!function_exists('en_woo_get_usps_fields_value_query')) {

    function en_woo_get_usps_fields_value_query()
    {
        if (isset($_POST['postId']) && !empty($_POST['postId'])) {
            $postId = (isset($_POST["postId"])) ? sanitize_text_field($_POST["postId"]) : 0;
            $box_sizing = (isset($_POST["en_selected_post_type"])) ? sanitize_text_field($_POST["en_selected_post_type"]) : FALSE;
            $post_meta = get_post_meta($postId, $box_sizing, true);
            $meta_array['box_fee'] = isset($post_meta['en_box_usps_box_fee']) && !empty($post_meta['en_box_usps_box_fee']) ? $post_meta['en_box_usps_box_fee'] : '';
            $meta_array['box_type'] = isset($post_meta['en_box_usps_box_type']) && !empty($post_meta['en_box_usps_box_type']) ? $post_meta['en_box_usps_box_type'] : '';
            print_r(json_encode($meta_array));
        }
        exit;
    }
}

define('SPEED_EN_LOAD_SBS_FEDEX_IMAGES', plugin_dir_url(__FILE__) . 'admin/assets/images/boxes/');
