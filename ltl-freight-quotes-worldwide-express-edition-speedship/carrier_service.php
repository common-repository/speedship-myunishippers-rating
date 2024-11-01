<?php

/**
 * WWE LTL Carrier Service
 *
 * @package     WWE LTL Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class speed_ltl_shipping_get_quotes
 */
class speed_ltl_shipping_get_quotes extends Wwe_Ltl_Liftgate_As_Option
{

    /**
     * $EndPointURL
     * @var string type
     */
    protected $EndPointURL = SPEED_WWE_FREIGHT_DOMAIN_HITTING_URL . '/carriers/wwe-freight/speedFreightQuotes.php';
    public $en_wd_origin_array;
    public $InstorPickupLocalDelivery;

    /**
     * details array
     * @var array type
     */
    public $quote_settings;

    function __construct()
    {
        $this->quote_settings = [];
    }

    /**
     * Get Web Service Array
     * @param $packages
     * @return array
     */
    function ltl_shipping_get_web_service_array($packages, $package_plugin = "")
    {
        // FDO
        $SpeedEnSpeedfreightFdo = new SpeedEnSpeedfreightFdo();
        $en_fdo_meta_data = [];

        $destinationAddressWwe = $this->destinationAddressWwe();
        $packages['origin']['city'] = (isset($packages['origin']['corrected_city']) && !empty($packages['origin']['corrected_city'])) ? $packages['origin']['corrected_city'] : $packages['origin']['city'];

        $wwe_residential_delivery = 'N';
        if (get_option('wc_settings_wwe_residential_delivery') == 'yes') {
            $wwe_residential_delivery = 'Y';
        }

        $wwe_lift_gate_delivery = 'N';
        if (get_option('wc_settings_wwe_lift_gate_delivery') == 'yes') {
            $wwe_lift_gate_delivery = 'Y';
        }

        $wwe_notify_delivery = 'N';
        if (get_option('wwe_quests_notify_delivery_as_option') == 'yes') {
            $wwe_notify_delivery = 'Y';
        }

        $freightClass_ltl_gross = "";
        $doNesting = 0;
        $pricing_per_product = $nmfc_number = $lineItemPackageType = $lineItemPalletFlag = $stakingProperty = $nestedItems = $nestedDimension = $nestedDimension = $productName = $productQty = $productPrice = $productWeight = $productLength = $productWidth = $productHeight = $productClass = $product_name = $nestingPercentage = [];
        $hazmat_line_item = array(
            'isHazmatLineItem' => 'Y', // Y / N
            'lineItemHazmatUNNumberHeader' => 'UN #', // Valid values are : UN #, ID #, NA #.
            'lineItemHazmatUNNumber' => 'UN 1139', // Every account have its own UN Number
            'lineItemHazmatClass' => '1.1', // valid hazmat class (1, 2.1, 2.2, 2.3, 3, 4.1, 4.2, 4.3, 5.1, 5.2, 6.1, 6.2,7, 8.,9) (Optional)
            'lineItemHazmatEmContactPhone' => '4043308699', // hazmat contact phone number (Required)
            'lineItemHazmatPackagingGroup' => 'I', // hazmat packaging group (I, II, III) (Optional)
        );

        // Cuttoff Time
        $shipment_week_days = "";
        $order_cut_off_time = "";
        $shipment_off_set_days = "";
        $modify_shipment_date_time = "";
        $store_date_time = "";
        $wwe_lfq_delivery_estimates = get_option('wwe_lfq_delivery_estimates');
        $wwe_lfq_show_delivery_estimates = apply_filters('speed_wwe_quests_quotes_plans_suscription_and_features', 'wwe_lfq_show_delivery_estimates');
        $shipment_week_days = $this->wwe_lfq_shipment_week_days();
        if ($wwe_lfq_delivery_estimates == 'delivery_days' || $wwe_lfq_delivery_estimates == 'delivery_date' && !is_array($wwe_lfq_show_delivery_estimates)) {
            $order_cut_off_time = $this->quote_settings['orderCutoffTime'];
            $shipment_off_set_days = $this->quote_settings['shipmentOffsetDays'];
            $modify_shipment_date_time = ($order_cut_off_time != '' || $shipment_off_set_days != '' || (is_array($shipment_week_days) && count($shipment_week_days) > 0)) ? 1 : 0;
            $store_date_time = $today = date('Y-m-d H:i:s', current_time('timestamp'));
        }

        //      check plan for nested material
        $nested_plan = apply_filters('speed_wwe_quests_quotes_plans_suscription_and_features', 'nested_material');

        $this->en_wd_origin_array = (isset($packages['origin'])) ? $packages['origin'] : [];
        $doNesting = "";

        foreach ($packages['items'] as $item) {

            $productName[] = $item['productName'];
            $productWeight[] = $item['productWeight'];
            $productLength[] = $item['productLength'];
            $productWidth[] = $item['productWidth'];
            $productHeight[] = $item['productHeight'];
            $productQty[] = $item['productQty'];
            $productPrice[] = $item['productPrice'];
            $productClass[] = $item['productClass'];
            $product_name[] = $item['product_name'];
            $nestingPercentage[] = $item['nestedPercentage'];
            $nestedDimension[] = $item['nestedDimension'];
            $nestedItems[] = $item['nestedItems'];
            $stakingProperty[] = $item['stakingProperty'];

            $nmfc_number[] = (isset($item['nmfc_number'])) ? $item['nmfc_number'] : '';

            $pricing_per_product[] = [
                'product_insurance' => $item['product_insurance'],
                'product_markup' => $item['product_markup'],
                'product_quantity' => $item['product_quantity'],
                'product_price' => $item['product_price']
            ];

            // Shippable handling units
            $lineItemPalletFlag[] = $item['lineItemPalletFlag'];
            $lineItemPackageType[] = $item['lineItemPackageType'];

            isset($item['nestedMaterial']) && !empty($item['nestedMaterial']) &&
                $item['nestedMaterial'] == 'yes' && !is_array($nested_plan) ? $doNesting = 1 : "";
        }

        $aPluginVersions = $this->ltl_get_woo_version_number();
        $domain = wwe_quests_get_domain();

        $residential_detecion_flag = get_option("en_woo_addons_auto_residential_detecion_flag");

        // FDO
        $en_fdo_meta_data = $SpeedEnSpeedfreightFdo->en_cart_package($packages);

        $post_data = array(
            'plateform' => 'WordPress',
            'plugin_version' => $aPluginVersions["wwe_ltl_plugin_version"],
            'wordpress_version' => get_bloginfo('version'),
            'woocommerce_version' => $aPluginVersions["woocommerce_plugin_version"],
            'speed_freight_username' => get_option('wc_settings_wwe_speed_freight_username'),
            'speed_freight_password' => get_option('wc_settings_wwe_speed_freight_password'),
            'authentication_key' => get_option('wc_settings_wwe_authentication_key'),
            'world_wide_express_account_number' => get_option('wc_settings_wwe_world_wide_express_account_number'),
            'plugin_licence_key' => get_option('wc_settings_wwe_licence_key'),

            //Blaze added oauth to post data
            // 'speedship_url' => get_option('wc_settings_speedship_url_wwe_small_packages_quotes'),
            // 'oauth_url' => get_option('wc_settings_oauth_url_wwe_small_packages_quotes'),
            'oauth_clientid' => get_option('wc_settings_oauth_clientid_wwe_small_packages_quotes'),
            'oauth_client_secret' => get_option('wc_settings_oauth_client_secret_wwe_small_packages_quotes'),
            // 'oauth_audience' => get_option('wc_settings_oauth_audience_wwe_small_packages_quotes'),
            // 'oauth_username' => get_option('wc_settings_oauth_username_wwe_small_packages_quotes'),
            // 'oauth_password' => get_option('wc_settings_oauth_password_wwe_small_packages_quotes'),

            'suspend_residential' => get_option('suspend_automatic_detection_of_residential_addresses'),
            'residential_detecion_flag' => $residential_detecion_flag,
            'plugin_domain_name' => speed_ltl_speedfreight_parse_url($domain),
            'freight_reciver_city' => $destinationAddressWwe['city'],
            'freight_receiver_state' => $destinationAddressWwe['state'],
            'freight_receiver_zip_code' => $destinationAddressWwe['zip'],
            'speed_freight_residential_delivery' => $wwe_residential_delivery,
            'speed_freight_lift_gate_delivery' => $wwe_lift_gate_delivery,
            'speed_freight_notify_before_delivery' => $wwe_notify_delivery,
            'speed_freight_senderCity' => $packages['origin']['city'],
            'speed_freight_senderState' => $packages['origin']['state'],
            'speed_freight_senderZip' => $packages['origin']['zip'],
            'speed_freight_senderCountryCode' => $packages['origin']['country'],
            'sender_origin' => $packages['origin']['location'] . ": " . $packages['origin']['city'] . ", " . $packages['origin']['state'] . " " . $packages['origin']['zip'],
            'product_name' => $product_name,
            'speed_freight_class' => $productClass,
            'product_width_array' => $productWidth,
            'product_height_array' => $productHeight,
            'product_length_array' => $productLength,
            //insurance
            'insureShipment' => isset($packages['insurance']) ? $packages['insurance'] : '',
            'speed_freight_product_price_array' => $productPrice,
            'speed_freight_product_weight' => $productWeight,
            'speed_freight_post_title_array' => $productName,
            'speed_freight_post_quantity_array' => $productQty,
            //          Nested indexes
            'doNesting' => $doNesting,
            'nesting_percentage' => $nestingPercentage,
            'nesting_dimension' => $nestedDimension,
            'nested_max_limit' => $nestedItems,
            'nested_stack_property' => $stakingProperty,
            'handlingUnitWeight' => get_option('wwe_freight_handling_weight'),
            // Max Handling Weight
            'maxWeightPerHandlingUnit' => get_option('wwe_freight_maximum_handling_weight'),
            // FDO
            'en_fdo_meta_data' => $en_fdo_meta_data,
            'isFromSubDomain' => 1,
            // Shippable handling units
            'speed_freight_ship_as_pallet' => $lineItemPalletFlag,
            'speed_freight_package_types_array' => $lineItemPackageType,
            // NMFC Number
            'speed_freight_product_nmfc' => $nmfc_number,
            // Cuttoff Time
            'modifyShipmentDateTime' => $modify_shipment_date_time,
            'OrderCutoffTime' => $order_cut_off_time,
            'shipmentOffsetDays' => $shipment_off_set_days,
            'storeDateTime' => $store_date_time,
            'shipmentWeekDays' => $shipment_week_days,
            // Pricing per product
            'pricing_per_product' => $pricing_per_product
        );

        // Insurance
        $wc_settings_wwe_insurance_list = get_option('wc_settings_wwe_insurance');
        if (isset($packages['insurance']) && $packages['insurance'] == true) {
            switch ($wc_settings_wwe_insurance_list) {
                case 'general_merchandise':
                    $insuranceCategory['code'] = '84';
                    $insuranceCategory['value'] = 'general_merchandise';
                    break;
                case 'commercial_electronics':
                    $insuranceCategory['code'] = '86';
                    $insuranceCategory['value'] = 'commercial_electronics';
                    break;
                case 'consumer_electronics':
                    $insuranceCategory['code'] = '87';
                    $insuranceCategory['value'] = 'consumer_electronics';
                    break;
                case 'fragile_goods':
                    $insuranceCategory['code'] = '88';
                    $insuranceCategory['value'] = 'fragile_goods';
                    break;
                case 'Furniture':
                    $insuranceCategory['code'] = '89';
                    $insuranceCategory['value'] = 'Furniture';
                    break;
                case 'Machinery':
                    $insuranceCategory['code'] = '90';
                    $insuranceCategory['value'] = 'Machinery';
                    break;
                case 'Miscellaneous':
                    $insuranceCategory['code'] = '91';
                    $insuranceCategory['value'] = 'Miscellaneous';
                    break;
                case 'Beverages':
                    $insuranceCategory['code'] = '92';
                    $insuranceCategory['value'] = 'Beverages';
                    break;
                case 'Radioactive':
                    $insuranceCategory['code'] = '93';
                    $insuranceCategory['value'] = 'Radioactive';
                    break;
                case 'sewing_machines':
                    $insuranceCategory['code'] = '94';
                    $insuranceCategory['value'] = 'sewing_machines';
                    break;
                case 'Wine':
                    $insuranceCategory['code'] = '96';
                    $insuranceCategory['value'] = 'Wine';
                    break;
            }

            $post_data['insuranceCategory'] = $insuranceCategory;
        }

        //      Hazardous Material
        $hazardous_material = apply_filters('speed_wwe_quests_quotes_plans_suscription_and_features', 'hazardous_material');
        if (!is_array($hazardous_material)) {
            (isset($packages['hazardousMaterial']) == 'yes') ? $post_data['hazardous'][] = 'H' : '';
            (isset($packages['hazardousMaterial'])) ? $post_data['lineItemHazmatInfo'][] = $hazmat_line_item : "";
            // FDO
            $post_data['en_fdo_meta_data'] = array_merge($post_data['en_fdo_meta_data'], $SpeedEnSpeedfreightFdo->en_package_hazardous($packages, $en_fdo_meta_data));
        }

        //      In-store pickup and local delivery
        $instore_pickup_local_devlivery_action = apply_filters('speed_wwe_quests_quotes_plans_suscription_and_features', 'instore_pickup_local_devlivery');
        if (!is_array($instore_pickup_local_devlivery_action)) {
            $post_data = apply_filters('en_wwe_ltl_wd_standard_plans', $post_data, $post_data['freight_receiver_zip_code'], $this->en_wd_origin_array, $package_plugin);
        }

        //      Hold At Terminal
        $hold_at_terminal = apply_filters('speed_wwe_quests_quotes_plans_suscription_and_features', 'hold_at_terminal');
        if (!is_array($hold_at_terminal)) {
            (isset($this->quote_settings['HAT_status']) && ($this->quote_settings['HAT_status'] == 'yes')) ? $post_data['holdAtTerminal'] = '1' : '';
        }

        $post_data = $this->wwe_ltl_update_carrier_service($post_data);
        $post_data = apply_filters("en_woo_addons_carrier_service_quotes_request", $post_data, speed_en_woo_plugin_wwe_quests);

        // Configure standard plugin with pallet packaging addon
        $post_data = apply_filters('en_pallet_identify', $post_data);

        do_action("eniture_debug_mood", "WWE LTL Quotes Request", $post_data);

        return $post_data;
    }

