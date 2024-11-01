<?php

/**
 * WWE Small Get Distance
 *
 * @package     WWE Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Distance Request Class
 */
class Speed_Get_sm_distance
{

    function __construct()
    {
        add_filter("en_wd_get_address", array($this, "sm_address"), 10, 2);
    }

    /**
     * Get Address Upon Access Level
     * @param $speed_map_address
     * @param $accessLevel
     */
    function sm_address($speed_map_address, $accessLevel, $destinationZip = [])
    {

        // $domain = wwe_small_get_domain();
        // $postData = array(
        //     'acessLevel' => $accessLevel,
        //     'address' => $speed_map_address,
        //     'originAddresses' => (isset($speed_map_address)) ? $speed_map_address : "",
        //     'destinationAddress' => (isset($destinationZip)) ? $destinationZip : "",
        //     'eniureLicenceKey' => get_option('wc_settings_plugin_licence_key_wwe_small_packages_quotes'),
        //     'ServerName' => $_SERVER['SERVER_NAME'],
        //     'ServerName' => $domain,
        // );

        // $Speed_Small_Package_Request = new Speed_Small_Package_Request();
        // $output = $Speed_Small_Package_Request->small_package_get_curl_response(SPEED_WWE_DOMAIN_HITTING_URL . '/addon/google-location.php', $postData);

        //get the google key, if it doesn't exist, then return the first warehouse
        $googlekey = get_option('wc_settings_googleapi_wwe_small_packages_quotes');
        if(!$googlekey) return $speed_map_address[0];

        //loop through speed_map_address and get each warehouse zip code
        $start = '';
        foreach($speed_map_address as $row){
            $start .= "|".$row->zip;
        }

        $url = "https://maps.googleapis.com/maps/api/distancematrix/json";
        $destination = $destinationZip['zip'];
        $getparameter = [
            "origins"=> $start,
            "destinations" => $destination,
            "key" => $googlekey
        ];

        //send request
        $params = http_build_query($getparameter);
        $result = wp_remote_get($url . "?" . $params);
        $body = wp_remote_retrieve_body($result);
        $distanceMatrix = json_decode($body,true);
        
        //loop through result and find key of lowest distance
        $lowestDistance = 0;
        $lowestIndex = 0;
        for($i = 0;$i<count($distanceMatrix['rows']);$i++){
            
            if($i == 0) $lowest = $distanceMatrix['rows'][$i]['elements'][0]['distance']['value'];
            if($distanceMatrix['rows'][$i]['elements'][0]['distance']['value'] <  $lowest){
                $lowest = $distanceMatrix['rows'][$i]['elements'][0]['distance']['value'];
                $lowestIndex = $i;
            }
            
        }

        //use index to return the closest warehouse
        return $speed_map_address[$lowestIndex];
    
    }
}
