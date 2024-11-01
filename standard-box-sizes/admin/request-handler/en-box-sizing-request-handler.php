<?php

/**
 *  Request hander class. (Singleton class)
 */
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists("En_Box_Sizing_Request_Handler")) {

    class En_Box_Sizing_Request_Handler
    {

        /**
         * Contains products in cart.
         * @var array
         */
        protected $items;

        /**
         * Contains boxes.
         * @var array
         */
        protected $bin;

        /**
         * Grouped package details.
         * @var array
         */
        protected $group_package_details;

        /**
         * Contains user details.
         * @var array
         */
        protected $user_details;

        /**
         * Contains final request.
         * @var array
         */
        protected $final_request;

        /**
         * Domain name.
         * @var string
         */
        protected $domain_name;

        /**
         * Add-on codes
         * @var array
         */
        public $addons_codes;

        /**
         * 3dbin status.
         * @var array
         */
        public $status;

        /**
         * Product class object.
         * @var string
         */
        protected $product_class;

        /**
         * Box sizing class.
         * @var object
         */
        protected $box_sizing;

        /**
         * Platform from which request hits.
         *
         * @var string
         */
        protected $platform;

        /**
         * License key of customer.
         *
         * @var string
         */
        public $helper_obj;

        /**
         * 3dbin details.
         * @var object
         */
        public $boxing_3D;

        /**
         * All shipments array.
         *
         * @var array
         */
        public $all_shipments;

        /**
         * Vertical Rotation array.
         *
         * @var array
         */
        public $vertical_rot;

        /**
         * Own Arrangement.
         *
         * @var array
         */
        public $own_arrangement;

        /**
         * Own Arrangement.
         *
         * @var array
         */
        public $item_multiple_package;

        /**
         * Box sizing object.
         *
         * @var string
         */
        public $box_sizing_obj;

        /**
         * Api response.
         *
         * @var array/object
         */
        public $api_response_bin;

        /**
         * Api response.
         *
         * @var array/object
         */

        /**
         * Request key.
         * @var object
         */
        public $EnWooAddonsGenrtRequestKey;

        /**
         *  Request key.
         * @var object
         */
        public $session_request_key;

        /**
         *  Multiple Package
         * @var object
         */
        public $en_quotes_return;
        public $en_multipackage_product;
        public $plugin_id;

        /**
         * Constructor.
         */
        public function __construct()
        {

            $this->helper_obj = new En_Box_Sizing_Helper_Function();
            $this->box_sizing_obj = new En_Box_Sizing_Class();
            $this->EnWooAddonsGenrtRequestKey = new EnWooBoxAddonsGenrtRequestKey();
            $this->product_class = new En_Addon_Products_Option('function_call');
            $this->addons_codes = array();
            $this->platform = 'wordpress';
            $this->domain_name = $this->helper_obj->en_parse_url(
                en_sbs_get_domain()
            );
            $this->en_process_hooks();
        }

        /**
         * Process all hooks related to this class.
         */
        protected function en_process_hooks()
        {
            /* Filter to update the quotes request */
            add_filter('enit_box_sizes_post_array_filter', array($this, 'en_get_group_data'), 10, 4);
            add_filter('enit_box_sizes_post_array_filter_new_api', array($this, 'en_set_product_details_new_api'), 10, 1);
            /* Filter to update the woocommerce session */
            add_filter('en_save_3dbin_session', array($this, 'en_handle_3dbin_session'), 10, 1);
        }

        /**
         * Set the product details.
         *
         */
        public function en_set_product_details_new_api($en_request)
        {
            $this->en_check_bin_admin_status();
            /* If 3dbin is disabled */
            if ($this->status == 'yes') {
                /* Return the same request */
                return $en_request;
            }
            $counter_product = 0;
            foreach ($en_request['commdityDetails'] as $zip => $items) {
                foreach ($items as $key => $item) {
                    $prod_id = $item['productId'];
                    /* Get vertical rotation */
                    $vert_rot = $this->product_class->en_get_vertical_rotation_field($prod_id);
                    /* Get own package  */
                    $own_package = $this->product_class->en_get_own_package_field($prod_id);
                    /* Get own package  */
                    $item_multiple_package = $this->product_class->en_get_multiple_packages_field($prod_id);
                    /* Item multiple Package */
                    $verticalRotation = $en_request['commdityDetails'][$zip][$key]['verticalRotation'] = ($vert_rot == true) ? "1" : "0";
                    $shipAsOwnPackage = $en_request['commdityDetails'][$zip][$key]['shipAsOwnPackage'] = ($own_package == true) ? "1" : "0";
                    $multiPackage = $en_request['commdityDetails'][$zip][$key]['multiPackage'] = ($item_multiple_package == true) ? "1" : "0";

                    $en_mutiple_packages_in_request = apply_filters('en_mutiple_packages_in_request', $multiPackage, $counter_product, $prod_id, $verticalRotation, $item, 'trinet');

                    if (!empty($en_mutiple_packages_in_request)) {
                        if (isset($en_mutiple_packages_in_request['en_quotes_return'])) {
                            $en_request['en_quotes_return'] = $en_mutiple_packages_in_request['en_quotes_return'];
                            unset($en_mutiple_packages_in_request['en_quotes_return']);
                        }

                        if (isset($en_mutiple_packages_in_request['en_box_fee'])) {
                            $en_request['extra_widget_detail'][$zip]['en_box_fee'] = !isset($en_request['extra_widget_detail'][$zip]['en_box_fee']) ? $en_mutiple_packages_in_request['en_box_fee'] : $en_request['extra_widget_detail'][$zip]['en_box_fee'] + $en_mutiple_packages_in_request['en_box_fee'];
                            unset($en_mutiple_packages_in_request['en_box_fee']);
                        }

                        if (isset($en_mutiple_packages_in_request['products'])) {
                            $en_request['extra_widget_detail'][$zip]['products'] = !isset($en_request['extra_widget_detail'][$zip]['products']) ? $en_mutiple_packages_in_request['products'] : $en_request['extra_widget_detail'][$zip]['products'] + $en_mutiple_packages_in_request['products'];
                            unset($en_mutiple_packages_in_request['products']);
                        }

                        if (isset($en_mutiple_packages_in_request['en_multi_box_qty'])) {
                            $en_request['extra_widget_detail'][$zip]['en_multi_box_qty'] = !isset($en_request['extra_widget_detail'][$zip]['en_multi_box_qty']) ? $en_mutiple_packages_in_request['en_multi_box_qty'] : $en_request['extra_widget_detail'][$zip]['en_multi_box_qty'] + $en_mutiple_packages_in_request['en_multi_box_qty'];
                            unset($en_mutiple_packages_in_request['en_multi_box_qty']);
                        }

                        unset($en_request['commdityDetails'][$zip][$key]);
                        $en_request['commdityDetails'][$zip] = $en_request['commdityDetails'][$zip] + $en_mutiple_packages_in_request;
                    }

                    $counter_product++;
                }
            }

            $en_request['binPackaging'] = 1;
            $bins = (isset($this->en_set_bins()['bins'])) ? $this->en_set_bins()['bins'] : [];
            // Check usps small standard box indexes
            foreach ($bins as $key => $bin) {
                (array_key_exists('box_category', $bin)) ? $en_request['usps_bins'][$key] = $bin : $en_request['bins'][$key] = $bin;
            }

            return $en_request;
        }

        /**
         * Handle the DB session.
         * @param: array $response
         */
        public function en_handle_3dbin_session($response)
        {

            $this->api_response_bin = array();

            $this->en_get_api_response($response);
            /* Set or Update session response */
            if (!empty($this->api_response_bin) && $this->api_response_bin != '') {
                WC()->session->set('3dbin_response', $this->api_response_bin);
            } else {
                /* If response is empty set empty */
                $set_empty = '';
                WC()->session->set('3dbin_response', $set_empty);
            }
        }

        /**
         * Get the detauls from plugin API response.
         * @param array $response
         */
        public function en_get_api_response($response)
        {
            ;
            foreach ($response as $zip => $value) {
                if (
                    isset($value->binPackaging->severity) &&
                    $value->binPackaging->severity == "ERROR"
                ) {
                    /* Check for error */
                } elseif (isset($value->binPackaging->response)) {
                    $this->api_response_bin[$zip] = $value->binPackaging->response;
                } //              Set Session for USPS
                elseif (isset($value->binPackagingData)) {
                    $this->api_response_bin[$zip] = $value->binPackagingData;
                }
            }
        }

        /**
         * Get the grouped packages array from plugin.
         *
         * @param array $data contains grouped package array
         * @param array $packages
         * @param string $zip
         */
        public function en_get_group_data($post_data, $packages, $zip, $services_list = array())
        {
            $this->session_request_key = $this->EnWooAddonsGenrtRequestKey->en_woo_addons_genrt_request_key();
            $this->plugin_id = (isset($post_data[$zip]['carrierName'])) ? $post_data[$zip]['carrierName'] : '';

            if (isset($post_data[$zip]['carrierName']) && $post_data[$zip]['carrierName'] == "fedexSmall") {
                require_once dirname(__FILE__) . '/../../one-rate/one-rate-request.php';
                $EnWooAddonBoxSizingOneRateReq = new EnWooAddonBoxSizingOneRateReq();
                $post_data = $EnWooAddonBoxSizingOneRateReq->one_rate_post_data($post_data, $packages, $zip, $services_list);
            }

            $this->en_check_bin_admin_status();
            /* If 3dbin is disabled */
            if ($this->status == 'yes') {
                /* Return the same request */
                return $post_data;
            }
            $this->vertical_rot = array();
            $this->own_arrangement = array();
            $this->en_box_fee = array();
            $this->group_package_details = $packages;

            /* Multiple Package */
            $this->en_quotes_return = 'yes';
            $this->en_multipackage_product = 'no';

            if (!empty($post_data) & count($post_data) > 0) {
                $this->en_update_request();
            } else {
                /* If wrong request from plugin */
                return false;
            }
            if (
                isset($this->vertical_rot) &&
                isset($this->own_arrangement)
            ) {
                /* Assign details */
                /* Assign details */
                if (isset($this->session_request_key)) {
                    $post_data[$zip]['requestKey'] = $this->session_request_key;
                    $post_data[$zip]['requestKeySBS'] = $this->session_request_key;
                }

                $post_data[$zip]['en_box_fee'] = $this->en_box_fee;
                $post_data[$zip]['vertical_rotation'] = $this->vertical_rot;
                $post_data[$zip]['shipBinAlone'] = $this->own_arrangement;

                /* Multiple Package */
                $post_data[$zip]['en_quotes_return'] = $this->en_quotes_return;
                $post_data[$zip]['en_multipackage_product'] = $this->en_multipackage_product;

                if (isset($post_data[$zip]['rate_type']['one_rate_pricing']) && $this->en_multipackage_product == 'yes') {
                    unset($post_data[$zip]['rate_type']['one_rate_pricing']);
                }

                if ($this->all_shipments == NULL || count($this->all_shipments) == 0) {
                    $post_data[$zip]['bins'] = array();
                } else {
//                  Check usps small standard box indexes
                    foreach ($this->all_shipments as $key => $single_shipment) {
                        (array_key_exists('box_category', $single_shipment)) ? $post_data[$zip]['usps_bins'][$key] = $single_shipment : $post_data[$zip]['bins'][$key] = $single_shipment;
                    }
                }

                if ($this->fedex_bins == NULL || count($this->fedex_bins) == 0) {
                    $post_data[$zip]['fedex_bins'] = array();
                } else {
                    $post_data[$zip]['fedex_bins'] = $this->fedex_bins;
                }

                $post_data[$zip]['binPackaging'] = '1';

                $post_data[$zip] = apply_filters('en_mutiple_packages_update_request', $post_data[$zip], $this->item_multiple_package);
            }

            $this->en_quotes_return == 'no' ? $post_data['en_quotes_return'] = 'yes' : '';

            return $post_data;
        }

        /**
         * Update request for 3dbin.
         *
         */
        protected function en_update_request()
        {
            $this->en_set_bins();
            $this->en_set_product_details();
        }

        /**
         * Set bins index.
         *
         */
        public function en_set_bins()
        {
            $this->bin = $this->box_sizing_obj->en_return_all_boxes();
            return $this->bin;
        }

        /**
         * Set the product details.
         *
         */
        public function en_set_product_details()
        {
            $counter_product = 0;
            $this->all_shipments = (isset($this->bin['bins'])) ? $this->bin['bins'] : [];
            $this->fedex_bins = (isset($this->bin['fedex_bins'])) ? $this->bin['fedex_bins'] : [];
            $this->group_package_details['items'] = isset($this->group_package_details['items']) ? $this->group_package_details['items'] : [];
            foreach ($this->group_package_details['items'] as $item) {

                $prod_id = $item['productId'];
                /* Get vertical rotation */
                $vert_rot = $this->product_class->
                en_get_vertical_rotation_field($prod_id);
                /* Get own package  */
                $own_package = $this->product_class->
                en_get_own_package_field($prod_id);
                /* Get own package  */
                $item_multiple_package = $this->product_class->
                en_get_multiple_packages_field($prod_id);
                /* Item multiple Package */
                $this->en_box_fee[$counter_product] = "0";
                $this->vertical_rot[$counter_product] = ($vert_rot == true) ? "1" : "0";
                $this->own_arrangement[$counter_product] = ($own_package == true) ? "1" : "0";

                /* Multiple Package */
                $this->item_multiple_package[$counter_product] = ($item_multiple_package == true) ? "1" : "0";
                $en_mutiple_packages_in_request = apply_filters('en_mutiple_packages_in_request', $this->item_multiple_package[$counter_product], $counter_product, $prod_id, $this->vertical_rot[$counter_product], $item, $this->plugin_id);

                if (isset($en_mutiple_packages_in_request['en_quotes_return']) && $en_mutiple_packages_in_request['en_quotes_return'] == 'no') {
                    $this->en_quotes_return = $en_mutiple_packages_in_request['en_quotes_return'];
                }

                $this->en_multipackage_product = (isset($en_mutiple_packages_in_request['en_multipackage_product'])) ? 'yes' : 'no';

                $this->item_multiple_package[$counter_product] = $en_mutiple_packages_in_request;

                $counter_product++;
            }
        }

        /**
         * Check 3dbin status.
         *
         */
        public function en_check_bin_admin_status()
        {

            $this->status = get_option('suspend_automatic_detection_of_box_sizing');
        }

    }

    /* Initialize object */
    new En_Box_Sizing_Request_Handler();
}