    /**
     * @return shipment days of a week  - Cuttoff time
     */
    public function wwe_lfq_shipment_week_days()
    {
        $shipment_days_of_week = [];

        if (get_option('all_shipment_days_wwe_lfq') == 'yes') {
            return $shipment_days_of_week;
        }

        if (get_option('monday_shipment_day_wwe_lfq') == 'yes') {
            $shipment_days_of_week[] = 1;
        }
        if (get_option('tuesday_shipment_day_wwe_lfq') == 'yes') {
            $shipment_days_of_week[] = 2;
        }
        if (get_option('wednesday_shipment_day_wwe_lfq') == 'yes') {
            $shipment_days_of_week[] = 3;
        }
        if (get_option('thursday_shipment_day_wwe_lfq') == 'yes') {
            $shipment_days_of_week[] = 4;
        }
        if (get_option('friday_shipment_day_wwe_lfq') == 'yes') {
            $shipment_days_of_week[] = 5;
        }

        return $shipment_days_of_week;
    }

    /**
     * destinationAddressWwe
     * @return array type
     */
    function destinationAddressWwe()
    {
        $en_order_accessories = apply_filters('en_order_accessories', []);
        if (isset($en_order_accessories) && !empty($en_order_accessories)) {
            return $en_order_accessories;
        }

        $changObj = new Speed_Woo_Update_Changes();
        $freight_zipcode = (strlen(WC()->customer->get_shipping_postcode()) > 0) ? WC()->customer->get_shipping_postcode() : $changObj->speedfreight_postcode();
        $freight_state = (strlen(WC()->customer->get_shipping_state()) > 0) ? WC()->customer->get_shipping_state() : $changObj->speedfreight_getState();
        $freight_country = (strlen(WC()->customer->get_shipping_country()) > 0) ? WC()->customer->get_shipping_country() : $changObj->speedfreight__getCountry();
        $freight_city = (strlen(WC()->customer->get_shipping_city()) > 0) ? WC()->customer->get_shipping_city() : $changObj->speedfreight_getCity();
        return array(
            'city' => $freight_city,
            'state' => $freight_state,
            'zip' => $freight_zipcode,
            'country' => $freight_country
        );
    }

