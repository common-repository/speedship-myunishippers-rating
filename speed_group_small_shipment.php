<?php

/**
 * WWE Small Group Packaging
 *
 * @package     WWE Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get Shipping Package Class
 */
class speed_group_small_shipment
{
    // Crowler work
    public $is_product_can_tag;
    public $is_product_lid_tag;
    public $warehouse_origin = [];
    public $single_product_tags = [];

    /** global */
    public $sm_errors = [];
    public $order_details;
    // Images for FDO
    public $en_fdo_image_urls = [];
    // Micro Warehouse
    public $products = [];
    public $dropship_location_array = [];
    public $warehouse_products = [];
    public $destination_Address_wwe_spq;
    public $origin = [];

    /**
     * Grouping For Shipments
     * @param $package
     * @param $web_service_inst
     * @return string
     */
    function small_package_shipments($package, $web_service_inst)
    {
        $sm_package = [];
        if (isset($package['sPackage']) && !empty($package['sPackage'])) {
            return $package['sPackage'];
        }
        $c = 0;
        $pStatus = (isset($package['itemType'])) ? $package['itemType'] : "";
        $wwe_small_woo_obj = new SPEED_WWE_Small_Woo_Update_Changes();
        $sm_zipcode = $wwe_small_woo_obj->wwe_small_postcode();
        if (isset($package['contents'])) {
            $pack = $package['contents'];

            $wc_settings_wwe_ignore_items = get_option("en_ignore_items_through_freight_classification");
            $en_get_current_classes = strlen($wc_settings_wwe_ignore_items) > 0 ? trim(strtolower($wc_settings_wwe_ignore_items)) : '';
            $en_get_current_classes_arr = strlen($en_get_current_classes) > 0 ? array_map('trim', explode(',', $en_get_current_classes)) : [];

            // Micro Warehouse
            $smallPluginExist = 0;
            $sm_package = $items = $items_shipment = [];
            $speed_smallpkg_shipping_get_quotes = new speed_smallpkg_shipping_get_quotes();
            $this->destination_Address_wwe_spq = $speed_smallpkg_shipping_get_quotes->destinationAddressWweSmall();

            $weight_threshold = get_option('en_weight_threshold_lfq');
            $weight_threshold = isset($weight_threshold) && $weight_threshold > 0 ? $weight_threshold : 150;

            $flat_rate_shipping_addon = apply_filters('en_add_flat_rate_shipping_addon', false);
            $en_pricing_per_product = apply_filters('en_pricing_per_product_existence', false);
            foreach ($pack as $item_id => $values) {
                $_product = $values['data'];

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

                // Nesting Material
                $nestedPercentage = 0;
                $nestedDimension = "";
                $nestedItems = "";
                $StakingProperty = "";

                $locationId = 0;
                $sample = isset($values['sample']) ? $values['sample'] : '';

                // Crowler work
                $terms = get_the_terms($values['product_id'], 'product_tag');

                $this->is_product_can_tag = FALSE; // SBS Customization
                $this->is_product_lid_tag = FALSE; // SBS Customization
                $this->single_product_tags = []; // SBS Customization

                $product_tag = $this->get_tag($terms);

                $dimension_unit = get_option('woocommerce_dimension_unit');
                //  convert product dimensions in feet ,centimeter,miles,kilometer into Inches
                $height = 0;
                $width = 0;
                $length = 0;
                if ($dimension_unit == 'ft' || $dimension_unit == 'cm' || $dimension_unit == 'mi' || $dimension_unit == 'km') {

                    $dimensions = $this->dimensions_conversion($_product);
                    if (isset($dimensions['height'])) $height = $dimensions['height'];
                    if (isset($dimensions['width'])) $width = $dimensions['width'];
                    if (isset($dimensions['length'])) $length = $dimensions['length'];
                } else {
                    $height = wc_get_dimension($_product->get_height(), 'in');
                    $width = wc_get_dimension($_product->get_width(), 'in');
                    $length = wc_get_dimension($_product->get_length(), 'in');
                }

                $product_weight = wc_get_weight($_product->get_weight(), 'lbs');
                $height = (strlen($height) > 0) ? $height : "0";
                $width = (strlen($width) > 0) ? $width : "0";
                $length = (strlen($length) > 0) ? $length : "0";

                $product_weight = (strlen($product_weight) > 0) ? $product_weight : "0";

                $product_quantity = (isset($values['quantity'])) ? $values['quantity'] : 0;

                $dimenssions = $length * $width * $height;
                $exceedWeight = get_option('en_plugins_return_LTL_quotes');
                $weight = ($product_weight * $product_quantity);
                $freight_enable_class = $this->wwe_small_check_freight_class($_product);
                $locations_list = $this->wwe_small_origin_address($values, $_product);
                $origin_address = $web_service_inst->wwe_smpkg_multi_warehouse($locations_list, $sm_zipcode);

                // Crowler work
                $this->warehouse_origin = (isset($origin_address['location']) && $origin_address['location'] == 'warehouse') ? $origin_address : [];

                $ptype = $this->wwe_small_check_product_type($freight_enable_class, $exceedWeight, $product_weight);
                $insurance = $this->en_insurance_checked($values, $_product);
                $locationId = (isset($origin_address['locationId'])) ? $origin_address['locationId'] : 0;
                $locationZip = (isset($origin_address['zip'])) ? $origin_address['zip'] : '';
                $locationId = $locationZip;

                // Micro Warehouse
                (isset($values['variation_id']) && $values['variation_id'] > 0) ? $post_id = $values['variation_id'] : $post_id = $_product->get_id();
                $this->products[] = $post_id;

                // Crowler work
                if ($this->is_product_can_tag) {
                    $locationId = apply_filters('en_check_is_can_product', $locationId, $this->is_product_can_tag);
                } else if ($this->is_product_lid_tag) {
                    $this->is_product_lid_tag ? $locationId .= '12345' : '';
                }

                // Pricing per product
                $product_insurance = $product_markup = $product_rental = 0;
                extract($pricing_per_product);
                if ($en_pricing_per_product && ($product_markup > 0 || $product_rental == 'yes')) {
                    $locationId = $item_id;
                }

                if (isset($sm_package[$locationId]['is_shipment']) && $sm_package[$locationId]['is_shipment'] == 'ltl') {
                    $sm_package[$locationId]['is_shipment'] = 'ltl';
                } else {
                    $sm_package[$locationId]['is_shipment'] = $ptype;
                }

                if (!empty($origin_address) && $product_weight <= $weight_threshold) {
                    // Nested Material
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
                    $hm_plan = apply_filters('speed_wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'hazardous_material');
                    $hm_status = (!is_array($hm_plan) && $hazardous_material == 'yes') ? TRUE : FALSE;

                    $product_title = str_replace(array("'", '"'), '', $_product->get_title());

                    // Shippable handling units
                    $ship_item_alone = '0';
                    extract($shippable);

                    if ($en_pricing_per_product) {
                        $ship_item_alone = 1;
                    }

                    $sm_package[$locationId]['origin'] = $origin_address;
                    if (!$_product->is_virtual()) {
                        $en_items = array(
                            'productId' => $_product->get_id(),
                            'productName' => $product_title,
                            'productQty' => $product_quantity,
                            'product_name' => $product_quantity . " x " . $product_title,
                            'products' => $product_title,
                            'productPrice' => $insurance == "yes" ? $_product->get_price() : 0,
                            'productWeight' => $product_weight,
                            'productLength' => $length,
                            'productWidth' => $width,
                            'productHeight' => $height,
                            'ptype' => $ptype,
                            'sample' => $sample,
                            // FDO
                            'hazardousMaterial' => $hm_status,
                            'productType' => ($_product->get_type() == 'variation') ? 'variant' : 'simple',
                            'productSku' => $_product->get_sku(),
                            'actualProductPrice' => $_product->get_price(),
                            'attributes' => $_product->get_attributes(),
                            'variantId' => ($_product->get_type() == 'variation') ? $_product->get_id() : '',
                            // Nested Material
                            'nestedMaterial' => $nested_material,
                            'nestedPercentage' => $nestedPercentage,
                            'nestedDimension' => $nestedDimension,
                            'nestedItems' => $nestedItems,
                            'stakingProperty' => $StakingProperty,
                            // Crowler work
                            'product_tag' => $product_tag,
                            'single_product_tags' => $this->single_product_tags,

                            // Shippable handling units
                            'ship_item_alone' => $ship_item_alone,

                            // Pricing per product
                            'product_insurance' => $product_insurance,
                            'product_markup' => $product_markup,
                            'product_rental' => $product_rental,
                            'product_quantity' => $product_quantity,
                            'product_price' => $_product->get_price()
                        );

                        // Pricing per product
                        if ($product_insurance > 0) {
                            $en_items['productPrice'] = $product_insurance;
                        }

                        // Hook for flexibility adding to package
                        $en_items = apply_filters('en_group_package', $en_items, $values, $_product);

                        // Micro Warehouse
                        $items[$post_id] = $en_items;

                        $sm_package[$locationId]['items'][] = $en_items;

                        // Hazardous Material
                        if ($hazardous_material == "yes" && !isset($sm_package[$locationId]['hazardous_material'])) {
                            $sm_package[$locationId]['hazardous_material'] = TRUE;
                        }
                    }

                    $sm_package[$locationId]['origin']['ptype'] = $ptype;
                    $sm_package[$locationId][$ptype] = 1;
                }

                // Crowler work
                $en_post_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
                $sm_package[$locationId]['en_product_combination'][] = $en_post_id;
                $this->is_product_can_tag ? $sm_package[$locationId]['is_product_can_tag'] = $this->is_product_can_tag : '';


                // check if LTL enable
                $ltl_enable = $this->wwe_small_enable_shipping_class($_product);

                // Micro Warehouse
                $items_shipment[$post_id] = $ltl_enable;

                $sm_package[$locationId]['shipment_weight'] = isset($sm_package[$locationId]['shipment_weight']) ? $sm_package[$locationId]['shipment_weight'] + $weight : $weight;
                if ($pStatus == '' && $ptype == 'ltl') {
                    return $sm_package = [];
                }
                if ($dimenssions == 0 && $product_weight == 0) {
                    $sm_package[$locationId]['no_parameter'] = 'NOPARAM';
                }
            }

            // Crowler work
            if (!empty($this->warehouse_origin)) {
                $sm_package['warehouse_origin'] = $this->warehouse_origin;
            }

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

            // Micro Warehouse
            $eniureLicenceKey = get_option('wc_settings_plugin_licence_key_wwe_small_packages_quotes');
            $sm_package = apply_filters('en_micro_warehouse', $sm_package, $this->products, $this->dropship_location_array, $this->destination_Address_wwe_spq, $this->origin, $smallPluginExist, $items, $items_shipment, $this->warehouse_products, $eniureLicenceKey, 'small');
            do_action("eniture_debug_mood", "Product Detail (s)", $sm_package);
            return $sm_package;
        }
        return false;
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

                // Start SBS Customization
                $is_tags = explode("-", strtolower($term->name));
                in_array('cans', $is_tags) ? $this->is_product_can_tag = TRUE : '';
                // End SBS Customization

                $before_term = (isset($term_exploer[0])) ? strtolower($term_exploer[0]) : "";

                if (strlen($before_term) > 0 && $before_term == "box") {
                    $after_term = (isset($term_exploer[1])) ? strtolower(trim($term_exploer[1])) : "";
                    $matched_term = $this->search_tag($nikname, $after_term);
                    if (!strlen($tag_name) > 0 && $matched_term) {
                        $tag_name = $term->name;
                    }
                }

                $term_name = (isset($term->name)) ? strtolower(trim($term->name)) : "";
                $this->single_product_tags[] = $term_name;
                if (strlen($term_name) > 0 && $term_name == "lid") {
                    $this->is_product_lid_tag = TRUE;
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
     * Get Enabled Shipping Class Of Product
     * @param type object
     */
    function wwe_small_check_freight_class($_product)
    {

        if ($_product->get_type() == 'variation') {
            $ship_class_id = $_product->get_shipping_class_id();
            if ($ship_class_id == 0) {
                $parent_data = $_product->get_parent_data();
                $get_parent_term = get_term_by('id', $parent_data['shipping_class_id'], 'product_shipping_class');
                $freight_enable_class = (isset($get_parent_term->slug)) ? $get_parent_term->slug : "";
            } else {
                $freight_enable_class = $_product->get_shipping_class();
            }
        } else {
            $freight_enable_class = $_product->get_shipping_class();
        }
        return $freight_enable_class;
    }

    /**
     * Check Product Type
     * @param $freight_enable_class
     * @param $exceedWeight
     * @param $weight
     * @return string
     */
    function wwe_small_check_product_type($freight_enable_class, $exceedWeight, $weight)
    {
        $weight_threshold = get_option('en_weight_threshold_lfq');
        $weight_threshold = isset($weight_threshold) && $weight_threshold > 0 ? $weight_threshold : 150;
        if ($freight_enable_class == 'ltl_freight') {
            $ptype = 'ltl';
        } elseif ($exceedWeight == 'yes' && $weight > $weight_threshold) {
            $ptype = 'ltl';
        } else {
            $ptype = 'small';
        }
        return $ptype;
    }

    function en_hazardous_material($values, $_product)
    {
        $post_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
        return get_post_meta($post_id, '_hazardousmaterials', true);
    }

    function en_insurance_checked($values, $_product)
    {
        $post_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
        return get_post_meta($post_id, '_en_insurance_fee', true);
    }

    /**
     * Get Origin Address
     * @param $values
     * @param $_product
     * @return array
     * @global $wpdb
     */
    function wwe_small_origin_address($values, $_product)
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
                return array('error' => 'wwe small dp location not found!');
            }

            //          Multi Dropship
            $multi_dropship = apply_filters('speed_wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'multi_dropship');

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

            //          Multi Warehouse
            $multi_warehouse = apply_filters('speed_wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'multi_warehouse');
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
        return $locations_list;
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
     * Check Product Enable Against LTL Freight
     * @param $_product
     * @return string
     */
    function wwe_small_enable_shipping_class($_product)
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
     * @param object
     * @return type array
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
