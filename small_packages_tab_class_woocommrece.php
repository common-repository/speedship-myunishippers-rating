<?php

/**
 * WWE Small Tab Class
 *
 * @package     WWE Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Woo-commerce Setting Tab Class
 */
class SPEED_WC_Settings_Small_Packages extends WC_Settings_Page
{

    /**
     * Constructor
     */
    public function __construct()
    {

        $this->id = 'wwe_small_packages_quotes';
        add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_tab'), 50);
        add_action('woocommerce_sections_' . $this->id, array($this, 'output_sections'));
        add_action('woocommerce_settings_' . $this->id, array($this, 'output'));
        add_action('woocommerce_settings_save_' . $this->id, array($this, 'save'));
    }

    /**
     * Setting Tab
     * @param array $settings_tabs
     */
    public function add_settings_tab($settings_tabs)
    {

        $settings_tabs[$this->id] = __('UPS Settings', 'speed-woocommerce-settings-wwe_small_packages_quotes');
        return $settings_tabs;
    }

    /**
     * Sections
     */
    public function get_sections()
    {

        $sections = array(
            '' => __('Connection Settings', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
            'section-1' => __('Quote Settings', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
            'section-2' => __('Warehouses', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
            'section-3' => __('User Guide', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
        );
        $sections = apply_filters('en_woo_addons_sections', $sections, speed_en_woo_plugin_wwe_small_packages_quotes);
        return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
    }

    /**
     * Warehouse Portion
     */
    public function sm_warehouse()
    {
        require_once 'warehouse-dropship/wild/warehouse/wwe_small_warehouse_template.php';
        require_once 'warehouse-dropship/wild/dropship/wwe_small_dropship_template.php';
    }

    /**
     * User Guide
     */
    public function sm_user_guide()
    {
        include_once('template/guide.php');
    }

    /**
     * Conn Settings
     * @return array
     */
    public function speeship_con_setting()
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
     * Settings
     * @param $section
     */
    public function get_settings($section = null)
    {
        ob_start();
        switch ($section) {

            case 'section-0':
                $settings = $this->speeship_con_setting();
                break;

            case 'section-1':

                $disable_transit = "";
                $transit_package_required = "";

                $disable_hazardous = "";
                $hazardous_package_required = "";

                $action_transit = apply_filters('speed_wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'transit_days');
                if (is_array($action_transit)) {
                    // $disable_transit = "disabled_me";
                    // $transit_package_required = apply_filters('speed_wwe_small_packages_quotes_plans_notification_link', $action_transit);
                }

                $action_hazardous = apply_filters('speed_wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'hazardous_material');
                if (is_array($action_hazardous)) {
                    // $disable_hazardous = "disabled_me";
                    // $hazardous_package_required = apply_filters('speed_wwe_small_packages_quotes_plans_notification_link', $action_hazardous);
                }

                //**Plan_Validation: Cut Off Time & Ship Date Offset
                $disable_wwe_small_cutOffTime_shipDateOffset = "";
                $wwe_small_cutOffTime_shipDateOffset_package_required = "";
                $action_wwe_small_cutOffTime_shipDateOffset = apply_filters('speed_wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'wwe_small_cutOffTime_shipDateOffset');
                if (is_array($action_wwe_small_cutOffTime_shipDateOffset)) {
                    // $disable_wwe_small_cutOffTime_shipDateOffset = "disabled_me";
                    // $wwe_small_cutOffTime_shipDateOffset_package_required = apply_filters('speed_wwe_small_packages_quotes_plans_notification_link', $action_wwe_small_cutOffTime_shipDateOffset);
                }

                //**Plan_Validation: Cut Off Time & Ship Date Offset
                $disable_show_delivery_estimates = "";
                $wwe_small_esimate_delivery_package_required = "";
                $action_estimate_delivery_action = apply_filters('speed_wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'wwe_small_show_delivery_estimates');
                if (is_array($action_estimate_delivery_action)) {
                    // $disable_show_delivery_estimates = "disabled_me";
                    // $wwe_small_esimate_delivery_package_required = apply_filters('speed_wwe_small_packages_quotes_plans_notification_link', $action_estimate_delivery_action);
                }

                //**End: Cut Off Time & Ship Date Offset

                echo '<div class="custom_box_message" style="display: none">Markup Field: (Will accept Dollars and Percentages) You can markup the rates for the individual services. Markup can be either a flat Dollar Amount i.e. If you want the rate quoted to the customer to be $5 higher than the rate you will pay carrier, then enter into the field 5.00. On the other hand, if you would like charges to be enhanced by 5% of what a carrier is charging you. Then enter 5.00% into the field.</div>';
                echo '<div class="quote_section_class_smpkg">';
                $settings = array(
                    'Services_quoted_wwe_small_packages' => array(
                        'title' => __('', 'woocommerce'),
                        'name' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'desc' => '',
                        'id' => 'woocommerce_Services_quoted_wwe_small_packages',
                        'css' => '',
                        'default' => '',
                        'type' => 'title',
                    ),
                    'Sevice_wwe_small_packages' => array(
                        'name' => __('Quote Service Options ', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'title',
                        'desc' => '',
                        'id' => 'wc_settings_Sevice_wwe_small_packages'
                    ),
                    'select_smpkg_services' => array(
                        'name' => __('Select All', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'id' => 'wc_settings_select_all_ampkg_services',
                        'class' => 'sm_all_services',
                    ),
                    'Service_UPS_Ground_quotes' => array(
                        'name' => __('UPS Ground', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wc_settings_Service_UPS_Ground_quotes',
                        'class' => 'quotes_services',
                    ),
                    'Service_UPS_Ground_quotes_markup' => array(
                        'name' => __('', 'wwesmall_Service_UPS_Ground_quotes_markup'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%).', 'wwesmall_Service_UPS_Ground_quotes_markup'),
                        'id' => 'wwesmall_Service_UPS_Ground_quotes_markup',
                        'class' => 'wwe_small_markup',
                    ),
                    'Service_UPS_3rd_Day_quotes' => array(
                        'name' => __('UPS 3 Day Select', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wc_settings_Service_UPS_3rd_Day_quotes',
                        'class' => 'quotes_services',
                    ),
                    'Service_UPS_3rd_Day_quotes_markup' => array(
                        'name' => __('', 'wwesmall_Service_UPS_3rd_Day_quotes_markup'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%).', 'wwesmall_Service_UPS_3rd_Day_quotes_markup'),
                        'id' => 'wwesmall_Service_UPS_3rd_Day_quotes_markup',
                        'class' => 'wwe_small_markup',
                    ),
                    'Service_UPS_2nd_Day_Saturday_quotes' => array(
                        'name' => __('UPS 2nd Day Air (Saturday Delivery)', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wc_settings_Service_UPS_2nd_Day_Saturday_quotes',
                        'class' => 'quotes_services',
                    ),
                    'Service_UPS_2nd_Day_Saturday_quotes_markup' => array(
                        'name' => __('', 'wwesmall_Service_UPS_2nd_Day_Saturday_quotes_markup'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%).', 'wwesmall_Service_UPS_2nd_Day_Saturday_quotes_markup'),
                        'id' => 'wwesmall_Service_UPS_2nd_Day_Saturday_quotes_markup',
                        'class' => 'wwe_small_markup',
                    ),
                    'Service_UPS_2nd_Day_PM_quotes' => array(
                        'name' => __('UPS 2nd Day Air', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wc_settings_Service_UPS_2nd_Day_PM_quotes',
                        'class' => 'quotes_services',
                    ),
                    'Service_UPS_2nd_Day_PM_quotes_markup' => array(
                        'name' => __('', 'wwesmall_Service_UPS_2nd_Day_PM_quotes_markup'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%).', 'wwesmall_Service_UPS_2nd_Day_PM_quotes_markup'),
                        'id' => 'wwesmall_Service_UPS_2nd_Day_PM_quotes_markup',
                        'class' => 'wwe_small_markup',
                    ),
                    'Service_UPS_2nd_Day_AM_quotes' => array(
                        'name' => __('UPS 2nd Day Air A.M.', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wc_settings_Service_UPS_2nd_Day_AM_quotes',
                        'class' => 'quotes_services',
                    ),
                    'Service_UPS_2nd_Day_AM_quotes_markup' => array(
                        'name' => __('', 'wwesmall_Service_UPS_2nd_Day_AM_quotes_markup'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%).', 'wwesmall_Service_UPS_2nd_Day_AM_quotes_markup'),
                        'id' => 'wwesmall_Service_UPS_2nd_Day_AM_quotes_markup',
                        'class' => 'wwe_small_markup',
                    ),
                    'Service_UPS_Next_Day_Air_Saver_small_packages_quotes' => array(
                        'name' => __('UPS Next Day Air Saver', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wc_settings_Service_UPS_Next_Day_Air_Saver_small_packages_quotes',
                        'class' => 'quotes_services',
                    ),
                    'Service_UPS_Next_Day_Air_Saver_small_packages_quotes_markup' => array(
                        'name' => __('', 'wwesmall_Service_UPS_Next_Day_Air_Saver_small_packages_quotes_markup'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%).', 'wwesmall_Service_UPS_Next_Day_Air_Saver_small_packages_quotes_markup'),
                        'id' => 'wwesmall_Service_UPS_Next_Day_Air_Saver_small_packages_quotes_markup',
                        'class' => 'wwe_small_markup',
                    ),
                    'Service_UPS_Next_Day_Air_small_packages_quotes' => array(
                        'name' => __('UPS Next Day Air', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wc_settings_Service_UPS_Next_Day_Air_small_packages_quotes',
                        'class' => 'quotes_services',
                    ),
                    'Service_UPS_Next_Day_Air_small_packages_quotes_markup' => array(
                        'name' => __('', 'wwesmall_Service_UPS_Next_Day_Air_small_packages_quotes_markup'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%).', 'wwesmall_Service_UPS_Next_Day_Air_small_packages_quotes_markup'),
                        'id' => 'wwesmall_Service_UPS_Next_Day_Air_small_packages_quotes_markup',
                        'class' => 'wwe_small_markup',
                    ),
                    'Service_UPS_Next_Day_Early_AM_small_packages_quotes_tab_class' => array(
                        'name' => __('UPS Next Day Air Early', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wc_settings_Service_UPS_Next_Day_Early_AM_small_packages_quotes',
                        'class' => 'quotes_services',
                    ),
                    'Service_UPS_Next_Day_Early_AM_small_packages_quotes_markup' => array(
                        'name' => __('', 'wwesmall_Service_UPS_Next_Day_Early_AM_small_packages_quotes_markup'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%).', 'wwesmall_Service_UPS_Next_Day_Early_AM_small_packages_quotes_markup'),
                        'id' => 'wwesmall_Service_UPS_Next_Day_Early_AM_small_packages_quotes_markup',
                        'class' => 'wwe_small_markup',
                    ),
                    'wwe_small_sort_wwe_small' => array(
                        'name' => __("Don't sort shipping methods by price  ", 'woocommerce-settings-wwe_small_quotes'),
                        'type' => 'checkbox',
                        'desc' => 'By default, the plugin will sort all shipping methods by price in ascending order.',
                        'id' => 'shipping_methods_do_not_sort_by_price'
                    ),
                    // show delivery estimates options
                    'service_wwe_small_estimates_title' => array(
                        'name' => __('Delivery Estimate Options ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'desc' => $wwe_small_esimate_delivery_package_required,
                        'id' => 'service_wwe_small_estimates_title'
                    ),
                    'dont_show_estimates_wwe_small' => array(
                        'name' => __('', 'woocommerce-settings-wwe_small_quotes'),
                        'type' => 'radio',
                        'class' => "$disable_show_delivery_estimates",
                        'default' => "dont_show_estimates",
                        'options' => array(
                            'dont_show_estimates' => __("Don't display delivery estimates.", 'woocommerce'),
                            'delivery_days' => __('Display estimated number of days until delivery.', 'woocommerce'),
                            'delivery_date' => __('Display estimated delivery date.', 'woocommerce'),
                        ),
                        'id' => 'wwe_small_delivery_estimates',
                    ),
                    //**Start: Cut Off Time & Ship Date Offset
                    'wwe_small_cutOffTime_shipDateOffset_wwe_small' => array(
                        'name' => __('Cut Off Time & Ship Date Offset ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'desc' => $wwe_small_cutOffTime_shipDateOffset_package_required,
                        'id' => 'wwe_small_cutOffTime_shipDateOffset'
                    ),
                    'orderCutoffTime_wwe_small' => array(
                        'name' => __('Order Cut Off Time ', 'woocommerce-settings-wwe_small_freight_orderCutoffTime'),
                        'type' => 'text',
                        'placeholder' => '--:-- --',
                        'desc' => 'Enter the cut off time (e.g. 2.00) for the orders. Orders placed after this time will be quoted as shipping the next business day.',
                        'id' => 'wwe_small_orderCutoffTime',
                        'class' => $disable_wwe_small_cutOffTime_shipDateOffset,
                    ),
                    'shipmentOffsetDays_wwe_small' => array(
                        'name' => __('Fulfilment Offset Days ', 'woocommerce-settings-wwe_small_shipmentOffsetDays'),
                        'type' => 'text',
                        'desc' => 'The number of days the ship date needs to be moved to allow the processing of the order.',
                        'placeholder' => 'Fulfilment Offset Days, e.g. 2',
                        'id' => 'wwe_small_shipmentOffsetDays',
                        'class' => $disable_wwe_small_cutOffTime_shipDateOffset,
                    ),
                    //                  Start Transit days            
                    'ground_transit_label' => array(
                        'name' => __('Ground transit time restriction', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'desc' => $transit_package_required,
                        'id' => 'ground_transit_label'
                    ),
                    'ground_transit_resident_wwe_small_packages' => array(
                        'name' => __('Enter the number of transit days to restrict ground service to. Leave blank to disable this feature.', 'ground-transit-settings-ground_transit'),
                        'type' => 'text',
                        'class' => $disable_transit,
                        'id' => 'ground_transit_wwe_small_packages'
                    ),
                    'restrict_calendar_transit_wwe_small_packages' => array(
                        'name' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'radio',
                        'class' => "$disable_transit restrict_by_calendar_days_in_transit_1st_option",
                        'options' => array(
                            'TransitTimeInDays' => __('Restrict the carriers in transit days metric.', 'woocommerce'),
                            'CalenderDaysInTransit' => __('Restrict by calendar days in transit.', 'woocommerce'),
                        ),
                        'id' => 'restrict_calendar_transit_wwe_small_packages',
                    ),
                    //                  End Transit days 
                    'Service_UPS_Next_Day_Early_AM_small_packages_quotes' => array(
                        'name' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'title',
                        'class' => 'hidden',
                    ),
                    'residential_delivery_options_label' => array(
                        'name' => __('Residential Delivery', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'id' => 'residential_delivery_options_label'
                    ),
                    // 'quest_as_residential_delivery_wwe_small_packages' => array(
                    //     'name' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                    //     'type' => 'checkbox',
                    //     'class' => 'hidden',
                    //     'desc' => __('Always quote as residential delivery.', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                    //     'id' => 'wc_settings_quest_as_residential_delivery_wwe_small_packages'
                    // ),
                    'wwex_quest_as_residential_delivery_wwe_small_packages' => array(
                        'name' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'radio',
                        'default' => "quoteAsResidential",
                        'options' => array(
                            'quoteAsResidential' => __('Always quote as residential delivery.', 'woocommerce'),
                            'autoDetectResidential' => __('Auto-detect residential addresses.', 'woocommerce'),
                        ),
                        'id' => 'wc_settings_wwex_quest_as_residential_delivery_wwe_small_packages',
                    ),
                    //                  Auto-detect residential addresses notification
                    // 'avaibility_auto_residential' => array(
                    //     'name' => __('', 'woocommerce-settings-fedex_small'),
                    //     'type' => 'text',
                    //     'class' => 'hidden',
                    //     'desc' => "Click <a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/'>here</a> to add the Auto-detect residential addresses module. (<a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/#documentation'>Learn more</a>)",
                    //     'id' => 'avaibility_auto_residential'
                    // ),
                    //                  Use my standard box sizes notification
                    // 'avaibility_box_sizing' => array(
                    //     'name' => __('Use my standard box sizes', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                    //     'type' => 'text',
                    //     'class' => 'hidden',
                    //     'desc' => "Click <a target='_blank' href='https://eniture.com/woocommerce-standard-box-sizes/'>here</a> to add the Standard Box Sizes module. (<a target='_blank' href='https://eniture.com/woocommerce-standard-box-sizes/#documentation'>Learn more</a>)",
                    //     'id' => 'avaibility_box_sizing'
                    // ),
                    //                  Start Hazardous Material
                    'hazardous_material_settings' => array(
                        'name' => __('Hazardous material settings', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'desc' => $hazardous_package_required,
                        'id' => 'hazardous_material_settings'
                    ),
                    'only_quote_ground_service_for_hazardous_materials_shipments' => array(
                        'name' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => 'Only quote ground service for hazardous materials shipments',
                        'class' => $disable_hazardous,
                        'id' => 'only_quote_ground_service_for_hazardous_materials_shipments',
                    ),
                    'ground_hazardous_material_fee' => array(
                        'name' => __('Ground Hazardous Material Fee', 'ground-transit-settings-ground_transit'),
                        'type' => 'text',
                        'desc' => 'Enter an amount, e.g 20. or Leave blank to disable.',
                        'class' => $disable_hazardous,
                        'id' => 'ground_hazardous_material_fee'
                    ),
                    'air_hazardous_material_fee' => array(
                        'name' => __('Air Hazardous Material Fee', 'ground-transit-settings-ground_transit'),
                        'type' => 'text',
                        'desc' => 'Enter an amount, e.g 20. or Leave blank to disable.',
                        'class' => $disable_hazardous,
                        'id' => 'air_hazardous_material_fee'
                    ),
                    //                  End Hazardous Material
                    'hand_free_mark_up_wwe_small_packages' => array(
                        'name' => __('Handling Fee / Markup ', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'text',
                        'desc' => 'Amount excluding tax. Enter an amount, e.g 3.75, or a percentage, e.g, 5%. Leave blank to disable.',
                        'id' => 'wc_settings_hand_free_mark_up_wwe_small_packages'
                    ),
                    //Ignore items with the following Shipping Class(es) By (K)
                    'en_ignore_items_through_freight_classification' => array(
                        'name' => __('Ignore items with the following Shipping Class(es)', 'speed-woocommerce-settings-wwe_quotes'),
                        'type' => 'text',
                        'desc' => "Enter the <a target='_blank' href = '" . get_admin_url() . "admin.php?page=wc-settings&tab=shipping&section=classes'>Shipping Slug</a> you'd like the plugin to ignore. Use commas to separate multiple Shipping Slug.",
                        'id' => 'en_ignore_items_through_freight_classification'
                    ),
                    'allow_other_plugins_wwe_small_packages' => array(
                        'name' => __('Allow other plugins to show quotes ', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'select',
                        'default' => '3',
                        'desc' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wc_settings_wwe_small_allow_other_plugins',
                        'options' => array(
                            'no' => __('NO', 'NO'),
                            'yes' => __('YES', 'YES')
                        )
                    ),
                    'unable_retrieve_shipping_clear_wwe_small_packages' => array(
                        'title' => __('', 'woocommerce'),
                        'name' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'desc' => '',
                        'id' => 'woocommerce_unable_retrieve_shipping_clear_wwe_small_packages',
                        'css' => '',
                        'default' => '',
                        'type' => 'title',
                    ),
                    'unable_retrieve_shipping_wwe_small_packages' => array(
                        'name' => __('Checkout options if the plugin fails to return a rate ', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'title',
                        'desc' => 'When the plugin is unable to retrieve shipping quotes and no other shipping options are provided by an alternative source:',
                        'id' => 'wc_settings_unable_retrieve_shipping_wwe_small_packages'
                    ),
                    'pervent_checkout_proceed_wwe_small_packages' => array(
                        'name' => __('', 'speed-woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'radio',
                        'id' => 'pervent_checkout_proceed_wwe_small_packages',
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

            case 'section-2':
                $this->sm_warehouse();
                $settings = [];
                break;

            case 'section-3':
                $this->sm_user_guide();
                $settings = [];
                break;

            default:
                $settings = $this->speeship_con_setting();
                break;
        }
        $settings = apply_filters('en_woo_addons_settings', $settings, $section, speed_en_woo_plugin_wwe_small_packages_quotes);
        $settings = apply_filters('en_woo_pallet_addons_settings', $settings, $section, speed_en_woo_plugin_wwe_small_packages_quotes);
        $settings = $this->avaibility_addon($settings);
        return apply_filters('speed-woocommerce-settings-wwe_small_packages_quotes', $settings, $section);
    }

    function avaibility_addon($settings)
    {
        // if (is_plugin_active('residential-address-detection/residential-address-detection.php')) {
        //     unset($settings['avaibility_auto_residential']);
        // }

        if (is_plugin_active('standard-box-sizes/en-standard-box-sizes.php') || is_plugin_active('standard-box-sizes/standard-box-sizes.php')) {
            unset($settings['avaibility_box_sizing']);
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
        $settings = $this->get_settings($current_section);
        if (isset($_POST['wwe_small_orderCutoffTime']) && $_POST['wwe_small_orderCutoffTime'] != '') {
            $time24Formate = $this->getTimeIn24Hours(sanitize_text_field($_POST['wwe_small_orderCutoffTime']));
            $_POST['wwe_small_orderCutoffTime'] = $time24Formate;
        }
        WC_Admin_Settings::save_fields($settings);
    }

    /**
     * @param $timeStr
     * @return false|string
     */
    public function getTimeIn24Hours($timeStr)
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

return new SPEED_WC_Settings_Small_Packages();