    /**
     * Get Web Quotes CURL Call
     * @param $request_data
     * @return json
     */
    function ltl_shipping_get_web_quotes($request_data)
    {

        //      Eniture debug mood
        do_action("eniture_debug_mood", "WWE Build Query", http_build_query($request_data));
        //      check response from session 
        $currentData = md5(json_encode($request_data));
        $requestFromSession = WC()->session->get('previousRequestData');
        $requestFromSession = ((is_array($requestFromSession)) && (!empty($requestFromSession))) ? $requestFromSession : [];

        if (isset($requestFromSession[$currentData]) && (!empty($requestFromSession[$currentData]))) {

            //          Eniture debug mood
            do_action("eniture_debug_mood", "WWE Features", get_option('eniture_plugin_2'));

            do_action("eniture_debug_mood", "WWE LTL Quotes session Response", json_decode($requestFromSession[$currentData]));

            $quotes['quotes'] = json_decode($requestFromSession[$currentData]);
            $quotes['markup'] = (isset($request_data['markup'])) ? $request_data['markup'] : "";

            return $this->parse_wwe_ltl_output($quotes, $request_data);

            return $requestFromSession[$currentData];
        }

        if (is_array($request_data) && count($request_data) > 0) {

            $wwe_ltl_curl_obj = new Speed_WWE_LTL_Curl_Request();
            $output = $wwe_ltl_curl_obj->wwe_ltl_get_curl_response($this->EndPointURL, $request_data);

            //          Eniture debug mood
            do_action("eniture_debug_mood", "WWE LTL Quotes Response", json_decode($output));

            //          set response in session                
            $response = json_decode($output);

            $errorDescriptions = (isset($response->q->quoteSpeedFreightShipmentReturn->errorDescriptions) ? $response->q->quoteSpeedFreightShipmentReturn->errorDescriptions : NULL);

            if (isset($response->q) && (!empty($response->q)) && ($errorDescriptions == NULL)) {
                if (
                    isset($response->autoResidentialSubscriptionExpired) &&
                    ($response->autoResidentialSubscriptionExpired == 1)
                ) {
                    $flag_api_response = "no";
                    $request_data['residential_detecion_flag'] = $flag_api_response;
                    $currentData = md5(json_encode($request_data));
                }

                $requestFromSession[$currentData] = $output;
                WC()->session->set('previousRequestData', $requestFromSession);
            }

            $quotes['quotes'] = $response;
            $quotes['markup'] = "";
            return $this->parse_wwe_ltl_output($quotes, $request_data);
        }
    }

