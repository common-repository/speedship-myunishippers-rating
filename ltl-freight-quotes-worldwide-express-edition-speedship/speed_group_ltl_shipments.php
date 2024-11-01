<?php

/**
 * WWE LTL Grouping
 *
 * @package     WWE LTL Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class speed_group_ltl_shipments
 */
class speed_group_ltl_shipments extends Wwe_Ltl_Liftgate_As_Option
{
    // Crowler work
    public $is_product_can_tag;
    public $is_product_lid_tag;
    public $warehouse_origin = [];
    public $single_product_tags = [];

    /** hasLTLShipment */
    public $hasLTLShipment;

    // Images for FDO
    public $en_fdo_image_urls = [];

    /** $errors */
    public $errors = [];
    public $ValidShipmentsArr = [];
    // Micro Warehouse
    public $products = [];
    public $dropship_location_array = [];
    public $warehouse_products = [];
    public $destination_Address_wwe_lfq;
    public $origin = [];

    /**
     * Shipment Packages
     * @param $package
     * @param $ltl_res_inst
     * @param $freight_zipcode
     * @return boolean|int
     */
    function ltl_package_shipments($package, $ltl_res_inst, $freight_zipcode)
    {
        if (empty($freight_zipcode)) {
            return [];
        }

        $sbs_customization = is_plugin_active('sbs-customization/sbs-customization.php');

        $changObj = new Speed_Woo_Update_Changes();
        $weight = 0;
        $dimensions = 0;
        $ltl_enable = false;
        $sample = "";

        $weight_threshold = get_option('en_weight_threshold_lfq');
        $weight_threshold = isset($weight_threshold) && $weight_threshold > 0 ? $weight_threshold : 150;

        // Micro Warehouse
        $smallPluginExist = 0;
        $ltl_package = $items = $items_shipment = [];
        $speed_ltl_shipping_get_quotes = new speed_ltl_shipping_get_quotes();
        $this->destination_Address_wwe_lfq = $speed_ltl_shipping_get_quotes->destinationAddressWwe();

        $ltl_zipcode = (strlen(WC()->customer->get_shipping_postcode()) > 0) ? WC()->customer->get_shipping_postcode() : $changObj->speedfreight_postcode();
        $wc_settings_wwe_ignore_items = get_option("en_ignore_items_through_freight_classification");
        $en_get_current_classes = strlen($wc_settings_wwe_ignore_items) > 0 ? trim(strtolower($wc_settings_wwe_ignore_items)) : '';
        $en_get_current_classes_arr = strlen($en_get_current_classes) > 0 ? array_map('trim', explode(',', $en_get_current_classes)) : [];

        $flat_rate_shipping_addon = apply_filters('en_add_flat_rate_shipping_addon', false);
        $en_pricing_per_product = apply_filters('en_pricing_per_product_existence', false);
        $count = 0;
        foreach ($package['contents'] as $item_id => $values) {

            $nestedPercentage = 0;
            $nestedDimension = "";
            $nestedItems = "";
            $StakingProperty = "";

            $_product = (isset($values['data'])) ? $values['data'] : "";
            $sample = (isset($values['sample'])) ? $values['sample'] : "";

            // Images for FDO
            $this->en_fdo_image_urls($values, $_product);

            // Flat rate pricing
            $product_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
            $en_flat_rate_price = get_post_meta($product_id, 'en_flat_rate_price', true);
            if ($flat_rate_shipping_addon && isset($en_flat_rate_price) && strlen($en_flat_rate_price) > 0) {
                continue;
            }

            //get product shipping class
            $en_ship_class = strtolower($values['data']->get_shipping_class());
            if (in_array($en_ship_class, $en_get_current_classes_arr)) {
                continue;
            }

            // Shippable handling units
            $values = apply_filters('en_shippable_handling_units_request', $values, $values, $_product);
            $shippable = [];
            if (isset($values['shippable']) && !empty($values['shippable'])) {
                $shippable = $values['shippable'];
            }

            // Pricing per product
            $values = apply_filters('en_pricing_per_product_request', $values, $values, $_product);
            $pricing_per_product = [];
            if (isset($values['pricing_per_product']) && !empty($values['pricing_per_product'])) {
                $pricing_per_product = $values['pricing_per_product'];
            }

            // make weight and DIM units standarize to lbs and inches 
            $dimension_unit = get_option('woocommerce_dimension_unit');

            // convert product dimensions in feet ,centimeter,miles,kilometer into Inches
            $height = 0;
            $width = 0;
            $length = 0;
            if ($dimension_unit == 'ft' || $dimension_unit == 'cm' || $dimension_unit == 'mi' || $dimension_unit == 'km') {

                $dimensions = $this->dimensions_conversion($_product);
                $height = (isset($dimensions['height'])) ? $dimensions['height'] : "0";
                $width = (isset($dimensions['width'])) ? $dimensions['width'] : "0";
                $length = (isset($dimensions['length'])) ? $dimensions['length'] : "0";
            } else {
                $height = wc_get_dimension($_product->get_height(), 'in');
                $width = wc_get_dimension($_product->get_width(), 'in');
                $length = wc_get_dimension($_product->get_length(), 'in');
            }

            $height = (strlen($height) > 0) ? $height : "0";
            $width = (strlen($width) > 0) ? $width : "0";
            $length = (strlen($length) > 0) ? $length : "0";

            $product_weight = wc_get_weight($_product->get_weight(), 'lbs');
            $product_weight = (strlen($product_weight) > 0) ? $product_weight : "0";

            $product_quantity = isset($values['quantity']) ? $values['quantity'] : 0;

            $weight = ($product_quantity == 1) ? $product_weight : $product_weight * $product_quantity;

            // Crowler work
            $this->is_product_can_tag = FALSE;
            $this->is_product_lid_tag = FALSE;
            $this->single_product_tags = [];

            // check if LTL enable
            $ltl_enable = $this->wwe_ltl_enable_shipping_class($_product);

            // Quotes settings option get LTL rates if weight > 150
            $exceedWeight = get_option('en_plugins_return_LTL_quotes');

            // Crowler work
            $ship_type = 'LTL';
            $terms = get_the_terms($values['product_id'], 'product_tag');
            if ($sbs_customization) {
                if ($ltl_enable == true || ($weight > $weight_threshold && $exceedWeight == 'yes')) {
                    $this->find_tag_lid($terms);
                    $ship_type = "LTL";
                } else {
                    $this->find_tag_lid($terms);
                    $ship_type = $this->is_product_lid_tag && !$this->is_product_can_tag ? "LTL" : "SMALL";
                    $product_tag_action = TRUE;
                }
            }

            // groping
            $locationId = 0;

            //Insurance
            $insurance = $this->en_insurance_checked($values, $_product);

            $origin_address = $this->wwe_ltl_get_origin($_product, $values, $ltl_res_inst, $ltl_zipcode);

            // get product class
            $freightClass_ltl_gross = $this->speed_wwe_ltl_freight_class($values, $_product);

            if (!empty($origin_address)) {

                $locationId = $origin_address['locationId'];

                // Micro Warehouse
                (isset($values['variation_id']) && $values['variation_id'] > 0) ? $post_id = $values['variation_id'] : $post_id = $_product->get_id();
                $this->products[] = $post_id;

                // Crowler work
                $locationId = apply_filters('en_check_is_can_product', $locationId, $this->is_product_can_tag);

                // Pricing per product
                $product_insurance = $product_markup = 0;
                extract($pricing_per_product);
                if ($en_pricing_per_product && $product_markup > 0) {
                    $locationId = $item_id;
                }

                $ltl_package[$ship_type][$locationId]['origin'] = $origin_address;

                //  Nested Material
                $nested_material = $this->en_nested_material($values, $_product);

                if ($nested_material == "yes") {
                    $post_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
                    $nestedPercentage = get_post_meta($post_id, '_nestedPercentage', true);
                    $nestedDimension = get_post_meta($post_id, '_nestedDimension', true);
                    $nestedItems = get_post_meta($post_id, '_maxNestedItems', true);
                    $StakingProperty = get_post_meta($post_id, '_nestedStakingProperty', true);
                }

                // Hazardous Material
                $hazardous_material = $this->en_hazardous_material($values, $_product);
                $hm_plan = apply_filters('speed_wwe_quests_quotes_plans_suscription_and_features', 'hazardous_material');
                $hm_status = (!is_array($hm_plan) && $hazardous_material == 'yes') ? TRUE : FALSE;

                if (!$_product->is_virtual() && !in_array($en_ship_class, $en_get_current_classes_arr)) {

                    $product_title = str_replace(array("'", '"'), '', $_product->get_title());

                    // Shippable handling units
                    $lineItemPalletFlag = $lineItemPackageCode = $lineItemPackageType = '0';
                    extract($shippable);

                    $en_items = array(
                        'productId' => $_product->get_id(),
                        'productName' => $product_title,
                        'productQty' => $product_quantity,
                        'product_name' => $product_quantity . " x " . $product_title,
                        'products' => $product_title,
                        'insurance' => $insurance,
                        'productPrice' => $_product->get_price(),
                        'productWeight' => $product_weight,
                        'productLength' => $length,
                        'productWidth' => $width,
                        'productHeight' => $height,
                        'productClass' => $freightClass_ltl_gross,
                        'sample' => $sample,
                        'nestedMaterial' => $nested_material,
                        'nestedPercentage' => $nestedPercentage,
                        'nestedDimension' => $nestedDimension,
                        'nestedItems' => $nestedItems,
                        'stakingProperty' => $StakingProperty,
                        'hazardousMaterial' => $hm_status,
                        'hazardous_material' => $hm_status,
                        'hazmat' => $hm_status,
                        'productType' => ($_product->get_type() == 'variation') ? 'variant' : 'simple',
                        'productSku' => $_product->get_sku(),
                        'actualProductPrice' => $_product->get_price(),
                        'attributes' => $_product->get_attributes(),
                        'variantId' => ($_product->get_type() == 'variation') ? $_product->get_id() : '',
                        // crowler work
                        'single_product_tags' => $this->single_product_tags,

                        // Shippable handling units
                        'lineItemPalletFlag' => $lineItemPalletFlag,
                        'lineItemPackageCode' => $lineItemPackageCode,
                        'lineItemPackageType' => $lineItemPackageType,

                        // Pricing per product
                        'product_insurance' => $product_insurance,
                        'product_markup' => $product_markup,
                        'product_quantity' => $product_quantity,
                        'product_price' => $_product->get_price()
                    );

                    // Pricing per product
                    if ($product_insurance > 0) {
                        $en_items['productPrice'] = $product_insurance;
                        $en_items['insurance'] = 'yes';
                        $insurance = 'yes';
                    }

                    // Hook for flexibility adding to package
                    $en_items = apply_filters('en_group_package', $en_items, $values, $_product);

                    // Micro Warehouse
                    $items[$post_id] = $en_items;

                    $ltl_package[$ship_type][$locationId]['items'][$count] = $en_items;

                    // Hazardous Material
                    if ($hazardous_material == "yes" && !isset($ltl_package[$ship_type][$locationId]['hazardous_material'])) {
                        $ltl_package[$ship_type][$locationId]['hazardousMaterial'] = TRUE;
                    }

                    //insurance
                    if ($insurance == "yes" && !isset($ltl_package[$ship_type][$locationId]['insurance'])) {
                        $ltl_package[$ship_type][$locationId]['insurance'] = TRUE;
                    }

                    $ltl_package[$ship_type][$locationId]['shipment_weight'] = isset($ltl_package[$ship_type][$locationId]['shipment_weight']) ? $ltl_package[$ship_type][$locationId]['shipment_weight'] + $weight : $weight;
                }
            }

            // SBS Customization
            $en_post_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
            $ltl_package[$ship_type][$locationId]['en_product_combination'][] = $en_post_id;
            $this->is_product_can_tag ? $ltl_package[$ship_type][$locationId]['is_product_can_tag'] = $this->is_product_can_tag : '';

            if (isset($product_tag_action)) {
                $product_tag = $this->get_tag($terms);
                $ltl_package[$ship_type][$locationId]['items'][$count]['product_tag'] = $product_tag;
            }


            // Micro Warehouse
            $items_shipment[$post_id] = $ltl_enable;

            $smallPluginExist = 0;
            $calledMethod = [];
            $eniturePluigns = json_decode(get_option('EN_Plugins'));
            if (!empty($eniturePluigns)) {
                foreach ($eniturePluigns as $enIndex => $enPlugin) {

                    $freightSmallClassName = 'WC_' . $enPlugin;

                    if (!in_array($freightSmallClassName, $calledMethod)) {

                        if (class_exists($freightSmallClassName)) {
                            $smallPluginExist = 1;
                        }

                        $calledMethod[] = $freightSmallClassName;
                    }
                }
            }

            if ($ltl_enable == true || ($ltl_package[$ship_type][$locationId]['shipment_weight'] > $weight_threshold && $exceedWeight == 'yes')) {
                $ltl_package[$ship_type][$locationId]['ltl'] = 1;
                $this->hasLTLShipment = 1;
                $this->ValidShipmentsArr[] = "ltl_freight";
            } elseif (isset($ltl_package[$ship_type][$locationId]['ltl'])) {
                $ltl_package[$ship_type][$locationId]['ltl'] = 1;
                $this->hasLTLShipment = 1;
                $this->ValidShipmentsArr[] = "ltl_freight";
            } elseif ($smallPluginExist == 1) {
                $ltl_package[$ship_type][$locationId]['small'] = 1;
                $this->ValidShipmentsArr[] = "small_shipment";
            } else {
                $this->ValidShipmentsArr[] = "no_shipment";
            }

            if (empty($ltl_package[$ship_type][$locationId]['items'])) {
                unset($ltl_package[$ship_type][$locationId]);
                $ltl_package[$ship_type][$locationId]["NOPARAM"] = 1;
            }

            $count++;
        }

        // Crowler work
        $ltl_package = array_merge((isset($ltl_package['LTL'])) ? $ltl_package['LTL'] : [], (isset($ltl_package['SMALL'])) ? $ltl_package['SMALL'] : []);
        if (!empty($this->warehouse_origin)) {
            $ltl_package['warehouse_origin'] = $this->warehouse_origin;
        }

        // Eniture debug mood
        // Micro Warehouse
        $eniureLicenceKey = get_option('wc_settings_wwe_licence_key');
        $ltl_package = apply_filters('en_micro_warehouse', $ltl_package, $this->products, $this->dropship_location_array, $this->destination_Address_wwe_lfq, $this->origin, $smallPluginExist, $items, $items_shipment, $this->warehouse_products, $eniureLicenceKey, 'ltl');
        do_action("eniture_debug_mood", "WWE LTL Product Detail", $ltl_package);
        return $ltl_package;
    }

