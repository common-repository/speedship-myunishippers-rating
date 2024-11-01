<?php

//  Not allowed to access directly
if (!defined('ABSPATH')) {
    exit;
}


define('SPEED_EN_PPFW_DIR_FILE', plugin_dir_url(__FILE__));

/**
 * Get Host
 * @param type $url
 * @return type
 */
if (!function_exists('en_get_host')) {

    function en_get_host($url)
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
if (!function_exists('en_pallet_get_domain')) {

    function en_pallet_get_domain()
    {
        global $wp;
        $url = home_url($wp->request);
        return en_get_host($url);
    }
}

require_once('adding-pallets/includes/pallets-per-product.php');
require_once('adding-pallets/adding-pallets.php');
require_once 'adding-pallets/template/adding-pallets-template.php';
require_once 'adding-pallets/includes/adding-pallets-ajax.php';
new \SpeedEnPpfwEnpAjax\SpeedEnPpfwEnpAjax();
require_once('adding-pallets/db/adding-pallets-db.php');
new \SpeedEnPpfwPallethouse\SpeedEnPpfwPallethouse();
require_once('pallet-plugin-details.php');
require_once('pallet-packaging.php');
require_once('packaging-tab.php');
require_once('pallet-addons-curl-request.php');
require_once('pallet-addons-ajax-request.php');

/**
 * App install hook
 */
if (!function_exists('en_adding_pallets_installation')) {

    function en_adding_pallets_installation()
    {
        apply_filters('en_register_activation_hook', false);
    }

    register_activation_hook(__FILE__, 'en_adding_pallets_installation');
}

/**
 * Load script
 */
if (!function_exists('en_pallet_script')) {

    function en_pallet_script()
    {
        wp_enqueue_script('en_pallet_script', plugin_dir_url(__FILE__) . '/assets/js/standard-packaging-script.js', array(), '1.0.1');
        wp_localize_script('en_pallet_script', 'script', array(
            'pluginsUrl' => plugins_url(),
        ));

        wp_enqueue_script('en_adding_pallets_script', plugin_dir_url(__FILE__) . '/adding-pallets/assets/js/adding-pallets.js', array(), '1.0.0');
        wp_localize_script('en_adding_pallets_script', 'script', array(
            'pluginsUrl' => plugins_url(),
        ));

        wp_register_style('en_pallet_style', plugin_dir_url(__FILE__) . '/assets/css/standard-packaging-style.css', false, '1.0.2');
        wp_enqueue_style('en_pallet_style');

        wp_register_style('en_adding_pallets_style', plugin_dir_url(__FILE__) . '/adding-pallets/assets/css/adding-pallets.css', false, '1.0.1');
        wp_enqueue_style('en_adding_pallets_style');
    }

    add_action('admin_enqueue_scripts', 'en_pallet_script');
}

/**
 * globally script variable
 */
if (!function_exists('pallet_admin_inline_js')) {

    function pallet_admin_inline_js()
    {
?>
        <script>
            var sp_plugins_url = "";
        </script>
<?php
    }

    add_action('admin_print_scripts', 'pallet_admin_inline_js');
}
