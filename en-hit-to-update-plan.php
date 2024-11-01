<?php
$get_option = get_option('wwe_small_packages_quotes_web_hook_plan_requests');
$en_web_hook_requests = (isset($get_option) && (!empty($get_option))) ? json_decode($get_option, TRUE) : [];
$en_web_hook_requests[] = (isset($_GET)) ? $_GET : [];
update_option('wwe_small_packages_quotes_web_hook_plan_requests', json_encode($en_web_hook_requests));

$plan = isset($_GET['pakg_group']) ? sanitize_text_field($_GET['pakg_group']) : '';
$plan = '3';
$_GET['pakg_price'] = '9.99';
$_GET['plan_type'] = 'sp';
$_GET['pakg_duration'] = '999999';
$_GET['expiry_date'] = '2022-10-10';
if ($plan == "0" || $plan == "1" || $plan == "2" || $plan == "3") {
    if ($_GET['pakg_price'] == '0') {
        $plan = '0';
    }

    update_option('wwe_small_packages_quotes_package', "$plan");

    $plan_type = isset($_GET['plan_type']) ? sanitize_text_field($_GET['plan_type']) : '';
    update_option('wwe_small_packages_quotes_store_type', "$plan_type");

    $expire_days = isset($_GET['pakg_duration']) ? sanitize_text_field($_GET['pakg_duration']) : '';
    update_option('wwe_small_package_expire_days', "$expire_days");

    $expiry_date = isset($_GET['expiry_date']) ? sanitize_text_field($_GET['expiry_date']) : '';
    update_option('wwe_small_package_expire_date', "$expiry_date");

    speed_en_check_wwe_small_plan_on_product_detail();
}
