<?php

/**
 * Includes Ajax Request class
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooBoxAddonsAjaxReqIncludes")) {

    class EnWooBoxAddonsAjaxReqIncludes extends EnWooBoxAddonsInclude
    {

        public $plugin_standards;
        public $selected_plan;
        public $sm_box_sizing_old_type;
        public $en_box_sizing_nickname;
        public $en_box_sizing_width;
        public $en_box_sizing_weight;
        public $en_box_sizing_length;
        public $en_box_sizing_height;
        public $en_box_sizing_max_weight;
        public $fedex_box_type;
        public $en_box_sizing_action;
        public $en_box_sizing_row_id;
        public $en_box_sizing_available;
        public $post_title;
        public $meta_key;
        public $post_meta;
        public $wp_post_id;
        public $success;
        public $postId;
        public $En_Woo_Addon_Box_Size_Detection_Template;
        public $en_box_sizing_usps_box_fee;
        public $en_box_sizing_usps_box_type;
        public $en_post_type;
        public $en_multipackage_product_id;
        public $sm_box_sizing_quantity;

        /**
         * Constructer
         */
        public function __construct()
        {
            /**
             * Box sizing ajax request
             */
            add_action('wp_ajax_nopriv_en_woo_addons_upgrade_plan_submit_box', array($this, 'en_woo_box_addons_upgrade_plan_submit'));
            add_action('wp_ajax_en_woo_addons_upgrade_plan_submit_box', array($this, 'en_woo_box_addons_upgrade_plan_submit'));

            //        end box sizing ajax request 
            /**
             * box sizing table save ajax request
             */
            add_action('wp_ajax_nopriv_en_box_sizing_submit', array($this, 'en_box_sizing_submit'));
            add_action('wp_ajax_en_box_sizing_submit', array($this, 'en_box_sizing_submit'));

            /**
             * or box sizing table save ajax request
             */
            add_action('wp_ajax_nopriv_or_box_sizing_submit', array($this, 'or_box_sizing_submit'));
            add_action('wp_ajax_or_box_sizing_submit', array($this, 'or_box_sizing_submit'));

            /**
             * or box sizing table save ajax request
             */
            add_action('wp_ajax_nopriv_or_get_box_sizing_details', array($this, 'or_get_box_sizing_details'));
            add_action('wp_ajax_or_get_box_sizing_details', array($this, 'or_get_box_sizing_details'));

            /**
             * box sizing table update available anchor
             */
            add_action('wp_ajax_nopriv_en_box_update_available', array($this, 'en_box_update_available'));
            add_action('wp_ajax_en_box_update_available', array($this, 'en_box_update_available'));

            //       end box sizing table update available 

            /**
             * box sizing table delete ajax request
             */
            add_action('wp_ajax_nopriv_en_box_sizing_delete', array($this, 'en_box_sizing_delete'));
            add_action('wp_ajax_en_box_sizing_delete', array($this, 'en_box_sizing_delete'));

            //       end box sizing table delete ajax request 
            /**
             * Suspend automatic detection of box sizing.
             */
            add_action('wp_ajax_nopriv_suspend_automatic_detection_box', array($this, 'suspend_automatic_detection_box'));
            add_action('wp_ajax_suspend_automatic_detection_box', array($this, 'suspend_automatic_detection_box'));

            //       end box sizing table delete ajax request
            /**
             * Suspend automatic detection of box sizing.
             */
            add_action('wp_ajax_nopriv_en_add_box_sizing_one_rate', array($this, 'en_add_box_sizing_one_rate'));
            add_action('wp_ajax_en_add_box_sizing_one_rate', array($this, 'en_add_box_sizing_one_rate'));
        }

        /**
         * Box sizing table save data ajax request
         */
        public function en_box_update_available()
        {

            $this->wp_post_id = (isset($_POST['postId'])) ? sanitize_text_field($_POST['postId']) : "";
            $this->en_box_sizing_available = (isset($_POST['availableLabel'])) ? sanitize_text_field($_POST['availableLabel']) : "";
            $wp_post = array("ID" => $this->wp_post_id, "post_content" => $this->en_box_sizing_available);
            $update = wp_update_post($wp_post, true);
            echo json_encode(array('success' => true, 'message' => $update));
            die();
        }

        /**
         * Box sizing table delete data ajax request
         */
        public function en_box_sizing_delete($post_id = "")
        {

            $this->postId = (isset($_POST['postId'])) ? sanitize_text_field($_POST['postId']) : $post_id;
            $delete_post = wp_delete_post($this->postId, true);

            if (!isset($post_id)) {
                echo json_encode($delete_post);
                die();
            }
        }

        /**
         * Nickname available already response
         */
        public function en_box_sizing_available_nickname_response()
        {

            echo json_encode(array('available_response' => true, 'en_post_type' => $this->en_post_type));
            die;
        }

        public function en_add_box_sizing_one_rate()
        {

            $args = array('post_type' => array("or_box_sizing"), 'posts_per_page' => -1);
            $query = new WP_Query($args);
            $fedex_one_rate_old_data = array();

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $postId = get_the_ID();

                    $value = get_post_meta($postId, 'or_box_sizing', true);
                    $fedex_one_rate_old_data[$value['en_box_array_order_id']] = $postId;
                }
            }

            ob_start();