    /**
     * Get Shipping Array For Single Shipment
     * @param $output
     * @return Single Quote Array
     */
    function parse_wwe_ltl_output($result, $request_data)
    {
        // Pricing per product
        $pricing_per_product = (isset($request_data['pricing_per_product'])) ? $request_data['pricing_per_product'] : [];

        // FDO
        $en_fdo_meta_data = (isset($request_data['en_fdo_meta_data'])) ? $request_data['en_fdo_meta_data'] : '';
        if (isset($result['quotes']->debug)) {
            $en_fdo_meta_data['handling_unit_details'] = $result['quotes']->debug;
        }

        if (isset($result['quotes']->requestedLineItems)) {
            $en_fdo_meta_data['requested_line_items'] = $result['quotes']->requestedLineItems;
        }

        $accessorials = [];
        ($this->quote_settings['liftgate_delivery'] == "yes") ? $accessorials[] = "L" : "";
        ($this->quote_settings['residential_delivery'] == "yes") ? $accessorials[] = "R" : "";
        (isset($request_data['hazardous']) && is_array($request_data['hazardous']) && in_array('H', $request_data['hazardous'])) ? $accessorials[] = "H" : "";

        $this->InstorPickupLocalDelivery = (isset($result['quotes']->InstorPickupLocalDelivery)) ? $result['quotes']->InstorPickupLocalDelivery : [];

        $quote_results = (isset($result['quotes']->q->quoteSpeedFreightShipmentReturn->freightShipmentQuoteResults->freightShipmentQuoteResult)) ? $result['quotes']->q->quoteSpeedFreightShipmentReturn->freightShipmentQuoteResults->freightShipmentQuoteResult : [];
        $quote_error = (isset($result['quotes']->q->quoteSpeedFreightShipmentReturn->errorDescriptions) ? $result['quotes']->q->quoteSpeedFreightShipmentReturn->errorDescriptions : NULL);
        $standard_packaging = isset($result['quotes']->standardPackagingData) ? $result['quotes']->standardPackagingData : [];

        $hat_quote_results = (isset($result['quotes']->holdAtTerminal->quoteSpeedFreightShipmentReturn->freightShipmentQuoteResults->freightShipmentQuoteResult)) ? $result['quotes']->holdAtTerminal->quoteSpeedFreightShipmentReturn->freightShipmentQuoteResults->freightShipmentQuoteResult : [];

        //      Hold At Terminal
        $hold_at_terminal = apply_filters('speed_wwe_quests_quotes_plans_suscription_and_features', 'hold_at_terminal');

        $api_single_quote = new stdClass();
        $api_single_hat_quote = [];
        $allServices = [];

        if (!isset($quote_error) && (count((array)$quote_results) > 0)) {

            // Update origin city with correct city for WWE
            $originCityData = (isset($result['quotes']->originCityData) && !empty($result['quotes']->originCityData)) ? $result['quotes']->originCityData : '';
            if (isset($originCityData) && !empty($originCityData)) {
                $this->wwe_ltl_update_origin_data($originCityData);
            }

            (isset($quote_results->t)) ? $this->quote_settings['sandbox'] = "sandbox" : "";

            $label_sufex_arr = $this->filter_label_sufex_array_wwe_ltl($result['quotes']);

            if (count($quote_results) == 1) {
                $api_single_quote->{0} = $quote_results;
                $quote_results = (object)$api_single_quote;
            }

            if (count($hat_quote_results) == 1) {
                $api_single_hat_quote[] = $hat_quote_results;
                $hat_quote_results = $api_single_hat_quote;
            }

            $count = 0;
            $price_sorted_key = [];
            $simple_quotes = [];

            foreach ($quote_results as $quote_key => $quote) {

                if (in_array($quote->carrierSCAC, $this->quote_settings['enable_carriers'])) {

                    // Cuttoff Time
                    $delivery_estimates = (isset($quote->totalTransitTimeInDays)) ? $quote->totalTransitTimeInDays : '';
                    $delivery_time_stamp = (isset($quote->deliveryTimestamp)) ? $quote->deliveryTimestamp : '';

                    $surcharge = $quote->carrierNotifications->freightShipmentCarrierNotification;
                    $surcharges = (isset($surcharge)) ? $this->update_parse_wwe_ltl_output($surcharge) : 0;
                    $gurentee = ($quote->guaranteedService == 'Y') ? 'With Guaranteed' : 'Without Guaranteed';
                    if ((isset($quote->guaranteedService)) && ($quote->guaranteedService != 'Y')) {

                        // Pricing per product
                        $total_product_markup = 0;
                        if (!empty($pricing_per_product)) {
                            foreach ($pricing_per_product as $key => $per_product) {
                                $product_markup = (isset($per_product['product_markup'])) ? $per_product['product_markup'] : 0;
                                $product_quantity = (isset($per_product['product_quantity'])) ? $per_product['product_quantity'] : 0;
                                $total_product_markup += (float)$this->calculate_markup($product_markup, $quote->totalPrice, $product_quantity);
                            }
                        }

                        $meta_data['accessorials'] = json_encode($accessorials);
                        $meta_data['sender_origin'] = $request_data['sender_origin'];
                        $meta_data['product_name'] = json_encode($request_data['product_name']);
                        $meta_data['address'] = [];
                        $meta_data['_address'] = '';
                        $meta_data['standard_packaging'] = wp_json_encode($standard_packaging);

                        $allServices[$count] = array(
                            'id' => $quote->carrierSCAC,
                            'code' => $quote->carrierSCAC,
                            'scac' => $quote->carrierSCAC,
                            'label' => $quote->carrierName,
                            'cost' => $quote->totalPrice,
                            'transit_days' => $quote->transitDays,
                            // Cuttoff Time
                            'delivery_estimates' => $delivery_estimates,
                            'delivery_time_stamp' => $delivery_time_stamp,
                            'gurentee' => $gurentee,
                            'gurentee_ny' => $quote->guaranteedService,
                            'markup' => (isset($result['markup'])) ? $result['markup'] : "",
                            'label_sfx_arr' => $label_sufex_arr,
                            // Pricing per product
                            'total_product_markup' => $total_product_markup,
                            'surcharges' => $surcharges,
                            'meta_data' => $meta_data,
                            'transit_label' => ($this->quote_settings['rating_method'] != "average_rate" &&
                                $this->quote_settings['transit_days'] == "yes") ?
                                ' ( Estimated transit time of ' . $quote->transitDays . ' business days. )' : ""
                        );

                        // FDO
                        $en_fdo_meta_data['rate'] = $allServices[$count];
                        if (isset($en_fdo_meta_data['rate']['meta_data'])) {
                            unset($en_fdo_meta_data['rate']['meta_data']);
                        }

                        $en_fdo_meta_data['quote_settings'] = $this->quote_settings;
                        $allServices[$count]['meta_data']['en_fdo_meta_data'] = $en_fdo_meta_data;

                        if (!is_array($hold_at_terminal) && !empty($hat_quote_results) && $this->quote_settings['HAT_status'] == "yes") {
                            if (isset($hat_quote_results[$quote_key])) {
                                $quote_hat = $hat_quote_results[$quote_key];
                                $hold_at_terminal_fee = $quote_hat->totalPrice;

                                // Pricing per product
                                $total_product_markup = 0;
                                if (!empty($pricing_per_product)) {
                                    foreach ($pricing_per_product as $key => $per_product) {
                                        $product_markup = (isset($per_product['product_markup'])) ? $per_product['product_markup'] : 0;
                                        $product_quantity = (isset($per_product['product_quantity'])) ? $per_product['product_quantity'] : 0;
                                        $total_product_markup += $this->calculate_markup($product_markup, $hold_at_terminal_fee, $product_quantity);
                                    }
                                }

                                if (isset($this->quote_settings['HAT_fee']) && (strlen($this->quote_settings['HAT_fee']) > 0)) {
                                    $WC_speedfreight_Shipping_Method = new WC_speedfreight_Shipping_Method();
                                    $hold_at_terminal_fee = $WC_speedfreight_Shipping_Method->add_handling_fee($hold_at_terminal_fee, $this->quote_settings['HAT_fee']);
                                }

                                // Cuttoff Time
                                $delivery_estimates = (isset($quote_hat->totalTransitTimeInDays)) ? $quote_hat->totalTransitTimeInDays : '';
                                $delivery_time_stamp = (isset($quote_hat->deliveryTimestamp)) ? $quote_hat->deliveryTimestamp : '';

                                $meta_data['accessorials'] = json_encode(['HAT']);

                                $allServicesHAT[$count] = array(
                                    'id' => $quote_hat->carrierSCAC . '_HAT',
                                    'quote_type' => 'hold_at_terminal_quote',
                                    'code' => $quote_hat->carrierSCAC,
                                    'scac' => $quote_hat->carrierSCAC,
                                    'label' => $quote_hat->carrierName,
                                    'cost' => $hold_at_terminal_fee,
                                    // Pricing per product
                                    'total_product_markup' => $total_product_markup,
                                    'transit_days' => $quote_hat->transitDays,
                                    // Cuttoff Time
                                    'delivery_estimates' => $delivery_estimates,
                                    'delivery_time_stamp' => $delivery_time_stamp,
                                    'gurentee' => $gurentee,
                                    'gurentee_ny' => $quote_hat->guaranteedService,
                                    'markup' => (isset($result['markup'])) ? $result['markup'] : "",
                                    'label_sufex' => ['HAT'],
                                    'hat_append_label' => ' with hold at terminal',
                                    'surcharges' => $surcharges,
                                    'meta_data' => $meta_data,
                                    'transit_label' => ($this->quote_settings['rating_method'] != "average_rate" &&
                                        $this->quote_settings['transit_days'] == "yes") ?
                                        ' ( Estimated transit time of ' . $quote_hat->transitDays . ' business days. )' : ""
                                );

                                (isset($request_data['hazardous']) && is_array($request_data['hazardous']) && in_array('H', $request_data['hazardous'])) ? $allServicesHAT[$count]['label_sufex'][] = 'H' : '';

                                // FDO
                                $en_fdo_meta_data['rate'] = $allServicesHAT[$count];
                                if (isset($en_fdo_meta_data['rate']['meta_data'])) {
                                    unset($en_fdo_meta_data['rate']['meta_data']);
                                }
                                $en_fdo_meta_data['quote_settings'] = $this->quote_settings;
                                $allServicesHAT[$count]['meta_data']['en_fdo_meta_data'] = $en_fdo_meta_data;
                                $accessorials_hat = [
                                    'holdatterminal' => true,
                                    'residential' => false,
                                    'liftgate' => false,
                                ];
                                if (isset($allServicesHAT[$count]['meta_data']['en_fdo_meta_data']['accessorials'])) {
                                    $allServicesHAT[$count]['meta_data']['en_fdo_meta_data']['accessorials'] = array_merge($allServicesHAT[$count]['meta_data']['en_fdo_meta_data']['accessorials'], $accessorials_hat);
                                } else {
                                    $allServicesHAT[$count]['meta_data']['en_fdo_meta_data']['accessorials']['holdatterminal'] = true;
                                }
                            }
                        }

                        $allServices[$count] = apply_filters("en_woo_addons_web_quotes", $allServices[$count], speed_en_woo_plugin_wwe_quests);

                        $label_sufex = (isset($allServices[$count]['label_sufex'])) ? $allServices[$count]['label_sufex'] : [];
                        $label_sufex = $this->label_R_wwe_ltl($label_sufex);
                        $allServices[$count]['label_sufex'] = $label_sufex;

                        in_array('R', $label_sufex_arr) ? $allServices[$count]['meta_data']['en_fdo_meta_data']['accessorials']['residential'] = true : '';
                        ($this->quote_settings['liftgate_resid_delivery'] == "yes") && (in_array("R", $label_sufex)) && in_array('L', $label_sufex_arr) ? $allServices[$count]['meta_data']['en_fdo_meta_data']['accessorials']['liftgate'] = true : '';

                        if (($this->quote_settings['liftgate_delivery_option'] == "yes") &&
                            (($this->quote_settings['liftgate_resid_delivery'] == "yes") && (!in_array("R", $label_sufex)) ||
                                ($this->quote_settings['liftgate_resid_delivery'] != "yes"))
                        ) {
                            $service = $allServices[$count];
                            $allServices[$count]['id'] .= "WL";

                            (isset($allServices[$count]['label_sufex']) &&
                                (!empty($allServices[$count]['label_sufex']))) ?
                                array_push($allServices[$count]['label_sufex'], "L") : // IF
                                $allServices[$count]['label_sufex'] = array("L");       // ELSE

                            // FDO
                            $allServices[$count]['meta_data']['en_fdo_meta_data']['accessorials']['liftgate'] = true;
                            $allServices[$count]['append_label'] = " with lift gate delivery ";

                            $liftgate_charge = (isset($service['surcharges']['(FEE)Liftgate Delivery'])) ? $service['surcharges']['(FEE)Liftgate Delivery'] : 0;
                            $service['cost'] = (isset($service['cost'])) ? $service['cost'] - $liftgate_charge : 0;
                            (!empty($service)) && (in_array("R", $service['label_sufex'])) ? $service['label_sufex'] = array("R") : $service['label_sufex'] = [];

                            $simple_quotes[$count] = $service;

                            // FDO
                            if (isset($simple_quotes[$count]['meta_data']['en_fdo_meta_data']['rate']['cost'])) {
                                $simple_quotes[$count]['meta_data']['en_fdo_meta_data']['rate']['cost'] = $service['cost'];
                            }

                            $price_sorted_key[$count] = (isset($simple_quotes[$count]['cost'])) ? $simple_quotes[$count]['cost'] : 0;
                        }

                        $count++;
                    }
                }
            }
        } else {

            $label_sufex_arr = $this->filter_label_sufex_array_wwe_ltl($result['quotes']);

            $count = 0;
            $price_sorted_key = [];
            $simple_quotes = [];

            $meta_data['accessorials'] = json_encode($accessorials);
            $meta_data['sender_origin'] = $request_data['sender_origin'];
            $meta_data['product_name'] = json_encode($request_data['product_name']);
            $meta_data['address'] = [];
            $meta_data['_address'] = '';

            $allServices[$count] = array(
                'id' => 'no_quotes',
                'label' => '',
                'scac' => '',
                'cost' => 0,
                'transit_days' => '',
                'markup' => (isset($result['markup'])) ? $result['markup'] : "",
                'label_sfx_arr' => $label_sufex_arr,
                'surcharges' => [],
                'meta_data' => $meta_data,
            );

            // FDO
            $en_fdo_meta_data['rate'] = $allServices[$count];
            if (isset($en_fdo_meta_data['rate']['meta_data'])) {
                unset($en_fdo_meta_data['rate']['meta_data']);
            }

            $en_fdo_meta_data['quote_settings'] = $this->quote_settings;
            $allServices[$count]['meta_data']['en_fdo_meta_data'] = $en_fdo_meta_data;

            if (!is_array($hold_at_terminal) && $this->quote_settings['HAT_status'] == "yes") {
                $meta_data['accessorials'] = json_encode(['HAT']);
                $allServicesHAT[$count] = array(
                    'id' => 'no_quotes_HAT',
                    'quote_type' => 'hold_at_terminal_quote',
                    'scac' => '',
                    'label' => '',
                    'cost' => 0,
                    'markup' => (isset($result['markup'])) ? $result['markup'] : "",
                    'label_sufex' => ['HAT'],
                    'hat_append_label' => ' with hold at terminal',
                    'surcharges' => [],
                    'meta_data' => $meta_data,
                );

                // FDO
                $en_fdo_meta_data['rate'] = $allServicesHAT[$count];
                if (isset($en_fdo_meta_data['rate']['meta_data'])) {
                    unset($en_fdo_meta_data['rate']['meta_data']);
                }

                $en_fdo_meta_data['quote_settings'] = $this->quote_settings;
                $allServicesHAT[$count]['meta_data']['en_fdo_meta_data'] = $en_fdo_meta_data;
                $accessorials_hat = [
                    'holdatterminal' => true,
                    'residential' => false,
                    'liftgate' => false,
                ];
                if (isset($allServicesHAT[$count]['meta_data']['en_fdo_meta_data']['accessorials'])) {
                    $allServicesHAT[$count]['meta_data']['en_fdo_meta_data']['accessorials'] = array_merge($allServicesHAT[$count]['meta_data']['en_fdo_meta_data']['accessorials'], $accessorials_hat);
                } else {
                    $allServicesHAT[$count]['meta_data']['en_fdo_meta_data']['accessorials']['holdatterminal'] = true;
                }
            }

            $allServices[$count] = apply_filters("en_woo_addons_web_quotes", $allServices[$count], speed_en_woo_plugin_wwe_quests);

            $label_sufex = (isset($allServices[$count]['label_sufex'])) ? $allServices[$count]['label_sufex'] : [];
            $label_sufex = $this->label_R_wwe_ltl($label_sufex);
            $allServices[$count]['label_sufex'] = $label_sufex;

            in_array('R', $label_sufex_arr) ? $allServices[$count]['meta_data']['en_fdo_meta_data']['accessorials']['residential'] = true : '';
            ($this->quote_settings['liftgate_resid_delivery'] == "yes") && (in_array("R", $label_sufex)) && in_array('L', $label_sufex_arr) ? $allServices[$count]['meta_data']['en_fdo_meta_data']['accessorials']['liftgate'] = true : '';

            if (($this->quote_settings['liftgate_delivery_option'] == "yes") &&
                (($this->quote_settings['liftgate_resid_delivery'] == "yes") && (!in_array("R", $label_sufex)) ||
                    ($this->quote_settings['liftgate_resid_delivery'] != "yes"))
            ) {
                $service = $allServices[$count];
                $allServices[$count]['id'] .= "WL";

                (isset($allServices[$count]['label_sufex']) &&
                    (!empty($allServices[$count]['label_sufex']))) ?
                    array_push($allServices[$count]['label_sufex'], "L") : // IF
                    $allServices[$count]['label_sufex'] = array("L");       // ELSE

                // FDO
                $allServices[$count]['meta_data']['en_fdo_meta_data']['accessorials']['liftgate'] = true;
                $allServices[$count]['append_label'] = " with lift gate delivery ";

                $liftgate_charge = (isset($service['surcharges']['(FEE)Liftgate Delivery'])) ? $service['surcharges']['(FEE)Liftgate Delivery'] : 0;
                $service['cost'] = (isset($service['cost'])) ? $service['cost'] - $liftgate_charge : 0;
                (!empty($service)) && (in_array("R", $service['label_sufex'])) ? $service['label_sufex'] = array("R") : $service['label_sufex'] = [];

                $simple_quotes[$count] = $service;

                // FDO
                if (isset($simple_quotes[$count]['meta_data']['en_fdo_meta_data']['rate']['cost'])) {
                    $simple_quotes[$count]['meta_data']['en_fdo_meta_data']['rate']['cost'] = $service['cost'];
                }

                $price_sorted_key[$count] = (isset($simple_quotes[$count]['cost'])) ? $simple_quotes[$count]['cost'] : 0;
            }
        }

        //       array_multisort
        (!empty($simple_quotes)) ? array_multisort($price_sorted_key, SORT_ASC, $simple_quotes) : "";

        (!empty($simple_quotes)) ? $allServices['simple_quotes'] = $simple_quotes : "";
        (!empty($allServicesHAT)) ? $allServices['hold_at_terminal_quotes'] = $allServicesHAT : "";

        return $allServices;
    }

