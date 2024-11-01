<?php

/**
 * WWE Small Group Packaging
 *
 * @package     WWE Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists("En_WWE_Order_Widget_Details")) {

    class En_WWE_Order_Widget_Details
    {

        /**
         * Handling fee status
         * @var string
         */
        public $handling_fee;

        /**
         * Selected shipping status.
         * @var string/int
         */
        public $ship_status;

        /**
         *  current curreny symbol.
         * @var string
         */
        public $currency_symbol;

        /**
         *  Response of order from our custom table.
         * @var array
         */
        public $result_details;

        /**
         * Order key.
         * @var string
         */
        public $order_key;

        /**
         * Selected shippping title.
         * @var type
         */
        public $shipping_method_title;

        /**
         * Selected shippping ID.
         * @var string
         */
        public $shipping_method_id;

        /**
         * Selected shippping price.
         * @var int/float/string
         */
        public $shipping_method_total;

        /**
         * Set 1 if any eniture service selected.
         * @var string
         */
        public $shipment_status;

        /**
         * Set 1 if any eniture service selected.
         * @var string
         */
        public $accessorials;

        /**
         * Helper object.
         * @var object
         */
        public $helper_obj;

        /**
         * Multishipment id.
         * @var string
         */
        public $multi_ship_id;
        public $get_formatted_meta_data;

        /**
         * Constructor.
         */
        public function __construct()
        {
            $this->multi_ship_id = '10001';
            $this->helper_obj = new Speed_En_Helper_Class();
            $this->en_call_hooks();
        }

        /**
         * Call needed hooks.
         */
        public function en_call_hooks()
        {
            /* Woocommerce order action hook */
            add_action(
                'woocommerce_order_actions', array($this, 'en_assign_order_details'), 10
            );
        }

        /**
         * Adding Meta container admin shop_order pages
         * @param $actions
         */
        function en_create_meta_box_order_details()
        {

            $this->en_assign_order_details();
        }

        /**
         * Assign order details.
         */
        function en_assign_order_details($actions)
        {
            global $wpdb;
            $this->shipment_status = 'single';
            $order_id = get_the_ID();
            $order = new WC_Order($order_id);
            $this->order_key = $order->get_order_key();
            $shipping_details = $order->get_items('shipping');
            foreach ($shipping_details as $item_id => $shipping_item_obj) {
                $this->shipping_method_title = $shipping_item_obj->get_method_title();
                $this->shipping_method_id = $shipping_item_obj->get_method_id();
                $this->shipping_method_total = $shipping_item_obj->get_total();
                $this->get_formatted_meta_data = $shipping_item_obj->get_formatted_meta_data();
            }

            $this->result_details = [];
            $enit_order_details_table = $wpdb->prefix . "enit_order_details";
            $query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($enit_order_details_table));
            if ($wpdb->get_var($query) == $enit_order_details_table) {
                $this->result_details = $wpdb->get_results(
                    "SELECT * FROM `" . $wpdb->prefix . "enit_order_details` WHERE order_id = '" . $this->order_key . "'", ARRAY_A
                );
            }

            /* Add metabox if user selected our service */
            if (!empty($this->result_details) && count($this->result_details) > 0) {
                /* Add metabox for 3dbin visual details */
                add_meta_box(
                    'en_additional_order_details', __('Additional Order Details', 'woocommerce'), array($this, 'en_add_meta_box_order_widget'), 'shop_order', 'side', 'low', 'core');
            } elseif (!empty($this->get_formatted_meta_data) && count($this->get_formatted_meta_data) > 0) {
                add_meta_box('en_additional_order_details', __('Additional Order Details', 'woocommerce'), array($this, 'en_wwe_small_add_meta_box_order_widget'), 'shop_order', 'side', 'low', 'core');
            }

            return $actions;
        }

        /**
         * Add order details in metabox.
         */
        public function en_wwe_small_add_meta_box_order_widget()
        {
            $order_details = $this->get_formatted_meta_data;
            $this->en_wwe_small_origin_services_details($order_details);
        }

        /**
         * Origin & Services details.
         * @param array $order_data
         */
        function en_wwe_small_origin_services_details($order_data)
        {
            $this->currency_symbol = get_woocommerce_currency_symbol(get_option('woocommerce_currency'));
            $this->count = 0;
            $en_account_details = $min_prices = [];
            $shipment = 'single';
            foreach ($order_data as $key => $is_meta_data) {
                if (isset($is_meta_data->key) && $is_meta_data->key === "min_prices") {
                    $shipment = 'multiple';
                    $min_prices = $is_meta_data->value;
                }
                (isset($is_meta_data->key) && $is_meta_data->key === "en_fdo_meta_data") ? $fdo_meta_data = json_decode($is_meta_data->value, true) : '';
                (isset($is_meta_data->key) && $is_meta_data->key === "en_account_details") ? $en_account_details = json_decode($is_meta_data->value, true) : '';
            }

            if (!empty($en_account_details) && is_array($en_account_details)) {
                echo '<h4 style="text-decoration: underline;margin: 4px 0px 4px 0px;"> Shipment Account Number Details:</h4>';
                echo '<ul class="en-list" style="list-style: disc;list-style-position: inside;">';
                foreach ($en_account_details as $acc_field => $acc_field_val) {
                    echo '<li>' . esc_html($acc_field) . ': ' . esc_html($acc_field_val) . '</li>';
                }
                echo '</ul>';
                return;
            }

            if ($shipment == 'multiple') {
                if (strlen($min_prices) > 0) {
                    $order_data = json_decode($min_prices, TRUE);
                    foreach ($order_data as $key => $quote) {
                        if (isset($quote['meta_data']['min_prices'])) {
                            $order_data_spq = json_decode($quote['meta_data']['min_prices'], true);
                            foreach ($order_data_spq as $key_spq => $quote_spq) {
                                $this->en_get_services_details_through_meta_data($quote_spq);
                            }
                        } else {
                            $this->en_get_services_details_through_meta_data($quote);
                        }
                    }
                }
            } else {
                foreach ($order_data as $key => $value) {
                    $this->get_meta_data_from_rate($value);
                }
                $this->count++;
                $this->shipping_method_title .= ": ";
                $this->show_order_widget_detail();
            }
        }

        /**
         * Get Services details from meta data.
         * @param array $order_data
         */
        function en_get_services_details_through_meta_data($quote)
        {
            $this->get_meta_data_for_mutiple_ship($quote);
            $label = (isset($quote['label']) && strlen($quote['label']) > 0) ? $quote['label'] : "Shipping";
            $this->shipping_method_title = $this->filter_from_label_sufex($this->label_sufex, $label) . ": ";
            $this->shipping_method_total = (isset($quote['cost'])) ? $quote['cost'] : 0;
            $this->count++;
            $this->show_order_widget_detail();
        }

        /**
         * Get data from meta array
         * @param array $meta_data
         */
        public function get_meta_data_for_mutiple_ship($meta_data)
        {
            $this->sender_origin = (isset($meta_data['meta_data']['sender_origin'])) ?
                ucwords($meta_data['meta_data']['sender_origin']) : '';
            $this->accessorials = (isset($meta_data['meta_data']['accessorials'])) ?
                json_decode($meta_data['meta_data']['accessorials'], true) : [];
            $this->product_name = (isset($meta_data['meta_data']['product_name'])) ?
                json_decode($meta_data['meta_data']['product_name'], true) : [];
            $this->label_sufex = (isset($meta_data['label_sufex'])) ?
                $meta_data['label_sufex'] : [];
        }

        /**
         * Get data from meta array
         * @param array $meta_data
         */
        public function get_meta_data_from_rate($meta_data)
        {
            (isset($meta_data->key) && $meta_data->key === 'sender_origin') ?
                $this->sender_origin = ucwords($meta_data->value) : '';
            (isset($meta_data->key) && $meta_data->key === 'accessorials') ?
                $this->accessorials = json_decode($meta_data->value, true) : [];
            (isset($meta_data->key) && $meta_data->key === 'label_sufex') ?
                $this->label_sufex = json_decode($meta_data->value, true) : [];
            (isset($meta_data->key) && $meta_data->key === 'product_name') ?
                $this->product_name = json_decode($meta_data->value, true) : [];
        }

        /**
         * Show Order Detai on order page
         */
        public function show_order_widget_detail()
        {
            if (!strlen($this->sender_origin) > 0) {
                return;
            }
            $this->label_sufex = isset($this->label_sufex) && is_array($this->label_sufex) ? $this->label_sufex : [];
            $this->accessorials = isset($this->accessorials) && is_array($this->accessorials) ? $this->accessorials : [];
            echo '<h4 style="text-decoration: underline;margin: 4px 0px 4px 0px;">Shipment ' . esc_html($this->count) . " > Origin & Services </h4>";
            echo '<ul class="en-list" style="list-style: disc;list-style-position: inside;">';
            echo '<li>';

            echo esc_attr($this->sender_origin);

            echo '<br />';

            echo '</li>';

            echo '<li>' . esc_attr($this->shipping_method_title) . $this->en_format_price($this->shipping_method_total) . '</li>';

            /* Show accessorials */
            $this->en_wwe_small_show_accessorials(array_unique(array_merge($this->accessorials, $this->label_sufex)));

            echo "</ul>";
            echo "<br />";
            echo '<h4 style="text-decoration: underline;margin: 4px 0px 4px 0px;">Shipment ' . esc_html($this->count) . " > items </h4>";
            echo '<ul id="product-details-order" class="en-list" style="list-style: disc;list-style-position: inside;">';

            foreach (array_filter($this->product_name) as $product_str) {
                echo '<li>' . esc_attr($product_str) . '</li>';
            }

            echo '</ul>';
            echo "<br /><br />";
        }

        /**
         * set accessorials in label of rate
         * @param array $label_sufex
         * @param string $label
         * @return string
         */
        public function filter_from_label_sufex($label_sufex, $label)
        {
            $accessorials = [
                'L' => 'liftgate delivery',
                'T' => 'tailgate delivery',
            ];

            if (strpos($label, 'residential delivery') == false) {
                $accessorials['R'] = 'residential delivery';
            }

            $label_sufex = is_array($label_sufex) ? $label_sufex : [];
            $label_sufex = array_intersect_key($accessorials, array_flip($label_sufex));
            $label .= (!empty($label_sufex)) ? ' with ' . implode(' and ', $label_sufex) : '';
            return $label;
        }

        /**
         * Show accessorial on order detail page.
         * @param array $service_order_data
         */
        public function en_wwe_small_show_accessorials($service_order_data)
        {
            foreach ($service_order_data as $key => $value) {
                echo ($value === 'R') ? '<li>Residential delivery</li>' : "";
                echo ($value == 'L') ? '<li>Lift gate delivery</li>' : "";
                echo ($value == 'H') ? '<li>Hazardous Material</li>' : "";
                echo ($value == 'HAT') ? '<li>Hold At Terminal</li>' : "";
            }
        }

        /**
         * Add order details in metabox.
         */
        public function en_add_meta_box_order_widget()
        {

            /* In case of single shipment remove index 0 */
            if (count($this->result_details) == 1) {
                $order_details = reset($this->result_details);
            }

            /* In case of multishipment */
            if (count($this->result_details) > 1) {
                $order_details = $this->en_return_multiship_row($this->result_details);
            }

            /* Check multi-shipment or single-shipment */
            if (!is_array(json_decode($order_details['data'], true))) {
                /* Multishipment case */
                $this->shipment_status = 'multishipment';
                $this->en_multi_shipment_order($order_details, $this->shipment_status, $this->order_key);
            } elseif (is_array(json_decode($order_details['data'], true))) {
                /* singleshipment case */
                $this->shipment_status = 'single';
                $single_price_details['ship_details'] = array(
                    'title' => $this->shipping_method_title,
                    'id' => $this->shipping_method_id,
                    'rate' => $this->shipping_method_total,
                );
                $this->en_single_shipment_order($order_details, $this->shipment_status, $single_price_details);
            }
        }

        /**
         * Return the multiship row.
         */
        public function en_return_multiship_row($details)
        {
            foreach ($details as $key => $value) {
                $data = json_decode($value['data']);

                if (is_string($data)) {
                    return $value;
                }
            }
            return false;
        }

        /**
         * Single shipment order details.
         * @param array $order_details
         * @param string $shipment_status
         * @param array $single_price_details
         */
        function en_single_shipment_order($order_details, $shipment_status, $single_price_details)
        {
            if (isset($this->get_formatted_meta_data) && (!empty($this->get_formatted_meta_data))) {
                $get_formatted_meta_data = reset($this->get_formatted_meta_data);

                if (isset($get_formatted_meta_data->key) && $get_formatted_meta_data->key == "hazardous" && $get_formatted_meta_data->value > 0) {
                    $order_details_data = json_decode($order_details['data'], TRUE);
                    $order_details_data['accessorials']['hazardous'] = $get_formatted_meta_data->value;
                    $order_details['data'] = json_encode($order_details_data);
                }
            }

            $ship_count = 1;
            $service_details = reset($order_details);

            $this->en_origin_services_details($order_details, $shipment_status, $ship_count, $single_price_details);
        }

        /**
         * Multi shipment order details.
         * @param array $order_details
         * @param string $shipment_status
         * @param string $order_key
         * @global object $wpdb
         */
        function en_multi_shipment_order($order_details, $shipment_status, $order_key)
        {

            global $wpdb;
            $cheapest_ids = explode(", ", $order_details['data']);
            $ship_count = 1;
            foreach ($cheapest_ids as $key => $value) {
                $service_id = str_replace('"', "", $value);
                $service_details = $this->en_get_service_details_by_id($service_id, $order_key);
                $this->en_origin_services_details($service_details[0], $shipment_status, $ship_count);
                $ship_count++;
                /* Horizontal line */
                echo "<hr>";
            }
        }

        /**
         * Get service details from id.
         * @param int $id
         * @param string $order_key
         * @return array
         * @global object $wpdb
         */
        function en_get_service_details_by_id($id, $order_key)
        {

            global $wpdb;
            $result_details = $wpdb->get_results(
                "SELECT * FROM `" . $wpdb->prefix . "enit_order_details` WHERE `service_id` = '" . $id . "' AND order_id = '" . $order_key . "'", ARRAY_A
            );
            return $result_details;
        }

        /**
         * Origin & Services details.
         * @param array $order_data
         * @param string $shipment_status
         * @param int $ship_count
         * @param array $single_price_details
         */
        function en_origin_services_details($order_data, $shipment_status, $ship_count, $single_price_details = array())
        {

            $this->currency_symbol = get_woocommerce_currency_symbol(get_option('woocommerce_currency'));
            $code = $order_data['service_id'];
            $service_order_data = json_decode($order_data['data']);
            $accessorials = array();
            if (isset($service_order_data->accessorials->hazardous) && $service_order_data->accessorials->hazardous > 0) {
                $accessorials = $service_order_data->accessorials;
            }

            /* Check handling fee */
            $this->en_check_accessorials($service_order_data, $shipment_status);
            /* In case of single shipment reset the array */
            if ($shipment_status == 'single') {
                $service_order_data = reset($service_order_data);
                if (!empty($accessorials)) {
                    $service_order_data->accessorials = $accessorials;
                }
            }
            echo '<h4 style="text-decoration: underline;margin: 4px 0px 4px 0px;">Shipment ' . esc_html($ship_count) . " > Origin & Services </h4>";
            echo '<ul class="en-list" style="list-style: disc;list-style-position: inside;">';
            echo '<li>';
            echo ucwords($service_order_data->origin->location) . ', ';
            echo esc_html($service_order_data->origin->zip) . ', ';
            echo esc_html($service_order_data->origin->city) . ', ';
            echo esc_html($service_order_data->origin->state) . ', ';
            echo esc_html($service_order_data->origin->country) . "<br />";
            echo '</li>';
            /* Run in case of multishipment only */
            if ($shipment_status != 'single') {
                if (
                    isset($service_order_data->accessorials->R) &&
                    $service_order_data->accessorials->R == 'R'
                ) {
                    if (isset($service_order_data->cheapest_services->title) && $service_order_data->cheapest_services->title != '') {
                        $resd = '(R) ';
                        $title = $service_order_data->cheapest_services->title . ' : ';
                    } else {
                        $resd = '';
                        $title = '';
                    }
                    /* Run in case of single shipment inside multishipment only */
                    if (isset($service_order_data->cheapest_services->rate)) {
                        echo '<li>';
                        echo esc_html($title) . ' ' . esc_html($resd) . ' ' . esc_html($this->en_format_price($service_order_data->cheapest_services->rate));
                        echo '</li>';
                    } else {
                        echo '<li>';
                        echo esc_html($title) . ' ' . esc_html($resd) . ' ' . esc_html($this->currency_symbol) . '0.00';
                        echo '</li>';
                    }
                } else {

                    if (isset($service_order_data->cheapest_services->title) && $service_order_data->cheapest_services->title != '') {
                        $resd = $service_order_data->cheapest_services->title . ' : ';
                    } else {
                        $resd = '';
                    }
                    /* Run in case of single shipment inside multishipment only */
                    if (isset($service_order_data->cheapest_services->rate)) {
                        echo '<li>';
                        echo esc_html($resd) . '  ' . esc_html($this->en_format_price($service_order_data->cheapest_services->rate));
                        echo '</li>';
                    } else {
                        echo '<li>';
                        echo esc_html($resd) . '  ' . esc_html($this->currency_symbol) . '0.00';
                        echo '</li>';
                    }
                }
            } else {
                if (isset($single_price_details['ship_details'])) {
                    /* Run in case of single shipment only */
                    echo '<li>' . esc_html($single_price_details['ship_details']['title']) . ' : ' . esc_html($this->en_format_price($single_price_details['ship_details']['rate'])) . '</li>';
                }
            }
            /* Show accessorials */
            $this->en_show_accessorials($service_order_data);
            echo "</ul>";
            echo "<br />";
            echo '<h4 style="    text-decoration: underline;margin: 4px 0px 4px 0px;">Shipment ' . esc_html($ship_count) . " > items </h4>";
            echo '<ul id="product-details-order" class="en-list" style="list-style: disc;list-style-position: inside;">';
            foreach ($service_order_data->items as $value) {
                /* Check for variations */
                $product_name = wc_get_product($value->productId);
                echo '<li>' . esc_html($value->productQty) . ' x ' . esc_html($product_name->get_name()) . '</li>';
            }
            echo '</ul>';
            echo "<br /><br />";
        }

        /**
         * Price format.
         * @param int/double/string $dollars
         * @return string
         */
        function en_format_price($dollars)
        {
            return $this->currency_symbol . number_format(sprintf('%0.2f', preg_replace("/[^0-9.]/", "", $dollars)), 2);
        }

        /**
         * Show accessorial.
         */
        public function en_show_accessorials($service_order_data)
        {

            $residential_del = get_option('wc_settings_quest_as_residential_delivery_wwe_small_packages');
            if (
                (isset($residential_del) &&
                    $residential_del == 'yes') || (isset($service_order_data->accessorials->R) &&
                    $service_order_data->accessorials->R == 'R')
            ) {
                echo '<li>Residential Delivery</li>';
            }

            if (isset($service_order_data->accessorials->hazardous) && $service_order_data->accessorials->hazardous > 0) {
                echo '<li>Hazardous Material</li>';
            }
        }

        /**
         * Check accessorial.
         * @param array $service_order_data
         * @param string $shipment_status
         */
        public function en_check_accessorials($service_order_data, $shipment_status)
        {

            /* In case of singleshipment */
            if ($shipment_status == 'single') {
                $service_order_data = reset($service_order_data);
            }
            if (isset($service_order_data->handling_fee) && $service_order_data->handling_fee == 1) {
                $this->handling_fee = 1;
            }
        }
    }

    /* Initialize class object */
    new En_WWE_Order_Widget_Details();
}
    