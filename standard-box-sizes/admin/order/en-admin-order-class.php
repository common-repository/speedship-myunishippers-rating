<?php

/**
 * Class related to admin order.
 *
 * @author Eniture Technology
 */
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists("En_Admin_Order_Class")) {

    /**
     * Admin order class.
     */
    class En_Admin_Order_Class
    {

        /**
         * Current order id.
         * @var int
         */
        public $order_id;

        /**
         * Current order key.
         * @var string
         */
        public $order_key;

        /**
         * Current order bin details.
         * @var array
         */
        public $order_bin_details;

        /**
         * API response.
         * @var array
         */
        public $api_response;
        public $total_box = 0;
        public $package_count = 0;
        public $item_per_shipment;
        public $unpacked_wt_dims;
        public $carrier_from_meta = '';

        /**
         * Bin response.
         * @var array
         */
        public $bin_response;

        /**
         * Item details.
         * @var array
         */
        public $item_details;

        /**
         * Session data.
         * @var array
         */
        public $session_data;

        /**
         * Set status.
         * @var int
         */
        public $set_status;

        /**
         * Suspend status.
         * @var int
         */
        public $suspend;

        /**
         *  carrier name
         * @var string
         */
        public $carrier;

        /**
         *  Shipping title
         * @var string
         */
        public $shipping_method_title;
        public $shipment_zip;
        public $shipping_method_title_for_usps;

        /**
         * Constructor.
         */
        public function __construct()
        {
            $this->en_call_hooks();
        }

        /**
         * Call hooks.
         */
        public function en_call_hooks()
        {

            /* Woocommerce order action hook */
            add_action('woocommerce_order_actions', array($this, 'en_create_meta_box'), 10, 1);
            /* Action hook to get & set response from API */
            add_action('en_box_sizing_response', array($this, 'en_set_api_response'), 10, 1);
            /* Set bin response */
            $this->en_set_bin_response();
        }

        /**
         * Set API response.
         * @param array $response
         */
        public function en_set_api_response($response)
        {
            if (!empty($response)) {
                $this->api_response = $response;
            }
        }

        /**
         * Set Bin response.
         */
        public function en_set_bin_response()
        {
            if (!empty($this->api_response)) {
                $this->order_bin_details = $response;
            }
        }

        /**
         * Adding Meta container admin shop_order pages
         * @param string/array $actions
         */
        public function en_create_meta_box($actions)
        {
            $bin_packaging = [];
            $order_id = get_the_ID();
            $order = wc_get_order($order_id);
            $shipping_details = $order->get_items('shipping');
            foreach ($shipping_details as $item_id => $shipping_item_obj) {
                $this->result_details = $shipping_item_obj->get_formatted_meta_data();
                $this->shipping_method_title_for_usps[] = $shipping_item_obj->get_method_title();
            }

            $order_data = $this->result_details;
            $order_data = isset($order_data) && is_array($order_data) ? $order_data : [];
            $shipment = 'single';
            $en_data = $min_prices = false;
            foreach ($order_data as $key => $is_meta_data) {

                if (isset($is_meta_data->key) && $is_meta_data->key === "min_prices") {
                    $min_prices = true;
                    $shipment = 'multiple';
                    $order_data = $is_meta_data->value;
                }

                if (isset($is_meta_data->key) && $is_meta_data->key === "en_data") {
                    $en_data = true;
                    $shipment = 'multiple';
                    $order_data = $is_meta_data->value;
                }
            }

            if ($shipment == 'multiple') {
                if ($en_data) {
                    $order_data = json_decode($order_data, TRUE);
                    foreach ($order_data as $key => $meta_data) {
                        (isset($meta_data['bin_packaging']) && !empty($meta_data['bin_packaging'])) ? $bin_packaging[] = json_decode($meta_data['bin_packaging']) : '';
                    }
                } else if ($min_prices) {
                    $order_data = json_decode($order_data, TRUE);
                    $order_data = !empty($order_data) && is_array($order_data) ? $order_data : [];
                    $this->shipping_method_title_for_usps = [];
                    foreach ($order_data as $key => $meta_data) {
                        (isset($meta_data['meta_data']['bin_packaging']) && !empty($meta_data['meta_data']['bin_packaging'])) ? $bin_packaging[] = json_decode($meta_data['meta_data']['bin_packaging']) : '';
                        (isset($meta_data['meta_data']['plugin_name'])) ? $this->carrier_from_meta = $meta_data['meta_data']['plugin_name'] : '';
                        (isset($meta_data['service_code'])) ? $this->shipping_method_title_for_usps[] = $meta_data['service_code'] : '';
                    }
                }
            } else {
                foreach ($order_data as $key => $meta_data) {
                    (isset($meta_data->key) && $meta_data->key === 'bin_packaging') ? $bin_packaging[] = json_decode($meta_data->value) : '';
                    (isset($meta_data->key) && $meta_data->key === 'plugin_name') ? $this->carrier_from_meta = $meta_data->value : '';
                }
            }

            if (!empty($bin_packaging)) {
                $this->set_status = 1;
                $this->order_id = $order_id;
                $this->bin_response = $bin_packaging;
                $this->en_add_meta_box();
                return $actions;
            } else {
                $this->shipping_method_title_for_usps = [];
            }

            /* Set order_id property */
            $this->en_get_set_order_id();

            /* If selected service is one of our plugin */
            $this->en_set_status_bin();
            if (isset($this->order_key) && !empty($this->order_key) && $this->order_id > 0 && isset($this->set_status) && $this->set_status == 1) {
                /* Get order details */
                $this->en_get_order_key_3dbin_details();
            }

            if ($this->order_id > 0 && isset($this->order_id) && isset($this->set_status) && $this->set_status == 1) {
                /* Get the current order details 
                 * $this->en_get_order_details();                
                 *
                  /* Add metabox function */
                $this->en_add_meta_box();
            }

            return $actions;
        }

        /**
         * Metabox for packaging id suspended.
         */
        public function en_assign_box_details_for_suspend()
        {

            echo '<p>No packaging found for this order.</p>';
        }

        /**
         * Get the post type 3dbin details against order key.
         */
        public function en_get_order_key_3dbin_details()
        {

            if (isset($this->set_status) && $this->set_status == 1) {
                $order_details = get_page_by_title(/* Get post by title */
                    $this->order_key, /* post_title */ 'ARRAY_A', /* output_type */ 'threedbin' /* post_type */
                );
                if (isset($order_details['post_content']) && !empty($order_details['post_content'])) {
                    $this->bin_response = json_decode($order_details['post_content']);
                }
            }
        }

        /**
         * Check if our service selected.
         */
        public function en_set_status_bin()
        {

            global $wpdb;

            $result_details = [];
            $enit_order_details_table = $wpdb->prefix . "enit_order_details";
            $query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($enit_order_details_table));
            if ($wpdb->get_var($query) == $enit_order_details_table) {
                $result_details = $wpdb->get_results(
                    "SELECT * FROM `" . $wpdb->prefix . "enit_order_details` WHERE order_id = '" . $this->order_key . "'", ARRAY_A
                );
            }

            if (!empty($result_details) || count($result_details) > 0) {
                $this->set_status = 1;
            }
        }

        /**
         * Add metaboxes.
         */
        public function en_add_meta_box()
        {
            /* Add metabox for 3dbin visual details */
            add_meta_box('en_order_visual_bin_details', __('Packaging Details', 'woocommerce'), array($this, 'en_assign_box_details'), 'shop_order', 'normal', 'core');
        }

        /**
         * Assign metabox data.
         */
        public function en_assign_box_details()
        {
            if (isset($this->bin_response) && !empty($this->bin_response)) {
                $this->en_get_bins_packed();
            } else {
                /* In case of LTL */
                echo '<p>No packaging found for this order.</p>';
            }
        }

        public function packed_item_shipment($shipment_array, $shipment_origin)
        {
            $shipment = 'single';
            $count = 0;
            $dims = '';
            $packed_dims = NULL;
            $index = 't';
            $index_count = 0;

            foreach ($shipment_array as $key => $bin_detail) {
                $bin_data_w = (isset($bin_detail->bins_packed[$index_count]->bin_data->w)) ? $bin_detail->bins_packed[$index_count]->bin_data->w : 0;
                if (empty($bin_detail->bins_packed[$index_count]->bin_data->type) && ($bin_data_w != 0)) {
                    $index = $index . $index_count;
                    $this->item_per_shipment[$index] = count($bin_detail->bins_packed);
                    $index_count = $index_count + 1;
                    $index = 't';
                }
                $count = $count + 1;
            }
        }

        /**
         * Get the bins packed
         */
        public function en_get_bins_packed()
        {

            $count = 1;
            $packaging_output = "";
            $total_count = 0;
            $shipment = '';
            $unpacked_shipment = '';
            $bins_array = array();

//          Get shipping method title for USPS
            $order_id = get_the_ID();
            $order = new WC_Order($order_id);
            $shipping_details = $order->get_items('shipping');
            if (isset($shipping_details) && !empty($shipping_details)) {
                foreach ($shipping_details as $item_id => $shipping_item_obj) {
                    $this->shipping_method_title = $shipping_item_obj->get_method_title();
                }
            }

            $shipment_array = (array)$this->bin_response;

            $shipment_origin = array_keys($shipment_array);

//          Get Carrier Name for USPS

            if (isset($shipment_array) && !empty($shipment_array)) {
                $shipment_array = current($shipment_array);

                $this->carrier = isset($shipment_array->carrier) && !empty($shipment_array->carrier) ? $shipment_array->carrier : '';
            }

            (strlen($this->carrier_from_meta) > 0 && $this->carrier_from_meta == 'usps_small') ? $this->carrier = 'usps' : '';
            $bins_array = $this->arrange_bins_response_order($this->bin_response);

//          get count of each shipment
            if (isset($bins_array['1'])) {
                $this->packed_item_shipment($shipment_array, $shipment_origin);
            }
            if (isset($bins_array['3'])) {
                $this->unpacked_wt_dims = count($bins_array['3']);

                $this->unpacked_wt_dims = isset($this->unpacked_wt_dims) && ($this->unpacked_wt_dims > 1) ? 'multiple' : 'single';
            }

            $shipment = isset($shipment_origin) && (count($shipment_origin) == 1) ? 'single' : 'multiple';

//          check shipment of unpacked items who have dims
            if (isset($bins_array['2']) && count($bins_array['2']) > 1 && ($bins_array['2']['0']->bin_data->w != 0)) {
                $dims = NULL;
                foreach ($bins_array['2'] as $value) {
                    if ($dims == NULL) {
                        $dims = $value->bin_data->w;
                    } else {
                        if ($value->bin_data->w == $dims && $shipment != 'multiple') {
                            $unpacked_shipment = 'single';
                        } else {
                            $unpacked_shipment = 'multiple';
                        }
                    }
                }
            }

            foreach ($this->bin_response as $zip => $details) {
                if (isset($details->home_ground_pricing->bins_packed)) {
                    $details = $details->home_ground_pricing;
                } elseif (isset($details->weight_based_pricing->bins_packed)) {
                    $details = $details->weight_based_pricing;
                } elseif (isset($details->one_rate_pricing->bins_packed)) {
                    $details = $details->one_rate_pricing;
                }

                (isset($details->bins_packed) && !empty($details->bins_packed)) ? $total_count = $total_count + $this->getBinDataCount($details->bins_packed) : '';
            }

//          set bins array for USPS
            if (isset($this->carrier) && !empty($this->carrier) && $this->carrier == 'usps') {
                $bins_array = $this->bin_response;
            }

            $shipping_method_title_for_usps_count = 0;
            foreach ($bins_array as $zip => $details) {
                $this->shipment_zip = '_' . $zip;

                $shipping_method_title_for_usps = (isset($this->shipping_method_title_for_usps[$shipping_method_title_for_usps_count])) ? $this->shipping_method_title_for_usps[$shipping_method_title_for_usps_count] : '';

                $packaging_output .= "<div class='en-package-details'>";
                $packaging_output .= $this->en_output_bins_packed($unpacked_shipment, $shipment, $details, $zip, $total_count, $shipping_method_title_for_usps);
                $packaging_output .= "</div>";
                $count++;
                $shipping_method_title_for_usps_count++;
            }
            // echo $packaging_output;
        }

        /**
         * Bins response.
         * @param object $bins_response
         * @return array
         */
        function arrange_bins_response_order($bins_response)
        {
            $sorted_bins_resp = array();

            foreach ($bins_response as $zip => $details) {

                if (isset($details->home_ground_pricing->bins_packed)) {
                    $details = $details->home_ground_pricing;
                } elseif (isset($details->weight_based_pricing->bins_packed)) {
                    $details = $details->weight_based_pricing;
                } elseif (isset($details->one_rate_pricing->bins_packed)) {
                    $details = $details->one_rate_pricing;
                }

                if (isset($details->bins_packed) && !empty($details->bins_packed)) {
                    foreach ($details->bins_packed as $bins_detail) {
                        if ($bins_detail->bin_data->w == 0 && $bins_detail->bin_data->type == 'item') {
                            $sorted_bins_resp['3'][] = $bins_detail;
                        } else {
                            if (isset($bins_detail->bin_data->type) && $bins_detail->bin_data->type == 'item') {
                                $sorted_bins_resp['2'][] = $bins_detail;
                            } else {
                                $sorted_bins_resp['1'][] = $bins_detail;
                            }
                        }
                    }
                }
            }

            $keys = array();
            isset($sorted_bins_resp['1']) && !empty($sorted_bins_resp['1']) ? $keys['1'] = $sorted_bins_resp['1'] : array();
            isset($sorted_bins_resp['2']) && !empty($sorted_bins_resp['2']) ? $keys['2'] = $sorted_bins_resp['2'] : array();
            isset($sorted_bins_resp['3']) && !empty($sorted_bins_resp['3']) ? $keys['3'] = $sorted_bins_resp['3'] : array();

            return $keys;
        }

        /**
         * Bins count.
         * @param object $details
         * @return bool
         */
        function getBinDataCount($details)
        {
            $binDataCount = 0;
            foreach ($details as $key => $binDetails) {
                $bin_data_count = isset($binDetails->bin_data) && !empty($binDetails->bin_data) && !isset($binDetails->bin_data->w) ? count((array)$binDetails->bin_data) : 1;
                $binDataCount = $binDataCount + $bin_data_count;
            }
            $this->package_count = $this->package_count + $binDataCount;
            return $binDataCount;
        }

        /**
         * Box dimension.
         * @param array $bin_data
         * @return bool
         */
        public function box_dims($bin_data)
        {
            return (isset($bin_data->w, $bin_data->h, $bin_data->d) && !($bin_data->w > 0 || $bin_data->h > 0 || $bin_data->d > 0)) ? TRUE : FALSE;
        }

        /**
         * Bins packets ouput.
         * @param array $bin_details
         * @param string/int $zip
         */
        public function en_output_bins_packed($unpacked_shipment, $shipment, $details, $zip, $total_count, $shipping_method_title_for_usps)
        {

            $main_bin_img = '';
            $item_own_pkg = '';
            $bin_count = 1;
            $unpacked_flag = 0;
            $box_output = "";
            $packed_items = 1;
            $count = 0;
            $index = 't';
            $index_count = 0;
            $previous_dims = NULL;
            $packed_dims = NULL;
            $current_dims = NULL;
            $boxes_output_arr = array();
            $total_complete_img = 0;
            $box_output .= "<div class='per-package'>";

//          find selected service index in usps response
            if (isset($this->carrier) && !empty($this->carrier) && $this->carrier == 'usps') {
                if (isset($details->carrier)) {
                    unset($details->carrier);
                }
                $details = $this->get_usps_services_index($details, $shipping_method_title_for_usps);
            }

            if (isset($details) && !empty($details)) {
                foreach ($details as $key => $bin_details) {

                    $box_output = "";
                    $usps_response_index = "";

                    $dims = $this->box_dims($bin_details->bin_data);

                    switch (TRUE) {
                        case (isset($bin_details->bin_data->type) && $bin_details->bin_data->type == "item" && $dims):
                            $index = "These items don't have dimensions and therefore couldn't be placed in a box. Shipping rates for these items were retrieved based on weight only.";
                            $packed_items = 3;
                            break;

                        case (isset($bin_details->bin_data->type) && $bin_details->bin_data->type == "item"):
                            $index = "These items were quoted as shipping as their own package.";
                            $packed_items = 2;
                            break;
                        default :
                            $index = "Packed items";
                            $packed_items = 1;
                            break;
                    }

                    $main_bin_img = $bin_details->image_complete;

                    if ($packed_items == 2 || $packed_items == 3) {
                        $dims = $bin_details->items['0']->w;

                        if ($packed_items == 2) {
                            $dims = $bin_details->items['0']->w . $bin_details->items['0']->h . $bin_details->items['0']->d;
                        }

                        isset($dims) && ($previous_dims == NULL) ? $previous_dims = $dims : '';

                        if ($unpacked_shipment == 'single') {
                            (!isset($boxes_output_arr[$packed_items])) ? $box_output .= "<h4 class='packed_items'>$index</h4>" : "";
                        } else {
                            isset($unpacked_flag) && ($unpacked_flag == 0) ? $box_output .= "<h4 class='packed_items'>$index</h4>" : '';
                            $unpacked_flag = 1;
                            if ($dims == $previous_dims && ($current_dims == NULL)) {
                                $current_dims = $previous_dims;
                            } elseif ($current_dims != $dims) {
                                $current_dims = $dims;
                                $box_output .= "<h4 class='packed_items'>$index</h4>";
                            }
                        }
                        $box_output .= '<div class="en-package-' . $bin_count . ' unpacked_item_parent unpacked_setting">';
                        $box_output .= '<div class="unpacked_item_child">';
                        $product_name = (isset($bin_details->bin_data->product_name)) ? $bin_details->bin_data->product_name : '';

                        if ($packed_items != 3) {
                            $box_output .= '<div class="en-product-steps-details">';
                            $box_output .= "<span class='set_position'>" . $product_name . "</span> <br>";
                            $box_output .= "<span class='en-prdouct-steps-dimensions'>" . $bin_details->bin_data->d . ' x ' . $bin_details->bin_data->w . ' x ' . $bin_details->bin_data->h . "</span>";
                            $box_output .= '</div>';
                            $box_output .= '<img  class="package-complete-image-tag image_setting" src="' . $main_bin_img . '" />';
                        } else {
                            $box_output .= '<img  class="package-complete-image-tag image_setting_no_dims" src="' . $main_bin_img . '" />';
                            $box_output .= '<div class="en-product-steps-details">';
                            $box_output .= '<span class="en_product_weight"> ' . $product_name . ' </span> ';
                            $box_output .= '<span class="en_product_weight"> Weight = ' . $bin_details->bin_data->weight . ' lbs</span> ';
                            $box_output .= '</div>';
                        }
                        $box_output .= '</div>';
                        $box_output .= '</div>';
                    } else {
                        $dims = $bin_details->items['0']->w;
                        isset($dims) && ($previous_dims == NULL) ? $previous_dims = $dims : '';
                        if ($shipment == 'single') {
                            (!isset($boxes_output_arr[$packed_items])) ? $box_output .= "<h4 class=''>$index</h4>" : "";
                        } else {
                            if (($dims == $previous_dims && ($current_dims == NULL)) || (isset($this->item_per_shipment['t' . ($bin_count - 1)]) && $this->item_per_shipment['t' . ($bin_count - 1)] == $bin_count)) {
                                $current_dims = $previous_dims;
                                $bin_count = isset($this->item_per_shipment['t' . ($bin_count - 1)]) && ($this->item_per_shipment['t' . ($bin_count - 1)] == $bin_count) ? 1 : $bin_count;
                                $box_output .= "<h4 class=''>$index</h4>";
                            } elseif ($current_dims != $dims) {
                                $current_dims = $dims;
                                $box_output .= "<h4 class=''>$index</h4>";
                            }
                        }

                        $total_count = isset($shipment, $this->item_per_shipment['t' . ($bin_count - 1)]) && ($shipment != "single") ? $this->item_per_shipment['t' . ($bin_count - 1)] : $total_count;

//                      For USPS count box
                        $total_count = (isset($this->carrier) && !empty($this->carrier) && $this->carrier == 'usps') ? 1 : $total_count;

                        $box_output .= '<div class="en-package-' . $bin_count . ' packed_items">';
                        $box_output .= '<div class="en-full-row">';
                        $box_output .= '<div class="en-left before-steps-info">';
                        $box_output .= '<p class="reduce_space"><b>Box ' . $bin_count . ' of ' . $total_count . '</b></p>';
                        $box_output .= '<p class="reduce_space_total_item"><b>Number of items: ' . count($bin_details->items) . '</b></p>';
                        $box_output .= '<p class="reduce_space_total_item box-prod-title">' . get_the_title($bin_details->bin_data->id) . ' <strong>' . $item_own_pkg . '</strong></p>';
                        $box_output .= '<div class="package-dimensions align_pkg_dims">'
                            . '<p>' . $bin_details->bin_data->d . ' x ' . $bin_details->bin_data->w . ' x ' . $bin_details->bin_data->h . '</p>'
                            . '</div>';
                        $box_output .= '</div>';

                        $bin_count = isset($this->item_per_shipment['t' . ($bin_count - 1)]) && ($this->item_per_shipment['t' . ($bin_count - 1)] == $bin_count) ? 0 : $bin_count;

                        $box_output .= '<div class="package-complete-image align_pkg">';
                        $box_output .= '<img class="package-complete-image-tag" src="' . $main_bin_img . '" />';
                        $box_output .= '</div>';
                        $box_output .= '</div>';
                        $box_output .= '</div>';
                        $box_output .= $this->en_output_items_packed($bin_details, $zip);
                    }
                    $bin_count++;


                    (isset($boxes_output_arr[$packed_items])) ? $boxes_output_arr[$packed_items] .= $box_output : $boxes_output_arr[$packed_items] = $box_output;
                }
            }
            $box_output = implode("", $boxes_output_arr);
            return $box_output;
        }

        /**
         * Get usps service index in response
         * return response index
         */
        public function get_usps_services_index($response, $shipping_method_title_for_usps)
        {
            global $wpdb, $post;
            $usps_array = array();
            $usps_response = (array)$response;

//          check for mutliple shipment
            $order = new WC_Order($post->ID);
            $order_key = $order->order_key;

            $result_details = $wpdb->get_results(
                "SELECT * FROM `" . $wpdb->prefix . "enit_order_details` WHERE `service_id` = '" . $this->shipment_zip . "' AND order_id = '" . $order_key . "'", ARRAY_A
            );
            $result_details = json_decode($result_details[0]['data']);
            $data = isset($result_details->cheapest_services) ? $result_details->cheapest_services : '';
            $this->shipping_method_title = isset($data->title) ? $data->title : '';

            $this->shipping_method_title = $this->carrier == 'usps' && !empty($this->shipping_method_title_for_usps) ? $shipping_method_title_for_usps : $this->shipping_method_title;

            $custom_box = (array_key_exists('customBoxes', $usps_response)) ? $response->customBoxes : '';

            switch ($this->shipping_method_title) {

                case $this->shipping_method_title == 'Retail Ground':
                    $usps_array = (array_key_exists('customBoxes', $usps_response)) ? $response->customBoxes : '';
                    break;
                case $this->shipping_method_title == 'First-Class Package International Service':
                    $usps_array = (array_key_exists('customBoxes', $usps_response)) ? $response->customBoxes : '';
                    break;
                case $this->shipping_method_title == 'First Class Mail':
                    $usps_array = (array_key_exists('customBoxes', $usps_response)) ? $response->customBoxes : '';
                    break;
                case $this->shipping_method_title == 'Priority Mail':
                    $usps_array = (array_key_exists('UPMB', $usps_response)) ? $response->UPMB : $custom_box;
                    break;
                case $this->shipping_method_title == 'Priority Mail International':
                    $usps_array = (array_key_exists('UPMB', $usps_response)) ? $response->UPMB : $custom_box;
                    break;
                case $this->shipping_method_title == 'Priority Mail Express':
                    $usps_array = (array_key_exists('UMEB', $usps_response)) ? $response->UMEB : $custom_box;
                    break;
                case $this->shipping_method_title == 'Priority Mail International Express':
                    $usps_array = (array_key_exists('UMEB', $usps_response)) ? $response->UMEB : $custom_box;
                    break;
                case $this->shipping_method_title == 'Priority Mail Flat Rate':
                    $usps_array = (array_key_exists('UFLAT', $usps_response)) ? $response->UFLAT : '';
                    break;
                case $this->shipping_method_title == 'Priority Mail International Flat Rate Box':
                    $usps_array = (array_key_exists('UFLAT', $usps_response)) ? $response->UFLAT : '';
                    break;
                default :
                    $usps_array = array();
                    break;
            }

            return $usps_array;
        }

        /**
         * Items packet details.
         * @param array $bin_details
         * @param string/int $zip
         */
        public function en_output_items_packed($bin_details, $zip)
        {

            $box_output = "";
            $box_output .= '<div class="package-steps-block">';
            $box_output .= "<p class='packed_items'><strong>Steps:</strong></p>";
            $total_items_packet = count($bin_details->items);
            $item_image = '';
            /* Items packed details */
            foreach ($bin_details->items as $item_details) {
                $product_title = wc_get_product($item_details->id);
                $product_title = (isset($product_title) && (!empty($product_title))) ? $product_title->get_name() : "NA";
                $box_output .= '<div class="package-steps-product">';
                $box_output .= '<img class="en-prduct-steps-image" src="' . $item_details->image_sbs . '" />';
                $box_output .= '<div class="en-product-steps-details">';
                $product_name = (isset($item_details->product_name)) ? $item_details->product_name : '';
                $box_output .= '<p class="en-prdouct-steps-dimensions">' . $product_name . '</p>';
                $box_output .= '<p class="en-prdouct-steps-dimensions">' . $item_details->d . ' x ' . $item_details->w . ' x ' . $item_details->h . '</p>';
                $box_output .= '</div>';
                $box_output .= '</div>';
            }
            /* Clear the float effect */
            $box_output .= '<div class="en-clear"></div>';
            $box_output .= '</div>';
            return $box_output;
        }

        /**
         *  Count the items.
         * @param array $details
         */
        public function en_total_items_count($details)
        {

            $items = 0;
            foreach ($details->bins_packed as $d) {
                $items += count($d->items);
            }
            return $items;
        }

        /**
         * Get-Set the order details.
         */
        public function en_get_set_order_id()
        {

            $this->order_id = get_the_ID();
            $order = new WC_Order($this->order_id);
            $this->order_key = $order->get_order_key();
        }

    }

    /* Initialize object */
    new En_Admin_Order_Class();
}