?>
            </form>
            <div class="sm_add_warehouse_popup">
                <h2 class="one-rate-label">
                    Choose your FedEx One Rate Boxes
                </h2>

                <form method="POST" id="or_add_box_sizing">
                    <?php
                    (!class_exists("EnWooAddonFedexOneRate")) ? include('en-woo-box-addons-fedex-one-rate.php') : "";
                    $EnWooAddonFedexOneRate = new EnWooAddonFedexOneRate();
                    $fedex_one_rate_data = $EnWooAddonFedexOneRate->fedex_one_rate_data();
                    $fedex_one_rate_img = $EnWooAddonFedexOneRate->fedex_one_rate_img();

                    $count = 0;
                    echo '<div class="one-rate">';
                    foreach ($fedex_one_rate_data as $key => $one_rate) {

                        $one_rate_pckg_type = (isset($one_rate['5'])) ? sanitize_text_field($one_rate['5']) : "";
                        echo '<div class="one-rate-block block-' . $count . ' ' . $one_rate_pckg_type . '">';

                        if (isset($fedex_one_rate_img[$key])) {
                            echo '<img src="' . SPEED_EN_LOAD_SBS_FEDEX_IMAGES . $fedex_one_rate_img[$key] . '" height="60" width="75"/>';
                        }

                        $nickname = end($one_rate);

                        $checked = (isset($fedex_one_rate_old_data[$key])) ? "checked='checked'" : "";
                        $slice_me = ($nickname == "FedEx Envelope" || $nickname == "FedEx Reusable Envelope") ? 2 : 3;
                        echo "<input type='checkbox' id='add_box_packg_id_$key' name='one_rate[]' value='" . $key . "' " . $checked . "/>  " . $nickname . ' (' . implode(" x ", array_slice($one_rate, 0, $slice_me)) . ')<br>';

                        echo '</div>';

                        $count++;
                    }
                    ?>
            </div>

            <div style="clear: both;"></div>
            <input type="hidden" id="fedex_one_rate_data" name="fedex_one_rate_data" value='<?php echo json_encode($fedex_one_rate_data); ?>' />
            <input type="hidden" id="fedex_one_rate_old_data" name="fedex_one_rate_old_data" value='<?php echo json_encode($fedex_one_rate_old_data); ?>' />
            <input type="reset" id="reset" style="display:none" />
            <div class="del_btns sm_add_box_sizing_input">
                <a style="cursor: pointer" onclick="or_add_box_cancel()" class="or_add_box_cancel">Cancel</a>
                <a style="cursor: pointer" onclick="or_add_box_submit()" class="or_add_box_submit">Save</a>
            </div>
            </form>
            </div>
