<?php

/**
 * WWE LTL Tab Class
 *
 * @package     WWE LTL Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class SPEED_WC_ltl_Settings_tabs
 */
class SPEED_WC_ltl_Settings_tabs extends WC_Settings_Page
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->id = 'wwe_quests';
        add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_tab'), 50);
        add_action('woocommerce_sections_' . $this->id, array($this, 'output_sections'));
        add_action('woocommerce_settings_' . $this->id, array($this, 'output'));
        add_action('woocommerce_settings_save_' . $this->id, array($this, 'save'));
    }

    /**
     * Add Setting Tab
     * @param $settings_tabs
     * @return array
     */
    public function add_settings_tab($settings_tabs)
    {

        $settings_tabs[$this->id] = __('LTL Settings', 'speed-woocommerce-settings-wwe_quotes');
        return $settings_tabs;
    }

    /**
     * Get Section
     * @return array
     */
    public function get_sections()
    {

        $sections = array(
            '' => __('Connection Settings', 'speed-woocommerce-settings-wwe_quotes'),
            'section-1' => __('Carriers', 'speed-woocommerce-settings-wwe_quotes'),
            'section-2' => __('Quote Settings', 'speed-woocommerce-settings-wwe_quotes'),
            'section-3' => __('Warehouses', 'speed-woocommerce-settings-wwe_quotes'),
            'section-4' => __('User Guide', 'speed-woocommerce-settings-wwe_quotes'),
        );

        $sections = apply_filters('en_woo_addons_sections', $sections, speed_en_woo_plugin_wwe_quests);
        // Standard packaging
        $sections = apply_filters('en_woo_pallet_addons_sections', $sections, speed_en_woo_plugin_wwe_quests);
        return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
    }

    /**
     * Warehouses
     */
    public function ltl_warehouse()
    {
        require_once 'warehouse-dropship/wild/warehouse/wwe_ltl_warehouse_template.php';
        require_once 'warehouse-dropship/wild/dropship/wwe_ltl_dropship_template.php';
    }

    /**
     * User Guide
     */
    public function ltl_user_guide()
    {

        include_once('template/guide.php');
    }

    /**
     * Setting Tab
     * @return array
     */
    public function ltl_section_setting_tab()
    {

        echo '<div class="wwex_connection_section_class" id="wwesmpkg-conn-section">';
        $settings = array(
            'section_title_wwe_small_packages' => array(
                'name' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'title',
                'desc' => '<br> ',
                'id' => 'wc_settings_wwe_small_packages_title_section_connection',
            ),
            // 'speedship_url_wwe_small_packages_quotes' => array(
            //     'name' => __('Speedship /myUnishippers URL ', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
            //     'type' => 'text',
            //     'desc' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
            //     'id' => 'wc_settings_speedship_url_wwe_small_packages_quotes'
            // ),
            // 'oauth_url_wwe_small_packages_quotes' => array(
            //     'name' => __('OAuth URL ', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
            //     'type' => 'text',
            //     'desc' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
            //     'id' => 'wc_settings_oauth_url_wwe_small_packages_quotes'
            // ),
            'oauth_clientid_wwe_small_packages_quotes' => array(
                'name' => __('OAuth Client ID ', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'desc' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                'id' => 'wc_settings_oauth_clientid_wwe_small_packages_quotes'
            ),
            'oauth_client_secret_wwe_small_packages_quotes' => array(
                'name' => __('OAuth Client Secret ', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'desc' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                'id' => 'wc_settings_oauth_client_secret_wwe_small_packages_quotes'
            ),
            // 'oauth_audience_wwe_small_packages_quotes' => array(
            //     'name' => __('OAuth Audience ', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
            //     'type' => 'text',
            //     'desc' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
            //     'id' => 'wc_settings_oauth_audience_wwe_small_packages_quotes'
            // ),
            // 'oauth_username_wwe_small_packages_quotes' => array(
            //     'name' => __('OAuth Username ', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
            //     'type' => 'text',
            //     'desc' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
            //     'id' => 'wc_settings_oauth_username_wwe_small_packages_quotes'
            // ),
            // 'oauth_password_wwe_small_packages_quotes' => array(
            //     'name' => __('OAuth Password ', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
            //     'type' => 'text',
            //     'desc' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
            //     'id' => 'wc_settings_oauth_password_wwe_small_packages_quotes'
            // ),
            'googleapi_wwe_small_packages_quotes' => array(
                'name' => __('Google Api Key (optional for warehouse) ', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'desc' => __('<a href="https://developers.google.com/maps/documentation/distance-matrix/get-api-key?utm_source=devtools" target="_blank" >Get Google API Key </a>', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                'id' => 'wc_settings_googleapi_wwe_small_packages_quotes'
            ),
            'section_end_wwe_small_packages' => array(
                'type' => 'sectionend',
                'id' => 'wc_settings_googleapi_wwe_small_packages_quotes'
            ),
        );
        return $settings;
    }

    /**
     * Get Settings
     * @param $section
     * @return array
     * @global $wpdb
     */
    public function get_settings($section = null)
    {
        ob_start();
        $settings = [];
        switch ($section) {
            case 'section-0':
                echo '<div class="ltl_connection_section_class">';
                $settings = $this->ltl_section_setting_tab();
                break;
            case 'section-1':
                echo '<div class="carrier_section_class">';
?>
                <div class="carrier_section_class wrap woocommerce">
                    <p>
                        Identifies which carriers are included in the quote response, not what is displayed in the
                        shopping cart. Identify what displays in the shopping cart in the Quote Settings. For example,
                        you may include quote responses from all carriers, but elect to only show the cheapest three in
                        the shopping cart. <br> <br>
                        Not all carriers service all origin and destination points. If a carrier doesn`t service the
                        ship to address, it is automatically omitted from the quote response. Consider conferring with
                        your Worldwide Express representative if you`d like to narrow the number of carrier responses.
                        <br> <br> <br>
                    </p>
                    <table>
                        <tbody>
                            <thead>
                                <tr class="WWE_even_odd_class">
                                    <th class="WWE_carrier_carrier">Carrier Name</th>
                                    <th class="WWE__carrier_logo">Logo</th>
                                    <th class="WWE_carrier_include"><input type="checkbox" name="include_all" class="include_all" /></th>
                                </tr>
                            </thead>
                            <?php
                            global $wpdb;
                            $all_freight_array = [];
                            $count_carrier = 1;
                            $ltl_freight_all = $wpdb->get_results("SELECT * FROM wp_freights group by speed_freight_carrierSCAC order by speed_freight_carrierName ASC");
                            foreach ($ltl_freight_all as $ltl_freight_value) :
                            ?>
                                <tr <?php
                                    if ($count_carrier % 2 == 0) {

                                        echo 'class="WWE_even_odd_class"';
                                    }
                                    ?>>

                                    <td class="WWE_carrier_Name_td">
                                        <?php echo esc_html($ltl_freight_value->speed_freight_carrierName); ?>
                                    </td>
                                    <td>
                                        <img src="<?php echo plugins_url('Carrier_Logos/' . $ltl_freight_value->carrier_logo, __FILE__) ?> ">
                                    </td>
                                    <td>
                                        <input <?php
                                                if ($ltl_freight_value->carrier_status == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?> name="<?php echo esc_attr($ltl_freight_value->speed_freight_carrierSCAC) . esc_attr($ltl_freight_value->id); ?>" class="carrier_check" id="<?php echo esc_attr($ltl_freight_value->speed_freight_carrierSCAC) . esc_attr($ltl_freight_value->id); ?>" type="checkbox">
                                    </td>
                                </tr>
                            <?php
                                $count_carrier++;
                            endforeach;
                            ?>
                            <input name="action" value="save_carrier_status" type="hidden" />
                        </tbody>
                    </table>
                </div>
<?php
                break;

            case 'section-2':

                $disable_hold_at_terminal = "";
                $hold_at_terminal_package_required = "";

                $action_hold_at_terminal = apply_filters('speed_wwe_quests_quotes_plans_suscription_and_features', 'hold_at_terminal');
                if (is_array($action_hold_at_terminal)) {
                    // $disable_hold_at_terminal = "disabled_me";
                    // $hold_at_terminal_package_required = apply_filters('speed_wwe_quests_plans_notification_link', $action_hold_at_terminal);
                }

                // Cuttoff Time
                $wwe_lfq_disable_cutt_off_time_ship_date_offset = "";
                $wwe_lfq_cutt_off_time_package_required = "";
                $wwe_lfq_disable_show_delivery_estimates = "";
                $wwe_lfq_show_delivery_estimates_required = "";

                //  Check the cutt of time & offset days plans for disable input fields
                $wwe_lfq_action_cutOffTime_shipDateOffset = apply_filters('speed_wwe_quests_quotes_plans_suscription_and_features', 'wwe_lfq_cutt_off_time');
                if (is_array($wwe_lfq_action_cutOffTime_shipDateOffset)) {
                    // $wwe_lfq_disable_cutt_off_time_ship_date_offset = "disabled_me";
                    // $wwe_lfq_cutt_off_time_package_required = apply_filters('speed_wwe_quests_plans_notification_link', $wwe_lfq_action_cutOffTime_shipDateOffset);
                }
                // check the delivery estimate option plan
                $wwe_lfq_show_delivery_estimates = apply_filters('speed_wwe_quests_quotes_plans_suscription_and_features', 'wwe_lfq_show_delivery_estimates');
                if (is_array($wwe_lfq_show_delivery_estimates)) {
                    // $wwe_lfq_disable_show_delivery_estimates = "disabled_me";
                    // $wwe_lfq_show_delivery_estimates_required = apply_filters('speed_wwe_quests_plans_notification_link', $wwe_lfq_show_delivery_estimates);
                }

                $ltl_enable = get_option('en_plugins_return_LTL_quotes');
                $weight_threshold_class = $ltl_enable == 'yes' ? 'show_en_weight_threshold_lfq' : 'hide_en_weight_threshold_lfq';
                $weight_threshold = get_option('en_weight_threshold_lfq');
                $weight_threshold = isset($weight_threshold) && $weight_threshold > 0 ? $weight_threshold : 150;

                echo '<div class="quote_section_class_ltl">';
                $settings = array(
                    'section_title_quote' => array(
                        'title' => __('', 'speed-woocommerce-settings-wwe_quotes'),
                        'type' => 'title',
                        'desc' => '',
                        'id' => 'wc_settings_wwe_section_title_quote'
                    ),
                    'rating_method_wwe' => array(
                        'name' => __('Rating Method ', 'speed-woocommerce-settings-wwe_quotes'),
                        'type' => 'select',
                        'desc' => __('Displays only the cheapest returned Rate.', 'speed-woocommerce-settings-wwe_quotes'),
                        'id' => 'wc_settings_wwe_rate_method',
                        'options' => array(
                            'Cheapest' => __('Cheapest', 'Cheapest'),
                            'cheapest_options' => __('Cheapest Options', 'cheapest_options'),
                            'average_rate' => __('Average Rate', 'average_rate')
                        )
                    ),
                    'number_of_options_wwe' => array(
                        'name' => __('Number Of Options ', 'speed-woocommerce-settings-wwe_quotes'),
                        'type' => 'select',
                        'default' => '3',
                        'desc' => __('Number of options to display in the shopping cart.', 'speed-woocommerce-settings-wwe_quotes'),
                        'id' => 'wc_settings_wwe_Number_of_options',
                        'options' => array(
                            '1' => __('1', '1'),
                            '2' => __('2', '2'),
                            '3' => __('3', '3'),
                            '4' => __('4', '4'),
                            '5' => __('5', '5'),
                            '6' => __('6', '6'),
                            '7' => __('7', '7'),
                            '8' => __('8', '8'),
                            '9' => __('9', '9'),
                            '10' => __('10', '10')
                        )
                    ),
                    'label_as_wwe' => array(
                        'name' => __('Label As ', 'speed-woocommerce-settings-wwe_quotes'),
                        'type' => 'text',
                        'desc' => __('What The User Sees During Checkout, e.g "Freight" Leave Blank to Display The Carrier Name.', 'speed-woocommerce-settings-wwe_quotes'),
                        'id' => 'wc_settings_wwe_label_as'
                    ),
                    'price_sort_wwe_ltl' => array(
                        'name' => __("Don't sort shipping methods by price  ", 'woocommerce-settings-wwe_ltl_quotes'),
                        'type' => 'checkbox',
                        'desc' => 'By default, the plugin will sort all shipping methods by price in ascending order.',
                        'id' => 'shipping_methods_do_not_sort_by_price'
                    ),
                    //** Start Delivery Estimate Options - Cuttoff Time
                    'service_wwe_lfq_estimates_title' => array(
                        'name' => __('Delivery Estimate Options ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                        'type' => 'text',
                        'desc' => $wwe_lfq_show_delivery_estimates_required,
                        'id' => 'service_wwe_lfq_estimates_title'
                    ),
                    'wwe_lfq_show_delivery_estimates_options_radio' => array(
                        'name' => __("", 'woocommerce-settings-wwe_lfq'),
                        'type' => 'radio',
                        'default' => 'dont_show_estimates',
                        'options' => array(
                            'dont_show_estimates' => __("Don't display delivery estimates.", 'woocommerce'),
                            'delivery_days' => __("Display estimated number of days until delivery.", 'woocommerce'),
                            'delivery_date' => __("Display estimated delivery date.", 'woocommerce'),
                        ),
                        'id' => 'wwe_lfq_delivery_estimates',
                        'class' => $wwe_lfq_disable_show_delivery_estimates . ' wwe_lfq_dont_show_estimate_option',
                    ),
                    //** End Delivery Estimate Options
                    //**Start: Cut Off Time & Ship Date Offset
                    'cutOffTime_shipDateOffset_wwe_lfq_freight' => array(
                        'name' => __('Cut Off Time & Ship Date Offset ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'desc' => $wwe_lfq_cutt_off_time_package_required,
                        'id' => 'wwe_lfq_freight_cutt_off_time_ship_date_offset'
                    ),
                    'orderCutoffTime_wwe_lfq_freight' => array(
                        'name' => __('Order Cut Off Time ', 'woocommerce-settings-wwe_lfq_freight_freight_orderCutoffTime'),
                        'type' => 'text',
                        'placeholder' => '-- : -- --',
                        'desc' => 'Enter the cut off time (e.g. 2.00) for the orders. Orders placed after this time will be quoted as shipping the next business day.',
                        'id' => 'wwe_lfq_freight_order_cut_off_time',
                        'class' => $wwe_lfq_disable_cutt_off_time_ship_date_offset,
                    ),
                    'shipmentOffsetDays_wwe_lfq_freight' => array(
                        'name' => __('Fullfillment Offset Days ', 'woocommerce-settings-wwe_lfq_freight_shipment_offset_days'),
                        'type' => 'text',
                        'desc' => 'The number of days the ship date needs to be moved to allow the processing of the order.',
                        'placeholder' => 'Fullfillment Offset Days, e.g. 2',
                        'id' => 'wwe_lfq_freight_shipment_offset_days',
                        'class' => $wwe_lfq_disable_cutt_off_time_ship_date_offset,
                    ),
                    'all_shipment_days_wwe_lfq' => array(
                        'name' => __("What days do you ship orders?", 'woocommerce-settings-wwe_lfq_quotes'),
                        'type' => 'checkbox',
                        'desc' => 'Select All',
                        'class' => "all_shipment_days_wwe_lfq $wwe_lfq_disable_cutt_off_time_ship_date_offset",
                        'id' => 'all_shipment_days_wwe_lfq'
                    ),
                    'monday_shipment_day_wwe_lfq' => array(
                        'name' => __("", 'woocommerce-settings-wwe_lfq_quotes'),
                        'type' => 'checkbox',
                        'desc' => 'Monday',
                        'class' => "wwe_lfq_shipment_day $wwe_lfq_disable_cutt_off_time_ship_date_offset",
                        'id' => 'monday_shipment_day_wwe_lfq'
                    ),
                    'tuesday_shipment_day_wwe_lfq' => array(
                        'name' => __("", 'woocommerce-settings-wwe_lfq_quotes'),
                        'type' => 'checkbox',
                        'desc' => 'Tuesday',
                        'class' => "wwe_lfq_shipment_day $wwe_lfq_disable_cutt_off_time_ship_date_offset",
                        'id' => 'tuesday_shipment_day_wwe_lfq'
                    ),
                    'wednesday_shipment_day_wwe_lfq' => array(
                        'name' => __("", 'woocommerce-settings-wwe_lfq_quotes'),
                        'type' => 'checkbox',
                        'desc' => 'Wednesday',
                        'class' => "wwe_lfq_shipment_day $wwe_lfq_disable_cutt_off_time_ship_date_offset",
                        'id' => 'wednesday_shipment_day_wwe_lfq'
                    ),
                    'thursday_shipment_day_wwe_lfq' => array(
                        'name' => __("", 'woocommerce-settings-wwe_lfq_quotes'),
                        'type' => 'checkbox',
                        'desc' => 'Thursday',
                        'class' => "wwe_lfq_shipment_day $wwe_lfq_disable_cutt_off_time_ship_date_offset",
                        'id' => 'thursday_shipment_day_wwe_lfq'
                    ),
                    'friday_shipment_day_wwe_lfq' => array(
                        'name' => __("", 'woocommerce-settings-wwe_lfq_quotes'),
                        'type' => 'checkbox',
                        'desc' => 'Friday',
                        'class' => "wwe_lfq_shipment_day $wwe_lfq_disable_cutt_off_time_ship_date_offset",
                        'id' => 'friday_shipment_day_wwe_lfq'
                    ),
                    'show_delivery_estimate_wwe' => array(
                        'title' => __('', 'woocommerce'),
                        'name' => __('', 'woocommerce-settings-wwe_lfq_quotes'),
                        'desc' => '',
                        'id' => 'wwe_lfq_show_delivery_estimates',
                        'css' => '',
                        'default' => '',
                        'type' => 'title',
                    ),
                    //**End: Cut Off Time & Ship Date Offset
                    'Services_to_include_in_quoted_price_wwe' => array(
                        'title' => __('', 'woocommerce'),
                        'name' => __('', 'speed-woocommerce-settings-wwe_quotes'),
                        'desc' => '',
                        'id' => 'woocommerce_wwe_specific_Qurt_Price',
                        'css' => '',
                        'default' => '',
                        'type' => 'title'
                    ),
                    'residential_delivery_options_label' => array(
                        'name' => __('Residential Delivery', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'id' => 'residential_delivery_options_label'
                    ),
                    'residential_delivery_wwe' => array(
                        'name' => __('Always quote as residential delivery ', 'speed-woocommerce-settings-wwe_quotes'),
                        'type' => 'checkbox',
                        'desc' => '',
                        'id' => 'wc_settings_wwe_residential_delivery'
                    ),
                    // Auto-detect residential addresses notification
                    // 'avaibility_auto_residential' => array(
                    //     'name' => __('Auto-detect residential addresses', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                    //     'type' => 'text',
                    //     'class' => 'hidden',
                    //     'desc' => "Click <a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/'>here</a> to add the Residential Address Detection module. (<a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/#documentation'>Learn more</a>)",
                    //     'id' => 'avaibility_auto_residential'
                    // ),
                    'liftgate_delivery_options_label' => array(
                        'name' => __('Lift Gate Delivery ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'id' => 'liftgate_delivery_options_label'
                    ),
                    'lift_gate_delivery_wwe' => array(
                        'name' => __('Always quote lift gate delivery ', 'speed-woocommerce-settings-wwe_quotes'),
                        'type' => 'checkbox',
                        'desc' => '',
                        'id' => 'wc_settings_wwe_lift_gate_delivery',
                        'class' => 'accessorial_service checkbox_fr_add',
                    ),
                    'wwe_quests_liftgate_delivery_as_option' => array(
                        'name' => __('Offer lift gate delivery as an option ', 'woocommerce-settings-wwe_freight'),
                        'type' => 'checkbox',
                        'desc' => __('', 'woocommerce-settings-fedex_freight'),
                        'id' => 'wwe_quests_liftgate_delivery_as_option',
                        'class' => 'accessorial_service checkbox_fr_add',
                    ),
                    // Use my liftgate notification
                    'avaibility_lift_gate' => array(
                        'name' => __('Always include lift gate delivery when a residential address is detected', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'desc' => "Click <a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/'>here</a> to add the Residential Address Detection module. (<a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/#documentation'>Learn more</a>)",
                        'id' => 'avaibility_lift_gate'
                    ),
                    // start notify delivery
                    'notify_delivery_options_label' => array(
                        'name' => __('Notify Before Delivery ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'id' => 'liftgate_delivery_options_label'
                    ),
                    'wwe_quests_notify_delivery_as_option' => array(
                        'name' => __('Always notify before delivery ', 'woocommerce-settings-fedex_freight'),
                        'type' => 'checkbox',
                        'desc' => __('', 'woocommerce-settings-fedex_freight'),
                        'id' => 'wwe_quests_notify_delivery_as_option',
                        'class' => 'accessorial_service checkbox_fr_add',
                    ),
                    // end notify delivery
                    // Start Hot At Terminal
                    'wwe_ltl_hold_at_terminal_checkbox_status' => array(
                        'name' => __('Hold At Terminal', 'woocommerce-settings-fedex_small'),
                        'type' => 'checkbox',
                        'desc' => 'Offer Hold At Terminal as an option ' . $hold_at_terminal_package_required,
                        'class' => $disable_hold_at_terminal,
                        'id' => 'wwe_ltl_hold_at_terminal_checkbox_status',
                    ),
                    'wwe_ltl_hold_at_terminal_fee' => array(
                        'name' => __('', 'ground-transit-settings-ground_transit'),
                        'type' => 'text',
                        'desc' => 'Adjust the price of the Hold At Terminal option.Enter an amount, e.g. 3.75, or a percentage, e.g. 5%.  Leave blank to use the price returned by the carrier.',
                        'class' => $disable_hold_at_terminal,
                        'id' => 'wwe_ltl_hold_at_terminal_fee'
                    ),
                    // Handling Weight
                    'wwe_label_handling_unit' => array(
                        'name' => __('Handling Unit ', 'wwe_freight_wc_settings'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'id' => 'wwe_label_handling_unit'
                    ),
                    'wwe_freight_handling_weight' => array(
                        'name' => __('Weight of Handling Unit  ', 'wwe_freight_wc_settings'),
                        'type' => 'text',
                        'desc' => 'Enter in pounds the weight of your pallet, skid, crate or other type of handling unit.',
                        'id' => 'wwe_freight_handling_weight'
                    ),
                    // max Handling Weight
                    'wwe_freight_maximum_handling_weight' => array(
                        'name' => __('Maximum Weight per Handling Unit  ', 'wwe_freight_wc_settings'),
                        'type' => 'text',
                        'desc' => 'Enter in pounds the maximum weight that can be placed on the handling unit.',
                        'id' => 'wwe_freight_maximum_handling_weight'
                    ),
                    'hand_free_mark_up_wwe' => array(
                        'name' => __('Handling Fee / Markup ', 'speed-woocommerce-settings-wwe_quotes'),
                        'type' => 'text',
                        'desc' => 'Amount excluding tax. Enter an amount, e.g 3.75, or a percentage, e.g, 5%. Leave blank to disable.',
                        'id' => 'wc_settings_wwe_hand_free_mark_up'
                    ),
                    //Ignore items with the following Shipping Class(es) By (K)
                    'en_ignore_items_through_freight_classification' => array(
                        'name' => __('Ignore items with the following Shipping Class(es)', 'speed-woocommerce-settings-wwe_quotes'),
                        'type' => 'text',
                        'desc' => "Enter the <a target='_blank' href = '" . get_admin_url() . "admin.php?page=wc-settings&tab=shipping&section=classes'>Shipping Slug</a> you'd like the plugin to ignore. Use commas to separate multiple Shipping Slug.",
                        'id' => 'en_ignore_items_through_freight_classification'
                    ),
                    //insurance dropdown
                    'wc_settings_wwe_insurance' => array(
                        'name' => __('Insurance Category List', 'speed-woocommerce-settings-wwe_quotes'),
                        'type' => 'select',
                        'default' => 'general_merchandise',
                        'id' => 'wc_settings_wwe_insurance',
                        'options' => array(
                            'general_merchandise' => __('General Merchandise', 'general_merchandise'),
                            'commercial_electronics' => __('Commercial Electronics (Audio; Computer: Hardware, Servers, Parts & Accessories)', 'commercial_electronics'),
                            'consumer_electronics' => __('Consumer Electronics (laptops, cellphones, PDAs, iPads, tablets, notebooks, etc.)', 'consumer_electronics'),
                            'fragile_goods' => __('Fragile Goods (Glass, Ceramic, Porcelain, etc.)', 'fragile_goods'),
                            'Furniture' => __('Furniture (Pianos, Glassware, Tableware, Outdoor Furniture)', 'Furniture'),
                            'Machinery' => __('Machinery, Appliances and Equipment (Medical, Restaurant, Industrial, Scientific)', 'Machinery'),
                            'Miscellaneous' => __('Miscellaneous / Other / Mixed', 'Miscellaneous'),
                            'Beverages' => __('Non-Perishable Foods / Beverages / Commodities / Vitamins', 'Beverages'),
                            'Radioactive' => __('Radioactive / Hazardous / Restricted or Controlled Items', 'Radioactive'),
                            'sewing_machines' => __('Sewing Machines, Equipment and Accessories', 'sewing_machines'),
                            'Wine' => __('Wine / Spirits / Alcohol / Beer', 'Wine'),
                        )
                    ),
                    'allow_for_own_arrangment_wwe' => array(
                        'name' => __('Allow For Own Arrangement ', 'speed-woocommerce-settings-wwe_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('<span class="description">Adds an option in the shipping cart for users to indicate that they will make and pay for their own LTL shipping arrangements.</span>', 'speed-woocommerce-settings-wwe_quotes'),
                        'id' => 'wc_settings_wwe_allow_for_own_arrangment'
                    ),
                    'text_for_own_arrangment_wwe' => array(
                        'name' => __('Text For Own Arrangement ', 'speed-woocommerce-settings-wwe_quotes'),
                        'type' => 'text',
                        'desc' => '',
                        'default' => "I'll arrange my own freight",
                        'id' => 'wc_settings_wwe_text_for_own_arrangment'
                    ),
                    'allow_other_plugins' => array(
                        'name' => __('Show WooCommerce Shipping Options ', 'speed-woocommerce-settings-wwe_quotes'),
                        'type' => 'select',
                        'default' => '3',
                        'desc' => __('Enabled options on WooCommerce Shipping page are included in quote results.', 'speed-woocommerce-settings-wwe_quotes'),
                        'id' => 'wc_settings_wwe_allow_other_plugins',
                        'options' => array(
                            'yes' => __('YES', 'YES'),
                            'no' => __('NO', 'NO'),
                        )
                    ),
                    'return_LTL_quotes_wwe' => array(
                        'name' => __("Return LTL quotes when an order parcel shipment weight exceeds the weight threshold ", 'speed-woocommerce-settings-wwe_quotes'),
                        'type' => 'checkbox',
                        'desc' => '<span class="description" >When checked, the LTL Freight Quote will return quotes when an order’s total weight exceeds the weight threshold (the maximum permitted by WWE and UPS), even if none of the products have settings to indicate that it will ship LTL Freight. To increase the accuracy of the returned quote(s), all products should have accurate weights and dimensions. </span>',
                        'id' => 'en_plugins_return_LTL_quotes'
                    ),
                    // Weight threshold for LTL freight
                    'en_weight_threshold_lfq' => [
                        'name' => __('Weight threshold for LTL Freight Quotes ', 'woocommerce-settings-wwe_freight'),
                        'type' => 'text',
                        'default' => $weight_threshold,
                        'class' => $weight_threshold_class,
                        'id' => 'en_weight_threshold_lfq'
                    ],
                    'unable_retrieve_shipping_clear_wwe' => array(
                        'title' => __('', 'woocommerce'),
                        'name' => __('', 'woocommerce-settings-wwe-quotes'),
                        'desc' => '',
                        'id' => 'unable_retrieve_shipping_clear_wwe',
                        'css' => '',
                        'default' => '',
                        'type' => 'title',
                    ),
                    'unable_retrieve_shipping_wwe' => array(
                        'name' => __('Checkout options if the plugin fails to return a rate ', 'speed-woocommerce-settings-wwe_quotes'),
                        'type' => 'title',
                        'desc' => '<span>When the plugin is unable to retrieve shipping quotes and no other shipping options are provided by an alternative source:</span>',
                        'id' => 'wc_settings_unable_retrieve_shipping_wwe',
                    ),
                    'pervent_checkout_proceed_wwe' => array(
                        'name' => __('', 'speed-woocommerce-settings-wwe_quotes'),
                        'type' => 'radio',
                        'id' => 'pervent_checkout_proceed_wwe_packages',
                        'options' => array(
                            'allow' => __('', 'woocommerce'),
                            'prevent' => __('', 'woocommerce'),
                        ),
                        'id' => 'wc_pervent_proceed_checkout_eniture',
                    ),
                    'section_end_quote' => array(
                        'type' => 'sectionend',
                        'id' => 'wc_settings_quote_section_end'
                    )
                );
                break;

            case 'section-3':

                $this->ltl_warehouse();
                $settings = [];
                break;

            case 'section-4':

                $this->ltl_user_guide();
                $settings = [];
                break;

            default:

                echo '<div class="ltl_connection_section_class">';
                $settings = $this->ltl_section_setting_tab();
                break;
        }

        $settings = apply_filters('en_woo_addons_settings', $settings, $section, speed_en_woo_plugin_wwe_quests);
        // Standard packaging
        $settings = apply_filters('en_woo_pallet_addons_settings', $settings, $section, speed_en_woo_plugin_wwe_quests);
        $settings = $this->avaibility_addon($settings);
        return apply_filters('speed-woocommerce-settings-wwe_quotes', $settings, $section);
    }

    /**
     * avaibility_addon
     * @param array type $settings
     * @return array type
     */
    function avaibility_addon($settings)
    {
        if (is_plugin_active('residential-address-detection/residential-address-detection.php')) {
            unset($settings['avaibility_lift_gate']);
            unset($settings['avaibility_auto_residential']);
        }

        return $settings;
    }

    /**
     * Output
     * @global $current_section
     */
    public function output()
    {

        
        global $current_section;
        $settings = $this->get_settings($current_section);
        WC_Admin_Settings::output_fields($settings);
    }

    /**
     * Save
     * @global $current_section
     */
    public function save()
    {

        global $current_section;
        if ($current_section != 'section-1') {
            $settings = $this->get_settings($current_section);
            // Cuttoff Time
            if (isset($_POST['wwe_lfq_freight_order_cut_off_time']) && $_POST['wwe_lfq_freight_order_cut_off_time'] != '') {
                $time_24_format = $this->wwe_lfq_get_time_in_24_hours(sanitize_text_field($_POST['wwe_lfq_freight_order_cut_off_time']));
                $_POST['wwe_lfq_freight_order_cut_off_time'] = $time_24_format;
            }
            WC_Admin_Settings::save_fields($settings);
        }
    }

    /**
     * Cuttoff Time
     * @param $timeStr
     * @return false|string
     */
    public function wwe_lfq_get_time_in_24_hours($timeStr)
    {
        $cutOffTime = explode(' ', $timeStr);
        $hours = $cutOffTime[0];
        $separator = $cutOffTime[1];
        $minutes = $cutOffTime[2];
        $meridiem = $cutOffTime[3];
        $cutOffTime = "{$hours}{$separator}{$minutes} $meridiem";
        return date("H:i", strtotime($cutOffTime));
    }
}

return new SPEED_WC_ltl_Settings_tabs();
