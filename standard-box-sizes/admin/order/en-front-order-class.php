<?php

/**
 * Front order class.
 * @author Eniture Technologies.
 */
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists("En_Front_Order_Class")) {

    /**
     * Front end order managment class.
     */
    class En_Front_Order_Class
    {

        /**
         * Contains woocommerce session data.
         * @var array
         */
        public $session_data;

        /**
         * Contains order key.
         * @var array
         */
        public $order_key;

        /**
         * Contains order id.
         * @var array
         */
        public $order_id;
        public $service_type;
        public $plugin_name;
        public $service_name;
        public $shipping_meta_data;

        /*
         * Constructor.
         */

        public function __construct()
        {

            /* Woocommerce thankyou hook */
            add_filter(
                'woocommerce_thankyou', array($this, 'en_woocommerce_order_page_handler'), 10, 1
            );
        }

        /**
         * Update the successful order.
         * @param int order_key
         */
        public function en_woocommerce_order_page_handler($order_id)
        {
            $this->fedex_one_rate_service($order_id);

            /* Set 3dbin session data */
            $this->session_data = (isset($this->plugin_name) && ($this->plugin_name == "fedex_small")) ? WC()->session->get('fedex_bin_response') : WC()->session->get('3dbin_response');

            /* One Rate Exist */
            $this->session_data = $this->one_rate_exist();
            /* Set order detials */
            $this->en_set_order_details($order_id);
            /* Insert the post type 3dbin against there order_key */
            $this->en_insert_post_type();
        }

        public function get_meta_service_name($shipping_meta_data)
        {
            foreach ($shipping_meta_data as $key => $value) {
                (isset($value->key) && $value->key == "service_name") ? $this->service_name = $value->value : "";
                (isset($value->key) && $value->key == "service_type") ? $this->service_type = $value->value : "";
                (isset($value->key) && $value->key == "plugin_name") ? $this->plugin_name = $value->value : "";
            }
        }

        public function fedex_one_rate_service($order_id)
        {
            $this->shipping_meta_data = array();

            $order = new WC_Order($order_id);
            /* Get shipping details */
            $shipping_details = $order->get_items('shipping');

            /* Update shipping details */
            foreach ($shipping_details as $item_id => $shipping_item_obj) {
                $this->shipping_meta_data = $shipping_item_obj->get_formatted_meta_data();
            }

            $this->get_meta_service_name($this->shipping_meta_data);
        }

        /**
         * One Rate
         * @param type $order_id
         * @return type
         */
        public function one_rate_exist()
        {
            $session_data = json_decode(json_encode($this->session_data), TRUE);
            $_session_data = array();

            if (is_array($session_data) && !empty($session_data)) {
                $_session_data = reset($session_data);
            }

            $one_rate_boxes_list = array();

            if (isset($_session_data['one_rate_pricing']) || isset($_session_data['home_ground_pricing']) || isset($_session_data['weight_based_pricing'])) {

                /* Get order details */
                $order_details = WC()->session->get('en_order_detail');
                $order_details = (isset($order_details['en_shipping_details'])) ? $order_details['en_shipping_details'] : array();

                $services = isset($order_details['en_fedex_small']['services']) ? $order_details['en_fedex_small']['services'] : array();

                foreach ($services as $service_zip => $value) {
                    if (isset($value['minPrices'])) {
                        foreach ($value['minPrices'] as $service_zip => $minPrices) {
                            isset($minPrices['service_name']) ? $one_rate_boxes_list[$service_zip] = $session_data[$service_zip][$minPrices['service_name']] : "";
                        }
                    } elseif (isset($value['service_name'])) {
                        $service_zip = key($session_data);
                        (isset($value['id'], $value['service_name']) && ($value['id'] == ($this->service_type . "_" . $this->service_name))) ? $one_rate_boxes_list[$service_zip] = $_session_data[$value['service_name']] : "";
                    }
                }
            }

            return (!empty($one_rate_boxes_list)) ? $one_rate_boxes_list : $this->session_data;
        }

        /**
         * Set the order details.
         * @param int order_id
         */
        public function en_set_order_details($order_id)
        {

            $order = new WC_Order($order_id);
            $this->order_id = $order_id;
            $this->order_key = $order->get_order_key();
        }

        /**
         * Insert the post type.
         */
        public function en_insert_post_type()
        {

            if (
                isset($this->order_key) &&
                !empty($this->order_key) &&
                $this->order_id > 0 &&
                !empty($this->session_data)
            ) {
                $result = post_exists($this->order_key);
                /* If same order key not already exists */
                if ($result == 0) {
                    $my_post = array(
                        'post_type' => 'threedbin',
                        'post_title' => $this->order_key,
                        /* Save response in post_content */
                        'post_content' => json_encode($this->session_data),
                        'post_password' => $this->order_key
                    );
                    /* Insert the post into the database */
                    wp_insert_post($my_post);
                } else {
                    /* Order key already exists */
                    return false;
                }
            }
        }

    }

    /* Initialzie object */
    new En_Front_Order_Class();
} 