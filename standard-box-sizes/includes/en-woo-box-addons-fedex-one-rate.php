<?php

/**
 *  Box sizes template
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooAddonFedexOneRate")) {

    class EnWooAddonFedexOneRate
    {

        public function __construct()
        {
            add_filter('fedex_one_rate_data', array($this, 'fedex_one_rate_data'));
            add_filter('fedex_one_rate_img', array($this, 'fedex_one_rate_img'));
        }

        public function fedex_one_rate_img()
        {
            return array
            (
                0 => 'Envelope-172x115.png',
                2 => 'Pak-192x115.png',
                6 => 'Small-Box-151x115.png',
                8 => 'Medium-Box-148x115.png',
                10 => 'Large-Box-146x115.png',
                12 => 'Extra-Large-Box-103x115.png'
            );
        }

        public function fedex_one_rate_tilte()
        {
            return array
            (
                'FedEx Envelope',
                'FedEx Reusable Envelope',
                'FedEx Pak - Small',
                'FedEx Pak - Large',
                'FedEx Pak - Padded',
                'FedEx Pak - Reusable',
                'FedEx Small Box',
                'FedEx Small Box',
                'FedEx Medium Box',
                'FedEx Medium Box',
                'FedEx Large Box',
                'FedEx Large Box',
                'FedEx Extra Large Box',
                'FedEx Extra Large Box'
            );
        }

        public function fedex_one_rate_data()
        {
            $domain = en_sbs_get_domain();
            if ('drhoys.com' == $domain || 'localhost' == $domain || 'wpqa1.eniture-qa.com' == $domain) {
                return [
                    array(9.5, 12.5, 0.5, 0, 0, 0, 10, 0, 'FEDEX_ENVELOPE', 0, 'FedEx Envelope'),
                    array(9.5, 15.5, 0.5, 0, 0, 0, 10, 0, 'FEDEX_ENVELOPE', 1, 'FedEx Reusable Envelope'),
                    array(10.25, 12.75, 1.5, 0, 0, 0, 50, 0, 'FEDEX_PAK', 2, 'FedEx Pak - Small'),
                    array(12, 15.5, 1.5, 0, 0, 0, 50, 0, 'FEDEX_PAK', 3, 'FedEx Pak - Large'),
                    array(11.75, 14.75, 1.25, 0, 0, 0, 50, 0, 'FEDEX_PAK', 4, 'FedEx Pak - Padded'),
                    array(10, 14.5, 1.25, 0, 0, 0, 50, 0, 'FEDEX_PAK', 5, 'FedEx Pak - Reusable'),
                    array(7.5, 8.5, 3.5, 0, 0, 0, 50, 0, 'FEDEX_PAK', 6, "FedEx Pak - Dr Hoys"),
                    array(10.875, 1.5, 12.375, 0, 0, 0, 50, 0, 'FEDEX_SMALL_BOX', 7, 'FedEx Small Box'),
                    array(8.75, 2.625, 11.25, 0, 0, 0, 50, 0, 'FEDEX_SMALL_BOX', 8, 'FedEx Small Box'),
                    array(11.5, 2.375, 13.25, 0, 0, 0, 50, 0, 'FEDEX_MEDIUM_BOX', 9, 'FedEx Medium Box'),
                    array(8.75, 4.375, 11.25, 0, 0, 0, 50, 0, 'FEDEX_MEDIUM_BOX', 10, 'FedEx Medium Box'),
                    array(12.375, 3, 17.5, 0, 0, 0, 50, 0, 'FEDEX_LARGE_BOX', 11, 'FedEx Large Box'),
                    array(8.75, 7.75, 11.25, 0, 0, 0, 50, 0, 'FEDEX_LARGE_BOX', 12, 'FedEx Large Box'),
                    array(11.875, 10.75, 11, 0, 0, 0, 50, 0, 'FEDEX_EXTRA_LARGE_BOX', 13, 'FedEx Extra Large Box'),
                    array(15.75, 14.125, 6, 0, 0, 0, 50, 0, 'FEDEX_EXTRA_LARGE_BOX', 14, 'FedEx Extra Large Box'),
                ];
            }

            return array
            (
                array(9.5, 12.5, 0.5, 0, 0, 0, 10, 0, 'FEDEX_ENVELOPE', 0, 'FedEx Envelope'),
                array(9.5, 15.5, 0.5, 0, 0, 0, 10, 0, 'FEDEX_ENVELOPE', 1, 'FedEx Reusable Envelope'),
                array(10.25, 12.75, 1.5, 0, 0, 0, 50, 0, 'FEDEX_PAK', 2, 'FedEx Pak - Small'),
                array(12, 15.5, 1.5, 0, 0, 0, 50, 0, 'FEDEX_PAK', 3, 'FedEx Pak - Large'),
                array(11.75, 14.75, 1.25, 0, 0, 0, 50, 0, 'FEDEX_PAK', 4, 'FedEx Pak - Padded'),
                array(10, 14.5, 1.25, 0, 0, 0, 50, 0, 'FEDEX_PAK', 5, 'FedEx Pak - Reusable'),
                array(10.875, 1.5, 12.375, 0, 0, 0, 50, 0, 'FEDEX_SMALL_BOX', 6, 'FedEx Small Box'),
                array(8.75, 2.625, 11.25, 0, 0, 0, 50, 0, 'FEDEX_SMALL_BOX', 7, 'FedEx Small Box'),
                array(11.5, 2.375, 13.25, 0, 0, 0, 50, 0, 'FEDEX_MEDIUM_BOX', 8, 'FedEx Medium Box'),
                array(8.75, 4.375, 11.25, 0, 0, 0, 50, 0, 'FEDEX_MEDIUM_BOX', 9, 'FedEx Medium Box'),
                array(12.375, 3, 17.5, 0, 0, 0, 50, 0, 'FEDEX_LARGE_BOX', 10, 'FedEx Large Box'),
                array(8.75, 7.75, 11.25, 0, 0, 0, 50, 0, 'FEDEX_LARGE_BOX', 11, 'FedEx Large Box'),
                array(11.875, 10.75, 11, 0, 0, 0, 50, 0, 'FEDEX_EXTRA_LARGE_BOX', 12, 'FedEx Extra Large Box'),
                array(15.75, 14.125, 6, 0, 0, 0, 50, 0, 'FEDEX_EXTRA_LARGE_BOX', 13, 'FedEx Extra Large Box'),
            );
        }

        public function fedex_one_rate_data_backup()
        {
            return array
            (
                array(9.5, 12.5, 0.5, 10, 0, 'FEDEX_ENVELOPE', 0, 'FedEx Envelope'),
                array(9.5, 15.5, 0.5, 10, 0, 'FEDEX_ENVELOPE', 1, 'FedEx Reusable Envelope'),
                array(10.25, 12.75, 1.5, 50, 0, 'FEDEX_PAK', 2, 'FedEx Pak - Small'),
                array(12, 15.5, 1.5, 50, 0, 'FEDEX_PAK', 3, 'FedEx Pak - Large'),
                array(11.75, 14.75, 1.25, 50, 0, 'FEDEX_PAK', 4, 'FedEx Pak - Padded'),
                array(10, 14.5, 1.25, 50, 0, 'FEDEX_PAK', 5, 'FedEx Pak - Reusable'),
                array(10.875, 1.5, 12.375, 50, 0, 'FEDEX_SMALL_BOX', 6, 'FedEx Small Box'),
                array(8.75, 2.625, 11.25, 50, 0, 'FEDEX_SMALL_BOX', 7, 'FedEx Small Box'),
                array(11.5, 2.375, 13.25, 50, 0, 'FEDEX_MEDIUM_BOX', 8, 'FedEx Medium Box'),
                array(8.75, 4.375, 11.25, 50, 0, 'FEDEX_MEDIUM_BOX', 9, 'FedEx Medium Box'),
                array(12.375, 3, 17.5, 50, 0, 'FEDEX_LARGE_BOX', 10, 'FedEx Large Box'),
                array(8.75, 7.75, 11.25, 50, 0, 'FEDEX_LARGE_BOX', 11, 'FedEx Large Box'),
                array(11.875, 10.75, 11, 50, 0, 'FEDEX_EXTRA_LARGE_BOX', 12, 'FedEx Extra Large Box'),
                array(15.75, 14.125, 6, 50, 0, 'FEDEX_EXTRA_LARGE_BOX', 13, 'FedEx Extra Large Box'),
            );
        }

    }

    new EnWooAddonFedexOneRate();
}

