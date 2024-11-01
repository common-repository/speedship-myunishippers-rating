<?php

/**
 * WWE Small Shipping Class
 *
 * @package     WWE Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialization
 */
function speed_smallpkg_shipping_method_init()
{

    if (!class_exists('WC_speedship')) {

        /**
         * WWE Small Shipping Calculation Class
         */
        class WC_speedship extends WC_Shipping_Method
        {

            /**
             * Woo-commerce Shipping Field Attributes
             * @param $instance_id
             */
            public $smallInluded = false;
            public $order_detail;
            public $is_autoresid;
            public $accessorials;
            public $helper_obj;
            public $instore_pickup_and_local_delivery;
            public $speed_group_small_shipments;
            public $web_service_inst;
            public $package_plugin;
            public $InstorPickupLocalDelivery;
            public $woocommerce_package_rates;
            public $quote_settings;
            public $shipment_type;
            public $eniture_rates;
            public $VersionCompat;
            public $en_ignore_rate_cost;
            public $en_not_returned_the_quotes = FALSE;
            public $minPrices = [];
            public $en_fdo_meta_data = [];

            public function __construct($instance_id = 0)
            {
                $this->id = 'speedship';
                $this->helper_obj = new Speed_En_Helper_Class();
                $this->instance_id = absint($instance_id);
                $this->method_title = __('Small Package (parcel)');
                $this->method_description = __('Real-time small package (parcel) shipping rates from Worldwide Express.');
                $this->supports = array(
                    'shipping-zones',
                    'instance-settings',
                    'instance-settings-modal',
                );
                $this->enabled = "yes";
                $this->title = "Small Package Quotes - Worldwide Express Edition ";
                $this->init();
            }

            /**
             * Update WWE Small Woo-commerce Shipping Settings
             */
            function init()
            {

                $this->init_form_fields();
                $this->init_settings();
                add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
            }

            /**
             * Ignore Products
             */
            public function en_ignored_products($package)
            {
                global $woocommerce;
                $products = $woocommerce->cart->get_cart();
                $items = $product_name = [];
                $lobster_list = [
                    '6-lobster-package',
                    '12-lobster-package'
                ];

                $this->en_ignore_rate_cost = 0;
                $en_ignore_rates = isset($package['rates']) ? $package['rates'] : '';
                foreach ($en_ignore_rates as $en_ignore_rates_key => $en_ignore_rate) {
                    if (isset($en_ignore_rate->method_id) && $en_ignore_rate->method_id == 'flat_rate') {
                        $this->en_ignore_rate_cost = $en_ignore_rate->cost;
                        continue;
                    }
                }

                $wc_settings_wwe_ignore_items = get_option("en_ignore_items_through_freight_classification");
                $en_get_current_classes = strlen($wc_settings_wwe_ignore_items) > 0 ? trim(strtolower($wc_settings_wwe_ignore_items)) : '';
                $en_get_current_classes_arr = strlen($en_get_current_classes) > 0 ? array_map('trim', explode(',', $en_get_current_classes)) : [];

                foreach ($products as $key => $product_obj) {
                    $product = $product_obj['data'];

                    //get product shipping class
                    $en_ship_class = strtolower($product_obj['data']->get_shipping_class());
                    if (in_array($en_ship_class, $lobster_list) && in_array($en_ship_class, $en_get_current_classes_arr)) {
                        $attributes = $product->get_attributes();
                        $product_qty = $product_obj['quantity'];
                        $product_title = str_replace(array("'", '"'), '', $product->get_title());
                        $product_name[] = $product_qty . " x " . $product_title;

                        $meta_data = [];
                        if (!empty($attributes)) {
                            foreach ($attributes as $attr_key => $attr_value) {
                                $meta_data[] = [
                                    'key' => $attr_key,
                                    'value' => $attr_value,
                                ];
                            }
                        }

                        $items[] = [
                            'id' => $product_obj['product_id'],
                            'name' => $product_title,
                            'quantity' => $product_qty,
                            'price' => $product->get_price(),
                            'weight' => wc_get_weight($product->get_weight(), 'lbs'),
                            'length' => wc_get_dimension($product->get_length(), 'in'),
                            'width' => wc_get_dimension($product->get_width(), 'in'),
                            'height' => wc_get_dimension($product->get_height(), 'in'),
                            'type' => 'flat_rate',
                            'product' => $product_obj['variation_id'] > 0 ? 'variable' : 'simple',
                            'sku' => $product->get_sku(),
                            'attributes' => $attributes,
                            'variant_id' => $product_obj['variation_id'],
                            'meta_data' => $meta_data,
                        ];
                    }
                }

                $flat_rate = [];

                if (!empty($items)) {
                    $flat_rate = [
                        'id' => 'en_flat_rate',
                        'label' => 'Flat Rate',
                        'cost' => $this->en_ignore_rate_cost,
                        'label_sufex' => ['S'],
                    ];

                    $flat_rate_fdo = [
                        'plugin_type' => 'small',
                        'plugin_name' => 'wwe_small_packages_quotes',
                        'accessorials' => '',
                        'items' => $items,
                        'address' => '',
                        'handling_unit_details' => '',
                        'rate' => $flat_rate,
                    ];

                    $meta_data = [
                        'sender_origin' => 'Flat Rate Product',
                        'product_name' => wp_json_encode($product_name),
                        'en_fdo_meta_data' => $flat_rate_fdo,
                    ];

                    $flat_rate['meta_data'] = $meta_data;
                }

                return $flat_rate;
            }

            /**
             * Virtual Products
             */
            public function en_virtual_products()
            {
                global $woocommerce;
                $products = $woocommerce->cart->get_cart();
                $items = $product_name = [];
                foreach ($products as $key => $product_obj) {
                    $product = $product_obj['data'];
                    $is_virtual = $product->get_virtual();

                    if ($is_virtual == 'yes') {
                        $attributes = $product->get_attributes();
                        $product_qty = $product_obj['quantity'];
                        $product_title = str_replace(array("'", '"'), '', $product->get_title());
                        $product_name[] = $product_qty . " x " . $product_title;

                        $meta_data = [];
                        if (!empty($attributes)) {
                            foreach ($attributes as $attr_key => $attr_value) {
                                $meta_data[] = [
                                    'key' => $attr_key,
                                    'value' => $attr_value,
                                ];
                            }
                        }

                        $items[] = [
                            'id' => $product_obj['product_id'],
                            'name' => $product_title,
                            'quantity' => $product_qty,
                            'price' => $product->get_price(),
                            'weight' => 0,
                            'length' => 0,
                            'width' => 0,
                            'height' => 0,
                            'type' => 'virtual',
                            'product' => 'virtual',
                            'sku' => $product->get_sku(),
                            'attributes' => $attributes,
                            'variant_id' => 0,
                            'meta_data' => $meta_data,
                        ];
                    }
                }

                $virtual_rate = [];

                if (!empty($items)) {
                    $virtual_rate = [
                        'id' => 'en_virtual_rate',
                        'label' => 'Virtual Quote',
                        'cost' => 0,
                    ];

                    $virtual_fdo = [
                        'plugin_type' => 'small',
                        'plugin_name' => 'wwe_small_packages_quotes',
                        'accessorials' => '',
                        'items' => $items,
                        'address' => '',
                        'handling_unit_details' => '',
                        'rate' => $virtual_rate,
                    ];

                    $meta_data = [
                        'sender_origin' => 'Virtual Product',
                        'product_name' => wp_json_encode($product_name),
                        'en_fdo_meta_data' => $virtual_fdo,
                    ];

                    $virtual_rate['meta_data'] = $meta_data;
                }

                return $virtual_rate;
            }

            /**
             * Enable Woo-commerce Shipping For WWE Small
             */
            public function init_form_fields()
            {

                $this->instance_form_fields = array(
                    'enabled' => array(
                        'title' => __('Enable / Disable', 'woocommerce'),
                        'type' => 'checkbox',
                        'label' => __('Enable This Shipping Service', 'woocommerce'),
                        'default' => 'yes',
                        'id' => 'speed_ship_enable_disable_shipping'
                    )
                );
            }

            /**
             * Multi shipment query
             * @param array $en_rates
             * @param string $accessorial
             */
            public function en_multi_shipment($en_rates, $accessorial, $origin)
            {
                $accessorial .= '_wwe_small';
                $en_rates = (isset($en_rates) && (is_array($en_rates))) ? array_slice($en_rates, 0, 1) : [];
                $total_cost = array_sum($this->VersionCompat->enArrayColumn($en_rates, 'cost'));

                !$total_cost > 0 ? $this->en_not_returned_the_quotes = TRUE : '';

                $en_rates = !empty($en_rates) ? reset($en_rates) : [];
                $this->minPrices[$origin] = $en_rates;
                $this->en_fdo_meta_data[$origin] = (isset($en_rates['meta_data']['en_fdo_meta_data'])) ? $en_rates['meta_data']['en_fdo_meta_data'] : [];

                if (isset($this->eniture_rates[$accessorial])) {
                    $this->eniture_rates[$accessorial]['cost'] += $total_cost;
                } else {
                    $this->eniture_rates[$accessorial] = [
                        'id' => $accessorial,
                        'label' => 'Shipping',
                        'cost' => $total_cost,
                        'label_sufex' => str_split($accessorial),
                    ];
                }
            }

            /**
             * Single shipment query
             * @param array $en_rates
             * @param string $accessorial
             */
            public function en_single_shipment($en_rates, $accessorial, $origin)
            {
                $this->eniture_rates = array_merge($this->eniture_rates, $en_rates);
            }

            /**
             * Calculate Shipping Rates For WWE Small
             * @param $package
             * @return string
             * @global $wpdb
             * @global $current_user
             */
            public function calculate_shipping($package = [], $eniture_admin_order_action = false)
            {
                if (is_admin() && !wp_doing_ajax() && !$eniture_admin_order_action) {
                    return [];
                }

                $this->package_plugin = get_option('wwe_small_packages_quotes_package');
                $this->get_hazardous_fields();
                $this->instore_pickup_and_local_delivery = FALSE;

                $zipcode_for_handling_fee = 0;
                global $wpdb;
                global $current_user;
                $output = "";
                $sandBox = "";
                $rates = [];
                $label_sufex_arr = [];
                $rateArray = [];
                $quotesArray = [];
                $quotes = [];
                $web_service_arr = [];
                $request_for = "";
                (isset($package['itemType']) && $package['itemType'] == 'ltl' ? $request_for = "ltl" : $request_for = "small");
                /*  check coupon exist or not   */
                if (has_filter('check_coupons') && $request_for == 'small') {
                    $couponCode = apply_filters('check_coupons', $package);
                }

                $this->VersionCompat = new VersionCompat();
                $web_service_inst = new speed_smallpkg_shipping_get_quotes();

                $this->web_service_inst = $web_service_inst;

                $speed_group_small_shipments = new speed_group_small_shipment();

                $this->speed_group_small_shipments = $speed_group_small_shipments;

                $coupn = WC()->cart->get_coupons();
                if (isset($coupn) && !empty($coupn)) {
                    $freeShipping = $this->wweSmpkgFreeShipping($coupn);
                    if ($freeShipping == 'y')
                        return FALSE;
                }
                $changObj = new SPEED_WWE_Small_Woo_Update_Changes();
                (strlen(WC()->customer->get_shipping_postcode()) > 0) ? $freight_zipcode = WC()->customer->get_shipping_postcode() : $freight_zipcode = $changObj->wwe_small_postcode();
                if (empty($freight_zipcode)) {
                    return FALSE;
                }
                $this->create_speedship_small_option();
                $selected_quotes_service_options_array = $this->wwe_smpkg_get_active_services();

                $sm_package = $speed_group_small_shipments->small_package_shipments($package, $web_service_inst);

                // Crowler work
                $request_for != 'ltl' ? $sm_package = apply_filters('en_check_sbs_packaging', $sm_package) : '';
                if (isset($sm_package['warehouse_origin'])) unset($sm_package['warehouse_origin']);
                if (isset($sm_package) && !empty($sm_package)) {
                    $package_valid = true;
                    foreach ($sm_package as $sm_package_key => $sm_package_detail) {
                        $request_for != 'ltl' && isset($sm_package_detail['ltl']) ? $package_valid = false : '';
                    }

                    !$package_valid ? $sm_package = [] : '';
                }

                $no_param_multi_ship = 0;
                /* apply filter for filter count sample,simple,total,origion */
                if (has_filter('small_package_check_grouping') && $request_for == 'small') {
                    $sm_package = apply_filters('small_package_check_grouping', $sm_package);
                }
                $web_service_arr = $web_service_inst->get_web_service_array($sm_package, $package, $this->package_plugin);

                // Pricing per product
                $en_pricing_per_product = apply_filters('en_pricing_per_product_existence', false);

                if (isset($web_service_arr) && $web_service_arr != '') {

                    $SpeedEnWweSmallTransitDays = new SpeedEnWweSmallTransitDays();
                    foreach ($web_service_arr as $key => $request) {
                        if ($request != 'ltl') {
                            $sPackage = $request;
                            $package_bins = (isset($sPackage['bins'])) ? $sPackage['bins'] : [];
                            $en_box_fee = (isset($sPackage['en_box_fee'])) ? $sPackage['en_box_fee'] : [];
                            $en_multi_box_qty = (isset($sPackage['speed_ship_quantity_array'])) ? $sPackage['speed_ship_quantity_array'] : [];
                            $fedex_bins = (isset($sPackage['fedex_bins'])) ? $sPackage['fedex_bins'] : [];
                            $hazardous_status = (isset($sPackage['hazardous_status'])) ? $sPackage['hazardous_status'] : '';
                            $en_fdo_meta_data = (isset($sPackage['en_fdo_meta_data'])) ? $sPackage['en_fdo_meta_data'] : '';
                            // Pricing per product
                            $pricing_per_product = (isset($sPackage['pricing_per_product'])) ? $sPackage['pricing_per_product'] : '';
                            $package_bins = !empty($fedex_bins) ? $package_bins + $fedex_bins : $package_bins;
                            if (!isset($sPackage['speed_ship_senderZip'])) {
                                continue;
                            }

                            $speed_ship_senderZip = $sPackage['speed_ship_senderZip'];
                            if ($en_pricing_per_product && strlen($speed_ship_senderZip) > 0) {
                                $speed_ship_senderZip = $key;
                            }

                            $this->web_service_inst->product_detail[$speed_ship_senderZip]['product_name'] = json_encode($sPackage['product_name']);
                            $this->web_service_inst->product_detail[$speed_ship_senderZip]['products'] = $sPackage['products'];
                            $this->web_service_inst->product_detail[$speed_ship_senderZip]['sender_origin'] = $sPackage['sender_origin'];
                            $this->web_service_inst->product_detail[$speed_ship_senderZip]['package_bins'] = $package_bins;
                            $this->web_service_inst->product_detail[$speed_ship_senderZip]['en_box_fee'] = $en_box_fee;
                            $this->web_service_inst->product_detail[$speed_ship_senderZip]['en_multi_box_qty'] = $en_multi_box_qty;
                            $this->web_service_inst->product_detail[$speed_ship_senderZip]['hazardous_status'] = $hazardous_status;
                            $this->web_service_inst->product_detail[$speed_ship_senderZip]['en_fdo_meta_data'] = $en_fdo_meta_data;
                            // Pricing per product
                            $this->web_service_inst->product_detail[$speed_ship_senderZip]['pricing_per_product'] = $pricing_per_product;

                            if (isset($sPackage['forcefully_residential_delivery']) && $sPackage['forcefully_residential_delivery'] == 'on') {
                                $this->web_service_inst->forcefully_residential_delivery = TRUE;
                            }

                            $output = $web_service_inst->get_web_quotes($request, $this->package_plugin);
                            $zipcode_for_handling_fee = $key;

                            $output = $SpeedEnWweSmallTransitDays->wwe_small_enable_disable_ups_ground(json_decode($output));

                            $quotes[$key] = json_decode($output);

                            (isset($request['hazardous_material'])) ? $quotes[$key]->hazardous_material = TRUE : "";

                            $this->InstorPickupLocalDelivery = (isset($quotes[$key]->InstorPickupLocalDelivery)) ? $quotes[$key]->InstorPickupLocalDelivery : [];

                            $Speed_Wwe_Small_Auto_Residential_Detection = new Speed_Wwe_Small_Auto_Residential_Detection();
                            $label_sfx_rtrn = $Speed_Wwe_Small_Auto_Residential_Detection->filter_label_sufex_array($quotes[$key]);
                            $label_sufex_arr = array_merge($label_sufex_arr, $label_sfx_rtrn);
                        }
                    }
                }

                // Virtual products
                $virtual_rate = $this->en_virtual_products();
                if (!empty($virtual_rate)) {
                    $this->minPrices['virtual_rate'] = $virtual_rate;
                    $this->en_fdo_meta_data['virtual_rate'] = (isset($virtual_rate['meta_data']['en_fdo_meta_data'])) ? $virtual_rate['meta_data']['en_fdo_meta_data'] : [];
                }

                // Ignored products added to order widget details
                $en_ignored_rate = $this->en_ignored_products($package);
                if (!empty($en_ignored_rate)) {
                    $this->minPrices['flat_rate'] = $en_ignored_rate;
                    $this->en_fdo_meta_data['flat_rate'] = (isset($en_ignored_rate['meta_data']['en_fdo_meta_data'])) ? $en_ignored_rate['meta_data']['en_fdo_meta_data'] : [];
                }

                foreach ($quotes as $qIndex => $quote) {
                    //  Update origin city with correct city for WWE
                    $originCityData = (isset($quote->originCityData) && !empty($quote->originCityData)) ? $quote->originCityData : '';
                    if (isset($originCityData) && !empty($originCityData)) {
                        $this->wwe_small_update_origin_data($originCityData);
                    }

                    $quotesArray[$qIndex] = $quote;
                }

                $quotes = $quotesArray;
                $en_is_shipment = (count($quotes) > 1 || $no_param_multi_ship == 1) || $no_param_multi_ship == 1 ? 'en_multi_shipment' : 'en_single_shipment';
                $this->quote_settings['shipment'] = $en_is_shipment;
                $this->eniture_rates = [];

                $en_rates = $quotes;

                foreach ($en_rates as $origin => $step_for_rates) {

                    $product_detail = (isset($this->web_service_inst->product_detail[$origin])) ? $this->web_service_inst->product_detail[$origin] : [];
                    (isset($domestic_international[$origin])) ? $services = $domestic_international[$origin] : '';
                    $filterd_rates = $web_service_inst->parse_wwe_small_output($step_for_rates, $selected_quotes_service_options_array, $product_detail, $this->quote_settings);
                    $en_sorting_rates = (isset($filterd_rates['en_sorting_rates'])) ? $filterd_rates['en_sorting_rates'] : "";
                    if (isset($filterd_rates['en_sorting_rates']))
                        unset($filterd_rates['en_sorting_rates']);

                    if (is_array($filterd_rates) && !empty($filterd_rates)) {
                        foreach ($filterd_rates as $accessorial => $service) {
                            (!empty($filterd_rates[$accessorial])) ? array_multisort($en_sorting_rates[$accessorial], SORT_ASC, $filterd_rates[$accessorial]) : $en_sorting_rates[$accessorial] = [];
                            $this->$en_is_shipment($filterd_rates[$accessorial], $accessorial, $origin);
                        }
                    } else {
                        $this->en_not_returned_the_quotes = TRUE;
                    }
                }

                if ($en_is_shipment == 'en_single_shipment') {

                    // In-store pickup and local delivery
                    $instore_pickup_local_devlivery_action = apply_filters('speed_wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'instore_pickup_local_devlivery');
                    if (isset($this->web_service_inst->en_wd_origin_array['suppress_local_delivery']) && $this->web_service_inst->en_wd_origin_array['suppress_local_delivery'] == "1" && (!is_array($instore_pickup_local_devlivery_action))) {
                        $this->eniture_rates = apply_filters('suppress_local_delivery', $this->eniture_rates, $this->web_service_inst->en_wd_origin_array, $this->package_plugin, $this->InstorPickupLocalDelivery);
                    }
                }

                $accessorials = [
                    'R' => 'residential delivery',
                ];

                add_filter('woocommerce_package_rates', array($this, 'en_sort_woocommerce_available_shipping_methods'), 10, 2);

                $en_rates = $this->eniture_rates;

                // Custom work get from old programming.
                if (has_filter('count_sample') && $request_for == 'small') {
                    $sample = apply_filters('count_sample', $sm_package);
                    $sampleQuantity = $sample['sample'];
                }

                // Ignored products added to order widget details
                $en_ignored_flag = false;
                if (empty($en_rates)) {
                    $en_ignored_flag = true;
                    $en_rates = [$en_ignored_rate];
                }

                // Images for FDO
                $image_urls = apply_filters('en_fdo_image_urls_merge', []);

                foreach ($en_rates as $accessorial => $rate) {
                    // Custom work get from old programming.
                    if (has_filter('check_implements_coupons') && $request_for == 'small') {
                        if ($couponCode == 'handful' || $sampleQuantity > 0) {
                            $rate = apply_filters('check_implements_coupons', $rate);
                        }
                    }

                    // Show delivery estimates
                    if ($en_is_shipment == 'en_single_shipment') {

                        $wwe_small_show_delivery_estimates_plan = apply_filters('speed_wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'wwe_small_show_delivery_estimates');
                        $wwe_small_delivey_estimate = get_option('wwe_small_delivery_estimates');

                        if (isset($wwe_small_delivey_estimate) && !empty($wwe_small_delivey_estimate) && $wwe_small_delivey_estimate != 'dont_show_estimates' && !is_array($wwe_small_show_delivery_estimates_plan)) {
                            if ($wwe_small_delivey_estimate == 'delivery_date' && !empty($rate['transit_time'])) {
                                $rate['label'] .= ' ( Expected delivery by ' . date('Y-m-d', strtotime($rate['transit_time'])) . ' )';
                            } else if ($wwe_small_delivey_estimate == 'delivery_days' && !empty($rate['delivery_days'])) {
                                $correct_word = ($rate['delivery_days'] == 1) ? 'is' : 'are';
                                $rate['label'] .= ' ( Estimated number of days until delivery ' . $correct_word . ' ' . $rate['delivery_days'] . ' )';
                            }
                        }
                    }

                    if (isset($rate['label_sufex']) && !empty($rate['label_sufex'])) {
                        $label_sufex = array_intersect_key($accessorials, array_flip($rate['label_sufex']));
                        $rate['label'] .= (!empty($label_sufex)) ? ' with ' . implode(' and ', $label_sufex) : '';

                        // Order widget detail set
                        // FDO
                        if (isset($this->minPrices) && !empty($this->minPrices)) {
                            if ($en_is_shipment == 'en_single_shipment' && !$en_ignored_flag) {
                                $this->minPrices['speedship_rate'] = $rate;
                                $this->en_fdo_meta_data['speedship_rate'] = (isset($rate['meta_data']['en_fdo_meta_data'])) ? $rate['meta_data']['en_fdo_meta_data'] : [];
                            }

                            $rate['meta_data']['min_prices'] = wp_json_encode($this->minPrices);
                            $rate['minPrices'] = $this->minPrices;
                            $rate['meta_data']['en_fdo_meta_data']['data'] = array_values($this->en_fdo_meta_data);
                            $rate['meta_data']['en_fdo_meta_data']['shipment'] = 'multiple';
                            $rate['meta_data']['en_fdo_meta_data'] = wp_json_encode($rate['meta_data']['en_fdo_meta_data']);
                        } else {
                            $en_set_fdo_meta_data['data'] = [$rate['meta_data']['en_fdo_meta_data']];
                            $en_set_fdo_meta_data['shipment'] = 'sinlge';
                            $rate['meta_data']['en_fdo_meta_data'] = wp_json_encode($en_set_fdo_meta_data);
                        }

                        // Images for FDO
                        $rate['meta_data']['en_fdo_image_urls'] = wp_json_encode($image_urls);
                    }

                    if (isset($rate['cost']) && $rate['cost'] > 0) {
                        !$en_ignored_flag && isset($rate['cost']) ? $rate['cost'] += $this->en_ignore_rate_cost : '';
                        $this->add_rate($rate);
                    }

                    $en_rates[$accessorial] = $rate;
                }

                // Custom work get from old programming.
                $this->smallInluded = true;
                add_filter('decide_rm_third_party_quotes', array($this, 'decideRmThirdParty'), 99, 3);

                // Origin terminal address
                if ($en_is_shipment == 'en_single_shipment') {
                    (isset($this->InstorPickupLocalDelivery->localDelivery) && ($this->InstorPickupLocalDelivery->localDelivery->status == 1)) ? $this->local_delivery($this->web_service_inst->en_wd_origin_array['fee_local_delivery'], $this->web_service_inst->en_wd_origin_array['checkout_desc_local_delivery'], $this->web_service_inst->en_wd_origin_array) : "";
                    (isset($this->InstorPickupLocalDelivery->inStorePickup) && ($this->InstorPickupLocalDelivery->inStorePickup->status == 1)) ? $this->pickup_delivery($this->web_service_inst->en_wd_origin_array['checkout_desc_store_pickup'], $this->web_service_inst->en_wd_origin_array, $this->InstorPickupLocalDelivery->totalDistance) : "";
                }

                return $en_rates;
            }

            function get_hazardous_fields()
            {
                $this->quote_settings = [];
                $this->quote_settings['hazardous_materials_shipments'] = get_option('only_quote_ground_service_for_hazardous_materials_shipments');
                $this->quote_settings['ground_hazardous_material_fee'] = get_option('ground_hazardous_material_fee');
                $this->quote_settings['air_hazardous_material_fee'] = get_option('air_hazardous_material_fee');
                $this->quote_settings['dont_sort'] = get_option('shipping_methods_do_not_sort_by_price');
            }

            /**
             * Get Calculate service level markup
             * @param $total_charge
             * @param $international_markup
             */
            function calculate_service_level_markup($total_charge, $international_markup)
            {
                $grandTotal = 0;
                if (floatval($international_markup)) {
                    $pos = strpos($international_markup, '%');
                    if ($pos > 0) {
                        $rest = substr($international_markup, $pos);
                        $exp = explode($rest, $international_markup);
                        $get = $exp[0];
                        $percnt = $get / 100 * $total_charge;
                        $grandTotal += $total_charge + $percnt;
                    } else {
                        $grandTotal += $total_charge + $international_markup;
                    }
                } else {
                    $grandTotal += $total_charge;
                }
                return $grandTotal;
            }

            /**
             * Pickup delivery quote
             * @return array type
             */
            function pickup_delivery($label, $en_wd_origin_array, $total_distance)
            {
                $this->woocommerce_package_rates = 1;
                $this->instore_pickup_and_local_delivery = TRUE;

                $label = (isset($label) && (strlen($label) > 0)) ? $label : 'In-store pick up';
                // Origin terminal address
                $address = (isset($en_wd_origin_array['address'])) ? $en_wd_origin_array['address'] : '';
                $city = (isset($en_wd_origin_array['city'])) ? $en_wd_origin_array['city'] : '';
                $state = (isset($en_wd_origin_array['state'])) ? $en_wd_origin_array['state'] : '';
                $zip = (isset($en_wd_origin_array['zip'])) ? $en_wd_origin_array['zip'] : '';
                $phone_instore = (isset($en_wd_origin_array['phone_instore'])) ? $en_wd_origin_array['phone_instore'] : '';
                strlen($total_distance) > 0 ? $label .= ': Free | ' . str_replace("mi", "miles", $total_distance) . ' away' : '';
                strlen($address) > 0 ? $label .= ' | ' . $address : '';
                strlen($city) > 0 ? $label .= ', ' . $city : '';
                strlen($state) > 0 ? $label .= ' ' . $state : '';
                strlen($zip) > 0 ? $label .= ' ' . $zip : '';
                strlen($phone_instore) > 0 ? $label .= ' | ' . $phone_instore : '';

                $pickup_delivery = array(
                    'id' => 'in-store-pick-up',
                    'cost' => 0,
                    'label' => $label,
                );

                add_filter('woocommerce_package_rates', array($this, 'en_sort_woocommerce_available_shipping_methods'), 10, 2);
                $this->add_rate($pickup_delivery);
            }

            /**
             * Local delivery quote
             * @param string type $cost
             * @return array type
             */
            function local_delivery($cost, $label, $en_wd_origin_array)
            {
                $this->woocommerce_package_rates = 1;
                $this->instore_pickup_and_local_delivery = TRUE;
                $label = (isset($label) && (strlen($label) > 0)) ? $label : 'Local Delivery';
                $local_delivery = array(
                    'id' => 'local-delivery',
                    'cost' => $cost,
                    'label' => $label,
                );

                add_filter('woocommerce_package_rates', array($this, 'en_sort_woocommerce_available_shipping_methods'), 10, 2);
                $this->add_rate($local_delivery);
            }

            /**
             * final rates sorting
             * @param array type $rates
             * @param array type $package
             * @return array type
             */
            function en_sort_woocommerce_available_shipping_methods($rates, $package)
            {
                // if there are no rates don't do anything
                if (!$rates) {
                    return [];
                }

                // Check the option to sort shipping methods by price on quote settings
                if (get_option('shipping_methods_do_not_sort_by_price') != 'yes') {
                    // Get an array of prices
                    $prices = [];
                    foreach ($rates as $rate) {
                        $prices[] = $rate->cost;
                    }

                    // Use the prices to sort the rates
                    array_multisort($prices, $rates);
                }
                // Return the rates
                return $rates;
            }

            /**
             * Set residential accessorial access in multi-shipment.
             * @param Array $rates
             * @param object $speed_group_small_shipments
             */
            public function en_set_multiship_residential_del($rates, $speed_group_small_shipments)
            {
                $access_arr = [];
                if (!function_exists('array_column')) {
                    $access_arr = $this->helper_obj->array_column($rates, 'label_sufex');
                } else {
                    $access_arr = array_column($rates, 'label_sufex');
                }
                /* Assign Auto-residential/liftgate to order details */
                foreach ($rates as $key => $value) {
                    $residential_del = get_option('wc_settings_quest_as_residential_delivery_wwe_small_packages');
                    if (
                        isset($value['label_sufex']) &&
                        count($value['label_sufex']) > 0 &&
                        in_array('R', $value['label_sufex']) && $residential_del != 'yes'
                    ) {
                        $speed_group_small_shipments->order_details['accessorials']['R'] = 'R';
                    }
                }
            }

            /**
             * Function to update the filter data and session with order details.
             * @param object $speed_group_small_shipments
             */
            public function en_order_details_hooks_process($speed_group_small_shipments)
            {
                $order_details = [];
                $this->order_detail = $speed_group_small_shipments->order_details;
                /* Filter the data of order details */
                add_filter('en_fitler_order_data', array($this, 'en_update_order_data'));
                /* Passing empty array because data is updated using class property */
                $session_order_details = apply_filters(
                    'en_fitler_order_data',
                    []
                );

                /* Set the session */
                WC()->session->set('en_order_detail', $session_order_details);
            }

            /**
             * Filter function to update order details.
             * @param array $data
             * @return type
             */
            public function en_update_order_data($data)
            {

                $data['en_shipping_details']['en_wwe_small'] = $this->order_detail;
                return $data;
            }

            /**
             * Set the cheapest prices.
             * @param array $rates
             */
            public function en_set_cheapest_prcs($rates, $speed_group_small_shipments)
            {
                foreach ($rates as $key => $value) {
                    foreach ($rates[$key]['minPrices'] as $zip => $val) {
                        $speed_group_small_shipments->order_details['details'][$zip]['cheapest_services'][$val['code']] = $val;
                    }
                }
            }

            /**
             * Check the status of Show no other plugins option.
             * @param string $rmStatus
             * @param array $rmThirdPartyArr
             * @param array $available_methods
             * @return boolean
             */
            function decideRmThirdParty($rmStatus, $rmThirdPartyArr, $available_methods)
            {

                if (!isset($rmThirdPartyArr['wc_settings_wwe_small_allow_other_plugins'])) {
                    return $rmStatus;
                }
                return (($rmThirdPartyArr['wc_settings_wwe_small_allow_other_plugins'] == 'no') && ($this->smallInluded || $rmThirdPartyArr['shipment_id'] == 'speedship')) ? true : false;
            }

            /**
             * Check is free shipping or not
             * @param $coupon
             * @return string
             */
            function wweSmpkgFreeShipping($coupon)
            {
                foreach ($coupon as $key => $value) {
                    if ($value->get_free_shipping() == 1) {
                        $free = array(
                            'id' => 'free',
                            'label' => 'Free Shipping',
                            'cost' => 0
                        );
                        $this->add_rate($free);
                        return 'y';
                    }
                }
            }

            /**
             * Create plugin option
             */
            function create_speedship_small_option()
            {
                $eniture_plugins = get_option('EN_Plugins');
                if (!$eniture_plugins) {
                    add_option('EN_Plugins', json_encode(array('speedship')));
                } else {
                    $plugins_array = json_decode($eniture_plugins);
                    if (!in_array('speedship', $plugins_array)) {
                        array_push($plugins_array, 'speedship');
                        update_option('EN_Plugins', json_encode($plugins_array));
                    }
                }
            }

            /**
             * Get Active Service Options
             */
            function wwe_smpkg_get_active_services()
            {
                $selected_quotes_service_options_array = [];
                if (get_option('wc_settings_Service_UPS_Next_Day_Early_AM_small_packages_quotes') == 'yes') {
                    $selected_quotes_service_options_array['1DM'] = ['name' => '1DM', 'markup' => get_option('wwesmall_Service_UPS_Next_Day_Early_AM_small_packages_quotes_markup')];
                }
                if (get_option('wc_settings_Service_UPS_Next_Day_Air_small_packages_quotes') == 'yes') {
                    $selected_quotes_service_options_array['1DA'] = ['name' => '1DA', 'markup' => get_option('wwesmall_Service_UPS_Next_Day_Air_small_packages_quotes_markup')];
                }
                if (get_option('wc_settings_Service_UPS_Next_Day_Air_Saver_small_packages_quotes') == 'yes') {
                    $selected_quotes_service_options_array['1DP'] = ['name' => '1DP', 'markup' => get_option('wwesmall_Service_UPS_Next_Day_Air_Saver_small_packages_quotes_markup')];
                }
                if (get_option('wc_settings_Service_UPS_2nd_Day_AM_quotes') == 'yes') {
                    $selected_quotes_service_options_array['2DM'] = ['name' => '2DM', 'markup' => get_option('wwesmall_Service_UPS_2nd_Day_AM_quotes_markup')];
                }
                if (get_option('wc_settings_Service_UPS_2nd_Day_PM_quotes') == 'yes') {
                    $selected_quotes_service_options_array['2DA'] = ['name' => '2DA', 'markup' => get_option('wwesmall_Service_UPS_2nd_Day_PM_quotes_markup')];
                }
                if (get_option('wc_settings_Service_UPS_2nd_Day_Saturday_quotes') == 'yes') {
                    $selected_quotes_service_options_array['2DAS'] = ['name' => '2DAS', 'markup' => get_option('wwesmall_Service_UPS_2nd_Day_Saturday_quotes_markup')];
                }
                if (get_option('wc_settings_Service_UPS_3rd_Day_quotes') == 'yes') {
                    $selected_quotes_service_options_array['3DS'] = ['name' => '3DS', 'markup' => get_option('wwesmall_Service_UPS_3rd_Day_quotes_markup')];
                }
                if (get_option('wc_settings_Service_UPS_Ground_quotes') == 'yes') {
                    $selected_quotes_service_options_array['GND'] = ['name' => 'GND', 'markup' => get_option('wwesmall_Service_UPS_Ground_quotes_markup')];
                }
                return $selected_quotes_service_options_array;
            }

            /**
             *
             * @param type $origin_data
             * Update warehouse/dropship with correct for WWE
             * @global type $wpdb
             */
            function wwe_small_update_origin_data($origin_data)
            {
                global $wpdb;
                $data = array('wwe_correct_city' => $origin_data->validCity);
                $clause_array = array(
                    'zip' => $origin_data->currentZip,
                    'city' => $origin_data->currentCity,
                    'state' => $origin_data->currentState
                );
                $update_qry = $wpdb->update(
                    $wpdb->prefix . 'warehouse',
                    $data,
                    $clause_array
                );
            }
        }
    }
}
