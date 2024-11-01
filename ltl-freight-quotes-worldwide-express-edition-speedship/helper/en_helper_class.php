<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of en_helper_class
 *
 * @author alignpx02
 */
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists("En_Wwe_LTL_Helper_Class")) {

    class En_Wwe_LTL_Helper_Class {

        /**
         * Constructor.
         */
        public function __construct() {
            
        }

        /**
         * If array_columsn not exists.
         * @param array $input
         * @param type $columnKey
         * @param type $indexKey
         * @return boolean|array
         */
        function array_column(array $input, $columnKey, $indexKey = null) {
            $array = [];
            foreach ($input as $value) {
                if (!array_key_exists($columnKey, $value)) {
                  
                    return false;
                }
                if (is_null($indexKey)) {
                    $array[] = $value[$columnKey];
                } else {
                    if (!array_key_exists($indexKey, $value)) {
                        
                        return false;
                    }
                    if (!is_scalar($value[$indexKey])) {
                      
                        return false;
                    }
                    $array[$value[$indexKey]] = $value[$columnKey];
                }
            }
            return $array;
        }

    }

}
