<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Ltl_Freight_Quotes')) {
    class Ltl_Freight_Quotes
    {
        /**
         * rating method from quote settings
         * @var string type
         */
        public $rating_method;

        /**
         * rates from web service
         * @var array type
         */
        public $quotes;

        /**
         * wwe settings
         * @var array type
         */
        public $quote_settings;

        /**
         * label from quote settings
         * @var atring type
         */
        public $wwe_label;

        /**
         * class name
         * @var class type
         */
        public $VersionCompat;
        public $count = 0;

        function __construct()
        {
        }

        /**
         * set values in class attributes and return quotes
         * @param array type $quotes
         * @param array type $quote_settings
         * @return array type
         */
        public function calculate_quotes($quotes, $quote_settings)
        {
            $quotes = $this->sort_asec_order_arr($quotes, 'cost');
            $this->quotes = $quotes;
            $this->quote_settings = $quote_settings;
            $this->total_carriers = $this->quote_settings['total_carriers'];

            $this->VersionCompat = new VersionCompat();
            $rating_method = $this->quote_settings['rating_method'];
            return $this->$rating_method();
        }

        function rand_string()
        {
            return md5(uniqid(mt_rand(), true));
        }

        /**
         * calculate average for quotes
         * @return array type
         */
        public function average_rate()
        {
            $this->quotes = (isset($this->quotes) && (is_array($this->quotes))) ? array_slice($this->quotes, 0, $this->total_carriers) : [];
            $rate_list = $this->VersionCompat->enArrayColumn($this->quotes, 'cost');
            $rate_sum = array_sum($rate_list) / count($this->quotes);
            $quotes_reset = reset($this->quotes);
            $meta_data = (isset($quotes_reset['meta_data'])) ? $quotes_reset['meta_data'] : [];
            if (isset($meta_data['en_fdo_meta_data'], $meta_data['en_fdo_meta_data']['rate'], $meta_data['en_fdo_meta_data']['rate']['cost'])) {
                $meta_data['en_fdo_meta_data']['rate']['cost'] = $rate_sum;
                $meta_data['en_fdo_meta_data']['rate']['label'] = 'Freight';
            }

            $this->count++;

            $rate[] = array(
                'id' => $this->rand_string(),
                'id' => 'en_avg_wwe_' . $this->count++,
                'cost' => $rate_sum,
                'markup' => (isset($quotes_reset['markup'])) ? $quotes_reset['markup'] : "",
                'label_sufex' => (isset($quotes_reset['label_sufex'])) ? $quotes_reset['label_sufex'] : [],
                'append_label' => (isset($quotes_reset['append_label'])) ? $quotes_reset['append_label'] : "",
                'meta_data' => $meta_data,
                'total_product_markup' => (isset($quotes_reset['total_product_markup'])) ? $quotes_reset['total_product_markup'] : 0,
            );

            return $rate;
        }

        /**
         * sort array
         * @param array type $rate
         * @return array type
         */
        public function sort_asec_order_arr($rate, $index)
        {
            $price_sorted_key = [];
            foreach ($rate as $key => $cost_carrier) {
                $price_sorted_key[$key] = (isset($cost_carrier[$index])) ? $cost_carrier[$index] : 0;
            }
            array_multisort($price_sorted_key, SORT_ASC, $rate);

            return $rate;
        }

        /**
         * calculate cheapest rate
         * @return type
         */
        public function Cheapest()
        {
            return (isset($this->quotes) && (is_array($this->quotes))) ? array_slice($this->quotes, 0, 1) : [];
        }

        /**
         * calculate cheapest rate numbers
         * @return array type
         */
        public function cheapest_options()
        {
            return (isset($this->quotes) && (is_array($this->quotes))) ? array_slice($this->quotes, 0, $this->total_carriers) : [];
        }

    }
}