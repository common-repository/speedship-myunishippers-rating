<?php

/**
 * WWE LTL Test Connection
 *
 * @package     WWE LTL Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_nopriv_ltl_validate_keys', 'speed_ltl_speedfreight_submit');
add_action('wp_ajax_ltl_validate_keys', 'speed_ltl_speedfreight_submit');
/**
 * Test Connection Function
 */
function speed_ltl_speedfreight_submit()
{

    $speed_sp_user = sanitize_text_field($_POST['speed_freight_username']);
    $speed_sp_pass = sanitize_text_field($_POST['speed_freight_password']);
    $speed_sp_au_key = sanitize_text_field($_POST['authentication_key']);
    $speed_sp_acc = sanitize_text_field($_POST['world_wide_express_account_number']);
    $speed_sp_licence_key = sanitize_text_field($_POST['speed_freight_licence_key']);

    $speedship_url = (isset($_POST['speedship_url'])) ? sanitize_text_field($_POST['speedship_url']) : '';
    $oauth_url = (isset($_POST['oauth_url'])) ? sanitize_text_field($_POST['oauth_url']) : '';
    $oauth_clientid = (isset($_POST['oauth_clientid'])) ? sanitize_text_field($_POST['oauth_clientid']) : '';
    $oauth_client_secret = (isset($_POST['oauth_client_secret'])) ? sanitize_text_field($_POST['oauth_client_secret']) : '';
    $oauth_audience = (isset($_POST['oauth_audience'])) ? sanitize_text_field($_POST['oauth_audience']) : '';
    // $oauth_username = (isset($_POST['oauth_username'])) ? sanitize_text_field($_POST['oauth_username']) : '';
    // $oauth_password = (isset($_POST['oauth_password'])) ? sanitize_text_field($_POST['oauth_password']) : '';


    $domain = wwe_quests_get_domain();

    $post = array(
        'speed_freight_username' => $speed_sp_user,
        'speed_freight_password' => $speed_sp_pass,
        'authentication_key' => $speed_sp_au_key,
        'world_wide_express_account_number' => $speed_sp_acc,
        'plugin_licence_key' => $speed_sp_licence_key,
        'plugin_domain_name' => speed_ltl_speedfreight_parse_url($domain),

        'speedship_url' => $speedship_url,
        'oauth_url' => $oauth_url,
        'oauth_clientid' => $oauth_clientid,
        'oauth_client_secret' => $oauth_client_secret,
        'oauth_audience' => $oauth_audience,
        // 'oauth_username' => $oauth_username,
        // 'oauth_password' => $oauth_password,
    );

    if (is_array($post) && count($post) > 0) {

        $ltl_curl_obj = new Speed_WWE_LTL_Curl_Request();
        $output = $ltl_curl_obj->wwe_ltl_get_curl_response(SPEED_WWE_FREIGHT_DOMAIN_HITTING_URL . '/carriers/wwe-freight/speedfreightTest.php', $post);
    }
    print_r($output);
    die;
}

/**
 * URL parsing
 * @param $domain
 * @return url
 */
function speed_ltl_speedfreight_parse_url($domain)
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
