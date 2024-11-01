<?php
/**
 * Includes Form Hanlder
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooBoxSizeAddonsFormHandler")) {

    class EnWooBoxSizeAddonsFormHandler extends EnWooBoxAddonsInclude {

        public $settings;
        public $sections;
        public $section;
        public $plugin_id;
        public $plugins_dependencies;
        public $plugins_dependencies_script_css_files;
        protected $plugin_detail;

        /**
         * construct
         */
        public function __construct() {

            $this->plugins_dependencies = $this->plugins_dependencies();
            add_filter('en_woo_addons_settings', array($this, 'en_woo_addons_settings_arr'), 10, 3);
            add_filter('en_woo_addons_sections', array($this, 'en_woo_addons_sections_arr'), 10, 2);
            add_action('woocommerce_settings_tabs_array', array($this, 'en_woo_addons_popup_notifi_disabl_to_plan_box'), 10);
        }

        /**
         * Update web api sections array 
         * @param array $sections
         * @param string $plugin_id
         * @return array
         */
        public function en_woo_addons_sections_arr($sections, $plugin_id) {

            $this->sections = $sections;
            if (isset($this->plugins_dependencies[$plugin_id])) {
                $plugin_detail = $this->plugins_dependencies[$plugin_id];
                $addons = $plugin_detail['addons'];
                if ($addons['box_sizing_addon']['active'] === true) {
                    $key = key(array_slice($this->sections, -2, 1));
                    $new = array('section-box' => 'Box Sizes');
                    $this->sections = $this->addon_array_insert_after($this->sections, $key, $new);
                }
            }
            return $this->sections;
        }

        /**
         * Update web api settings array 
         * @param array $settings
         * @param string $section
         * @param string $plugin_id
         * @return array
         */
        public function en_woo_addons_settings_arr($settings, $section, $plugin_id) {

            $this->settings = $settings;
            $this->section = $section;
            $this->plugin_id = $plugin_id;
            $this->settings = $this->get_settings();
            return $this->settings;
        }

        /**
         * Find out exact module is running and return his fields web settings array 
         * @return array
         */
        public function get_settings() {

            if (isset($this->plugins_dependencies[$this->plugin_id])) {
                $plugin_detail = $this->plugins_dependencies[$this->plugin_id];
                $addons = $plugin_detail['addons'];
                if ($addons['box_sizing_addon']['active'] === true && $this->section == $addons['box_sizing_addon']['section']) {
                    $EnWooAddonBoxSizingTemplate = new EnWooAddonBoxSizingTemplate();
                    $this->settings = $EnWooAddonBoxSizingTemplate->en_woo_addons_box_sizing_fields_arr($this->plugin_id);
                }
            }

            return $this->settings;
        }

        /**
         * Array merge after specific index 
         * @param array $array
         * @param index of array $key
         * @param array $new
         * @return array
         */
        public function addon_array_insert_after(array $array, $key, array $new) {

            if (isset($key) && in_array($key, array_keys($array))) {
                $keys = array_keys($array);
                $index = array_search($key, $keys);
                $pos = false === $index ? count($array) : $index + 1;
                $array = array_merge(array_slice($array, 0, $pos), $new, array_slice($array, $pos));
            }
            return $array;
        }

        /**
         * Formate the given date time @param $datetime like in sow
         * @param datetime $datetime
         * @return string 
         */
        public function formate_date_time($datetime) {

            $date = date_create($datetime);
            return date_format($date, "M. d, Y");
        }

        /**
         * get_arr_filterd_val function see for if @param $arr_val type is array reset value return
         * @param array or string type $arr_val
         * @return string type
         */
        public function get_arr_filterd_val($arr_val) {

            return (isset($arr_val) && (!empty($arr_val)) ) ? (is_array($arr_val)) ? reset($arr_val) : $arr_val : "";
        }

        /**
         * Popup notification for using notification show during disable to plan through using jquery
         * @return html
         */
        public function en_woo_addons_popup_notifi_disabl_to_plan_box() {
            ?>
<div id="plan_confirmation_popup" class="sm_notification_disable_to_plan_overlay_box" style="display: none;">
                <div class="sm_wwe_small_notifi_disabl_to_plan_box">
                    <h2 class="del_hdng">
                        Note!
                    </h2>
                    <p class="confirmation_p">
                        Note! You have elected to enable the Standard Box Sizes feature. By confirming this election you will be charged for the <span id="selected_plan_popup_box">[plan]</span> plan. To ensure service continuity the plan will automatically renew each month, or when the plan is depleted, whichever comes first. You can change which plan is put into effect on the next renewal date by updating the selection on this page at anytime.
                    </p>
                    <div class="confirmation_btns">
                        <a style="cursor: pointer" class="cancel_plan">Cancel</a>
                        <a style="cursor: pointer" class="confirm_plan">OK</a>
                    </div>
                </div>
            </div>
            <?php
        }

    }

    new EnWooBoxSizeAddonsFormHandler();
}