    /**
     * Calculate Handeling Fee For Each Shipment
     * @param $handeling_fee
     * @param $total
     * @return int
     */
    function calculate_markup($handeling_fee, $total, $product_quantity)
    {
        $handeling_fee = isset($handeling_fee) && $handeling_fee > 0 ? $handeling_fee : 0;
        $handeling_fee = !$total > 0 ? 0 : $handeling_fee;
        $grandTotal = 0;
        if (floatval($handeling_fee)) {
            $pos = strpos($handeling_fee, '%');
            if ($pos > 0) {
                $rest = substr($handeling_fee, $pos);
                $exp = explode($rest, $handeling_fee);
                $get = $exp[0];
                $percnt = $get / 100 * $total;
                $handeling_fee = $percnt;
            }
        }

        $handeling_fee = $handeling_fee * $product_quantity;
        return $handeling_fee;
    }

    /**
     * check "R" in array
     * @param array type $label_sufex
     * @return array type
     */
    public function label_R_wwe_ltl($label_sufex)
    {
        if (get_option('wc_settings_wwe_residential_delivery') == 'yes' && (in_array("R", $label_sufex))) {
            $label_sufex = array_flip($label_sufex);
            unset($label_sufex['R']);
            $label_sufex = array_keys($label_sufex);
        }

        return $label_sufex;
    }

