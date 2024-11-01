<?php

/**
 *  Helper functions class.
 */
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists("En_Box_Sizing_Helper_Function")) {

    class En_Box_Sizing_Helper_Function {

        /**
         * Refine URL
         * @param $domain
         * @return Domain URL
         */
        function en_parse_url($domain) {

            $domain = trim($domain);
            $parsed = parse_url($domain);
            if (empty($parsed['scheme'])) {
                $domain = 'http://' . ltrim($domain, '/');
            }
            $parse = parse_url($domain);
            $refinded_domain_name = $parse['host'];
            $domain_array = explode('.', $refinded_domain_name);
            if (in_array('www', $domain_array)) {
                $key = array_search('www', $domain_array);
                unset($domain_array[$key]);
                $refinded_domain_name = implode($domain_array, '.');
            }
            return $refinded_domain_name;
        }

        /**
         * If array_columsn not exists.
         * @param array $input
         * @param type $columnKey
         * @param type $indexKey
         * @return boolean|array
         */
        function array_column(array $input, $columnKey, $indexKey = null) {

            $array = array();
            foreach ($input as $value) {
                if (!array_key_exists($columnKey, $value)) {
                    trigger_error("Key \"$columnKey\" does not exist in array");
                    return false;
                }
                if (is_null($indexKey)) {
                    $array[] = $value[$columnKey];
                } else {
                    if (!array_key_exists($indexKey, $value)) {
                        trigger_error("Key \"$indexKey\" does not exist in array");
                        return false;
                    }
                    if (!is_scalar($value[$indexKey])) {
                        trigger_error("Key \"$indexKey\" does not contain scalar value");
                        return false;
                    }
                    $array[$value[$indexKey]] = $value[$columnKey];
                }
            }
            return $array;
        }

    }

}