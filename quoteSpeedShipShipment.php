<?php

/**
 * WWE Small Test Connection
 *
 * @package     WWE Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_nopriv_speedship_action', 'speed_speedship_submit');
add_action('wp_ajax_speedship_action', 'speed_speedship_submit');
add_action('wp_ajax_speedship_action1', 'speed_speedship_submit1');

/**
 * WWE Small Test Connection AJAX Request
 */
function speed_speedship_submit1()
{

    $sp_user = (isset($_POST['speed_freight_username'])) ? sanitize_text_field($_POST['speed_freight_username']) : '';
    $sp_pass = (isset($_POST['speed_freight_password'])) ? sanitize_text_field($_POST['speed_freight_password']) : '';
    $sp_au_key = (isset($_POST['authentication_key'])) ? sanitize_text_field($_POST['authentication_key']) : '';
    $sp_acc = (isset($_POST['world_wide_express_account_number'])) ? sanitize_text_field($_POST['world_wide_express_account_number']) : '';
    $sp_licence_key = (isset($_POST['speed_freight_licence_key'])) ? sanitize_text_field($_POST['speed_freight_licence_key']) : '';
    $domain = $_SERVER['SERVER_NAME'];
    $domain = wwe_small_get_domain();

    $speedship_url = (isset($_POST['speedship_url'])) ? sanitize_text_field($_POST['speedship_url']) : '';
    $oauth_url = (isset($_POST['oauth_url'])) ? sanitize_text_field($_POST['oauth_url']) : '';
    $oauth_clientid = (isset($_POST['oauth_clientid'])) ? sanitize_text_field($_POST['oauth_clientid']) : '';
    $oauth_client_secret = (isset($_POST['oauth_client_secret'])) ? sanitize_text_field($_POST['oauth_client_secret']) : '';
    $oauth_audience = (isset($_POST['oauth_audience'])) ? sanitize_text_field($_POST['oauth_audience']) : '';
    // $oauth_username = (isset($_POST['oauth_username'])) ? sanitize_text_field($_POST['oauth_username']) : '';
    // $oauth_password = (isset($_POST['oauth_password'])) ? sanitize_text_field($_POST['oauth_password']) : '';

    $postData = array(
        'speed_freight_username' => $sp_user,
        'speed_freight_password' => $sp_pass,
        'authentication_key' => $sp_au_key,
        'world_wide_express_account_number' => $sp_acc,
        'plugin_licence_key' => $sp_licence_key,
        'plugin_domain_name' => speed_eniture_parse_url($domain),
        'platform' => 'wordpress',

        'speedship_url' => $speedship_url,
        'oauth_url' => $oauth_url,
        'oauth_clientid' => $oauth_clientid,
        'oauth_client_secret' => $oauth_client_secret,
        'oauth_audience' => $oauth_audience,
        // 'oauth_username' => $oauth_username,
        // 'oauth_password' => $oauth_password,


    );

    $url = 'https://www.convoy-connect.com/shopify/testConnection.php?shop=&ltl=1&debug=1';
    $field_string = http_build_query($postData);
    $response = wp_remote_post(
        $url,
        array(
            'method' => 'POST',
            'timeout' => 60,
            'redirection' => 5,
            'blocking' => true,
            'body' => $field_string,
        )
    );

    $output = wp_remote_retrieve_body($response);
    $response = json_decode($output);
    if (isset($response->error_desc) && substr($response->error_desc, 0, 5) == "<?xml") {
        $xmlparser = xml_parser_create();
        xml_parse_into_struct($xmlparser, $response->error_desc, $values);
        xml_parser_free($xmlparser);
        (isset($values[6]['tag']) && $values[6]['tag'] == 'ERRORDESCRIPTION') ? $error = $values[6]['value'] : '';
        $responseBack['error'] = 0;
        $responseBack['error_desc'] = $error;
        print_r(json_encode((object)$responseBack));
        exit;
    } elseif (isset($response->error_desc) && $response->error_desc != "") {
        print_r($output);
    } else {
        print_r($output);
    }

    exit();
}

function speed_speedship_submit()
{

    $sp_user = (isset($_POST['speed_freight_username'])) ? sanitize_text_field($_POST['speed_freight_username']) : '';
    $sp_pass = (isset($_POST['speed_freight_password'])) ? sanitize_text_field($_POST['speed_freight_password']) : '';
    $sp_au_key = (isset($_POST['authentication_key'])) ? sanitize_text_field($_POST['authentication_key']) : '';
    $sp_acc = (isset($_POST['world_wide_express_account_number'])) ? sanitize_text_field($_POST['world_wide_express_account_number']) : '';
    $sp_licence_key = (isset($_POST['speed_freight_licence_key'])) ? sanitize_text_field($_POST['speed_freight_licence_key']) : '';
    $domain = $_SERVER['SERVER_NAME'];
    $domain = wwe_small_get_domain();

    $postData = array(
        'speed_freight_username' => $sp_user,
        'speed_freight_password' => $sp_pass,
        'authentication_key' => $sp_au_key,
        'world_wide_express_account_number' => $sp_acc,
        'plugin_licence_key' => $sp_licence_key,
        'plugin_domain_name' => speed_eniture_parse_url($domain),
        'platform' => 'wordpress',
    );
    $url = SPEED_WWE_DOMAIN_HITTING_URL . '/carriers/wwe-small/speedshipTest.php';
    $field_string = http_build_query($postData);
    $response = wp_remote_post(
        $url,
        array(
            'method' => 'POST',
            'timeout' => 60,
            'redirection' => 5,
            'blocking' => true,
            'body' => $field_string,
        )
    );

    $output = wp_remote_retrieve_body($response);
    $response = json_decode($output);
    if (isset($response->error_desc) && substr($response->error_desc, 0, 5) == "<?xml") {
        $xmlparser = xml_parser_create();
        xml_parse_into_struct($xmlparser, $response->error_desc, $values);
        xml_parser_free($xmlparser);
        (isset($values[6]['tag']) && $values[6]['tag'] == 'ERRORDESCRIPTION') ? $error = $values[6]['value'] : '';
        $responseBack['error'] = 0;
        $responseBack['error_desc'] = $error;
        print_r(json_encode((object)$responseBack));
        exit;
    } elseif (isset($response->error_desc) && $response->error_desc != "") {
        print_r($output);
    } else {
        print_r($output);
    }

    exit();
}

function speed_eniture_parse_url($domain)
{

    $domain = trim($domain);
    $parsed = parse_url($domain);
    if (empty($parsed['scheme'])) {
        $domain = 'http://' . ltrim($domain, '/');
    }
    $parse = parse_url($domain);
    $refinded_domain_name = $parse['host'];
    $domain_array = explode('.', $refinded_domain_name);
    if (in_array('www', $domain_array)) {
        $key = array_search('www', $domain_array);
        unset($domain_array[$key]);
        $refinded_domain_name = implode($domain_array, '.');
    }
    return $refinded_domain_name;
}
