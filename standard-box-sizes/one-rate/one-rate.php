<?php

/**
 *  Box sizes One Rate 
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooAddonBoxSizingOneRate")) {

    class EnWooAddonBoxSizingOneRate {

        public function __construct() {
            add_action('fedex_small_detected', array($this, 'fedex_small_detected_template'), 10, 1);
        }

        public function fedex_small_detected_template() {

            echo '<p class="add_box_packaging_label">FedEx supplied packaging is required for the One Rate program.</p>';
        }

    }

    new EnWooAddonBoxSizingOneRate();
}