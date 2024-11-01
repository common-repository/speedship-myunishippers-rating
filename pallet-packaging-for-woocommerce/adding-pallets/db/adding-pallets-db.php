<?php

/**
 * Handle table.
 */

namespace SpeedEnPpfwPallethouse;

/**
 * Generic class to handle pallethouse data.
 * Class SpeedEnPpfwPallethouse
 * @package SpeedEnPpfwPallethouse
 */
if (!class_exists('SpeedEnPpfwPallethouse')) {

    class SpeedEnPpfwPallethouse
    {

        /**
         * Hook for call.
         * SpeedEnPpfwPallethouse constructor.
         */
        public function __construct()
        {
            add_action('admin_init', array($this, 'en_pallet_compatability'));
            add_filter('en_register_activation_hook', array($this, 'create_table'), 10, 1);
        }

        /**
         * get pallet option data
         */
        public function en_pallet_compatability()
        {
            global $wpdb;
            $pallet_option_data = get_option('pallet');
            $en_pallet_table = $wpdb->prefix . 'en_pallets';

            if ($wpdb->query("SHOW TABLES LIKE '" . $en_pallet_table . "'") != 0) {
                if (isset($pallet_option_data) && is_string($pallet_option_data) && strlen($pallet_option_data) > 0) {
                    $pallet_options = json_decode($pallet_option_data, true);
                    $en_pallet_nickname = $en_pallet_sizing_length = $en_pallet_width = $en_pallet_sizing_height = $en_pallet_sizing_max_weight = $en_pallet_sizing_weight = $en_pallet_available = '';
                    extract($pallet_options);
                    $pallet_data = [
                        'nickname' => $en_pallet_nickname,
                        'length' => $en_pallet_sizing_length,
                        'width' => $en_pallet_width,
                        'max_height' => $en_pallet_sizing_height,
                        'max_weight' => $en_pallet_sizing_max_weight,
                        'pallet_weight' => $en_pallet_sizing_weight,
                        'available' => $en_pallet_available
                    ];

                    $get_data = self::get_data(['nickname' => $en_pallet_nickname]);
                    if (empty($get_data)) {
                        $wpdb->insert($en_pallet_table, $pallet_data);
                        delete_option('pallet');
                    }
                }
            }
        }

        /**
         * Get pship list
         * @param array $en_enp_details
         * @return array|object|null
         */
        public static function get_data($en_enp_details = [])
        {
            global $wpdb;

            if (isset($en_enp_details['enp'])) {
                unset($en_enp_details['enp']);
            }
            $en_where_clause_str = '';
            $en_where_clause_param = [];
            if (isset($en_enp_details) && !empty($en_enp_details)) {

                foreach ($en_enp_details as $index => $value) {
                    $en_where_clause_str .= (strlen($en_where_clause_str) > 0) ? ' AND ' : '';
                    $en_where_clause_str .= $index . ' = %s ';
                    $en_where_clause_param[] = $value;
                }
                $en_where_clause_str = (strlen($en_where_clause_str) > 0) ? ' WHERE ' . $en_where_clause_str : '';
            }

            $en_table_name = $wpdb->prefix . 'en_pallets';
            if (!empty($en_where_clause_str) && !empty($en_where_clause_param)) {
                $sql = $wpdb->prepare("SELECT * FROM $en_table_name $en_where_clause_str", $en_where_clause_param);
                return (array)$wpdb->get_results($sql, ARRAY_A);
            } else {
                return (array)$wpdb->get_results("SELECT * FROM $en_table_name", ARRAY_A);
            }
        }

        /**
         * Create table for pallethouse, pship
         */
        public function create_table()
        {
            global $wpdb;

            $en_charset_collate = $wpdb->get_charset_collate();
            $en_table_name = $wpdb->prefix . 'en_pallets';
            if ($wpdb->query("SHOW TABLES LIKE '" . $en_table_name . "'") === 0) {
                $en_created_table = 'CREATE TABLE ' . $en_table_name . '( 
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                nickname varchar(255) NOT NULL,
                length varchar(255) NOT NULL,
                width varchar(255) NOT NULL,
                max_height varchar(255) NOT NULL,
                pallet_height varchar(255) NOT NULL,
                max_weight varchar(255) NOT NULL,
                pallet_weight varchar(255) NOT NULL,
                available varchar(20) NOT NULL,
                PRIMARY KEY  (id)        
                )' . $en_charset_collate;

                $wpdb->query($en_created_table);
                $success = empty($wpdb->last_error);

                return $success;
            }
        }
    }
}