    /**
     * Check LTL Freight Class
     * @param $slug
     * @param $values
     * @return array
     * @global $woocommerce
     */
    function cart_has_product_with_WWE_LTL_class($slug, $values)
    {

        global $woocommerce;
        $product_in_cart = false;
        $_product = $values['data'];
        $terms = get_the_terms($_product->get_id(), 'product_shipping_class');
        if ($terms) {
            foreach ($terms as $term) {
                $_shippingclass = "";
                $_shippingclass = $term->slug;
                if ($slug === $_shippingclass) {
                    $product_in_cart[] = $_shippingclass;
                }
            }
        }
        return $product_in_cart;
    }

    /**
     * Multi Warehouse
     * @param $warehous_list
     * @param $receiverZipCode
     * @return array
     */
    function wwe_ltl_multi_warehouse($warehous_list, $receiverZipCode)
    {
        if (count($warehous_list) == 1) {
            $warehous_list = reset($warehous_list);
            return $this->ltl_origin_array($warehous_list);
        }
        require_once 'warehouse-dropship/get-distance-request.php';

        $wwe_ltl_distance_request = new Speed_Get_ltl_distance();
        $accessLevel = "MultiDistance";
        $response = $wwe_ltl_distance_request->ltl_get_distance($warehous_list, $accessLevel, $this->destinationAddressWwe());

        return $this->ltl_origin_array($response);
    }

