<?php

/**
 * 3dbin template
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooBoxAddonPluginDetail")) {

    class EnWooBoxAddonPluginDetail
    {

        public $plugin_details;

        /**
         * setter plugin details
         * @param type $plugin_details
         */
        public function set_details($plugin_details)
        {

            $this->plugin_details = $plugin_details;
        }

        /**
         * getter plugin details
         * @return type
         */
        public function get_details()
        {

            return $this->plugin_details;
        }

        /**
         * Wwe_small_packages_quotes_dependencies for eniture addons
         * @return array
         */
        public function wwe_small_packages_quotes_dependencies()
        {

            $wwe_small_packages_quotes = array(
                "wwe_small_packages_quotes" => array(
                    'addons' => array(
                        'box_sizing_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'Service_UPS_Next_Day_Early_AM_small_packages_quotes'
                            ),
                            'unset_fields' => array(),
                            'reset_always_threed' => array(
                                'quest_as_residential_delivery_wwe_small_packages',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'box_sizing_current_usage'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(),
                        ),
                        'box_sizing_addon' => array(
                            'active' => true,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'wc_settings_plugin_licence_key_wwe_small_packages_quotes'
                    )
                )
            );
            return $wwe_small_packages_quotes;
        }

        /**
         * trinet_small_dependencies for eniture woo addons
         * @return array
         */
        public function trinet_small_dependencies()
        {
            $trinet_small = array(
                "trinet" => array(
                    'addons' => array(
                        'box_sizing_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'shipping_methods_do_not_sort_by_price'
                            ),
                            'unset_fields' => array(
                                'en_trinet_residential_delivery',
                            ),
                            'reset_always_threed' => array(
                                'en_trinet_residential_delivery',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => false,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                ''
                            ),
                            'unset_fields' => array(
                                ''
                            ),
                            'reset_always_lift_gate' => array(
                                ''
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => true,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        ),
                        'multi_packg_box_sizing_addon' => array(
                            'active' => true,
                            'section' => 'section-multi-packaging',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'en_connection_settings_license_key_trinet'
                    )
                )
            );
            return $trinet_small;
        }

        /**
         * Fedex_small_dependencies for eniture woo addons
         * @return array
         */
        public function fedex_small_dependencies()
        {
            $fedex_small = array(
                "fedex_small" => array(
                    'addons' => array(
                        'box_sizing_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'publish_negotiated_fedex_small_rates'
                            ),
                            'unset_fields' => array(
                                'fedex_small_residential_delivery',
                            ),
                            'reset_always_threed' => array(
                                'fedex_small_residential_delivery',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => false,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'box_sizing_current_usage'
                            ),
                            'unset_fields' => array(
                                'ups_freight_liftgate_delivery'
                            ),
                            'reset_always_lift_gate' => array(
                                'ups_freight_liftgate_delivery'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => true,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        ),
                        'multi_packg_box_sizing_addon' => array(
                            'active' => true,
                            'section' => 'section-multi-packaging',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'fedex_small_licence_key'
                    )
                )
            );
            return $fedex_small;
        }

        /**
         * unishipper_small_dependencies for eniture woo addons
         * @return array
         */
        public function unishipper_small_dependencies()
        {

            $unishipper_small = array(
                "unishepper_small" => array(
                    'addons' => array(
                        'box_sizing_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'publish_negotiated_fedex_small_rates'
                            ),
                            'unset_fields' => array(
                                'unishepper_small_residential_delivery',
                            ),
                            'reset_always_threed' => array(
                                'unishepper_small_residential_delivery',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => false,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'box_sizing_current_usage'
                            ),
                            'unset_fields' => array(
                                'unishepper_small_liftgate_delivery'
                            ),
                            'reset_always_lift_gate' => array(
                                'ups_freight_liftgate_delivery'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => true,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'unishepper_small_licence_key'
                    )
                )
            );
            return $unishipper_small;
        }

        /**
         * Purolator_small_dependencies for eniture woo addons
         * @return array
         */
        public function purolator_small_dependencies()
        {

            $purolator_small = array(
                "purolator_small" => array(
                    'addons' => array(
                        'box_sizing_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'purolator_small_int_distribution'
                            ),
                            'unset_fields' => array(),
                            'reset_always_threed' => array(),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => false,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'box_sizing_current_usage'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'accessorial_liftgate_delivery_rnl'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => true,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'purolator_small_licence_key'
                    )
                )
            );
            return $purolator_small;
        }

        /**
         * Ups_small_plugin_dependencies for eniture woo addons
         * @return array
         */
        public function ups_small_plugin_dependencies()
        {

            $ups_small_plugin = array(
                "ups_small" => array(
                    'addons' => array(
                        'box_sizing_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'ups_small_3day_select'
                            ),
                            'unset_fields' => array(),
                            'reset_always_threed' => array(
                                'ups_small_residential_delivery',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => false,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'box_sizing_current_usage'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'accessorial_liftgate_delivery_fedex_freight'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => true,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'ups_small_licence_key'
                    )
                )
            );
            return $ups_small_plugin;
        }

        /**
         * Usps_small_plugin_dependencies for eniture woo addons
         * @return array
         */
        public function usps_small_plugin_dependencies()
        {

            $usps_small_plugin = array(
                "usps_small" => array(
                    'addons' => array(
                        'box_sizing_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'usps_small_retail_ground'
                            ),
                            'unset_fields' => array(),
                            'reset_always_threed' => array(
                                'usps_small_residential_delivery',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => false,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'box_sizing_current_usage'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'accessorial_liftgate_delivery_fedex_freight'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => true,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'usps_small_licence_key'
                    )
                )
            );
            return $usps_small_plugin;
        }

    }

    new EnWooBoxAddonPluginDetail();
}