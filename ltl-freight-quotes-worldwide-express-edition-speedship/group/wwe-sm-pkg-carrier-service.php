<?php
/*
  LTL Freight Quotes for WooCommerce - Worldwide Express Edition
  Copyright (C) 2016  Eniture LLC d/b/a Eniture Technology

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License version 2
  as published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

  Inquiries can be emailed to info@eniture.com or sent via the postal service to Eniture Technology, 320 W. Lanier Ave, Suite 200, Fayetteville, GA 30214, USA.
 */

class smallpkg_get_quotes
{

    public $isPluginActive = 1;

    function __construct()
    {

        if (!is_plugin_active('ltl-freight-quotes-worldwide-express-edition-speedship/woocommerceShip.php')) {
            $this->isPluginActive = 0;
        }
    }

    function get_smallpkg_group_quotes($packages)
    {

        $freight_zipcode = "";
        $freight_state = "";
        $freight_city = '';
        (strlen(WC()->customer->get_shipping_postcode()) > 0) ? $freight_zipcode = WC()->customer->get_shipping_postcode() : $freight_zipcode = WC()->customer->get_postcode();
        (strlen(WC()->customer->get_shipping_state()) > 0) ? $freight_state = WC()->customer->get_shipping_state() : $freight_state = WC()->customer->get_state();

        $productName = [];
        $productQty = [];
        $productPrice = [];
        $productWeight = [];
        $productLength = [];
        $productWidth = [];
        $productHeight = [];

        foreach ($packages['items'] as $item) {
            $productName[] = $item['productName'];
            $productWeight[] = $item['productWeight'];
            $productLength[] = $item['productLength'];
            $productWidth[] = $item['productWidth'];
            $productHeight[] = $item['productHeight'];
            $productQty[] = $item['productQty'];
            $productPrice[] = $item['productPrice'];
        }
        $domain = $_SERVER['SERVER_NAME'];
        $post_data = array(
            'speed_ship_username' => get_option('wc_settings_username_wwe_small_packages_quotes'),
            'speed_ship_password' => get_option('wc_settings_password_wwe_small_packages'),
            'authentication_key' => get_option('wc_settings_authentication_key_wwe_small_packages_quotes'),
            'world_wide_express_account_number' => get_option('wc_settings_account_number_wwe_small_packages_quotes'),
            'plugin_licence_key' => get_option('wc_settings_plugin_licence_key_wwe_small_packages_quotes'),
            'speed_ship_domain_name' => $domain,
            'speed_ship_reciver_city' => $freight_city,
            'speed_ship_receiver_state' => $freight_state,
            'speed_ship_receiver_zip_code' => $freight_zipcode,
            'speed_ship_senderCity' => $packages['origin']['city'],
            'speed_ship_senderState' => $packages['origin']['state'],
            'speed_ship_senderZip' => $packages['origin']['zip'],
            'speed_ship_senderCountryCode' => $packages['origin']['country'],
            'residentials_delivery' => get_option('wc_settings_quest_as_residential_delivery_wwe_small_packages'),
            // Product Information
            'product_width_array' => $productWidth,
            'product_height_array' => $productHeight,
            'product_length_array' => $productLength,
            'speed_ship_product_price_array' => $productPrice,
            'speed_ship_product_weight' => $productWeight,
            'speed_ship_title_array' => $productName,
            'speed_ship_quantity_array' => $productQty,
        );
        return $post_data;
    }
}