    /**
     * Arrange Own Freight
     * @return array
     */
    function arrange_own_freight()
    {

        return array(
            'id' => 'own_freight',
            'cost' => 0,
            'label' => get_option('wc_settings_wwe_text_for_own_arrangment'),
            'calc_tax' => 'per_item'
        );
    }

    /**
     * Origin
     * @param $origin
     * @return array
     */
    function ltl_origin_array($origin)
    {
        // In-store pickup and local delivery
        if (has_filter("en_wwe_ltl_wd_origin_array_set")) {
            return apply_filters("en_wwe_ltl_wd_origin_array_set", $origin);
        }

        $zip = (isset($origin->zip)) ? $origin->zip : "";
        $city = (isset($origin->city)) ? $origin->city : "";
        $state = (isset($origin->state)) ? $origin->state : "";
        $country = (isset($origin->country)) ? $origin->country : "";
        $location = (isset($origin->location)) ? $origin->location : "";
        $locationId = (isset($origin->id)) ? $origin->id : "";
        $correctedCity = (isset($origin->wwe_correct_city)) ? $origin->wwe_correct_city : "";
        return array('locationId' => $locationId, 'zip' => $zip, 'city' => $city, 'state' => $state, 'location' => $location, 'country' => $country, 'corrected_city' => $correctedCity);
    }

