<?php

/**
 * WWE LTL Database
 *
 * @package     WWE LTL Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
if ($wpdb->query("SHOW TABLES LIKE 'wp_freights'") === 0) {
    $sql = "CREATE TABLE `wp_freights` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `speed_freight_shipmentQuoteId` varchar(600) NOT NULL,
    `speed_freight_carrierSCAC` varchar(600) NOT NULL,
    `speed_freight_carrierName` varchar(600) NOT NULL,
    `speed_freight_transitDays` varchar(600) NOT NULL,
    `speed_freight_guaranteedService` varchar(600) NOT NULL,
    `speed_freight_highCostDeliveryShipment` varchar(600) NOT NULL,
    `speed_freight_interline` varchar(600) NOT NULL,
    `speed_freight_nmfcRequired` varchar(600) NOT NULL,
    `speed_freight_carrierNotifications` varchar(600) NOT NULL,
    `carrier_logo` varchar(255) NOT NULL,
    `carrier_status` varchar(8) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

    dbDelta($sql);
}

/**
 * Create Warehouse Table
 * @global $wpdb
 */
function speed_create_ltl_wh_db()
{
    global $wpdb;
    $warehouse_table = $wpdb->prefix . "warehouse";
    if ($wpdb->query("SHOW TABLES LIKE '" . $warehouse_table . "'") === 0) {
        $origin = 'CREATE TABLE ' . $warehouse_table . '(
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    city varchar(200) NOT NULL,
                    state varchar(200) NOT NULL,
                    address varchar(255) NOT NULL,
                    phone_instore varchar(255) NOT NULL,
                    zip varchar(200) NOT NULL,
                    country varchar(200) NOT NULL,
                    location varchar(200) NOT NULL,
                    nickname varchar(200) NOT NULL,
                    enable_store_pickup VARCHAR(255) NOT NULL,
                    miles_store_pickup VARCHAR(255) NOT NULL ,
                    match_postal_store_pickup VARCHAR(255) NOT NULL ,
                    checkout_desc_store_pickup VARCHAR(255) NOT NULL ,
                    enable_local_delivery VARCHAR(255) NOT NULL ,
                    miles_local_delivery VARCHAR(255) NOT NULL ,
                    match_postal_local_delivery VARCHAR(255) NOT NULL ,
                    checkout_desc_local_delivery VARCHAR(255) NOT NULL ,
                    fee_local_delivery VARCHAR(255) NOT NULL ,
                    suppress_local_delivery VARCHAR(255) NOT NULL,
                    wwe_correct_city VARCHAR(100) NOT NULL,
                    PRIMARY KEY  (id) )';
        dbDelta($origin);
    }

    $enable_store_pickup_col = $wpdb->get_row("SHOW COLUMNS FROM " . $warehouse_table . " LIKE 'enable_store_pickup'");
    if (!(isset($enable_store_pickup_col->Field) && $enable_store_pickup_col->Field == 'enable_store_pickup')) {
        $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN enable_store_pickup VARCHAR(255) NOT NULL , "
            . "ADD COLUMN miles_store_pickup VARCHAR(255) NOT NULL , "
            . "ADD COLUMN match_postal_store_pickup VARCHAR(255) NOT NULL , "
            . "ADD COLUMN checkout_desc_store_pickup VARCHAR(255) NOT NULL , "
            . "ADD COLUMN enable_local_delivery VARCHAR(255) NOT NULL , "
            . "ADD COLUMN miles_local_delivery VARCHAR(255) NOT NULL , "
            . "ADD COLUMN match_postal_local_delivery VARCHAR(255) NOT NULL , "
            . "ADD COLUMN checkout_desc_local_delivery VARCHAR(255) NOT NULL , "
            . "ADD COLUMN fee_local_delivery VARCHAR(255) NOT NULL , "
            . "ADD COLUMN suppress_local_delivery VARCHAR(255) NOT NULL", $warehouse_table));
    }

    $wwe_correct_city = $wpdb->get_row("SHOW COLUMNS FROM " . $warehouse_table . " LIKE 'wwe_correct_city'");
    if (!(isset($wwe_correct_city->Field) && $wwe_correct_city->Field == 'wwe_correct_city')) {
        $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN wwe_correct_city VARCHAR(100) NOT NULL", $warehouse_table));
    }

    // Origin terminal address
    speed_wwe_ltl_update_warehouse();
}

