<?php

/**
 * WWE LTL Distance Get
 *
 * @package     WWE LTL Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Speed_Get_ltl_distance
 */
class Speed_Get_ltl_distance
{
    /**
     * Get Distance Function
     * @param $speed_map_address
     * @param $accessLevel
     * @return json
     */
    function ltl_get_distance($speed_map_address, $accessLevel, $destinationZip = [])
    {

        // $domain = wwe_quests_get_domain();
        // $post = array(
        //     'acessLevel' => $accessLevel,
        //     'address' => $speed_map_address,
        //     'originAddresses' => (isset($speed_map_address)) ? $speed_map_address : "",
        //     'destinationAddress' => (isset($destinationZip)) ? $destinationZip : "",
        //     'eniureLicenceKey' => get_option('wc_settings_wwe_licence_key'),
        //     'ServerName' => $domain,
        // );


        // if (is_array($post) && count($post) > 0) {

        //     $ltl_curl_obj = new Speed_WWE_LTL_Curl_Request();
        //     $output = $ltl_curl_obj->wwe_ltl_get_curl_response(SPEED_WWE_FREIGHT_DOMAIN_HITTING_URL . '/addon/google-location.php', $post);
        //     return $output;
        // }


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