    /**
     * Return woocomerce and wwe ltl plugin versions
     */
    function ltl_get_woo_version_number()
    {

        if (!function_exists('get_plugins'))
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');

        $plugin_folder = get_plugins('/' . 'woocommerce');
        $plugin_file = 'woocommerce.php';

        $plugin_folders = get_plugins('/' . 'speedship-myunishippers-rating');
        
        $plugin_files = 'woocommerceShip.php';

        $wc_plugin = (isset($plugin_folder[$plugin_file]['Version'])) ? $plugin_folder[$plugin_file]['Version'] : "";
        $ltl_plugin = (isset($plugin_folders[$plugin_files]['Version'])) ? $plugin_folders[$plugin_files]['Version'] : "";

        $pluginVersions = array(
            "woocommerce_plugin_version" => $wc_plugin,
            "wwe_ltl_plugin_version" => $ltl_plugin
        );

        return $pluginVersions;
    }

    /**
     * Return WWE LTL In-store Pickup Array
     */
    function wwe_ltl_return_local_delivery_store_pickup()
    {
        return $this->InstorPickupLocalDelivery;
    }

    /**
     *
     * @param type $origin_data
     * Update warehouse/dropship with correct for WWE
     * @global type $wpdb
     */
    function wwe_ltl_update_origin_data($origin_data)
    {

        global $wpdb;
        $data = array('wwe_correct_city' => $origin_data->validCity);
        $clause_array = array(
            'zip' => $origin_data->currentZip,
            'city' => $origin_data->currentCity,
            'state' => $origin_data->currentState
        );
        $update_qry = $wpdb->update(
            $wpdb->prefix . 'warehouse',
            $data,
            $clause_array
        );
    }
}