    /**
     * Set images urls | Images for FDO
     * @param array type $en_fdo_image_urls
     * @return array type
     */
    public function en_fdo_image_urls_merge($en_fdo_image_urls)
    {
        return array_merge($this->en_fdo_image_urls, $en_fdo_image_urls);
    }

    /**
     * Get images urls | Images for FDO
     * @param array type $values
     * @param array type $_product
     * @return array type
     */
    public function en_fdo_image_urls($values, $_product)
    {
        $product_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
        $gallery_image_ids = $_product->get_gallery_image_ids();
        foreach ($gallery_image_ids as $key => $image_id) {
            $gallery_image_ids[$key] = $image_id > 0 ? wp_get_attachment_url($image_id) : '';
        }

        $image_id = $_product->get_image_id();
        $this->en_fdo_image_urls[$product_id] = [
            'product_id' => $product_id,
            'image_id' => $image_id > 0 ? wp_get_attachment_url($image_id) : '',
            'gallery_image_ids' => $gallery_image_ids
        ];

        add_filter('en_fdo_image_urls_merge', [$this, 'en_fdo_image_urls_merge'], 10, 1);
    }

    /**
     * Product Tags
     * @param array type $terms
     * @return string type
     */
    public function search_tag($niknames, $after_term)
    {
        $product_cat_arr = explode(' ', $after_term);

        foreach ($niknames as $key => $nikname) {
            $match = true;
            $nikname_arr = explode(' ', $nikname);

            foreach ($product_cat_arr as $product_cat_key => $product_cat_value) {
                if (!(isset($nikname_arr[$product_cat_key]) && $nikname_arr[$product_cat_key] == $product_cat_value)) {
                    $match = false;
                }
            }

            if ($match) {
                return $match;
            }
        }

        return false;
    }

