<?php

/**
 *  Box sizes One Rate Request
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooAddonBoxSizingOneRateReq")) {

    class EnWooAddonBoxSizingOneRateReq {

        public function or_box_sizing_data() {
            $or_box_sizing = FALSE;

            $args = array(
                'post_type' => 'or_box_sizing',
                'posts_per_page' => -1,
                'post_status' => 'any'
            );

            $posts_array = get_posts($args);

            if ($posts_array) {
                foreach ($posts_array as $post) {
                    $status = get_post_field('post_content', $post->ID);
                    (isset($status) && ($status == "Yes")) ? $or_box_sizing = TRUE : "";
                }
            }

            return $or_box_sizing;
        }

        public function box_sizing_data() {
            $box_sizing = FALSE;

            $args = array(
                'post_type' => 'box_sizing',
                'posts_per_page' => -1,
                'post_status' => 'any'
            );

            $posts_array = get_posts($args);

            if ($posts_array) {
                foreach ($posts_array as $post) {
                    $status = get_post_field('post_content', $post->ID);
                    (isset($status) && ($status == "Yes")) ? $box_sizing = TRUE : "";
                }
            }

            return $box_sizing;
        }

        public function one_rate_post_data($post_data, $packages, $zip, $services_list) {
            $suspend_automatic_detection = get_option('suspend_automatic_detection_of_box_sizing');

            if (($this->or_box_sizing_data() || !empty($services_list['one_rate_services'])) && $suspend_automatic_detection != "yes") {

                $fedex_one_rate = array();

                if (!empty($services_list['one_rate_services'])) {
                    $fedex_one_rate['one_rate_pricing'] = 1;
                    $post_data[$zip]['fedex_bins'] = array();
                }

                $domestic_services = $services_list['domestic_services'];

                if (isset($domestic_services['GROUND_HOME_DELIVERY']) || (isset($domestic_services['FEDEX_GROUND']))) {
                    $fedex_one_rate['home_ground_pricing'] = 1;
                }

                if (isset($services_list['domestic_services']['GROUND_HOME_DELIVERY']))
                    unset($services_list['domestic_services']['GROUND_HOME_DELIVERY']);
                if (isset($services_list['domestic_services']['FEDEX_GROUND']))
                    unset($services_list['domestic_services']['FEDEX_GROUND']);

                if (!empty($services_list['intrntal_services']) || !empty($services_list['domestic_services'])) {
                    $fedex_one_rate['weight_based_pricing'] = 1;
                }

                if (isset($post_data[$zip]['senderCountry'], $post_data[$zip]['receiverCountry']) && $post_data[$zip]['senderCountry'] == $post_data[$zip]['receiverCountry']) {
                    $post_data[$zip]['rate_type'] = $fedex_one_rate;
                }
            }

            return $post_data;
        }

    }

}
    