/**
 * Update warehouse
 */
function speed_wwe_ltl_update_warehouse()
{
    // Origin terminal address
    // Terminal phone number
    global $wpdb;
    $warehouse_table = $wpdb->prefix . "warehouse";
    $warehouse_address = $wpdb->get_row("SHOW COLUMNS FROM " . $warehouse_table . " LIKE 'phone_instore'");
    if (!(isset($warehouse_address->Field) && $warehouse_address->Field == 'phone_instore')) {
        $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN address VARCHAR(255) NOT NULL", $warehouse_table));
        $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN phone_instore VARCHAR(255) NOT NULL", $warehouse_table));
    }
}

/**
 * Install Carriers On Activation
 */
function speed_ltl_freihgt_installation_carrier()
{
    $carriers_obj = new speed_wwe_ltl_carriers();
    $create_class_obj = new speed_wwe_ltl_carriers();
    $carriers_obj->carriers();
    if (!function_exists('create_ltl_class')) {
        $create_class_obj->create_ltl_class();
    }
}

/**
 *
 * Update warehouse/dropship correct city for WWE
 *
 */
function speed_wwe_ltl_get_all_warehouse_dropship($address = NULL)
{

    global $wpdb;
    $addresses = $wpdb->get_results("SELECT id, city as senderCity, state as senderState, zip as senderZip, country as senderCountryCode FROM " . $wpdb->prefix . "warehouse", ARRAY_A);
    $addresses = (isset($address) && !empty($address)) ? $address : $addresses;

    $update_status = isset($address['update_status']) ? $address['update_status'] : '';
    if (isset($addresses) && !empty($addresses)) {

        $domain = wwe_quests_get_domain();
        $api_credentials = array(
            'username' => get_option('wc_settings_wwe_speed_freight_username'),
            'password' => get_option('wc_settings_wwe_speed_freight_password'),
            'account_number' => get_option('wc_settings_wwe_world_wide_express_account_number'),
            'authentication_key' => get_option('wc_settings_wwe_authentication_key'),
        );

        $postData = array(
            'acessLevel' => 'wweOriginValidate',
            'carrier' => 'LTL',
            'address' => $addresses,
            'api' => $api_credentials,
            'eniureLicenceKey' => get_option('wc_settings_wwe_licence_key'),
            'ServerName' => $domain,
        );

        $field_string = http_build_query($postData);
        $response = wp_remote_post(
            SPEED_WWE_FREIGHT_DOMAIN_HITTING_URL . '/addon/google-location.php',
            array(
                'method' => 'POST',
                'timeout' => 60,
                'redirection' => 5,
                'blocking' => true,
                'body' => $field_string,
            )
        );

        $output = wp_remote_retrieve_body($response);

        if (isset($output) && !empty($output)) {

            $response = json_decode($output);
            $error_status = (isset($response->error) && !empty($response->error)) ? $response->error : '';
            if (empty($error_status) && is_array($response)) {

                foreach ($response as $id => $address) {
                    //                  if warehouse / dropship is updated then unset wwe corrected city 
                    if (isset($update_status) && !empty($update_status) && $update_status == 1) {
                        $data = array('wwe_correct_city' => '');
                        $wpdb->update($wpdb->prefix . 'warehouse', $data, array('id' => $id));
                    }

                    if ($address->severity == 'ERROR' && isset($address->validCity)) {
                        $data = array('wwe_correct_city' => $address->validCity);
                        $wpdb->update($wpdb->prefix . 'warehouse', $data, array('id' => $id));
                    }
                }
            }
        }
    }
}
