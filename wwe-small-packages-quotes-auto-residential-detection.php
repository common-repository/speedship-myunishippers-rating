<?php

if (!defined("ABSPATH")) {
    exit();
}

if (!class_exists("Speed_Wwe_Small_Auto_Residential_Detection")) {

    class Speed_Wwe_Small_Auto_Residential_Detection {

        public $label_sfx_arr;

        public function __construct() {

            $this->label_sfx_arr = [];
        }

        public function filter_label_sufex_array($result) {

            (isset($result->residentialStatus) && ($result->residentialStatus == "r")) ? array_push($this->label_sfx_arr, "R") : "";
            (isset($result->liftGateStatus) && ($result->liftGateStatus == "l")) ? array_push($this->label_sfx_arr, "L") : "";
            return array_unique($this->label_sfx_arr);
        }

    }

    new Speed_Wwe_Small_Auto_Residential_Detection();
}