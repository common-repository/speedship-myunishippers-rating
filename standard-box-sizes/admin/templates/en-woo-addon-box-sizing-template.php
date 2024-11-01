<?php

/**
 *  Box sizes template
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooAddonBoxSizingTemplate")) {

    class EnWooAddonBoxSizingTemplate extends EnWooBoxSizeAddonsFormHandler
    {

        public $subscriptionInfo;
        public $subscribedPackage;
        public $subscribedPackageHitsStatus;
        public $nextSubcribedPackage;
        public $statusRequestTime;
        public $subscriptionStatus;
        public $status;
        public $EnWooAddonsAjaxReqIncludes;
        public $EnWooAddonsCurlReqIncludes;
        public $reset_always_threed;
        public $reset_always_threed_id;
        public $settings;
        public $next_subcribed_package;
        public $subscription_details;
        public $lastUsageTime;
        public $subscription_packages_response;
        public $box_sizing_text_fields_arr;
        public $fedex_one_rate_old_data = [];
        /* Multiple Package */
        public $query = [];
        public $sbs_recursive = '';

        /**
         * name of plugin.
         * @var string
         */
        public $plugin_name;

        public function __construct()
        {

            $this->EnWooAddonsAjaxReqIncludes = new EnWooBoxAddonsAjaxReqIncludes();
            $this->EnWooAddonsCurlReqIncludes = new EnWooBoxAddonsCurlReqIncludes();
            $this->box_sizing_text_fields_arr = $this->en_woo_addons_box_sizing_text_fields_arr();
            add_action('woocommerce_settings_wc_settings_quote_section_end_box_sizing_after', array($this, 'en_woo_addons_box_sizing_table'));
        }

        /**
         * Revoke SBS template for multiple times
         * @return type array
         */
        public function en_sbs_recursive($sbs_recursive)
        {
            $sbs_recursive[] = $this->sbs_recursive;
            return $sbs_recursive;
        }

        /**
         * box sizing table html
         * @return type
         */
        public function en_woo_addons_box_sizing_table($product_id = 0, $query = [])
        {
            $this->sbs_recursive = 'sbs_template';
            $sbs_recursive = apply_filters('en_sbs_recursive', []);
            if (!empty($sbs_recursive) && in_array($this->sbs_recursive, $sbs_recursive) && !$product_id > 0) {
                return;
            }

            add_filter('en_sbs_recursive', [$this, 'en_sbs_recursive'], 10, 1);
            /* Multiple Package */
            $this->en_box_size_notifications_html($product_id);
?>

            <div class="add_box">

                <?php
                if ($product_id > 0) {
                    echo '<a class="woocommerce-add-button add_multi_box_popup_click" data-en_multi_pckg_product_id="' . esc_attr($product_id) . '">Add Box</a>';
                } else {
                    echo '<a class="woocommerce-add-button add_box_popup_click" >Add Box</a>';
                    do_action('fedex_small_detected', $this->plugin_name);
                }
                ?>

            </div>

            <?php
            if ($product_id > 0) {
                $box_table_header = $multi_box_package_th = ['Quantity', 'Box Nickname', 'Length (in)', 'Width (in)', 'Height (in)', 'Weight (LBS)', 'Box Fee', 'Action'];
                echo '<table class="en_box_sizing_list" id="en_multiple_package_num_' . esc_attr($product_id) . '" >';
                echo '<thead>';
                echo '<tr>';
                foreach ($box_table_header as $key => $table_head) {
                    echo '<th class="en_box_sizing_list_heading">';
                    echo esc_html($table_head);
                    echo '</th>';
                }
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

            } else {
                echo '<table class="en_box_sizing_list" id="en_multiple_package_num_' . esc_attr($product_id) . '" >';
                echo '<thead>';
                // First row
                echo '<tr>';
                $en_domain = en_sbs_get_domain();
                $is_custom_work = ($en_domain == 'localhost' || $en_domain == 'mgs4u.com' || $en_domain == 'wpqa2.eniture-qa.com') ? true : false;
                if ($is_custom_work) {
                    echo '<th class="en_box_sizing_list_heading" rowspan="2">Box ID</th>';
                }
                echo '<th class="en_box_sizing_list_heading" rowspan="2">Nickname</th>';
                echo '<th class="en_box_sizing_list_heading" colspan="3">Interior Dimensions (in)</th>';
                echo '<th class="en_box_sizing_list_heading" colspan="3">Exterior Dimensions (in)</th>';
                echo '<th class="en_box_sizing_list_heading" rowspan="2">Max Weight (LBS)</th>';
                echo '<th class="en_box_sizing_list_heading" rowspan="2">Box Weight (LBS)</th>';
                echo '<th class="en_box_sizing_list_heading" rowspan="2">Box Fee</th>';
                echo '<th class="en_box_sizing_list_heading" rowspan="2">Available</th>';
                echo '<th class="en_box_sizing_list_heading" rowspan="2">Action</th>';
                echo '</tr>';
                // Second row
                echo '<tr>';
                echo '<th class="en_box_sizing_list_heading">Length (in)</th>';
                echo '<th class="en_box_sizing_list_heading">Width (in)</th>';
                echo '<th class="en_box_sizing_list_heading">Height (in)</th>';
                echo '<th class="en_box_sizing_list_heading">Length (in)</th>';
                echo '<th class="en_box_sizing_list_heading">Width (in)</th>';
                echo '<th class="en_box_sizing_list_heading">Height (in)</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
            }

            /* Multiple Package */
            if ($product_id > 0) {
                $args = [
                    'post_type' => "en_multi_packaging",
                    'posts_per_page' => -1,
                ];
            } else {
                $args = array('post_type' => ["box_sizing", "or_box_sizing"], 'posts_per_page' => -1);
            }

            $query = $product_id > 0 && !empty($query) ? $query : new WP_Query($args);

            $this->fedex_one_rate_old_data = array();

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $postId = get_the_ID();
                    $postTitle = get_the_title();
                    $postContent = get_the_content();
                    $get_post_meta = get_post_meta($postId, 'box_sizing', true);
                    /* Multiple Package */
                    $en_multi_packaging = get_post_meta($postId, 'en_multi_packaging', true);

                    if (!empty($get_post_meta)) {
                        $value = $get_post_meta;
                        $class = "";
                    } elseif (!empty($en_multi_packaging)) { /* Multiple Package */
                        $value = $en_multi_packaging;
                        if (isset($value['en_multipackage_product_id']) && $value['en_multipackage_product_id'] != $product_id) {
                            continue;
                        }

                        $class = "";
                    } else {
                        $value = get_post_meta($postId, 'or_box_sizing', true);
                        if (!isset($value['en_box_usps_box_type']) && isset($value['en_box_array_order_id'])) {
                            $value['en_box_usps_box_type'] = $value['en_box_array_order_id'];
                        }
                        isset($value['en_box_array_order_id']) ? $this->fedex_one_rate_old_data[$value['en_box_array_order_id']] = $postId : '';
                        $class = "add_box_packaging_click";
                    }

                    $available_click = '<a class="available_click" id="' . $postId . '" onclick="availableClick(' . "'" . $postContent . "'" . ', ' . "'" . $postId . "'" . ', ' . "'" . $product_id . "'" . ')">' . $postContent . '</a>';
                    $is_on_delete_click = 'onClick="return edit_box_sizing(' . "'" . $postId . "'" . ',' . "'" . $product_id . "'" . ')"';
                    $action = '<a class="en_small_action_box_sizing ' . $class . '" id="' . $postId . '" ' . $is_on_delete_click . ' > Edit </a> | <a class="en_small_action_box_sizing" onclick="return delete_box_sizing(' . "'" . $postId . "'" . ',' . "'" . $product_id . "'" . ')" id="' . $postId . '"> Delete </a>';
                    $value['en_box_quantity'] = (isset($value['en_box_quantity'])) ? $value['en_box_quantity'] : 0;
                    $value['en_box_usps_box_fee'] = (isset($value['en_box_usps_box_fee'])) ? $value['en_box_usps_box_fee'] : 0;
                    $value['en_box_usps_box_type'] = (isset($value['en_box_usps_box_type'])) ? $value['en_box_usps_box_type'] : '';
                    $value['fedex_box_type'] = (isset($value['fedex_box_type'])) ? $value['fedex_box_type'] : '';

                    // Exterior dimensions for box.
                    $value['en_box_outer_length'] = (isset($value['en_box_outer_length'])) ? $value['en_box_outer_length'] : 0;
                    $value['en_box_outer_width'] = (isset($value['en_box_outer_width'])) ? $value['en_box_outer_width'] : 0;
                    $value['en_box_outer_height'] = (isset($value['en_box_outer_height'])) ? $value['en_box_outer_height'] : 0;

                    $box_package_th = [
                        isset($is_custom_work) && $is_custom_work ? ['td' => $postId, 'calss' => 'en_box_sizing_id'] : [],
                        ['td' => $postTitle, 'calss' => 'en_box_sizing_nickname_td'],
                        // Interior dimensions for box.
                        ['td' => $value['en_box_length'], 'calss' => 'en_box_sizing_length_td'],
                        ['td' => $value['en_box_width'], 'calss' => 'en_box_sizing_width_td'],
                        ['td' => $value['en_box_height'], 'calss' => 'en_box_sizing_height_td'],
                        // Start Exterior dimensions for box.
                        ['td' => $value['en_box_outer_length'], 'calss' => 'en_box_outer_sizing_length_td'],
                        ['td' => $value['en_box_outer_width'], 'calss' => 'en_box_outer_sizing_width_td'],
                        ['td' => $value['en_box_outer_height'], 'calss' => 'en_box_outer_sizing_height_td'],
                        // End Exterior dimensions for box.
                        ['td' => $value['en_box_max_weight'], 'calss' => 'en_box_sizing_weight_td'],
                        ['td' => $value['en_box_box_weight'], 'calss' => 'en_box_sizing_max_weight_td'],
                        ['td' => $value['en_box_usps_box_fee'], 'calss' => 'en_box_usps_box_fee_td'],
                        ['td' => $value['en_box_usps_box_type'], 'calss' => 'en_box_usps_box_type_td'],
                        ['td' => $value['fedex_box_type'], 'calss' => 'fedex_box_type'],
                        ['calss' => 'en_small_action_available_td', 'append' => $available_click],
                    ];

                    $multi_box_package_th = [
                        ['td' => $value['en_box_quantity'], 'calss' => 'en_box_sizing_quantity_td'],
                        ['td' => $postTitle, 'calss' => 'en_box_sizing_nickname_td'],
                        ['td' => $value['en_box_length'], 'calss' => 'en_box_sizing_length_td'],
                        ['td' => $value['en_box_width'], 'calss' => 'en_box_sizing_width_td'],
                        ['td' => $value['en_box_height'], 'calss' => 'en_box_sizing_height_td'],
                        ['td' => $value['en_box_box_weight'], 'calss' => 'en_box_sizing_max_weight_td'],
                        ['td' => $value['en_box_usps_box_fee'], 'calss' => 'en_box_usps_box_fee_td'],
                        ['td' => $value['en_box_usps_box_type'], 'calss' => 'en_box_usps_box_type_td'],
                    ];

                    $box_table_data = $product_id > 0 ? $multi_box_package_th : $box_package_th;
                    echo '<tr id="box_sizing_row_id_' . esc_attr($postId) . '">';
                    foreach ($box_table_data as $key => $table_data) {
                        if (!empty($table_data)) {
                            $table_class = (isset($table_data['calss'])) ? $table_data['calss'] : '';
                            $table_class_hide = $table_class == 'en_box_usps_box_type_td' || $table_class == 'fedex_box_type' ? "style='display: none;'" : '';
                            echo '<td class="en_box_sizing_list_data ' . esc_attr($table_class) . '" ' . esc_attr($table_class_hide) . '>';
                            echo (isset($table_data['td'])) ? esc_html($table_data['td']) : '';
                            echo (isset($table_data['append'])) ? '<a class="available_click" id="' . esc_attr($postId) . '" onclick="availableClick(' . "'" . esc_attr($postContent) . "'" . ', ' . "'" . esc_attr($postId) . "'" . ', ' . "'" . esc_attr($product_id) . "'" . ')">' . esc_html($postContent) . '</a>' : '';
                            echo '</td>';
                        }
                    }

                    echo '<td class="en_box_sizing_list_data">';
                    echo '<a class="en_small_action_box_sizing ' . esc_attr($class) . '" id="' . esc_attr($postId) . '" ' . esc_attr($is_on_delete_click) . ' > Edit </a> | <a class="en_small_action_box_sizing" onclick="return delete_box_sizing(' . "'" . esc_attr($postId) . "'" . ',' . "'" . esc_attr($product_id) . "'" . ')" id="' . esc_attr($postId) . '"> Delete </a>';
                    echo '</td>';

                }
            }

            wp_reset_query();
            ?>
            </tbody>
            </table>
            <?php
            if ($product_id > 0) {
                $this->query = $query;
            }

            if (!$product_id > 0) {
                $this->en_wwe_small_re_arrange_fields();
            }
        }

        /**
         * Notifications html.
         */
        public function en_box_size_notifications_html($product_id = 0)
        {
            /* Multiple Package */
            ?>
            <!-- Notifications -->
            <div id="en_box_size_notifications_block" class="en_box_size_notifications_block_<?php echo esc_attr($product_id); ?>">
                <!-- Add message -->
                <div id="message" class="en_box_sizing_notification_added updated inline" style="display:none">
                    <p>
                        <strong>Success! </strong>Box updated successfully.
                    </p>
                </div>
                <!-- Update message -->
                <div id="message" class="en_box_sizing_notification_update updated inline" style="display:none">
                    <p>
                        <strong>Success! </strong> Box added successfully.
                    </p>
                </div>
                <!-- One Rate Update message -->
                <div id="message" class="en_boxes_sizing_notification_update updated inline" style="display:none">
                    <p>
                        <strong>Success! </strong>Boxes added successfully.
                    </p>
                </div>
                <!-- Delete message -->
                <div id="message" class="en_box_sizing_notification_delete updated inline" style="display:none">
                    <p>
                        <strong>Success! </strong>Box deleted successfully.
                    </p>
                </div>
                <!-- One Rate Delete message -->
                <div id="message" class="en_boxes_sizing_notification_delete updated inline" style="display:none">
                    <p>
                        <strong>Success! </strong>Boxes deleted successfully.
                    </p>
                </div>

                <!-- One Rate Data saved -->
                <div id="message" class="en_boxes_sizing_notification_data_saved updated inline" style="display:none">
                    <p>
                        <strong>Success! </strong>Data saved successfully.
                    </p>
                </div>

                <!-- Delete message -->
                <div id="message" class="en_box_sizing_notification_box_availiable updated inline" style="display:none">
                    <p>
                        <strong>Success! </strong>Box status is updated successfully.
                    </p>
                </div>
            </div>
        <?php
        }

        /**
         *
         * @param array $box_sizes
         * @return array
         */
        public function en_woo_addons_box_sizing_type_arr($box_sizes)
        {
            $en_tab = (isset($_REQUEST['tab'])) ? sanitize_text_field($_REQUEST['tab']) : '';
            if ($en_tab == 'usps_small') {
                return $box_sizes;
            }

            $select_option_box = [
                'upm_default' => 'Merchant defined box (default)',
            ];

            if ($en_tab == 'usps_small') {
                $select_option_box = [
                    'upm_default' => 'Merchant defined box (default)',
                    'upm_express_box' => 'USPS Priority Mail Express Box',
                    'upm_box' => 'USPS Priority Mail Box',
                    'upm_large_flat_rate_box' => 'USPS Priority Mail Large Flat Rate Box',
                    'upm_medium_flat_rate_box' => 'USPS Priority Mail Medium Flat Rate Box',
                    'upm_small_flat_rate_box' => 'USPS Priority Mail Small Flat Rate Box',
                    'upm_padded_flat_rate_envelope' => 'USPS Priority Mail Padded Flat Rate Envelope',
                ];
            }

            if ($en_tab == 'fedex_small') {
                (!class_exists("EnWooAddonFedexOneRate")) ? include(speed_standard_box_sizes_addon_plugin_url . '/includes/en-woo-box-addons-fedex-one-rate.php') : "";
                $EnWooAddonFedexOneRate = new EnWooAddonFedexOneRate();
                $fedex_one_rate_data = $EnWooAddonFedexOneRate->fedex_one_rate_data();
                $fedex_on_rate_img = $EnWooAddonFedexOneRate->fedex_one_rate_img();

                foreach ($fedex_one_rate_data as $key => $one_rate) {
                    $nickname = end($one_rate);
                    $checked = (isset($fedex_one_rate_old_data[$key])) ? "checked='checked'" : "";
                    $slice_me = ($nickname == "FedEx Envelope" || $nickname == "FedEx Reusable Envelope") ? 2 : 3;
                    $one_rate_box_label = $nickname . ' (' . implode(" x ", array_slice($one_rate, 0, $slice_me)) . ')';
                    $select_option_box[$key] = $one_rate_box_label;
                }
            }

            $box_sizes_popup = array(
                "sm_box_sizing_nickname" => array(
                    "type" => "text",
                    "title" => "Nickname",
                    "label" => "Nickname",
                    "id" => "sm_box_sizing_nickname",
                    "class" => "form-control",
                    "placeholder" => "",
                    "position" => "col-md-12",
                    "data_type" => "string",
                    "data_length" => ''
                ),
                // usps flat rate
                "sm_box_size_type" => array(
                    "type" => "dropdown",
                    "title" => "Box Type",
                    "label" => "Box Type",
                    "id" => "sm_box_size_type",
                    "class" => "form-control en_usps_small_non_required",
                    "placeholder" => "",
                    "position" => "col-md-12",
                    "data_type" => "text",
                    "select_option" => $select_option_box,
                    "data_length" => 'data-length="108"'
                ),
                // Interior Box
                "sm_box_sizing_length" => array(
                    "type" => "text",
                    "title" => "Interior Length (in)",
                    "label" => "Interior Length (in)",
                    "id" => "sm_box_sizing_length",
                    "class" => "form-control",
                    "placeholder" => "",
                    "position" => "col-md-4",
                    "data_type" => "number",
                    "data_length" => 'data-length="108"'
                ),
                "sm_box_sizing_width" => array(
                    "type" => "text",
                    "title" => "Interior Width (in)",
                    "label" => "Interior Width (in)",
                    "id" => "sm_box_sizing_width",
                    "class" => "form-control",
                    "placeholder" => "",
                    "position" => "col-md-4",
                    "data_type" => "number",
                    "data_length" => 'data-length="108"'
                ),
                "sm_box_sizing_height" => array(
                    "type" => "text",
                    "title" => "Interior Height (in)",
                    "label" => "Interior Height (in)",
                    "id" => "sm_box_sizing_height",
                    "class" => "form-control",
                    "placeholder" => "",
                    "position" => "col-md-4",
                    "data_type" => "number",
                    "data_length" => 'data-length="108"'
                ),
                // Exterior Box
                "sm_box_outer_sizing_length" => array(
                    "type" => "text",
                    "title" => "Exterior Length (in)",
                    "label" => "Exterior Length (in)",
                    "id" => "sm_box_outer_sizing_length",
                    "class" => "form-control en_usps_small_non_required",
                    "placeholder" => "",
                    "position" => "col-md-4",
                    "data_type" => "number",
                ),
                "sm_box_outer_sizing_width" => array(
                    "type" => "text",
                    "title" => "Exterior Width (in)",
                    "label" => "Exterior Width (in)",
                    "id" => "sm_box_outer_sizing_width",
                    "class" => "form-control en_usps_small_non_required",
                    "placeholder" => "",
                    "position" => "col-md-4",
                    "data_type" => "number",
                ),
                "sm_box_outer_sizing_height" => array(
                    "type" => "text",
                    "title" => "Exterior Height (in)",
                    "label" => "Exterior Height (in)",
                    "id" => "sm_box_outer_sizing_height",
                    "class" => "form-control en_usps_small_non_required",
                    "placeholder" => "",
                    "position" => "col-md-4",
                    "data_type" => "number",
                ),
                // End Exterior Box
                "sm_box_sizing_max_weight" => array(
                    "type" => "text",
                    "title" => "Max Weight (LBS)",
                    "label" => "Max Weight (LBS)",
                    "id" => "sm_box_sizing_max_weight",
                    "class" => "form-control",
                    "placeholder" => "",
                    "position" => "col-md-4",
                    "data_type" => "number",
                    "data_length" => 'data-length="150"',
                ),
                "sm_box_sizing_weight" => array(
                    "type" => "text",
                    "title" => "Box Weight (LBS)",
                    "label" => "Box Weight (LBS)",
                    "id" => "sm_box_sizing_weight",
                    "class" => "form-control en_usps_small_non_required",
                    "placeholder" => "",
                    "position" => "col-md-4",
                    "data_type" => "number",
                    "data_length" => 'data-length="150"'
                ),
                "sm_box_sizing_fee" => array(
                    "type" => "text",
                    "title" => "Box Fee (e.g 1.75)",
                    "label" => "Box Fee (e.g 1.75)",
                    "id" => "sm_box_sizing_fee",
                    "class" => "form-control en_usps_small_non_required",
                    "placeholder" => "",
                    "position" => "col-md-4",
                    "data_type" => "number"
                ),
            );

            return $box_sizes_popup;
        }

        /**
         * Box sizes template.
         * @return array
         */
        function en_woo_addons_box_sizing_text_fields_arr()
        {
            $box_sizes_popup = array(
                "sm_box_sizing_nickname" => array(
                    "type" => "text",
                    "title" => "Nickname",
                    "label" => "Nickname",
                    "id" => "sm_box_sizing_nickname",
                    "class" => "add_box_popup_fields",
                    "placeholder" => "",
                    "position" => "box_sizing_left",
                    "data_type" => "string",
                    "data_length" => ''
                ),
                // Interior Box
                "sm_box_sizing_length" => array(
                    "type" => "text",
                    "title" => "Interior Length (in)",
                    "label" => "Interior Length (in)",
                    "id" => "sm_box_sizing_length",
                    "class" => "add_box_popup_fields",
                    "placeholder" => "",
                    "position" => "box_sizing_right",
                    "data_type" => "number",
                    "data_length" => 'data-length="108"'
                ),
                "sm_box_sizing_width" => array(
                    "type" => "text",
                    "title" => "Interior Width (in)",
                    "label" => "Interior Width (in)",
                    "id" => "sm_box_sizing_width",
                    "class" => "add_box_popup_fields",
                    "placeholder" => "",
                    "position" => "box_sizing_left",
                    "data_type" => "number",
                    "data_length" => 'data-length="108"'
                ),
                "sm_box_sizing_height" => array(
                    "type" => "text",
                    "title" => "Interior Height (in)",
                    "label" => "Interior Height (in)",
                    "id" => "sm_box_sizing_height",
                    "class" => "add_box_popup_fields",
                    "placeholder" => "",
                    "position" => "box_sizing_right",
                    "data_type" => "number",
                    "data_length" => 'data-length="108"'
                ),
                // Exterior Box
                "sm_box_outer_sizing_length" => array(
                    "type" => "text",
                    "title" => "Exterior Length (in)",
                    "label" => "Exterior Length (in)",
                    "id" => "sm_box_outer_sizing_length",
                    "class" => "add_box_popup_fields en_usps_small_non_required",
                    "placeholder" => "",
                    "position" => "box_sizing_right",
                    "data_type" => "number",
                ),
                "sm_box_outer_sizing_width" => array(
                    "type" => "text",
                    "title" => "Exterior Width (in)",
                    "label" => "Exterior Width (in)",
                    "id" => "sm_box_outer_sizing_width",
                    "class" => "add_box_popup_fields en_usps_small_non_required",
                    "placeholder" => "",
                    "position" => "box_sizing_left",
                    "data_type" => "number",
                ),
                "sm_box_outer_sizing_height" => array(
                    "type" => "text",
                    "title" => "Exterior Height (in)",
                    "label" => "Exterior Height (in)",
                    "id" => "sm_box_outer_sizing_height",
                    "class" => "add_box_popup_fields en_usps_small_non_required",
                    "placeholder" => "",
                    "position" => "box_sizing_right",
                    "data_type" => "number",
                ),
                // End Exterior Box
                "sm_box_sizing_max_weight" => array(
                    "type" => "text",
                    "title" => "Max Weight (LBS)",
                    "label" => "Max Weight (LBS)",
                    "id" => "sm_box_sizing_max_weight",
                    "class" => "add_box_popup_fields",
                    "placeholder" => "",
                    "position" => "box_sizing_left",
                    "data_type" => "number",
                    "data_length" => 'data-length="150"'
                ),
                "sm_box_sizing_weight" => array(
                    "type" => "text",
                    "title" => "Box Weight (LBS)",
                    "label" => "Box Weight (LBS)",
                    "id" => "sm_box_sizing_weight",
                    "class" => "add_box_popup_fields",
                    "placeholder" => "",
                    "position" => "box_sizing_right",
                    "data_type" => "number",
                    "data_length" => 'data-length="150"'
                ),
                "sm_box_sizing_fee" => array(
                    "type" => "text",
                    "title" => "Box Fee (e.g 1.75)",
                    "label" => "Box Fee (e.g 1.75)",
                    "id" => "sm_box_sizing_fee",
                    "class" => "add_box_popup_fields en_usps_small_non_required",
                    "placeholder" => "",
                    "position" => "box_sizing_left",
                    "data_type" => "number"
                ),
                // usps flat rate
                "sm_box_size_type" => array(
                    "type" => "dropdown",
                    "title" => "Box Type",
                    "label" => "Box Type",
                    "id" => "sm_box_size_type",
                    "class" => "add_box_popup_fields en_usps_small_non_required",
                    "placeholder" => "",
                    "sm_box_size_type_from_sbs" => "yes",
                    "position" => "box_sizing_full",
                    "data_type" => "text",
                    "select_option" => array(
                        'upm_default' => 'Merchant defined box (default)',
                        'upm_express_box' => 'USPS Priority Mail Express Box',
                        'upm_box' => 'USPS Priority Mail Box',
                        'upm_large_flat_rate_box' => 'USPS Priority Mail Large Flat Rate Box',
                        'upm_medium_flat_rate_box' => 'USPS Priority Mail Medium Flat Rate Box',
                        'upm_small_flat_rate_box' => 'USPS Priority Mail Small Flat Rate Box',
                        'upm_padded_flat_rate_envelope' => 'USPS Priority Mail Padded Flat Rate Envelope',
                    ),
                    "data_length" => 'data-length="108"'
                ),
            );

            $box_sizes_popup = apply_filters('en_woo_addons_box_sizing_flat_rate_text_fields_arr', $box_sizes_popup);
            $box_sizes_flat_rate_popup = $this->en_woo_addons_box_sizing_type_arr($box_sizes_popup);
            return array('fields' => $box_sizes_flat_rate_popup);
        }

        /**
         * multi-packages template.
         * @return array
         */
        function en_woo_addons_multi_package_box_sizing_text_fields_arr()
        {

            $box_sizes_popup = array(
                "sm_box_sizing_quantity" => array(
                    "type" => "text",
                    "title" => "Quantity",
                    "label" => "Quantity",
                    "id" => "sm_box_sizing_quantity",
                    "class" => "form-control",
                    "placeholder" => "",
                    "position" => "col-md-6",
                    "data_type" => "number",
                    "data_length" => 'data-length="15"'
                ),
                "sm_box_sizing_nickname" => array(
                    "type" => "text",
                    "title" => "Box Nickname",
                    "label" => "Box Nickname",
                    "id" => "sm_box_sizing_nickname",
                    "class" => "form-control",
                    "placeholder" => "",
                    "position" => "col-md-6",
                    "data_type" => "string",
                    "data_length" => 'data-length="25"'
                ),
                "sm_box_sizing_length" => array(
                    "type" => "text",
                    "title" => "Length (in)",
                    "label" => "Length (in)",
                    "id" => "sm_box_sizing_length",
                    "class" => "form-control",
                    "placeholder" => "",
                    "position" => "col-md-6",
                    "data_type" => "number",
                    "data_length" => 'data-length="108"'
                ),
                "sm_box_sizing_width" => array(
                    "type" => "text",
                    "title" => "Width (in)",
                    "label" => "Width (in)",
                    "id" => "sm_box_sizing_width",
                    "class" => "form-control",
                    "placeholder" => "",
                    "position" => "col-md-6",
                    "data_type" => "number",
                    "data_length" => 'data-length="108"'
                ),
                "sm_box_sizing_height" => array(
                    "type" => "text",
                    "title" => "Height (in)",
                    "label" => "Height (in)",
                    "id" => "sm_box_sizing_height",
                    "class" => "form-control",
                    "placeholder" => "",
                    "position" => "col-md-6",
                    "data_type" => "number",
                    "data_length" => 'data-length="108"'
                ),
                "sm_box_sizing_weight" => array(
                    "type" => "text",
                    "title" => "Weight (LBS)",
                    "label" => "Weight (LBS)",
                    "id" => "sm_box_sizing_weight",
                    "class" => "form-control",
                    "placeholder" => "",
                    "position" => "col-md-6",
                    "data_type" => "number",
                    "data_length" => 'data-length="150"'
                ),
                "sm_box_sizing_fee" => array(
                    "type" => "text",
                    "title" => "Box Fee (e.g 1.75)",
                    "label" => "Box Fee (e.g 1.75)",
                    "id" => "sm_box_sizing_fee",
                    "class" => "form-control en_usps_small_non_required",
                    "placeholder" => "",
                    "position" => "col-md-12",
                    "data_type" => "number"
                ),
            );

            return $box_sizes_popup;
        }

        /**
         * Get fields for template.
         * @param bool $multiple_packages
         */
        function en_wwe_small_re_arrange_fields($multiple_packages = FALSE)
        {
            /* Multiple Package */
            $en_box_sizing_plugin_name = (isset($_REQUEST['tab'])) ? sanitize_text_field($_REQUEST['tab']) : '';
            $en_hide_class = '';
            $one_rate_enabled = '';
            $en_add_box_sizing_overlay_append = 'en_add_box_sizing_overlay';
            if ($multiple_packages) {
                $en_hide_class = 'style="display: none;"';
                $this->box_sizing_text_fields_arr['fields'] = $this->en_woo_addons_multi_package_box_sizing_text_fields_arr();
                $en_add_box_sizing_overlay_append = 'en_add_multi_box_sizing_overlay';
            } else {
                $one_rate_enabled = '<div id="delete_dropship_btn" class="en_add_box_sizing_one_rate_overlay add_box_popup"></div>';
            }
        ?>
            </form>

            <?php  if (!$multiple_packages) echo '<div id="delete_dropship_btn" class="en_add_box_sizing_one_rate_overlay add_box_popup"></div>'; ?>

            <div id="delete_dropship_btn" class="<?php echo esc_html($en_add_box_sizing_overlay_append); ?> add_box_popup">
                <div class="sm_add_warehouse_popup">
                    <h2 class="del_hdng">
                        Add Box Sizes!
                    </h2>
                    <div class="girth_error">
                        <strong>Error!</strong> Interior Length plus girth can not exceeds 165.
                    </div>
                    <div class="outer_box_girth_error">
                        <strong>Error!</strong> Exterior Length plus girth can not exceeds 165.
                    </div>
                    <div class="bootstrap-iso form-wrp">
                        <div class="row">
                            <form method="POST" id="sm_add_box_sizing">
                                <?php foreach ($this->box_sizing_text_fields_arr['fields'] as $key => $value) { ?>
                                    <?php
                                    $sm_box_size_type_from_sbs = (isset($value['sm_box_size_type_from_sbs'])) && $value['sm_box_size_type_from_sbs'] == 'yes' ? TRUE : FALSE;
                                    $en_box_fee_class_hide = (!$multiple_packages &&
                                        isset($value['id']) &&
                                        $value['id'] == 'sm_box_sizing_fee' &&
                                        strlen($en_box_sizing_plugin_name) > 0 &&
                                        $en_box_sizing_plugin_name != 'trinet' &&
                                        $en_box_sizing_plugin_name != 'wwe_small_packages_quotes' &&
                                        $en_box_sizing_plugin_name != 'ups_small' &&
                                        $en_box_sizing_plugin_name != 'fedex_small' &&
                                        $en_box_sizing_plugin_name != 'unishepper_small' &&
                                        $en_box_sizing_plugin_name != 'usps_small') ||
                                        (!$multiple_packages && $sm_box_size_type_from_sbs) ? "style='display: none;'" : '';
                                    ?>
                                    <div class="sm_add_box_sizing_input <?php echo esc_attr($value['position']); ?>" <?php echo esc_attr($en_box_fee_class_hide); ?>>
                                        <label for="<?php echo esc_attr($value['label']); ?>"><?php echo esc_html($value['label']); ?><?php echo (isset($value['data_length'])) ? '<span>*</span>' : ''; ?> </label>

                                        <?php if (isset($value['type']) && ($value['type'] == "dropdown")) { ?>
                                            <select name="<?php echo esc_attr($value['id']); ?>" id="<?php echo esc_attr($value['id']); ?>" <?php echo esc_attr($value['data_length']); ?> title="<?php echo esc_attr($value['title']); ?>" data-type="<?php echo esc_attr($value['data_type']); ?>" class="<?php echo esc_attr($value['class']); ?>">

                                                <?php
                                                if (isset($value['select_option']) && (!empty($value['select_option']))) {
                                                    foreach ($value['select_option'] as $key => $value) {
                                                        echo '<option value="' . esc_attr($key) . '">' . esc_html($value) . '</option>';
                                                    }
                                                }
                                                ?>

                                            </select>
                                        <?php
                                        } else {
                                            $en_get_data_length = (isset($value['data_length'])) ? $value['data_length'] : '';
                                        ?>
                                            <input type="<?php echo esc_attr($value['type']); ?>" <?php echo (isset($value['data_length'])) ? '' : ' data-optional=1 '; ?> name="<?php echo esc_attr($value['id']); ?>" id="<?php echo esc_attr($value['id']); ?>" <?php echo esc_attr($en_get_data_length); ?> title="<?php echo esc_attr($value['title']); ?>" data-type="<?php echo esc_attr($value['data_type']); ?>" class="<?php echo esc_attr($value['class']); ?>" placeholder="<?php echo esc_attr($value['placeholder']); ?>">
                                        <?php } ?>
                                        <span class="add_box_popup_err err"></span>
                                    </div>

                                <?php } ?>
                                <div style="clear: both;"></div>
                                <?php if ($multiple_packages) { ?>
                                    <input type="hidden" id="en_multipackage_post_type" name="en_multipackage_post_type" value="en_multi_packaging" />
                                    <input type="hidden" id="en_multipackage_product_id" name="en_multipackage_product_id" value="" />
                                <?php } ?>
                                <input type="hidden" id="fedex_box_type" name="fedex_box_type" value="" />
                                <input type="hidden" id="sm_box_sizing_old_type" name="sm_box_sizing_old_type" value="default" />
                                <input type="hidden" id="sm_box_sizing_type" name="sm_box_sizing_type" value="default" />
                                <input type="hidden" id="sm_box_sizing_action" name="sm_box_sizing_action" value="add_action" />
                                <input type="hidden" id="sm_box_sizing_row_id" name="sm_box_sizing_row_id" value="" />
                                <input type="reset" id="reset" style="display:none" />
                                <div class="col-md-4 available_div" <?php echo esc_attr($en_hide_class); ?>>
                                    <div for="available">Available</div>
                                    <input type="checkbox" name="en_box_sizing_available" id="en_box_sizing_available" value="yes" checked="checked" />
                                </div>
                                <div class="del_btns sm_add_box_sizing_input">
                                    <a style="cursor: pointer" class="sm_add_box_cancel">Cancel</a>
                                    <a style="cursor: pointer" class="sm_add_box_submit">Save</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        <?php
            /* Ouput the delete popup. */
            $this->en_return_delete_confirmation_popup();
        }

        /**
         * Output the delete popup.
         */
        public function en_return_delete_confirmation_popup()
        {
        ?>
            <div id="box_size_delete_popup" class="box_size_delete_popup">
                <a href="#delete_ltl_dropship_btn" class="delete_box_size_dropship_btn hide_drop_val"></a>
                <div id="delete_ltl_dropship_btn" class="box_size_warehouse_overlay box_size_delete_popup_overly">
                    <div class="box_size_add_warehouse_popup">
                        <h2 class="del_hdng">
                            Warning!
                        </h2>
                        <p class="delete_p">
                            Are you sure you want to delete the box?
                        </p>
                        <div class="del_btns">
                            <a href="#" class="cancel_delete_box_sizing">Cancel</a>
                            <a href="#" class="confirm_delete_box_sizing">OK</a>
                        </div>
                    </div>
                </div>
            </div>
<?php
        }

        /**
         * Smart street api response curl from server
         * @return array type
         */
        public function customer_subscription_status()
        {

            $this->en_trial_activation_bin();
            $status = $this->EnWooAddonsCurlReqIncludes->smart_street_api_curl_request("s", $this->plugin_name);
            $this->en_check_plugin_status($status);
            $status = json_decode($status, true);
            return $status;
        }

        /**
         * Trial activation of 3dbin.
         */
        public function en_trial_activation_bin()
        {
            $trial_status = '';
            /* Trial activation code */
            $trial_status_3dbin = get_option('en_trial_3dbin_flag');
            if (!$trial_status_3dbin) {
                $trial_status = $this->EnWooAddonsCurlReqIncludes->smart_street_api_curl_request("c", $this->plugin_name, 'TR');
                $response_status = json_decode($trial_status);
                /* Trial Package activated succesfully */
                if (isset($response_status->severity) && $response_status->severity == "SUCCESS") {
                    update_option('en_trial_3dbin_flag', 1);
                }
                /* Error response */
                if (isset($response_status->severity) && $response_status->severity == "ERROR") {
                    /* Do anthing in case of error */
                }
            }
        }

        /**
         * Check plugin status.
         */
        public function en_check_plugin_status($current_status)
        {
            $current_status = json_decode($current_status);
            if (
                isset($current_status->status->subscribedPackage->packageSCAC) &&
                $current_status->status->subscribedPackage->packageSCAC == 'TR'
            ) {
                $plugin_status = $this->EnWooAddonsCurlReqIncludes->smart_street_api_curl_request("pluginType", $this->plugin_name, '');
                $decoded_plugin_status = json_decode($plugin_status);
                if ($decoded_plugin_status->severity == "SUCCESS") {
                    if ($decoded_plugin_status->pluginType == "trial") {
                        /* Plugin not activated notification */
                        echo '<div id="message" class="notice-dismiss-bin notice-dismiss-bin-php notice-warning notice is-dismissible"><p>The Small Packages Quotes plugin must have an active paid license to continue to use this feature.</p><button type="button" class="notice-dismiss notice-dismiss-bin"><span class="screen-reader-text notice-dismiss-bin">Dismiss this notice.</span></button></div>';
                    }
                }
            }
        }

        /**
         * Plans for SBS
         * @param array $packages_list
         * @return string
         */
        public function packages_list($packages_list)
        {
            $packages_list_arr = array();
            if (isset($packages_list) && (!empty($packages_list))) {

                $packages_list_arr['options']['disable'] = 'Disable (default)';
                foreach ($packages_list as $key => $value) {
                    $value['pPeriod'] = (isset($value['pPeriod']) && ($value['pPeriod'] == "Month")) ? "mo" : $value['pPeriod'];
                    $value['pHits'] = is_numeric($value['pHits']) ? number_format($value['pHits']) : $value['pHits'];
                    $value['pCost'] = is_numeric($value['pCost']) ? number_format($value['pCost'], 2, '.', '') : $value['pCost'];
                    $trial = (isset($value['pSCAC']) && $value['pSCAC'] == "TR") ? "(Trial)" : "";
                    $packages_list_arr['options'][$value['pSCAC']] = $value['pHits'] . "/" . $value['pPeriod'] . " ($" . number_format($value['pCost']) . ".00)" . " " . $trial;
                }
            }
            return $packages_list_arr;
        }

        /**
         * Ui display for next plan
         * @return string type
         */
        public function next_subcribed_package()
        {

            $this->next_subcribed_package = (isset($this->nextSubcribedPackage['nextToBeChargedStatus']) && $this->nextSubcribedPackage['nextToBeChargedStatus'] == 1) ? $this->nextSubcribedPackage['nextSubscriptionSCAC'] : "disable";
            return $this->next_subcribed_package;
        }

        /**
         * Get plan data
         * @return array
         */
        public function subscribed_package()
        {

            $subscribed_package = $this->subscribedPackage;
            $subscribed_package['packageDuration'] = (isset($subscribed_package['packageDuration']) && ($subscribed_package['packageDuration'] == "Month")) ? "mo" : $subscribed_package['packageDuration'];
            $subscribed_package['packageHits'] = is_numeric($subscribed_package['packageHits']) ? number_format($subscribed_package['packageHits']) : $subscribed_package['packageHits'];
            $subscribed_package['packageCost'] = is_numeric($subscribed_package['packageCost']) ? number_format($subscribed_package['packageCost'], 2, '.', '') : $subscribed_package['packageCost'];
            return $subscribed_package['packageHits'] . "/" . $subscribed_package['packageDuration'] . " ($" . number_format($subscribed_package['packageCost']) . ".00)";
        }

        /**
         * Response from smart street api and set in public attributes
         */
        function set_curl_res_attributes()
        {

            $this->subscriptionInfo = (isset($this->status['status']['subscriptionInfo'])) ? $this->status['status']['subscriptionInfo'] : "";
            $this->lastUsageTime = (isset($this->status['status']['lastUsageTime'])) ? $this->status['status']['lastUsageTime'] : "";
            $this->subscribedPackage = (isset($this->status['status']['subscribedPackage'])) ? $this->status['status']['subscribedPackage'] : "";
            $this->subscriptionStatus = (isset($this->status['status']['subscriptionInfo']['subscriptionStatus'])) ? ($this->status['status']['subscriptionInfo']['subscriptionStatus'] == 1) ? "yes" : "no" : "";
            $this->subscribedPackageHitsStatus = (isset($this->status['status']['subscribedPackageHitsStatus'])) ? $this->status['status']['subscribedPackageHitsStatus'] : "";
            $this->nextSubcribedPackage = (isset($this->status['status']['nextSubcribedPackage'])) ? $this->status['status']['nextSubcribedPackage'] : "";
            $this->statusRequestTime = (isset($this->status['statusRequestTime'])) ? $this->status['statusRequestTime'] : "";
        }

        /**
         * UI display Current Subscription & Current Usage
         * @param array type $status
         * @return array type
         */
        public function subscription($status = array())
        {

            if (isset($status) && (!empty($status)) && (is_array($status))) {
                $this->status = $status;
            } else { /* onload */
                $this->status = $this->customer_subscription_status();
                // All plans for 3dbin 
                $packages_list = isset($this->status['ListOfPackages']['Info']) ? $this->status['ListOfPackages']['Info'] : [];
                if (isset($packages_list) && (!empty($packages_list)) && is_array($packages_list)) {
                    $packages_list = $this->packages_list($packages_list);
                } else {
                    $packages_list = array(
                        'options' => array(
                            'disable' => 'Disable (default)'
                        )
                    );
                }
            }
            if (isset($this->status['severity']) && ($this->status['severity'] == "SUCCESS")) {
                $this->set_curl_res_attributes();
                if ($this->lastUsageTime == '0000-00-00 00:00:00') {
                    $this->lastUsageTime = 'yyyy-mm-dd hrs-min-sec';
                }
                $subscription_time = (isset($this->subscriptionInfo) && (!empty($this->subscriptionInfo['subscriptionTime']))) ? "Start date: " . $this->formate_date_time($this->subscriptionInfo['subscriptionTime']) : "NA";
                $status_request_time = (isset($this->lastUsageTime) && (!empty($this->lastUsageTime))) ? '(' . $this->lastUsageTime . ')' : "NA";
                $expiry_time = (isset($this->subscriptionInfo) && (!empty($this->subscriptionInfo['expiryTime']))) ? "End date: " . $this->formate_date_time($this->subscriptionInfo['expiryTime']) : "NA";
                $subscribed_package = (isset($this->subscribedPackage) && (!empty($this->subscribedPackage))) ? $this->subscribed_package() : "NA";
                $consumed_hits = (isset($this->subscribedPackageHitsStatus) && (!empty($this->subscribedPackageHitsStatus['consumedHits']))) ? $this->subscribedPackageHitsStatus['consumedHits'] : "";
                $available_hits = (isset($this->subscribedPackageHitsStatus) && (!empty($this->subscribedPackageHitsStatus['availableHits']))) ? $this->subscribedPackageHitsStatus['availableHits'] . "/" : "NA";
                $consumedHits = (isset($this->subscribedPackageHitsStatus) && (!empty($this->subscribedPackageHitsStatus['consumedHits']))) ? $this->subscribedPackageHitsStatus['consumedHits'] . "/" : "0/";
                $consumed_hits_prcent = (isset($this->subscribedPackageHitsStatus) && (!empty($this->subscribedPackageHitsStatus['consumedHitsPrcent']))) ? $this->subscribedPackageHitsStatus['consumedHitsPrcent'] . "%" : "0%";
                $package_hits = (isset($this->subscribedPackageHitsStatus) && (!empty($this->subscribedPackageHitsStatus['packageHits']))) ? $this->subscribedPackageHitsStatus['packageHits'] : "/NA";
                $next_subcribed_package = (isset($this->nextSubcribedPackage) && (!empty($this->nextSubcribedPackage))) ? $this->next_subcribed_package() : "NA";
                if ($this->subscriptionStatus == "yes") {
                    $current_subscription = '<span id="subscribed_package">' . $subscribed_package . '</span>'
                        . '&nbsp;&nbsp;&nbsp; '
                        . '<span id="subscription_time">' . $subscription_time . '</span>'
                        . '&nbsp;&nbsp;&nbsp;'
                        . '<span id="expiry_time">' . $expiry_time . '</span>';
                    $current_usage = '<span id="subscribed_package_status">' . $consumedHits . $package_hits . '</span> '
                        . '&nbsp;&nbsp;&nbsp;'
                        . '<span id="consumed_hits_prcent">' . $consumed_hits_prcent . '</span>'
                        . '&nbsp;&nbsp;&nbsp;'
                        . '<span id="status_request_time">' . $status_request_time . '</span>';
                    $this->subscription_packages_response = "yes";
                } else {
                    $current_subscription = '<span id="subscribed_package">Your current subscription is expired.</span>';
                    $current_usage = 'Not available.';
                    $this->subscription_packages_response = "no";
                }
            } else {
                $current_subscription = '<span id="subscribed_package">Not subscribed.</span>';
                $current_usage = 'Not available.';
                //              when no plan exist plan go to dislable 
                $next_subcribed_package = "disable";
                $this->subscription_packages_response = "no";
            }

            update_option("subscription_packages_response", $this->subscription_packages_response);

            $this->subscription_details = array(
                'current_usage' => (isset($current_usage)) ? $current_usage : "",
                'current_subscription' => (isset($current_subscription)) ? $current_subscription : "",
                'next_subcribed_package' => (isset($next_subcribed_package)) ? $next_subcribed_package : "",
                'packages_list' => (isset($packages_list)) ? $packages_list : "",
                'subscription_packages_response' => (isset($this->subscription_packages_response)) ? $this->subscription_packages_response : ""
            );
            return $this->subscription_details;
        }

        /**
         * new fields add for box sizing
         * @return array
         */
        function en_woo_addons_box_sizing_fields_arr($plugin_id)
        {
            $this->plugin_name = $plugin_id;
            extract($this->subscription());
            $this->plugin_name = $plugin_id;
            $settings = array(
                'Services_quoted_en_woo_addons_packages' => array(
                    'title' => __('', 'woocommerce'),
                    'name' => __('', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'desc' => '',
                    'id' => 'woocommerce_Services_quoted_en_woo_addons_packages',
                    'css' => '',
                    'default' => '',
                    'type' => 'title',
                ),
                'en_box_sizing_options_label_description' => array(
                    'name' => __('', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'title',
                    'desc' => 'The Box Sizes feature calculates the optimal packaging solution based on your standard box sizes. The solution is available graphically to assist order fulfillment. The next subscription begins when the current one expires or is depleted, which ever comes first. Refer to the <a target="_blank" href="https://wwex.com/woocommerce-standard-box-sizes/#documentation">User Guide</a> for more detailed information.',
                    'class' => 'hidden',
                    'id' => ' box_sizing_description'
                ),
                'en_box_sizing_options_plans' => array(
                    'name' => __(' ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'select',
                    'default' => $next_subcribed_package,
                    'id' => 'en_box_sizing_options_plans',
                    'class' => 'hidden',
                    'options' => $packages_list['options']
                ),
                // 'en_box_sizing_current_subscription' => array(
                //     'name' => __('Current plan', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                //     'type' => 'text',
                //     'class' => 'hidden',
                //     'desc' => $current_subscription,
                //     'id' => "en_box_sizing_current_subscription"
                // ),
                // 'en_box_sizing_current_usage' => array(
                //     'name' => __('Current usage', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                //     'type' => 'text',
                //     'class' => 'hidden',
                //     'desc' => $current_usage,
                //     'id' => 'en_box_sizing_current_usage'
                // ),
                'suspend_automatic_detection_of_box_sizing' => array(
                    'name' => __('Suspend use', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'checkbox',
                    'id' => 'suspend_automatic_detection_of_box_sizing',
                    'desc' => __(' ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'class' => 'suspend_automatic_detection_of_box_sizing'
                ),
                'en_box_sizing_start_title' => array(
                    'name' => __('Boxes ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'text',
                    'class' => "hidden",
                    'desc' => '',
                    'id' => 'en_box_sizing_start_title'
                ),
                'en_box_sizing_plugin_name' => array(
                    'name' => __('', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'text',
                    'class' => "hidden",
                    'placeholder' => $this->plugin_name,
                    'id' => "en_box_sizing_plugin_name",
                ),
                // 'en_box_sizing_subscription_status' => array(
                //     'name' => __('', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                //     'type' => 'text',
                //     'class' => "hidden",
                //     'placeholder' => $this->subscriptionStatus,
                //     'id' => "en_box_sizing_subscription_status",
                // ),
                'section_end_quote' => array(
                    'type' => 'sectionend',
                    'id' => 'wc_settings_quote_section_end'
                ),
                'section_end_quote_box_sizing' => array(
                    'type' => 'sectionend',
                    'id' => 'wc_settings_quote_section_end_box_sizing'
                )
            );
            $settings = apply_filters('en_woo_addons_box_sizing_updated_fields_messages_apply_filters', $settings);
            return $settings;
        }
    }
}
