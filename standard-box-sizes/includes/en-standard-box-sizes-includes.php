<?php

/**
 * Includes Engine class
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooBoxAddonsInclude")) {

    class EnWooBoxAddonsInclude extends EnWooBoxAddonPluginDetail
    {

        public $plugin_id;
        public $plugin_dependencies;
        public $plugin_standards;

        /**
         * construct
         */
        public function __construct()
        {
            $this->en_woo_addons_load_common_files();
            add_action('admin_print_scripts', array($this, 'admin_inline_js'));
        }

        /**
         * common files include for all addons
         */
        public function en_woo_addons_load_common_files()
        {
            include_once(speed_standard_box_sizes_addon_plugin_url . '/includes/en-woo-box-addons-ajax-request.php');
            include_once(speed_standard_box_sizes_addon_plugin_url . '/includes/en-woo-box-addons-forms-handler.php');
        }

        /**
         * globally script variable
         */
        public function admin_inline_js()
        {
?>
            <script>
                var plugins_url = "<?php echo plugins_url(); ?>";
            </script>
<?php
        }

        /**
         * Plugins dependencies array merge
         * @return array
         */
        public function plugins_dependencies()
        {
            $plugins_dependies = array();
            $plugins_dependies_function_arr = array(
                'wwe_small_packages_quotes_dependencies',
                'fedex_small_dependencies',
                'purolator_small_dependencies',
                'ups_small_plugin_dependencies',
                'unishipper_small_dependencies',
                'usps_small_plugin_dependencies',
                'trinet_small_dependencies',
            );

            foreach ($plugins_dependies_function_arr as $value) {
                $plugins_dependies = array_merge($plugins_dependies, $this->$value());
            }
            $plugins_dependies = apply_filters('en_woo_addons_plugin_dependencies_apply_filters', $plugins_dependies);

            return $plugins_dependies;
        }
    }

    new EnWooBoxAddonsInclude();
}
