<?php

/**
 *  Box sizes template 
 */
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists("En_Addon_Products_Option")) {

    class En_Addon_Products_Option {

        /**
         * Constructor.
         */
        public function __construct($action) {

            if ($action == 'hooks') {
                $this->en_add_simple_product_hooks();
                $this->en_add_variable_product_hooks();
            }
        }

        /**
         * Add simple product fields.
         */
        public function en_add_simple_product_hooks() {

            /* Add simple product fields */
            add_action(
                    'woocommerce_product_options_shipping', array($this, 'en_show_product_fields'), 100
            );
            add_action(
                    'woocommerce_process_product_meta', array($this, 'en_save_product_fields'), 10
            );
        }

        /**
         * Add variable product fields.
         */
        public function en_add_variable_product_hooks() {

            add_action(
                    'woocommerce_product_after_variable_attributes', array($this, 'en_show_product_fields'), 100, 3
            );
            add_action(
                    'woocommerce_save_product_variation', array($this, 'en_save_product_fields'), 10
            );
        }

        /**
         * Save the simple product fields.
         * @param int $post_id
         */
        function en_save_product_fields($post_id) {

            if (isset($post_id) && $post_id > 0) {
                $var_rot_val = ( isset($_POST['_en_rot_ver'][$post_id]) ) ? sanitize_text_field($_POST['_en_rot_ver'][$post_id]) : "";
                $var_own_pack = ( isset($_POST['_en_own_pack'][$post_id]) ) ? sanitize_text_field($_POST['_en_own_pack'][$post_id]) : "";
                $_en_multiple_packages = ( isset($_POST['_en_multiple_packages'][$post_id]) ) ? sanitize_text_field($_POST['_en_multiple_packages'][$post_id]) : "";
                update_post_meta(
                        $post_id, '_en_rot_ver', esc_attr($var_rot_val)
                );
                update_post_meta(
                        $post_id, '_en_own_pack', esc_attr($var_own_pack)
                );
                update_post_meta(
                        $post_id, '_en_multiple_packages', esc_attr($_en_multiple_packages)
                );
            }
        }

        /**
         * Show product fields in variation and simple product.
         * @param array $loop
         * @param object $variation_data
         * @param object $variation
         */
        function en_show_product_fields($loop, $variation_data = array(), $variation = array()) {

            if (!empty($variation) || isset($variation->ID)) {
                /* Variable products */
                $this->en_product_field_ver_rotation($variation->ID);
                $this->en_product_field_own_package($variation->ID);
                $this->en_product_field_multiple_packages($variation->ID);
            } else {
                /* Simple products */
                $post_id = get_the_ID();
                $this->en_product_field_ver_rotation($post_id);
                $this->en_product_field_own_package($post_id);
                $this->en_product_field_multiple_packages($post_id);
            }
        }

        /**
         * Add vertival rotation checkbox.
         * @global $wpdb
         * @param $loop
         * @param $variation_data
         * @param $variation
         */
        function en_product_field_ver_rotation($post_id) {

            $field_array = array(
                'id' => '_en_rot_ver[' . $post_id . ']',
                'class' => 'checkbox _en_rot_ver_clicked',
                'label' => __(
                        'Allow vertical rotation', 'woocommerce'
                ),
                'value' => get_post_meta(
                        $post_id, '_en_rot_ver', true
                ),
                'desc_tip' => true,
                'description' => __(
                        'Allow item to be rotated vertically when placing it in a box.', 'woocommerce'
                )
            );
            woocommerce_wp_checkbox($field_array);
        }

        /**
         * Add own package checkbox.
         * @global $wpdb
         * @param $loop
         * @param $variation_data
         * @param $variation
         */
        function en_product_field_own_package($post_id) {

            $field_array = array(
                'id' => '_en_own_pack[' . $post_id . ']',
                'class' => 'checkbox _en_own_pack_clicked',
                'label' => __(
                        'Ship as own package', 'woocommerce'
                ),
                'value' => get_post_meta(
                        $post_id, '_en_own_pack', true
                ),
                'desc_tip' => true,
                'description' => __(
                        'This item ships as its own package.', 'woocommerce'
                )
            );
            woocommerce_wp_checkbox($field_array);
        }

        /**
         * Add own package checkbox.
         * @global $wpdb
         * @param $loop
         * @param $variation_data
         * @param $variation
         */
        function en_product_field_multiple_packages($post_id) {

            $field_array = array(
                'id' => '_en_multiple_packages[' . $post_id . ']',
                'class' => 'checkbox _en_multiple_packages_clicked',
                'label' => __(
                        "This item ships as <br>multiple packages", 'woocommerce'
                ),
                'value' => get_post_meta(
                        $post_id, '_en_multiple_packages', true
                ),
            );
            woocommerce_wp_checkbox($field_array);
        }

        /**
         * Get the vertical rotataion.
         */
        public function en_get_vertical_rotation_field($prod_id) {

            $vertical_rotation = get_post_meta($prod_id, '_en_rot_ver', true);
            if ($vertical_rotation == 'yes') {
                return true;
            }
            return false;
        }

        /**
         * Get the multiple packages.
         */
        public function en_get_multiple_packages_field($prod_id) {

            $vertical_rotation = get_post_meta($prod_id, '_en_multiple_packages', true);
            if ($vertical_rotation == 'yes') {
                return true;
            }
            return false;
        }

        /**
         * Get the vertical rotataion.
         */
        public function en_get_own_package_field($prod_id) {

            $vertical_rotation = get_post_meta($prod_id, '_en_own_pack', true);
            if ($vertical_rotation == 'yes') {
                return true;
            }
            return false;
        }

    }

    /* Initialize object */
    new En_Addon_Products_Option('hooks');
}