    /**
     * Product Tags
     * @param array type $terms
     * @return string type
     */
    public function get_tag($terms)
    {
        $tag_name = "";

        if (!empty($terms) && !is_wp_error($terms)) {
            $nikname = $this->get_bins();

            foreach ($terms as $key => $term) {
                $term_exploer = explode("-", $term->name);
                $before_term = (isset($term_exploer[0])) ? strtolower($term_exploer[0]) : "";

                if (strlen($before_term) > 0 && $before_term == "box") {
                    $after_term = (isset($term_exploer[1])) ? strtolower(trim($term_exploer[1])) : "";
                    $matched_term = $this->search_tag($nikname, $after_term);
                    if ($matched_term) {
                        return $term->name;
                    }
                }
            }
        }

        return $tag_name;
    }

    /**
     * Get Bins
     * @return type
     */
    public function get_bins()
    {
        $nikname = [];

        $args = array(
            'post_type' => 'box_sizing',
            'posts_per_page' => -1,
            'post_status' => 'any'
        );

        $posts_array = get_posts($args);

        if ($posts_array) {
            foreach ($posts_array as $post) {
                $status = get_post_field('post_content', $post->ID);
                if ($status == "Yes") { /* If box available */
                    $nikname[] = trim(strtolower(get_post_field('post_title', $post->ID)));
                }
            }
        }

        return $nikname;
    }


