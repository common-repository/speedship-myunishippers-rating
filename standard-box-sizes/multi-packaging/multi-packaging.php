<?php

/**
 * Includes Form Hanlder
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooBoxSizeMultiPackage")) {

    class EnWooBoxSizeMultiPackage extends EnWooBoxAddonsInclude
    {

        public $EnWooAddonBoxSizingTemplate;
        public $query;
        public $sbs_recursive = '';

        public function __construct()
        {
            add_action('woocommerce_settings_wc_settings_quote_section_end_box_sizing_after', array($this, 'en_update_setings_box_sizes'), 999);
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
         * Add settings for multi package
         * @param array $settings
         * @param array $addons
         * @param string $section
         * @param string $plugin_id
         * @return array
         * @global type $product
         */
        public function en_update_setings_box_sizes()
        {
            $this->sbs_recursive = 'sbs_multipckg_template';
            $sbs_recursive = apply_filters('en_sbs_recursive', []);
            if (!empty($sbs_recursive) && in_array($this->sbs_recursive, $sbs_recursive)) {
                return;
            }

            add_filter('en_sbs_recursive', [$this, 'en_sbs_recursive'], 10, 1);

            $plugin_id = (isset($_REQUEST['tab'])) ? sanitize_text_field($_REQUEST['tab']) : '';
            $section = (isset($_REQUEST['section'])) ? sanitize_text_field($_REQUEST['section']) : '';
            $plugins_dependencies = $this->plugins_dependencies();

            if (isset($plugins_dependencies[$plugin_id])) {
                $plugin_detail = $plugins_dependencies[$plugin_id];
                $addons = $plugin_detail['addons'];

                if ($addons['box_sizing_addon']['active'] === true && $section == $addons['box_sizing_addon']['section']) {
                    $settings = [];
                    $products_args = array(
                        'post_type' => ['product', 'product_variation'],
                        'posts_per_page' => -1,
                        'meta_query' => array(
                            array(
                                'key' => '_en_multiple_packages',
                                'value' => 'yes',
                            ),
                        ),
                    );

                    $query = [];
                    $this->EnWooAddonBoxSizingTemplate = new EnWooAddonBoxSizingTemplate();

                    $products = new WP_Query($products_args);
                    echo '<div class="en_multiple_packages">';
                    echo '<div class="en_multiple_packages">';
                    echo '<h1>Items that ship as multiple packages</h1>';
                    echo '<p class="en_multiple_packages_instructions">In order for an item to appear below, it must have the <i>This item ships as multiple packages</i> setting enabled on its Product > Shipping parameters page.</p>';
                    $step_for_products = TRUE;
                    while ($products->have_posts()) : $products->the_post();

                        $step_for_products = FALSE;
                        global $product;
                        $get_sku = $product->get_sku();
                        $product_id = get_the_ID();
                        echo '<div class="en_multiple_package_list">';
                        echo '<p class="en_multiple_packages_headings"> SKU: ' . $get_sku . '</p>';
                        echo '<p class="en_multiple_packages_headings">' . get_the_title() . '</p>';
                        echo esc_html($this->EnWooAddonBoxSizingTemplate->en_woo_addons_box_sizing_table($product_id, $this->EnWooAddonBoxSizingTemplate->query));
                        echo '<br>';
                        echo '<br>';
                        echo '</div>';

                    endwhile;

                    wp_reset_query();

                    echo esc_html($this->EnWooAddonBoxSizingTemplate->en_wwe_small_re_arrange_fields(TRUE));
                    echo '</div>';
                }

                if ($step_for_products) {
                    echo '<p>No product checked for multi packaging</p>';
                }

                return $settings;
            }
        }

    }

    new EnWooBoxSizeMultiPackage();
}