<?php
            echo json_encode(ob_get_clean());
            die();
        }

        /**
         * Data saved from popup form fields box sizes
         */
        public function en_box_sizing_data_saved()
        {
            if (($this->post_title == 0) || ($this->post_title > 0 && $this->en_box_sizing_row_id == $this->post_title)) {
                $this->wp_post_id = $this->en_box_sizing_row_id;
                $wp_post = array("ID" => $this->wp_post_id, "post_title" => $this->en_box_sizing_nickname, "post_content" => $this->en_box_sizing_available);
                wp_update_post($wp_post, true);
                update_post_meta($this->wp_post_id, $this->meta_key, $this->post_meta);
                $this->success = true;
            } else {
                $this->en_box_sizing_available_nickname_response();
            }
        }

        /**
         * Data updated from popup form fields box sizes
         */
        public function en_box_sizing_data_update()
        {

            if ($this->post_title == 0) {
                $wp_post = array(
                    "post_title" => $this->en_box_sizing_nickname,
                    "post_content" => $this->en_box_sizing_available,
                    "post_excerpt" => 'custom_post',
                    /* Multiple Package */
                    "post_type" => $this->en_post_type,
                    "post_status" => 'publish',
                );

                $this->wp_post_id = wp_insert_post($wp_post, true);
                add_post_meta($this->wp_post_id, $this->meta_key, $this->post_meta);
                $this->success = true;
            } else {
                $this->en_box_sizing_available_nickname_response();
            }
        }

        /**
         * box sizing details.
         */
        public function or_get_box_sizing_details()
        {
            $en_selected_fedex_box = (isset($_POST['en_selected_fedex_box'])) ? sanitize_text_field($_POST['en_selected_fedex_box']) : '';
            (!class_exists("EnWooAddonFedexOneRate")) ? include('en-woo-box-addons-fedex-one-rate.php') : "";
            $EnWooAddonFedexOneRate = new EnWooAddonFedexOneRate();
            $fedex_one_rate_data = $EnWooAddonFedexOneRate->fedex_one_rate_data();
            if (is_array($fedex_one_rate_data) && !empty($fedex_one_rate_data) && isset($fedex_one_rate_data[$en_selected_fedex_box])) {
                $fedex_one_rate_box_details = $fedex_one_rate_data[$en_selected_fedex_box];

                $selection_for_box_name = [
                    'sm_box_sizing_length',
                    'sm_box_sizing_width',
                    'sm_box_sizing_height',
                    // Outer Box
                    'sm_box_outer_sizing_length',
                    'sm_box_outer_sizing_width',
                    'sm_box_outer_sizing_height',
                    // Outer Box End
                    'sm_box_sizing_max_weight',
                    'sm_box_sizing_weight',
                    'sm_box_size_type',
                    'en_bos_array_order_id',
                    'sm_box_sizing_nickname',
                ];

                // Get result
                $fedex_one_rate_box_detail = array_combine($selection_for_box_name, array_intersect_key($fedex_one_rate_box_details, $selection_for_box_name));
                $response = ['message' => $fedex_one_rate_box_detail];
                echo json_encode($response);
                die();
            }
        }

        /**
         * en_box_sizing_submit
         */
        public function or_box_sizing_submit()
        {

            $form_data = array();
            $response = array();
            parse_str(sanitize_text_field($_POST['form_data']), $form_data);

            $fedex_one_rate_old_data = (isset($form_data['fedex_one_rate_old_data'])) ? json_decode($form_data['fedex_one_rate_old_data'], TRUE) : array();

            if (isset($form_data['one_rate'], $form_data['fedex_one_rate_data'])) {
                $box_sizing_table_row = "";
                $fedex_one_rate_data = (json_decode($form_data['fedex_one_rate_data'], TRUE));
                foreach ($form_data['one_rate'] as $key => $one_rate) {
                    if (!isset($fedex_one_rate_old_data[$one_rate])) {
                        $value = $fedex_one_rate_data[$one_rate];

                        $this->en_box_sizing_nickname = (isset($value['7'])) ? sanitize_text_field($value['7']) : "";
                        $this->en_box_sizing_width = (isset($value['1'])) ? sanitize_text_field($value['1']) : "";
                        $this->en_box_sizing_weight = (isset($value['4'])) ? sanitize_text_field($value['4']) : "";
                        $this->en_box_sizing_length = (isset($value['0'])) ? sanitize_text_field($value['0']) : "";
                        $this->en_box_sizing_height = (isset($value['2'])) ? sanitize_text_field($value['2']) : "";
                        $this->en_box_sizing_max_weight = (isset($value['3'])) ? sanitize_text_field($value['3']) : "";
                        $this->fedex_box_type = (isset($value['5'])) ? sanitize_text_field($value['5']) : "";
                        $en_bos_array_order_id = (isset($value['6'])) ? sanitize_text_field($value['6']) : "";
                        $this->en_box_sizing_available = "Yes";
                        $this->meta_key = "or_box_sizing";
                        $this->post_meta = array(
                            "en_box_nickname" => $this->en_box_sizing_nickname,
                            "en_box_length" => $this->en_box_sizing_length,
                            "en_box_width" => $this->en_box_sizing_width,
                            "en_box_height" => $this->en_box_sizing_height,
                            "en_box_max_weight" => $this->en_box_sizing_max_weight,
                            "en_box_box_weight" => $this->en_box_sizing_weight,
                            "fedex_box_type" => $this->fedex_box_type,
                            "en_box_array_order_id" => $en_bos_array_order_id
                        );


                        $wp_post = array(
                            "post_title" => $this->en_box_sizing_nickname,
                            "post_content" => $this->en_box_sizing_available,
                            "post_excerpt" => 'custom_post',
                            "post_type" => 'or_box_sizing'
                        );

                        $this->wp_post_id = wp_insert_post($wp_post, true);
                        add_post_meta($this->wp_post_id, $this->meta_key, $this->post_meta);

                        $box_sizing_table_row .= $this->box_sizing_table_row("add_box_packaging_click");
                    } else {
                        unset($fedex_one_rate_old_data[$one_rate]);
                    }
                }
            }

            foreach ($fedex_one_rate_old_data as $key => $value) {
                $this->en_box_sizing_delete($value);
            }

            (strlen($box_sizing_table_row) > 0) ? $response['message'] = $box_sizing_table_row : "";
            (!empty($fedex_one_rate_old_data) > 0) ? $response['delete'] = $fedex_one_rate_old_data : "";

            echo json_encode($response);
            die();
        }

        /**
         * en_box_sizing_submit
         */
        public function en_box_sizing_submit()
        {

            $form_data = array();
            parse_str(sanitize_text_field($_POST['form_data']), $form_data);

            /* Multiple Package */
            $this->en_post_type = 'box_sizing';
            $this->sm_box_sizing_quantity = FALSE;
            if (isset($form_data['en_multipackage_post_type'])) {
                $this->en_multipackage_product_id = (isset($form_data['en_multipackage_product_id'])) ? sanitize_text_field($form_data['en_multipackage_product_id']) : "";
                $this->sm_box_sizing_quantity = (isset($form_data['sm_box_sizing_quantity'])) ? sanitize_text_field($form_data['sm_box_sizing_quantity']) : "";
                $this->en_post_type = 'en_multi_packaging';
            }

            $this->fedex_box_type = (isset($form_data['fedex_box_type'])) ? sanitize_text_field($form_data['fedex_box_type']) : "";
            $this->sm_box_sizing_old_type = (isset($form_data['sm_box_sizing_old_type'])) ? sanitize_text_field($form_data['sm_box_sizing_old_type']) : "";
            $this->en_box_sizing_nickname = (isset($form_data['sm_box_sizing_nickname'])) ? sanitize_text_field($form_data['sm_box_sizing_nickname']) : "";
            $this->en_box_sizing_usps_box_type = (isset($form_data['sm_box_size_type'])) ? sanitize_text_field($form_data['sm_box_size_type']) : "";
            $this->en_box_sizing_weight = (isset($form_data['sm_box_sizing_weight'])) ? sanitize_text_field($form_data['sm_box_sizing_weight']) : "";
            $this->en_box_sizing_length = (isset($form_data['sm_box_sizing_length'])) ? sanitize_text_field($form_data['sm_box_sizing_length']) : "";
            $this->en_box_sizing_width = (isset($form_data['sm_box_sizing_width'])) ? sanitize_text_field($form_data['sm_box_sizing_width']) : "";
            $this->en_box_sizing_height = (isset($form_data['sm_box_sizing_height'])) ? sanitize_text_field($form_data['sm_box_sizing_height']) : "";
            // Outer Box
            $this->en_box_outer_sizing_length = (isset($form_data['sm_box_outer_sizing_length']) && strlen($form_data['sm_box_outer_sizing_length']) > 0) ? sanitize_text_field($form_data['sm_box_outer_sizing_length']) : 0;
            $this->en_box_outer_sizing_width = (isset($form_data['sm_box_outer_sizing_width']) && strlen($form_data['sm_box_outer_sizing_width']) > 0) ? sanitize_text_field($form_data['sm_box_outer_sizing_width']) : 0;
            $this->en_box_outer_sizing_height = (isset($form_data['sm_box_outer_sizing_height']) && strlen($form_data['sm_box_outer_sizing_height']) > 0) ? sanitize_text_field($form_data['sm_box_outer_sizing_height']) : 0;
            // End Outer Box
            $this->en_box_sizing_max_weight = (isset($form_data['sm_box_sizing_max_weight'])) ? sanitize_text_field($form_data['sm_box_sizing_max_weight']) : "";
            $this->en_box_sizing_action = (isset($form_data['sm_box_sizing_action'])) ? sanitize_text_field($form_data['sm_box_sizing_action']) : "";
            $this->en_box_sizing_row_id = (isset($form_data['sm_box_sizing_row_id'])) ? sanitize_text_field($form_data['sm_box_sizing_row_id']) : "";
            $this->en_box_sizing_available = (isset($form_data['en_box_sizing_available'])) ? "Yes" : "No";
            /* Multiple Package */
            $this->meta_key = $this->en_post_type;
            $this->en_box_sizing_weight = strlen($this->en_box_sizing_weight) > 0 ? $this->en_box_sizing_weight : 0;
            $this->en_box_sizing_weight = strlen($this->en_box_sizing_weight) > 0 ? $this->en_box_sizing_weight : 0;
            $this->en_box_sizing_usps_box_fee = (isset($form_data['sm_box_sizing_fee'])) ? sanitize_text_field($form_data['sm_box_sizing_fee']) : 0;
            $this->en_box_sizing_usps_box_fee = strlen($this->en_box_sizing_usps_box_fee) > 0 ? $this->en_box_sizing_usps_box_fee : 0;

            // One Rate
            $this->post_title = 0;
            $sm_box_sizing_type = (isset($form_data['sm_box_sizing_type'])) ? sanitize_text_field($form_data['sm_box_sizing_type']) : "";

            if ($sm_box_sizing_type == 'fedex_box') {
                $this->meta_key = $this->en_post_type = 'or_box_sizing';

                // Validate one rate box is exists or not already.
                $wp_post = array(
                    "post_type" => $this->en_post_type,
                    'posts_per_page' => -1,
                );

                $wp_post_meta = new WP_Query($wp_post);
                if ($wp_post_meta->have_posts()) {
                    while ($wp_post_meta->have_posts()) {
                        $wp_post_meta->the_post();
                        $postId = get_the_ID();
                        $get_post_meta = get_post_meta($postId, $this->en_post_type, true);
                        $en_box_usps_box_type = (isset($get_post_meta['en_box_usps_box_type'])) ? $get_post_meta['en_box_usps_box_type'] : '';
                        $en_box_usps_box_type = (!isset($get_post_meta['en_box_usps_box_type']) && isset($get_post_meta['en_box_array_order_id'])) ? $get_post_meta['en_box_array_order_id'] : $en_box_usps_box_type;
                        if ($this->en_box_sizing_usps_box_type == $en_box_usps_box_type) {
                            $this->post_title = $postId;
                            continue;
                        }
                    }
                }
            } else {
                $this->post_title = post_exists($this->en_box_sizing_nickname);
            }

            $this->post_meta = array(
                "en_box_nickname" => $this->en_box_sizing_nickname,
                "en_box_length" => $this->en_box_sizing_length,
                "en_box_width" => $this->en_box_sizing_width,
                "en_box_height" => $this->en_box_sizing_height,
                // Outer Box
                "en_box_outer_length" => $this->en_box_outer_sizing_length,
                "en_box_outer_width" => $this->en_box_outer_sizing_width,
                "en_box_outer_height" => $this->en_box_outer_sizing_height,
                // End Outer Box
                "en_box_max_weight" => $this->en_box_sizing_max_weight,
                "en_box_box_weight" => $this->en_box_sizing_weight,
                "en_box_usps_box_type" => $this->en_box_sizing_usps_box_type,
                "en_box_usps_box_fee" => $this->en_box_sizing_usps_box_fee,
                /* Multiple Package */
                "en_multipackage_product_id" => $this->en_multipackage_product_id,
                "en_box_quantity" => $this->sm_box_sizing_quantity,
                "fedex_box_type" => $this->fedex_box_type,
            );

            // $this->sm_box_sizing_old_type
            if (isset($this->en_box_sizing_action) && ($this->en_box_sizing_action != "update_action")) {
                $this->en_box_sizing_data_update();
            } elseif ($this->en_box_sizing_action == "update_action" && $this->sm_box_sizing_old_type != $sm_box_sizing_type) {
                wp_delete_post($this->en_box_sizing_row_id, true);
                $this->en_box_sizing_data_update();
            } else {
                $this->en_box_sizing_data_saved();
            }
            $appendRow = $this->box_sizing_table_row();
            echo json_encode(array('success' => $this->success, 'message' => $appendRow));
            die();
        }

        /**
         * Table row data
         * @param type $class
         * @param type $key
         * @return type
         */
        public function box_sizing_table_row($class = "", $key = "")
        {
            $edit_box_sizing = (strlen($class) > 0) ? 'onclick="add_box_packaging_click()"' : 'onclick="edit_box_sizing(' . $this->wp_post_id . ',' . $this->en_multipackage_product_id . ')"';

            $en_domain = en_sbs_get_domain();
            $is_custom_work_post_id = ($en_domain == 'localhost' || $en_domain == 'mgs4u.com' || $en_domain == 'wpqa2.eniture-qa.com') ? '' : 'style="display: none;"';

            $hide_template_1_action = 'style="display: none;"';
            $hide_template_2_action = '';
            if ($this->sm_box_sizing_quantity) {
                $en_box_sizing_nickname = $this->en_box_sizing_nickname;
                $hide_template_1_action = '';
                $hide_template_2_action = 'style="display: none;"';
            }

            return '<tr id="box_sizing_row_id_' . $this->wp_post_id . '">
                                    <td class="sm_box_sizing_list_data en_box_sizing_id_td" ' . $is_custom_work_post_id . $hide_template_2_action . '>
                                         ' . $this->wp_post_id . '
                                    </td>
                                    <td class="sm_box_sizing_list_data en_box_sizing_quantity_td" ' . $hide_template_1_action . '>
                                         ' . $this->sm_box_sizing_quantity . '
                                    </td>
                                    <td class="sm_box_sizing_list_data en_box_sizing_nickname_td">
                                         ' . $this->en_box_sizing_nickname . '
                                    </td>
                                    <td class="sm_box_sizing_list_data en_box_sizing_length_td">
                                        ' . $this->en_box_sizing_length . '
                                    </td>
                                    <td class="sm_box_sizing_list_data en_box_sizing_width_td">
                                        ' . $this->en_box_sizing_width . '
                                    </td>
                                    <td class="sm_box_sizing_list_data en_box_sizing_height_td">
                                        ' . $this->en_box_sizing_height . '
                                    </td>
                                    
                                    <td class="sm_box_sizing_list_data en_box_outer_sizing_length_td" ' . $hide_template_2_action . '>
                                        ' . $this->en_box_outer_sizing_length . '
                                    </td>
                                    <td class="sm_box_sizing_list_data en_box_outer_sizing_width_td" ' . $hide_template_2_action . '>
                                        ' . $this->en_box_outer_sizing_width . '
                                    </td>
                                    <td class="sm_box_sizing_list_data en_box_outer_sizing_height_td" ' . $hide_template_2_action . '>
                                        ' . $this->en_box_outer_sizing_height . '
                                    </td>
                                    
                                    <td class="sm_box_sizing_list_data en_box_sizing_weight_td" ' . $hide_template_2_action . '>
                                        ' . $this->en_box_sizing_max_weight . '
                                    </td>
                                    <td class="sm_box_sizing_list_data en_box_sizing_max_weight_td">
                                        ' . $this->en_box_sizing_weight . '
                                    </td>
                                    <td class="sm_box_sizing_list_data en_box_usps_box_fee_td">
                                        ' . $this->en_box_sizing_usps_box_fee . '
                                    </td>
                                    <td class="sm_box_sizing_list_data en_box_usps_box_type_td" style="display: none;">
                                        ' . $this->en_box_sizing_usps_box_type . '
                                    </td>
                                    <td class="sm_box_sizing_list_data fedex_box_type" style="display: none;">
                                        ' . $this->fedex_box_type . '
                                    </td>
                                    <td class="sm_box_sizing_list_data en_small_action_available_td" ' . $hide_template_2_action . '>
                                        <a class="available_click" onclick="availableClick(' . "'" . $this->en_box_sizing_available . "'" . ',' . $this->wp_post_id . ',' . $this->en_multipackage_product_id . ')" id="' . $this->wp_post_id . '">' . $this->en_box_sizing_available . '</a>
                                    </td>
                                    <td class="sm_box_sizing_list_data">
                                       <a class="en_small_action_box_sizing ' . $class . '" ' . $edit_box_sizing . ' id="' . $this->wp_post_id . '"> Edit </a> | <a class="en_small_action_box_sizing" onclick="delete_box_sizing(' . $this->wp_post_id . ', ' . $this->en_multipackage_product_id . ')" id="' . $this->wp_post_id . '"> Delete </a>
                                    </td>
                                </tr>';
        }

        /**
         * Auto detect box sizing ajax request.
         */
        public function en_woo_box_addons_upgrade_plan_submit()
        {

            $packgInd = (isset($_POST['selected_plan'])) ? sanitize_text_field($_POST['selected_plan']) : '';
            $plugin_name = (isset($_POST['plugin_name'])) ? sanitize_text_field($_POST['plugin_name']) : '';
            $this->plugin_standards = $plugin_name;
            $this->selected_plan = $packgInd;
            $action = isset($packgInd) && ($packgInd == "disable") ? "d" : "c";
            $EnWooAddonsCurlReqIncludes = new EnWooBoxAddonsCurlReqIncludes();
            $status = $EnWooAddonsCurlReqIncludes->smart_street_api_curl_request($action, $this->plugin_standards, $this->selected_plan);
            $status = json_decode($status, true);
            if (isset($status['severity']) && $status['severity'] == "SUCCESS") {
                if (!class_exists("EnWooAddonBoxSizingTemplate")) {
                    include_once(speed_addon_plugin_url . '/admin/templates/en-woo-addon-box-sizing-template.php');
                }
                $this->En_Woo_Addon_Box_Size_Detection_Template = new EnWooAddonBoxSizingTemplate();
                $status = $this->En_Woo_Addon_Box_Size_Detection_Template->subscription($status);
                $status['severity'] = "SUCCESS";
            }
            echo json_encode($status);
            die();
        }

        public function suspend_automatic_detection_box()
        {

            $options = array();
            $suspend_automatic_detection_of_box_sizing = (isset($_POST['suspend_automatic_detection_of_box_sizing'])) ? sanitize_text_field($_POST['suspend_automatic_detection_of_box_sizing']) : '';
            (isset($suspend_automatic_detection_of_box_sizing) && (!empty($suspend_automatic_detection_of_box_sizing))) ?
                $options["suspend_automatic_detection_of_box_sizing"] = $suspend_automatic_detection_of_box_sizing : "";
            $this->update_db($options);
            echo json_encode($options);
            die();
        }

        /**
         * Update options table.
         * @param array $options
         */
        public function update_db($options)
        {

            if (isset($options) && (is_array($options))) {
                foreach ($options as $options_name => $options_value) {
                    update_option($options_name, $options_value);
                }
            }
        }
    }

    /* Initialize object */
    new EnWooBoxAddonsAjaxReqIncludes();
}
