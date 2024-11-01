<?php

/**
 * Includes Form Hanlder
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooBoxSizeMultiPackage")) {

    class EnWooBoxSizeMultiPackage
    {

        public $EnWooAddonBoxSizingTemplate;
        public $query;

        public function __construct()
        {
            add_filter('en_mutiple_packages_in_request', [$this, 'en_mutiple_packages_in_request'], 10, 6);
            add_filter('en_mutiple_packages_valid_request', [$this, 'en_mutiple_packages_valid_request'], 10, 1);
            add_filter('en_mutiple_packages_update_request', [$this, 'en_mutiple_packages_update_request'], 10, 2);
        }

        /**
         * When box not added against product
         * @param array $post_data
         * @return array
         */
        public function en_mutiple_packages_valid_request($post_data)
        {
            if (isset($post_data['en_quotes_return']) && $post_data['en_quotes_return'] == 'yes') {
                $post_data = [];
            }

            return $post_data;
        }

        /**
         * Handle multi package request
         * @param array $post_data
         * @param array $item_multiple_package
         * @return array
         */
        public function en_mutiple_packages_update_request($post_data, $item_multiple_package)
        {
            $update_the_request = $request_params = [];

            if (isset($post_data['carrierName']) && ($post_data['carrierName'] == 'wwe_small_packages_quotes' || $post_data['carrierName'] == 'fedexSmall' || $post_data['carrierName'] == 'upsSmall' || $post_data['carrierName'] == 'unisheppers') && !empty($post_data)) {
                switch ($post_data['carrierName']) {
                    case 'wwe_small_packages_quotes':
                        $request_params = ['speed_ship_product_weight', 'product_width_array', 'product_length_array', 'product_height_array', 'speed_ship_quantity_array', 'shipBinAlone', 'vertical_rotation', 'en_box_fee', 'products'];
                        break;
                    case 'fedexSmall':
                        $request_params = ['weight', 'width', 'length', 'height', 'count', 'shipBinAlone', 'vertical_rotation', 'en_box_fee', 'products'];
                        break;
                    case 'unisheppers':
                        $request_params = ['commdityDetails', 'count', 'shipBinAlone', 'vertical_rotation', 'en_box_fee', 'products'];
                        break;
                    case 'upsSmall':
                        $request_params = ['ups_small_pkg_product_weight', 'ups_small_pkg_product_width', 'ups_small_pkg_product_length', 'ups_small_pkg_product_height', 'ups_small_pkg_product_quantity', 'shipBinAlone', 'vertical_rotation', 'en_box_fee', 'products'];
                        break;
                }

                foreach ($request_params as $key => $param) {
                    if (isset($post_data[$param]) && !empty($post_data[$param])) {
                        foreach ($post_data[$param] as $param_key => $param_arr) {
                            $step_for_request = (isset($item_multiple_package[$param_key][$param])) ? $item_multiple_package[$param_key][$param] : [$param_arr];
                            $update_the_request[$param] = (isset($update_the_request[$param])) ? array_merge($update_the_request[$param], $step_for_request) : $step_for_request;
                        }
                    }
                }
            }

            return array_replace($post_data, $update_the_request);
        }

        /**
         * Insert array after specific position
         * @param array $array
         * @param array $key
         * @param array $new
         * @return array
         */
        public function addon_array_insert_after(array $array, $key, array $new)
        {

            if (isset($key) && in_array($key, array_keys($array))) {

                $keys = array_keys($array);
                $index = array_search($key, $keys);
                $pos = false === $index ? count($array) : $index + 1;
                $array = array_merge(array_slice($array, 0, $pos), $new, array_slice($array, $pos));
            }

            return $array;
        }

        /**
         * Get multi package boxes
         * @param string $item_multiple_package
         * @param string $counter_product
         * @param string $product_id
         * @param string $vertical_rot
         * @param array $item
         * @return array
         */
        public function en_mutiple_packages_in_request($item_multiple_package, $counter_product, $product_id, $vertical_rot, $item, $plugin_id)
        {

            $multi_packages_arr = [];
            $product_weight = (isset($item['productWeight'])) ? $item['productWeight'] : 0;
            $product_qty = (isset($item['productQty'])) ? $item['productQty'] : 0;
            $products = (isset($item['products'])) ? $item['products'] : '';
            if ($item_multiple_package == '1') {
                $multi_packages_arr['en_quotes_return'] = 'no';
                $args = array(
                    'post_type' => 'en_multi_packaging',
                    'posts_per_page' => -1
                );

                switch ($plugin_id) {
                    case 'wwe_small_packages_quotes':
                        $request_params = [
                            'en_weight' => 'speed_ship_product_weight',
                            'en_width' => 'product_width_array',
                            'en_length' => 'product_length_array',
                            'en_height' => 'product_height_array',
                            'en_count' => 'speed_ship_quantity_array',
                            'en_shipBinAlone' => 'shipBinAlone',
                            'en_vertical_rotation' => 'vertical_rotation',
                            'en_box_fee' => 'en_box_fee',
                            'en_products' => 'products'
                        ];
                        break;
                    case 'fedexSmall':
                        $request_params = [
                            'en_weight' => 'weight',
                            'en_width' => 'width',
                            'en_length' => 'length',
                            'en_height' => 'height',
                            'en_count' => 'count',
                            'en_shipBinAlone' => 'shipBinAlone',
                            'en_vertical_rotation' => 'vertical_rotation',
                            'en_box_fee' => 'en_box_fee',
                            'en_products' => 'products'
                        ];
                        break;
                    case 'unisheppers':
                        $request_params = [
                            'en_commdity_details' => 'commdityDetails',
                            'en_count' => 'count',
                            'en_shipBinAlone' => 'shipBinAlone',
                            'en_vertical_rotation' => 'vertical_rotation',
                            'en_box_fee' => 'en_box_fee',
                            'en_products' => 'products'
                        ];
                        break;
                    case 'trinet':
                        $request_params = [
                            'en_commdity_details' => 'commdityDetails',
                            'en_count' => 'count',
                            'en_shipBinAlone' => 'shipAsOwnPackage',
                            'en_vertical_rotation' => 'verticalRotation',
                            'en_box_fee' => 'en_box_fee',
                            'en_products' => 'products',
                        ];
                        break;
                    case 'upsSmall':
                        $request_params = [
                            'en_weight' => 'ups_small_pkg_product_weight',
                            'en_width' => 'ups_small_pkg_product_width',
                            'en_length' => 'ups_small_pkg_product_length',
                            'en_height' => 'ups_small_pkg_product_height',
                            'en_count' => 'ups_small_pkg_product_quantity',
                            'en_shipBinAlone' => 'shipBinAlone',
                            'en_vertical_rotation' => 'vertical_rotation',
                            'en_box_fee' => 'en_box_fee',
                            'en_products' => 'products'
                        ];
                        break;
                }

                $query = new WP_Query($args);
                if ($query->have_posts()) {
                    $count = 1;
                    while ($query->have_posts()) {
                        $en_add_count = $count . $counter_product;
                        $query->the_post();
                        $postId = get_the_ID();
                        $postTitle = get_the_title();
                        $postContent = get_the_content();
                        $value = get_post_meta($postId, 'en_multi_packaging', true);
                        $en_box_box_weight = $en_box_length = $en_box_width = $en_box_height = $en_box_quantity = $en_box_fee = '';
                        $lineItemPrice = $location = $isHazmatLineItem = '';
                        if (isset($value['en_multipackage_product_id']) && $value['en_multipackage_product_id'] == $product_id) {
                            extract($value);
                            extract($request_params);
                            if ($plugin_id == 'trinet') {
                                if (isset($en_commdity_details) && $en_commdity_details == 'commdityDetails') {
                                    $commdityDetails = [
                                        'lineItemWeight' => $en_box_box_weight,
                                        'lineItemLength' => $en_box_length,
                                        'lineItemWidth' => $en_box_width,
                                        'lineItemHeight' => $en_box_height,
                                        'piecesOfLineItem' => $en_box_quantity * $product_qty,
                                        // Extra fields
                                        'lineItemPrice' => $item['lineItemPrice'],
                                        'location' => $item['location'],
                                        'isHazmatLineItem' => $item['isHazmatLineItem'],
                                    ];
                                    $multi_packages_arr[$en_add_count] = $commdityDetails;
                                }
                                (isset($en_box_fee)) ? $multi_packages_arr[$en_add_count][$en_box_fee] = $en_box_usps_box_fee : '';

                                (isset($en_shipBinAlone)) ? $multi_packages_arr[$en_add_count][$en_shipBinAlone] = 1 : '';
                                (isset($en_vertical_rotation)) ? $multi_packages_arr[$en_add_count][$en_vertical_rotation] = $vertical_rot : '';

                                $multi_packages_arr[$en_add_count]['en_multipackage_product'] = 1;
                                $multi_packages_arr[$en_add_count]['en_quotes_return'] = 1;

                                (isset($en_box_fee)) ? $multi_packages_arr[$en_box_fee][$en_add_count] = $en_box_usps_box_fee : '';
                                (isset($en_products)) ? $multi_packages_arr[$en_products][$en_add_count] = $products : '';
                                (isset($commdityDetails['piecesOfLineItem'])) ? $multi_packages_arr['en_multi_box_qty'][$en_add_count] = $commdityDetails['piecesOfLineItem'] : '';
                            } else {
                                if (isset($en_commdity_details) && $en_commdity_details == 'commdityDetails') {
                                    $commdityDetails = [
                                        'lineItemWeight' => $en_box_box_weight,
                                        'lineItemLength' => $en_box_length,
                                        'lineItemWidth' => $en_box_width,
                                        'lineItemHeight' => $en_box_height,
                                        'piecesOfLineItem' => $en_box_quantity * $product_qty,
                                    ];
                                    $multi_packages_arr[$en_commdity_details][$en_add_count] = $commdityDetails;
                                }
                                (isset($en_box_fee)) ? $multi_packages_arr[$en_box_fee][$en_add_count] = $en_box_usps_box_fee : '';
                                (isset($en_weight) && strlen($en_weight) > 0) ? $multi_packages_arr[$en_weight][$en_add_count] = $en_box_box_weight : '';
                                (isset($en_width) && strlen($en_width) > 0) ? $multi_packages_arr[$en_width][$en_add_count] = $en_box_width : '';
                                (isset($en_length) && strlen($en_length) > 0) ? $multi_packages_arr[$en_length][$en_add_count] = $en_box_length : '';
                                (isset($en_height) && strlen($en_height) > 0) ? $multi_packages_arr[$en_height][$en_add_count] = $en_box_height : '';
                                (isset($en_count) && strlen($en_count) > 0) ? $multi_packages_arr[$en_count][$en_add_count] = $en_box_quantity * $product_qty : '';
                                (isset($en_shipBinAlone)) ? $multi_packages_arr[$en_shipBinAlone][$en_add_count] = 1 : '';
                                (isset($en_vertical_rotation)) ? $multi_packages_arr[$en_vertical_rotation][$en_add_count] = $vertical_rot : '';
                                (isset($en_products)) ? $multi_packages_arr[$en_products][$en_add_count] = $products : '';
                                $multi_packages_arr['en_quotes_return'] = 'yes';
                                $multi_packages_arr['en_multipackage_product'] = 'yes';
                            }
                        }

                        $count++;
                    }
                }
            }

            return $multi_packages_arr;
        }

    }

    new EnWooBoxSizeMultiPackage();
}
