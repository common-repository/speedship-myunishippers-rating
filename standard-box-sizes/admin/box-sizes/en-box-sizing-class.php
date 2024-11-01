<?php

/**
 *  Box sizing class.
 */
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists("En_Box_Sizing_Class")) {

    class En_Box_Sizing_Class {

        /**
         * Boxes array.
         * @var array
         */
        public $boxes;

        /**
         * Return boxes added by admin.
         */
        public function en_return_all_boxes() {

            $post_meta_data = array();
            $args = array(
                'post_type' => array('box_sizing', 'or_box_sizing'),
                'posts_per_page' => -1,
                'post_status' => 'any'
            );
            $posts_array = get_posts($args);

            if ($posts_array) {
                foreach ($posts_array as $post) {
                    $status = get_post_field('post_content', $post->ID);
                    if ($status == "Yes") { /* If box available */

                        $get_post_meta = get_post_meta($post->ID, 'box_sizing', true);
                        if ($get_post_meta) {
                            $post_meta_data = get_post_meta($post->ID, 'box_sizing', true);
                            $index = "bins";
                        } else {
                            $post_meta_data = get_post_meta($post->ID, 'or_box_sizing', true);
                            $index = "fedex_bins";
                        }

                        if (!empty($post_meta_data)) {
                            $this->en_set_boxes_array($post_meta_data, $post, $index);
                        }
                    }
                }
                wp_reset_postdata();
            }

            if (!empty($this->boxes['bins']) || !empty($this->boxes['fedex_bins'])) {
                return $this->boxes;
            }
            return false;
        }

        /**
         * Set the boxes array.
         * @param array $post_meta_data
         */
        protected function en_set_boxes_array($post_meta_data, $post, $index = "") {

            $max_weight = 0;
            /* Set max-weight */
            if ($post_meta_data['en_box_max_weight'] == 0) {
                $max_weight = 150 - $post_meta_data['en_box_box_weight'];
            } elseif (($post_meta_data['en_box_max_weight'] + $post_meta_data['en_box_box_weight']) > 150) {
                $max_weight = 150 - $post_meta_data['en_box_box_weight'];
            } else {
                $max_weight = $post_meta_data['en_box_max_weight'];
            }
            $this->boxes[$index][$post->ID] = array(
                'nickname' => $post_meta_data['en_box_nickname'],
                'w' => (isset($post_meta_data['en_box_width'])) ? $post_meta_data['en_box_width'] : 0,
                'h' => (isset($post_meta_data['en_box_height'])) ? $post_meta_data['en_box_height'] : 0,
                'd' => (isset($post_meta_data['en_box_length'])) ? $post_meta_data['en_box_length'] : 0,
                // Outer Box
                'o_w' => (isset($post_meta_data['en_box_outer_width'])) ? $post_meta_data['en_box_outer_width'] : 0,
                'o_h' => (isset($post_meta_data['en_box_outer_height'])) ? $post_meta_data['en_box_outer_height'] : 0,
                'o_d' => (isset($post_meta_data['en_box_outer_length'])) ? $post_meta_data['en_box_outer_length'] : 0,
                // End Outer Box
                'id' => $post->ID,
                'max_wg' => $max_weight,
                'box_weight' => $post_meta_data['en_box_box_weight'],
            );

//          Add category type for USPS
            if (isset($post_meta_data['en_box_usps_box_type']) && !empty($post_meta_data['en_box_usps_box_type'])) {
                $box_type = $post_meta_data['en_box_usps_box_type'];

                switch ($box_type) {

                    case 'upm_express_box':
                        $post_meta_data['en_box_usps_box_type'] = 'USPS Priority Mail Express Box';
                        $post_meta_data['box_category'] = 'UMEB';
                        break;

                    case 'upm_box':
                        $post_meta_data['en_box_usps_box_type'] = 'USPS Priority Mail Box';
                        $post_meta_data['box_category'] = 'UPMB';
                        break;

                    case 'upm_large_flat_rate_box':
                        $post_meta_data['en_box_usps_box_type'] = 'USPS Priority Mail Large Flat Rate Box';
                        $post_meta_data['box_category'] = 'UFLAT';
                        break;

                    case 'upm_medium_flat_rate_box':
                        $post_meta_data['en_box_usps_box_type'] = 'USPS Priority Mail Medium Flat Rate Box';
                        $post_meta_data['box_category'] = 'UFLAT';
                        break;

                    case 'upm_small_flat_rate_box':
                        $post_meta_data['en_box_usps_box_type'] = 'USPS Priority Mail Small Flat Rate Box';
                        $post_meta_data['box_category'] = 'UFLAT';
                        break;

                    case 'upm_padded_flat_rate_envelope':
                        $post_meta_data['en_box_usps_box_type'] = 'USPS Priority Mail Padded Flat Rate Envelope';
                        $post_meta_data['box_category'] = 'UFLAT';
                        break;

                    default :
                        $post_meta_data['en_box_usps_box_type'] = '';
                        $post_meta_data['box_category'] = '';
                }
            }

//          Add USPS standard box sizes filters
            if (isset($post_meta_data['box_category']) && !empty($post_meta_data['box_category'])) {
                $this->boxes[$index][$post->ID]['box_category'] = $post_meta_data['box_category'];
            }
            if (isset($post_meta_data['en_box_usps_box_type']) && !empty($post_meta_data['en_box_usps_box_type'])) {
                $this->boxes[$index][$post->ID]['box_type'] = $post_meta_data['en_box_usps_box_type'];
            }
            if (isset($post_meta_data['en_box_usps_box_fee']) && !empty($post_meta_data['en_box_usps_box_fee'])) {
                $this->boxes[$index][$post->ID]['box_price'] = $post_meta_data['en_box_usps_box_fee'];
            }

//           Add FedEx Small standard box sizes filters
            if (isset($post_meta_data['fedex_box_type'])) {
                $this->boxes[$index][$post->ID]['fedex_box_type'] = $post_meta_data['fedex_box_type'];
            }
        }

    }

}