    /**
     * Product Tags
     * @param array type $terms
     * @return string type
     */
    public function find_tag_lid($terms)
    {
        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $key => $term) {

                // Start SBS Customization
                $is_tags = explode("-", strtolower($term->name));
                in_array('cans', $is_tags) ? $this->is_product_can_tag = TRUE : '';
                // End SBS Customization

                $term_name = (isset($term->name)) ? strtolower(trim($term->name)) : "";
                $this->single_product_tags[] = $term_name;
                if (strlen($term_name) > 0 && $term_name == "lid") {
                    $this->is_product_lid_tag = TRUE;
                }
            }
        }

        return FALSE;
    }

    /**
     * Hazardous Insurance Product
     * @param array type $values
     * @param array type $_product
     * @return string type
     */
    function en_insurance_checked($values, $_product)
    {
        $post_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
        return get_post_meta($post_id, '_en_insurance_fee', true);
    }

    /**
     * Nested Material
     * @param array type $values
     * @param array type $_product
     * @return string type
     */
    function en_nested_material($values, $_product)
    {
        $post_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
        return get_post_meta($post_id, '_nestedMaterials', true);
    }

    /**
     * Hazardous Material
     * @param array type $values
     * @param array type $_product
     * @return string type
     */
    function en_hazardous_material($values, $_product)
    {
        $post_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
        return get_post_meta($post_id, '_hazardousmaterials', true);
    }

    /**
     * Small Packages Cost
     * will return $result['error'] = false even if any of the plugins returns quotes
     * @param $smallQuotes
     * @return int
     */
    function getSmallPackagesCost($smallQuotes)
    {
        $result = [];
        $minCostArr = [];

        if (isset($smallQuotes) && count($smallQuotes) > 0) {
            foreach ($smallQuotes as $smQuotes) { // applications
                $CostArr = [];
                if (!isset($smQuotes['error'])) {
                    foreach ($smQuotes as $smQuote) { // services
                        $CostArr[] = $smQuote['cost']; // pick cheapest value of all services of single apps
                        $result['error'] = false;
                    }
                    $minCostArr[] = !empty($CostArr) ? min($CostArr) : '';
                } else {
                    $result['error'] = !isset($result['error']) ? true : $result['error'];
                }
            }
            // get cheapest of all aplications
            $result['price'] = (isset($minCostArr) && count($minCostArr) > 0) ? min($minCostArr) : "";
        } else {
            //no small quotes required
            $result['error'] = false;
            $result['price'] = 0;
        }

        return $result;
    }

    /**
     * Get Shipment Origin
     * @param $_product
     * @param $values
     * @param $ltl_res_inst
     * @param $ltl_zipcode
     * @return array
     * @global $wpdb
     */
    function wwe_ltl_get_origin($_product, $values, $ltl_res_inst, $ltl_zipcode)
    {
        global $wpdb;

        //      UPDATE QUERY In-store pick up                           
        $en_wd_update_query_string = apply_filters("en_wd_update_query_string", "");

        (isset($values['variation_id']) && $values['variation_id'] > 0) ? $post_id = $values['variation_id'] : $post_id = $_product->get_id();
        $enable_dropship = get_post_meta($post_id, '_enable_dropship', true);
        if ($enable_dropship == 'yes') {
            $get_loc = get_post_meta($post_id, '_dropship_location', true);
            if ($get_loc == '') {
                // Micro Warehouse
                $this->warehouse_products[] = $post_id;
                return array('error' => 'wwe ltl dp location not found!');
            }

            //         Multi Dropship
            $multi_dropship = apply_filters('speed_wwe_quests_quotes_plans_suscription_and_features', 'multi_dropship');

            if (is_array($multi_dropship)) {
                $locations_list = $wpdb->get_results(
                    "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE location = 'dropship' LIMIT 1"
                );
            } else {
                $get_loc = ($get_loc !== '') ? maybe_unserialize($get_loc) : $get_loc;
                $get_loc = is_array($get_loc) ? implode(" ', '", $get_loc) : $get_loc;
                $locations_list = $wpdb->get_results(
                    "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE id IN ('" . $get_loc . "')"
                );
            }

            // Micro Warehouse
            $this->multiple_dropship_of_prod($locations_list, $post_id);
            $eniture_debug_name = "Dropships";
        } else {

            // Multi Warehouse
            $multi_warehouse = apply_filters('speed_wwe_quests_quotes_plans_suscription_and_features', 'multi_warehouse');
            if (is_array($multi_warehouse)) {
                $locations_list = $wpdb->get_results(
                    "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE location = 'warehouse' LIMIT 1"
                );
            } else {
                $locations_list = $wpdb->get_results(
                    "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE location = 'warehouse'"
                );
            }

            // Micro Warehouse
            $this->warehouse_products[] = $post_id;
            $eniture_debug_name = "Warehouses";
        }

        do_action("eniture_debug_mood", "Quotes $eniture_debug_name (s)", $locations_list);

        $origin_address = $ltl_res_inst->wwe_ltl_multi_warehouse($locations_list, $ltl_zipcode);

        // Crowler work
        $this->warehouse_origin = (isset($origin_address['location']) && $origin_address['location'] == 'warehouse') ? $origin_address : [];

        return $origin_address;
    }

    // Micro Warehouse
    public function multiple_dropship_of_prod($locations_list, $post_id)
    {
        $post_id = (string)$post_id;

        foreach ($locations_list as $key => $value) {
            $dropship_data = $this->address_array($value);

            $this->origin["D" . $dropship_data['zip']] = $dropship_data;
            if (!isset($this->dropship_location_array["D" . $dropship_data['zip']]) || !in_array($post_id, $this->dropship_location_array["D" . $dropship_data['zip']])) {
                $this->dropship_location_array["D" . $dropship_data['zip']][] = $post_id;
            }
        }
    }

    // Micro Warehouse
    public function address_array($value)
    {
        $dropship_data = [];

        $dropship_data['locationId'] = (isset($value->id)) ? $value->id : "";
        $dropship_data['zip'] = (isset($value->zip)) ? $value->zip : "";
        $dropship_data['city'] = (isset($value->city)) ? $value->city : "";
        $dropship_data['state'] = (isset($value->state)) ? $value->state : "";
        // Origin terminal address
        $dropship_data['address'] = (isset($value->address)) ? $value->address : "";
        // Terminal phone number
        $dropship_data['phone_instore'] = (isset($value->phone_instore)) ? $value->phone_instore : "";
        $dropship_data['location'] = (isset($value->location)) ? $value->location : "";
        $dropship_data['country'] = (isset($value->country)) ? $value->country : "";
        $dropship_data['enable_store_pickup'] = (isset($value->enable_store_pickup)) ? $value->enable_store_pickup : "";
        $dropship_data['fee_local_delivery'] = (isset($value->fee_local_delivery)) ? $value->fee_local_delivery : "";
        $dropship_data['suppress_local_delivery'] = (isset($value->suppress_local_delivery)) ? $value->suppress_local_delivery : "";
        $dropship_data['miles_store_pickup'] = (isset($value->miles_store_pickup)) ? $value->miles_store_pickup : "";
        $dropship_data['match_postal_store_pickup'] = (isset($value->match_postal_store_pickup)) ? $value->match_postal_store_pickup : "";
        $dropship_data['checkout_desc_store_pickup'] = (isset($value->checkout_desc_store_pickup)) ? $value->checkout_desc_store_pickup : "";
        $dropship_data['enable_local_delivery'] = (isset($value->enable_local_delivery)) ? $value->enable_local_delivery : "";
        $dropship_data['miles_local_delivery'] = (isset($value->miles_local_delivery)) ? $value->miles_local_delivery : "";
        $dropship_data['match_postal_local_delivery'] = (isset($value->match_postal_local_delivery)) ? $value->match_postal_local_delivery : "";
        $dropship_data['checkout_desc_local_delivery'] = (isset($value->checkout_desc_local_delivery)) ? $value->checkout_desc_local_delivery : "";

        $dropship_data['sender_origin'] = $dropship_data['location'] . ": " . $dropship_data['city'] . ", " . $dropship_data['state'] . " " . $dropship_data['zip'];

        return $dropship_data;
    }

    /**
     * Check Product Freight Class
     * @param $values
     * @param $_product
     * @return array
     */
    function speed_wwe_ltl_freight_class($values, $_product)
    {

        if ($_product->get_type() == 'variation') {
            $variation_class = get_post_meta($values['variation_id'], '_ltl_freight_variation', true);
            if ($variation_class == 0) {
                $variation_class = get_post_meta($values['product_id'], '_ltl_freight', true);
                $freightClass_ltl_gross = $variation_class;
            } else {
                if ($variation_class > 0) {
                    $freightClass_ltl_gross = get_post_meta($values['variation_id'], '_ltl_freight_variation', true);
                } else {
                    $freightClass_ltl_gross = get_post_meta($_product->get_id(), '_ltl_freight', true);
                }
            }
        } else {
            $freightClass_ltl_gross = get_post_meta($_product->get_id(), '_ltl_freight', true);
        }

        return $freightClass_ltl_gross;
    }

    /**
     * Check Product Enable Against LTL Freight
     * @param $_product
     * @return string
     */
    function wwe_ltl_enable_shipping_class($_product)
    {
        if ($_product->get_type() == 'variation') {
            $ship_class_id = $_product->get_shipping_class_id();

            if ($ship_class_id == 0) {
                $parent_data = $_product->get_parent_data();
                $get_parent_term = get_term_by('id', $parent_data['shipping_class_id'], 'product_shipping_class');
                $get_shipping_result = (isset($get_parent_term->slug)) ? $get_parent_term->slug : '';
            } else {
                $get_shipping_result = $_product->get_shipping_class();
            }

            $ltl_enable = ($get_shipping_result && $get_shipping_result == 'ltl_freight') ? true : false;
        } else {
            $get_shipping_result = $_product->get_shipping_class();
            $ltl_enable = ($get_shipping_result == 'ltl_freight') ? true : false;
        }

        return $ltl_enable;
    }

    /**
     * parameters $_product
     * return $dimensions in inches
     */
    function dimensions_conversion($_product)
    {

        $dimension_unit = get_option('woocommerce_dimension_unit');
        $dimensions = [];

        switch ($dimension_unit) {

            case 'ft':
                $dimensions['height'] = round($_product->get_height() * 12, 2);
                $dimensions['width'] = round($_product->get_width() * 12, 2);
                $dimensions['length'] = round($_product->get_length() * 12, 2);
                break;

            case 'cm':
                if (is_numeric($_product->get_height())) {
                    $dimensions['height'] = round($_product->get_height() * 0.3937007874, 2);
                }
                if (is_numeric($_product->get_width())) {
                    $dimensions['width'] = round($_product->get_width() * 0.3937007874, 2);
                }
                if (is_numeric($_product->get_length())) {
                    $dimensions['length'] = round($_product->get_length() * 0.3937007874, 2);
                }
                break;

            case 'mi':
                $dimensions['height'] = round($_product->get_height() * 63360, 2);
                $dimensions['width'] = round($_product->get_width() * 63360, 2);
                $dimensions['length'] = round($_product->get_length() * 63360, 2);
                break;

            case 'km':
                $dimensions['height'] = round($_product->get_height() * 39370.1, 2);
                $dimensions['width'] = round($_product->get_width() * 39370.1, 2);
                $dimensions['length'] = round($_product->get_length() * 39370.1, 2);
                break;
        }

        return $dimensions;
    }
}
