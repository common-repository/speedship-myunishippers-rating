<?php

/**
 * 3dbin template
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("En_Woo_Addon_Box_Size_Detection_Template")) {

    class En_Woo_Addon_Box_Size_Detection_Template extends EnWooBoxSizeAddonsFormHandler {

        public $subscriptionInfo;
        public $subscribedPackage;
        public $subscribedPackageHitsStatus;
        public $nextSubcribedPackage;
        public $statusRequestTime;
        public $subscriptionStatus;
        public $plugin_name;
        public $status;
        public $EnWooAddonsAjaxReqIncludes;
        public $EnWooAddonsCurlReqIncludes;
        public $reset_always_threed;
        public $reset_always_threed_id;
        public $settings;
        public $threedbin_dependencies;
        public $next_subcribed_package;
        public $subscription_details;
        public $lastUsageTime;

        public function __construct() {

            $this->EnWooAddonsAjaxReqIncludes = new EnWooBoxAddonsAjaxReqIncludes();
            $this->EnWooAddonsCurlReqIncludes = new EnWooBoxAddonsCurlReqIncludes();
        }

        /**
         * unset the given fields from settings array
         * @return array
         */
        public function unset_boxsizing_fields() {

            $unset_fields = $this->threedbin_dependencies['unset_fields'];
//             unset fields from @param $settings array standard plugin 
            if (isset($unset_fields) && (!empty($unset_fields)) && (is_array($unset_fields))) {
                foreach ($unset_fields as $value) {
                    unset($this->settings[$value]);
                }
            }

            return $this->settings;
        }

        /**
         * reset the existing box sizing field.
         * @return array type
         */
        public function reset_boxsizing_fields() {

            $this->reset_always_threed = $this->get_arr_filterd_val($this->threedbin_dependencies['reset_always_threed']);
            if (isset($this->reset_always_threed) && (!empty($this->reset_always_threed))) {
                $after_index_fields = "box_sizing_options_label";
                $this->reset_always_threed_id = ( isset($this->settings[$this->reset_always_threed]['id']) ) ? $this->settings[$this->reset_always_threed]['id'] : "en_woo_addons_always_include_threed_fee";
                $this->settings[$this->reset_always_threed]['class'] = (isset($this->settings[$this->reset_always_threed]['class'])) ? $this->settings[$this->reset_always_threed]['class'] : "NA";
                $this->settings[$this->reset_always_threed]['class'] .= " en_woo_addons_always_include_threed_fee";
                $this->settings[$this->reset_always_threed]['name'] = "Always include 3dbin fee ";
                $reset_auto_threed[$this->reset_always_threed] = $this->settings[$this->reset_always_threed];
                $this->settings = $this->addon_array_insert_after($this->settings, $after_index_fields, $reset_auto_threed);
            }
            return $this->settings;
        }

        /**
         * Updated box_sizing_detection_addon array return to standard plugin
         * @return array type
         */
        public function box_sizing_detection_addon($settings, $addons, $plugin_name) {

            $this->plugin_name = $plugin_name;
            $this->settings = $settings;
            $this->threedbin_dependencies = $addons['box_sizing_detection_addon'];
            $after_index_fields = $this->get_arr_filterd_val($this->threedbin_dependencies['after_index_fields']);
            if (isset($after_index_fields) && (!empty($after_index_fields)) && (isset($this->settings[$after_index_fields]))) {
                $this->settings = $this->addon_array_insert_after($this->settings, $after_index_fields, $this->box_sizing_update_fields_arr());
                $this->settings = $this->reset_boxsizing_fields();
                $this->settings = $this->unset_boxsizing_fields();
            }
            $this->settings = apply_filters('en_woo_addons_threed_updated_filters', $this->settings);
            return $this->settings;
        }

        /**
         * Smart street api response curl from server
         * @return array type
         */
        public function customer_subscription_status() {

            $status = $this->EnWooAddonsCurlReqIncludes->smart_street_api_curl_request("list", $this->plugin_name);
            $status = json_decode($status, true);
            return $status;
        }

        /**
         * All packages list auto residential detection
         * @param type $packages_list
         * @return string
         */
        public function packages_list($packages_list) {

            $packages_list_arr = array();
            if (isset($packages_list) && (!empty($packages_list))) {
                $packages_list_arr['options']['disable'] = 'Disable (default)';
                foreach ($packages_list as $key => $value) {
                    $value['pPeriod'] = (isset($value['pPeriod']) && ($value['pPeriod'] == "Month")) ? "mo" : $value['pPeriod'];
                    $value['pHits'] = is_numeric($value['pHits']) ? number_format($value['pHits']) : $value['pHits'];
                    $value['pCost'] = is_numeric($value['pCost']) ? number_format($value['pCost']) : $value['pCost'];
                    $packages_list_arr['options'][$value['pSCAC']] = $value['pHits'] . "/" . $value['pPeriod'] . " ($" . $value['pCost'] . ")";
                }
            }
            return $packages_list_arr;
        }

        /**
         * Ui display for next plan
         * @return string type
         */
        public function next_subcribed_package() {

            $this->next_subcribed_package = (isset($this->nextSubcribedPackage['nextToBeChargedStatus']) && $this->nextSubcribedPackage['nextToBeChargedStatus'] == 1) ? $this->nextSubcribedPackage['nextSubscriptionSCAC'] : "disable";
            return $this->next_subcribed_package;
        }

        /**
         * UI display subcribed package
         * @return string type
         */
        public function subscribed_package() {

            $subscribed_package = $this->subscribedPackage;
            $subscribed_package['packageDuration'] = (isset($subscribed_package['packageDuration']) && ($subscribed_package['packageDuration'] == "Month")) ? "mo" : $subscribed_package['packageDuration'];
            $subscribed_package['packageHits'] = is_numeric($subscribed_package['packageHits']) ? number_format($subscribed_package['packageHits']) : $subscribed_package['packageHits'];
            $subscribed_package['packageCost'] = is_numeric($subscribed_package['packageCost']) ? number_format($subscribed_package['packageCost']) : $subscribed_package['packageCost'];
            return $subscribed_package['packageHits'] . "/" . $subscribed_package['packageDuration'] . " ($" . $subscribed_package['packageCost'] . ")";
        }

        /**
         * Response from smart street api and set in public attributes
         */
        function set_curl_res_attributes() {

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
        public function subscription($status = array()) {

            if (isset($status) && (!empty($status)) && (is_array($status))) {
                $this->status = $status;
            } else { /* onload */
                $this->status = $this->customer_subscription_status();
                //           All plans for 3dbin 
                $packages_list = $this->status['ListOfPackages']['Info'];
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
                    $options = array("en_woo_addons_auto_threed_detection_flag" => $this->subscriptionStatus,
                        "box_sizing_plan_auto_renew" => $this->next_subcribed_package);
                    $this->EnWooAddonsAjaxReqIncludes->update_db($options);
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
                } else {
                    $current_subscription = '<span id="subscribed_package">Your current subscription is expired.</span>';
                    $current_usage = 'Not available.';
                }
            } else {
                $current_subscription = '<span id="subscribed_package">Not subscribed.</span>';
                $current_usage = 'Not available.';
//             when no plan exist plan go to dislable
                $next_subcribed_package = "disable";
            }
            $this->subscription_details = array('current_usage' => (isset($current_usage)) ? $current_usage : "",
                'current_subscription' => (isset($current_subscription)) ? $current_subscription : "",
                'next_subcribed_package' => (isset($next_subcribed_package)) ? $next_subcribed_package : "",
                'packages_list' => (isset($packages_list)) ? $packages_list : "");
            return $this->subscription_details;
        }

        /**
         * new fields add for 3dbin
         * @return array
         */
        public function box_sizing_update_fields_arr() {

            extract($this->subscription());
            $threed_updated_settings = array(
                'box_sizing_plan_auto_renew' => array(
                    'name' => __('Auto-renew ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'select',
                    'default' => $next_subcribed_package,
                    'id' => 'box_sizing_plan_auto_renew',
                    'options' => $packages_list['options']
                ),
                'box_sizing_current_subscription' => array(
                    'name' => __('Current Plan', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'text',
                    'class' => 'hidden',
                    'desc' => $current_subscription,
                    'id' => 'box_sizing_current_subscription'
                ),
                'box_sizing_current_usage' => array(
                    'name' => __('Current Usage', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'text',
                    'class' => 'hidden',
                    'desc' => $current_usage,
                    'id' => 'box_sizing_current_usage'
                ),
                'suspend_automatic_detection_of_box_sizing' => array(
                    'name' => __('Suspend automatic detection of residential addresses', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'checkbox',
                    'id' => 'suspend_automatic_detection_of_box_sizing',
                    'desc' => __(' ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'class' => 'suspend_automatic_detection_of_box_sizing'
                ),
                'box_sizing_plugin_name' => array(
                    'name' => __('', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'text',
                    'class' => "hidden",
                    'placeholder' => $this->plugin_name,
                    'id' => "box_sizing_plugin_name",
                ),
                'box_sizing_subscription_status' => array(
                    'name' => __('', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'text',
                    'class' => "hidden",
                    'placeholder' => $this->subscriptionStatus,
                    'id' => "box_sizing_subscription_status",
                ),
            );
            $box_sizing_updated_settings = apply_filters('en_woo_addons_box_sizing_new_fields_filters', $threed_updated_settings);
            return $box_sizing_updated_settings;
        }

    }

    new En_Woo_Addon_Box_Size_Detection_Template();
}