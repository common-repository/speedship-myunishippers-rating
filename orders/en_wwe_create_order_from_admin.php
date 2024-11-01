<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Wwe_Small_Order_From_Admin')) 
{
    class Wwe_Small_Order_From_Admin 
    {
        public function __construct()
        {
            add_action('wp_ajax_nopriv_en_wwe_small_admin_order_quotes', array($this, 'en_wwe_small_admin_order_quotes'));
            add_action('wp_ajax_en_wwe_small_admin_order_quotes', array($this, 'en_wwe_small_admin_order_quotes'));
        }
        
        public function en_wwe_small_admin_order_quotes()
        {
            global $woocommerce;
            $errors = array();

            $order_id = ( isset($_POST['order_id']) ) ? sanitize_text_field($_POST['order_id']) : '';
            $bill_zip = ( isset($_POST['bill_zip']) ) ? sanitize_text_field($_POST['bill_zip']) : '';
            $ship_zip = ( isset($_POST['ship_zip']) ) ? sanitize_text_field($_POST['ship_zip']) : '';
            
            (strlen($ship_zip) > 0 || strlen($bill_zip) > 0) ? "" : $errors[] = "Please enter billing or shipping address.";
            
            $order = new WC_Order( $order_id );

            $items = $order->get_items();
            
            (isset($woocommerce->cart) && !empty($woocommerce->cart)) ? $woocommerce->cart->empty_cart() : "";
            
            foreach ($items as $item) 
            {
                $product_id = (isset($item['variation_id']) && !empty($item['variation_id']))?$item['variation_id'] : $item['product_id'];
                $woocommerce->cart->add_to_cart($product_id, $item['qty']);
                $cart = array('contents' => $woocommerce->cart->get_cart($product_id));

            }
            
            ((isset($cart['contents'])) && empty($cart['contents']) || (empty($items))) ? $errors[] = "Empty shipping cart content." : "";
            
            if(!empty($errors))
            {
                echo json_encode(array('errors' => $errors));
                exit();
            }
            
            $WC_speedship = new WC_speedship();
            $response = $this->sort_asec_order_arr($WC_speedship->calculate_shipping($cart));
            
            $response = current($response);
            
            echo json_encode(isset($response['cost'],$response['label']) ? array('label' => $response['label'] , 'cost' => $response['cost']) : array('errors' => array("No Quotes return.")));
            
            exit();
        }
        
        /**
        * sort array
        * @param array type $rate
        * @return array type
        */
        public function sort_asec_order_arr($rate)
        {
            if(is_array($rate) && (count($rate) > 0))
            {
                 $price_sorted_key = array();
                 foreach ($rate as $key => $cost_carrier) {
                     $price_sorted_key[$key] = (isset($cost_carrier['cost'])) ? $cost_carrier['cost'] : 0;
                 }
                 array_multisort($price_sorted_key, SORT_ASC, $rate);
            }

            return $rate;
        }
    }
    
    new Wwe_Small_Order_From_Admin();
}
    
    