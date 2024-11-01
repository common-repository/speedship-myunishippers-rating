<?php

/**
 * Class Wwe_Feight_Curl_Request
 *
 * @package     Wwe Freight Quotes
 * @subpackage  Curl Call
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit; // exit if direct access
}

/**
 * Class to call curl request
 */
class Speed_Small_Package_Request
{

    /**
     * Get Curl Response 
     * @param  $url curl hitting URL
     * @param  $postData post data to get response
     * @return json
     */
    function small_package_get_curl_response($url, $postData)
    {
        if (!empty($url) && !empty($postData)) {
            // error_log(print_r($postData, true));
            //exit;
            $field_string = http_build_query($postData);

            //           Eniture debug mood
            do_action("eniture_debug_mood", "Build Query (s)", $field_string);

            $response = wp_remote_post(
                $url,
                array(
                    'method' => 'POST',
                    'timeout' => 60,
                    'redirection' => 5,
                    'blocking' => true,
                    'body' => $field_string,
                )
            );

            $output = wp_remote_retrieve_body($response);

            return $output;
        }
    }

    function small_package_get_curl_response1($url, $postData)
    {
        if (!empty($url) && !empty($postData)) {
            // error_log(print_r(['Packaging Request' => $postData], true));
            $totalWeight = 0;
            //grab boxes and put into containers
            $request['request']['boxes'] = [];
            foreach ($postData['product_width_array'] as $key => $value) {
                if ($value == 0) $value = 1;
                $request['request']['boxes'][$key]['width']  = $value;
            }
            foreach ($postData['product_height_array'] as $key => $value) {
                if ($value == 0) $value = 1;
                $request['request']['boxes'][$key]['height'] = $value;
            }
            foreach ($postData['product_length_array'] as $key => $value) {
                if ($value == 0) $value = 1;
                $request['request']['boxes'][$key]['length'] = $value;
            }
            foreach ($postData['speed_ship_product_weight'] as $key => $value) {
                if ($value == 0) $value = 1;
                $request['request']['boxes'][$key]['weight'] = $value;
                $totalWeight += $value * $postData['speed_ship_quantity_array'][$key];
            }
            foreach ($postData['speed_ship_quantity_array'] as $key => $value) {
                $request['request']['boxes'][$key]['count'] = $value;
            }

            $request['request']['containers'] = [];
            foreach ($postData['bins'] as $key => $value) {
                $request['request']['containers'][] = [
                    "name" => $value['nickname'],
                    "length" => $value['d'],
                    "width" => $value['w'],
                    "height" => $value['h'],
                    "weight" => $value['max_wg'],
                    "count" => 100
                ];
            }

            $oauthtoken = $this->getOauthToken($postData);




            //packaging algorithm
            $ratebyweight = false;

            if (isset($request['request']['boxes']) && isset($request['request']['containers'])) {
                $json = json_encode($request);

                $response = wp_remote_post(
                    'https://speedship.uat-wwex.com/svc/containerPackingFlow',
                    array(
                        'method' => 'POST',
                        'timeout' => 60,
                        'redirection' => 5,
                        'blocking' => true,
                        'headers' => array(
                            'Authorization' => $oauthtoken,
                            'Content-Type' => 'application/json',
                        ),
                        'body' => $json,
                    )
                );
                $jsonResponse = wp_remote_retrieve_body($response);


                $packaging = json_decode($jsonResponse, true);
                if (!isset($packaging['response'])) {
                    $ratebyweight = true;
                    $errormessage = $packaging['clientStatus']['message'];
                    // error_log(print_r(['Packaging Request' => $json], true));
                    // error_log(print_r(['Packaging Error' => $errormessage], true));
                } else {
                    //if more than 50 containers, rate ltl
                    $numberOfPackages = 0;
                    if (isset($packaging['response']['containers'])) {
                        foreach ($packaging['response']['containers'] as $containers) {
                            $numberOfPackages++;
                        }
                    }
                    if (isset($packaging['response']['boxesWontFitList'])) {
                        foreach ($packaging['response']['boxesWontFitList'] as $containers) {
                            $numberOfPackages++;
                        }
                    }

                    if ($numberOfPackages > 50) $allSP = 0;
                }
            } else {
                $ratebyweight = true;
            }

            if ($ratebyweight) {
                $jsonResponse = '{
                    "apiVersion": "1",
                    "clientStatus": {
                        "apiVersion": "1",
                        "success": true,
                        "message": ""
                    },
                    "correlationId": "NG-1a5fa890-3c56-4a07-99cc-c922cd8b6c0a",
                    "response": {
                        "apiVersion": "1",
                        "containers": [
                            {
                                "boxes": [
                                    {
                                        "apiVersion": "1",
                                        "name": "Item 1",
                                        "length": 4,
                                        "width": 4,
                                        "weight": ' . $totalWeight . ',
                                        "height": 1
                                    }
                                ],
                                "apiVersion": "1",
                                "name": "Small Container",
                                "length": 5,
                                "width": 5,
                                "weight": ' . $totalWeight . ',
                                "height": 2
                            }
                        ]
                    }
                }';
                $packaging = json_decode($jsonResponse, true);
                // error_log(print_r(['rating by weight (using default packaging)' => $packaging], true));
            }


            //Begin building rates
            $originA = explode(':', $postData['sender_origin']);
            $origin  = explode(' ', trim($originA[1]));



            $destination = [$postData['speed_ship_reciver_city'], $postData['speed_ship_receiver_state'], $postData['speed_ship_receiver_zip_code']];
            // $residentialdelivery = $postData['residentials_delivery'];
            $residentialdelivery = 1;
            if (get_option('wc_settings_wwex_quest_as_residential_delivery_wwe_small_packages') == 'quoteAsResidential') {
                $residentialdelivery = 1;
            } else if (get_option('wc_settings_wwex_quest_as_residential_delivery_wwe_small_packages') == 'autoDetectResidential') {
                //TODO add residential detection
            }
            $liftgatedelivery = 0;

            $sp = $this->sp($origin, $destination, $packaging, $residentialdelivery, $liftgatedelivery, $postData);
            $xmlResponseSP = $this->doQuoteRequest($sp, $oauthtoken, $postData);
            if (!$xmlResponseSP) {
                return false;
            }
            $getLowestPrice = 0;
            $rates = $this->getSPRates($xmlResponseSP, $postData);
            $i = 1;
            foreach ($packaging['response']['containers'] as $containers) {
                //     var_dump($containers);
                $l = $containers['length'];
                $w = $containers['width'];
                $h = $containers['height'];
                //         $weight = $containers['weight'];

                //loop through boxes and get sum weight of container
                $weight = 0;
                foreach ($containers['boxes'] as $boxes) {

                    $weight += $boxes['weight'];
                }
                $rates['requestedLineItems'][] = [
                    "length" => $l,
                    "height" => $h,
                    "width" => $w,
                    "weight" => $weight,
                    "packageType" => "00",
                    "packageNumber" => $i,
                    "largePackage" => 0,
                    "insuranceValue" => 0,
                    "additonalHandling" => "N"
                ];
                $i++;
            }


            $rates['residentialStatus'] = 'r';

            // error_log(print_r($rates, true));
            // exit;



            return json_encode($rates);
        }
    }

    function getOauthToken($postData)
    {
        $oauthtoken = get_option('oauthAccessToken');
        $expiresIn = get_option('tokenExpiresIn', time());
        $time = time();

        //if oauth expired - get new token
        if ($expiresIn <= $time) {

            $field_string = http_build_query(array(
                "grant_type" => "client_credentials",
                "client_id" => $postData['oauth_clientid'],
                "client_secret" => $postData['oauth_client_secret'],
                "audience" => "uat-wwex-apig"
            ));

            $response = wp_remote_post(
                "https://auth.uat-wwex.com/oauth/token",
                array(
                    'method' => 'POST',
                    'timeout' => 60,
                    'redirection' => 5,
                    'blocking' => true,
                    'headers' => array(
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ),
                    'body' => $field_string,
                )
            );

            $jsonResponse = wp_remote_retrieve_body($response);
            $jsonResponse = json_decode($jsonResponse, true);
            $oauthtoken = 'Bearer ' . $jsonResponse['access_token'];
            update_option('oauthAccessToken', $oauthtoken);
            //set expire time
            $expiresIn = time() + $jsonResponse['expires_in'];
            update_option('tokenExpiresIn', $expiresIn);
        }
        return $oauthtoken;
    }

    function sp($origin, $destination, $packaging, $residentialdelivery, $liftgatedelivery, $postData)
    {

        $opostal_code = isset($origin[2]) ? $this->test_input($origin[2]) : '';
        $oprovince = isset($origin[1]) ? $this->test_input($origin[1]) : '';
        $ocity = isset($origin[0]) ? $this->test_input($origin[0]) : '';

        $dpostal_code = isset($destination[2]) ? $this->test_input($destination[2]) : '';
        $dprovince = isset($destination[1]) ? $this->test_input($destination[1]) : '';
        $dcity = isset($destination[0]) ? $this->test_input($destination[0]) : '';

        //set time for shipment
        $corrID = 'woocommerce-' . time();
        if ($postData['modifyShipmentDateTime'] == 1) {
            $shipmentDate = date("Y-m-d H:i:s");
            $dayHour = explode(' ', $shipmentDate);
            $cutoff = $dayHour[0] . ' ' . $postData['OrderCutoffTime'];
            $cutoffTime = date("Y-m-d H:i:s", strtotime($cutoff));
            $orderTime = date("Y-m-d H:i:s", strtotime($postData['storeDateTime']));
            if ($orderTime > $cutoffTime) {
                //quote the next day
                $shipmentDate = date("Y-m-d 09:00:00", strtotime($dayHour[0] . ' +1 days'));
            }
            if (is_numeric($postData['shipmentOffsetDays'])) $shipmentDate = date("Y-m-d H:i:s", strtotime($shipmentDate . ' +' . $postData['shipmentOffsetDays'] . ' days'));
        } else {
            $shipmentDate = date("Y-m-d H:i:s");
        }


        //SP rate request xml
        $json = [
            "correlationId" => $corrID,
            "request" => [
                "shopRQId" => "03ca4207-de1e-4cec-a71e-6daba367a4cd",
                "productType" => "SMALLPACK",
                "shipment" => [
                    "originAddress" => [
                        "address" => [
                            "addressLineList" => [
                                "13 Fisher Island Dr"
                            ],
                            "locality" => $ocity,
                            "region" => $oprovince,
                            "postalCode" => $opostal_code,
                            "countryCode" => "US",
                            "addressType" => "PICKUP",
                            "companyName" => "Sutherland Exports",
                            "contactList" => [
                                [
                                    "companyName" => "Keith Kites",
                                    "firstName" => "",
                                    "lastName" => "Keith Kramer",
                                    "phone" => "12028528739",
                                    "email" => "keith@wwex.com",
                                    "fax" => null,
                                    "contactType" => "SENDER"
                                ]
                            ]
                        ],
                        "readyTime" => "09:00:00",
                        "closeTime" => "18:00:00"
                    ],
                    "destinationAddress" => [
                        "address" => [
                            "addressLineList" => [
                                "655 W 34TH ST"
                            ],
                            "locality" => $dcity,
                            "region" => $dprovince,
                            "postalCode" => $dpostal_code,
                            "countryCode" => "US",
                            "companyName" => "Jameson Manufacturers",
                            "addressType" => "Delivery",
                            "contactList" => [
                                [
                                    "companyName" => "Kite Warehouse",
                                    "firstName" => "",
                                    "lastName" => "Julie Bowen",
                                    "phone" => "12013897010",
                                    "email" => "julie@wwex.com",
                                    "fax" => null,
                                    "contactType" => "RECEIVER"
                                ]
                            ]
                        ],
                        "locationType" => "COMMERCIAL"
                    ],
                    "shipmentDate" => $shipmentDate,
                    "numberOfHandlingUnits" => 1,
                    "isCOD" => false,
                    "isInternationalShipment" => null,
                    "deliveryConfirmationFlag" => false,
                    "verbalDeliveryConfirmationFlag" => false,
                    "isCarbonNeutral" => false,
                    "isSignatureRequired" => false,
                    "adultSignatureRequiredFlag" => false,
                    "shipperReleaseFlag" => false,
                    "directDeliveryOnlyFlag" => true,
                    "insuranceRequestFlag" => false,
                    "residentialDeliveryFlag" => $residentialdelivery,
                    "residentialPickupFlag" => false,
                    "returnLabelFlag" => false,
                    "returnServiceType" => null,
                    "isSelfScheduled" => false
                ]
            ]
        ];
        if (isset($packaging['response']['containers'])) {
            foreach ($packaging['response']['containers'] as $containers) {

                $l = $containers['length'];
                $w = $containers['width'];
                $h = $containers['height'];
                $count = 1;
                $handlingUnitID = 'woocommerceHU-' . time();

                //loop through boxes and get sum weight of container
                $weight = 0;
                foreach ($containers['boxes'] as $boxes) {
                    $weight += $boxes['weight'];
                }


                $json['request']['shipment']['handlingUnitList'][] =
                    [
                        "billedDimension" => [
                            "length" => [
                                "value" => $l,
                                "unit" => "in"
                            ],
                            "width" => [
                                "value" => $w,
                                "unit" => "in"
                            ],
                            "height" => [
                                "value" => $h,
                                "unit" => "in"
                            ],
                            "dimensionType" => "BILLED"
                        ],
                        "description" => "TBD",
                        "handlingUnitId" => "$handlingUnitID",
                        "packagingType" => "02",
                        "packagingTypeName" => "Custom Package",
                        "quantity" => "1",
                        "isStackable" => false,
                        "referenceList" => [
                            [
                                "type" => "PO",
                                "value" => "25840214",
                                "isPrintAsBarCode" => false
                            ]
                        ],
                        "shippedItemList" => [
                            ["insuredValue" => [
                                "value" => 0,
                                "unit" => "USD"
                            ]]
                        ],
                        "weight" => [
                            "value" => "$weight",
                            "unit" => "LB"
                        ]
                    ];
            }
        }
        if (isset($packaging['response']['boxesWontFitList'])) {
            foreach ($packaging['response']['boxesWontFitList'] as $containers) {
                $l = $containers['length'];
                $w = $containers['width'];
                $h = $containers['height'];
                $count = $containers['count'];
                $weight = $containers['weight'];
                $handlingUnitID = 'shopifyHU-' . time();
                $insurance = [];
                for ($i = 0; $i < $count; $i++) {
                    $insurance[] = ["insuredValue" => [
                        "value" => 0,
                        "unit" => "USD"
                    ]];
                }

                $json['request']['shipment']['handlingUnitList'][] =
                    [
                        "billedDimension" => [
                            "length" => [
                                "value" => $l,
                                "unit" => "in"
                            ],
                            "width" => [
                                "value" => $w,
                                "unit" => "in"
                            ],
                            "height" => [
                                "value" => $h,
                                "unit" => "in"
                            ],
                            "dimensionType" => "BILLED"
                        ],
                        "description" => "TBD",
                        "handlingUnitId" => "$handlingUnitID",
                        "packagingType" => "02",
                        "packagingTypeName" => "Custom Package",
                        "quantity" => $count,
                        "isStackable" => false,
                        "referenceList" => [
                            [
                                "type" => "PO",
                                "value" => "25840214",
                                "isPrintAsBarCode" => false
                            ]
                        ],
                        "shippedItemList" => $insurance,
                        "weight" => [
                            "value" => "$weight",
                            "unit" => "LB"
                        ]
                    ];
            }
        }
        return json_encode($json);
    }


    function errorCheck($xmlResponse, $json, $postData)
    {

        if ($xmlResponse == '') {
            error_log(print_r(['Error' => 'Empty response', 'request' => $json], true));
            return false;
        }

        //invalid authentication, please update credentials
        if (trim($xmlResponse) == '{"message":"Unauthorized"}') {
            error_log(print_r(['Oauth Error' => 'Unauthorized, Please update oauth info', 'request' => $postData], true));
            return false;
        }

        $quotes = json_decode($xmlResponse, true);

        if (!isset($quotes['clientStatus'])) {
            error_log(print_r(['Error' => 'Invalid response', 'request' => $json, 'response' => $xmlResponse], true));
            return false;
        }

        $clientStatus = $quotes['clientStatus'];

        $quotes = $quotes['response'];

        //did not return any results
        if (isset($quotes['message']) && $quotes['message'] == 'No Offers created.') {
            error_log(print_r(['Error' => 'No Offers created', 'request' => $json, 'response' => $xmlResponse], true));
            return false;
        }

        if (isset($quotes['message']) && $clientStatus['success'] != 'true') {
            error_log(print_r(['Error' => 'Error returned', 'request' => $json, 'response' => $xmlResponse], true));
            return false;
        }

        if (!isset($quotes['offerList'])) {
            error_log(print_r(['Error' => 'Error offerList empty', 'request' => $json, 'response' => $xmlResponse], true));
            return false;
        }
        return true;
    }

    function getDistance($origin, $destination, $shop, $pdo, $googlekey)
    {

        extract($origin);

        $opostal_code = isset($postal_code) ? $this->test_input($postal_code) : '';
        $oprovince = isset($province) ? $this->test_input($province) : '';
        $ocity = isset($city) ? $this->test_input($city) : '';

        if ($googlekey == "") return [$ocity, $oprovince, $opostal_code];

        //get warehouse postalcodes
        //find closest warehouse to destination address
        $sql = "select * from shopify_warehouse where shop = :shopid";
        $sth = $pdo->prepare($sql);
        $sth->bindParam('shopid', $shop);
        $sth->execute();
        $hasWarehouses = 0;

        //first index is store location
        $warehouses = [[$ocity, $oprovince, $opostal_code]];
        $start = $opostal_code;
        while ($row = $sth->fetch()) {
            $hasWarehouses = 1;
            $start .= "|" . $row['zip'];
            $warehouses[] = [$row['city'], $row['wstate'], $row['zip']];
        }
        if (!$hasWarehouses) return false;

        $url = "https://maps.googleapis.com/maps/api/distancematrix/json";
        $getparameter = [
            "origins" => $start,
            "destinations" => $destination,
            "key" => $googlekey
        ];
        $response = $this->getapicurlconnect($url, $getparameter);
        $distanceMatrix = json_decode($response, true);

        //loop through result and find key of lowest distance
        $lowestDistance = 0;
        $lowestIndex = 0;
        for ($i = 0; $i < count($distanceMatrix['rows']); $i++) {

            if ($i == 0) $lowest = $distanceMatrix['rows'][$i]['elements'][0]['distance']['value'];
            if ($distanceMatrix['rows'][$i]['elements'][0]['distance']['value'] <  $lowest) {
                $lowest = $distanceMatrix['rows'][$i]['elements'][0]['distance']['value'];
                $lowestIndex = $i;
            }
        }

        //use index to select warehouse
        return $warehouses[$lowestIndex];
    }
    function getapicurlconnect($apiurl, $getparameter)
    {
        $params = http_build_query($getparameter);
        $result = wp_remote_get($apiurl . "?" . $params);
        $body = wp_remote_retrieve_body($result);
        return $body;
    }
    function doQuoteRequest($json, $oauthtoken, $postData)
    {

        $response = wp_remote_post(
            'https://speedship.UAT-wwex.com/svc/shopFlow',
            array(
                'method' => 'POST',
                'timeout' => 60,
                'redirection' => 5,
                'blocking' => true,
                'headers' => array(
                    'Authorization' => $oauthtoken,
                    'Content-Type' => 'application/json',
                ),
                'body' => $json,
            )
        );
        $xmlResponse = wp_remote_retrieve_body($response);

        if (!$this->errorCheck($xmlResponse, $json, $postData)) {
            return false;
        }

        return $xmlResponse;
    }

    function getSPRates($xmlResponseSP, $postData)
    {

        $quotes = json_decode($xmlResponseSP, true);
        $quotes = $quotes['response'];
        $quoteListSize = count($quotes['offerList']);

        //create Shopify Rate Response
        $totalSum = 0;
        $q = ['q' => []];
        for ($i = 0; $i < $quoteListSize; $i++) {

            $serviceCode = (string)$quotes['offerList'][$i]['offeredProductList'][0]['shopRQShipment']['timeInTransit']['upsServiceCode'];
            $serviceDescription = (string)$quotes['offerList'][$i]['offeredProductList'][0]['shopRQShipment']['timeInTransit']['serviceDescription'];

            $day = (string)$quotes['offerList'][$i]['offeredProductList'][0]['shopRQShipment']['timeInTransit']['estimatedDeliveryDate'];
            $time = (string)$quotes['offerList'][$i]['offeredProductList'][0]['shopRQShipment']['timeInTransit']['deliveryBy'];
            $estimateDelivery = date('h:s A l m/d/y', strtotime($day . ' ' . $time));
            $deliveryDate = date('Y-m-d', strtotime($day));

            $day = (string)$quotes['offerList'][$i]['offeredProductList'][0]['shopRQShipment']['timeInTransit']['latestPickupDate'];
            $time = (string)$quotes['offerList'][$i]['offeredProductList'][0]['shopRQShipment']['timeInTransit']['latestPickupTime'];
            $pickupBy = date('h:s A l m/d/y', strtotime($day . ' ' . $time));


            $rateEstimateId = (string)$quotes['offerList'][$i]['offeredProductList'][0]['offeredProductId'];

            $transitTimeInDays = (string)$quotes['offerList'][$i]['offeredProductList'][0]['shopRQShipment']['timeInTransit']['transitDays'];

            $total = floatval(preg_replace("/[^-0-9\.]/", "", $quotes['offerList'][$i]['totalOfferPrice']['value']));

            if (isset($creds['customservicename']) && $creds['customservicename'] != "") {
                $servicename = $creds['customservicename'];
                if (isset($creds['customdescription']) && $creds['customdescription'] != "") $service = $creds['customdescription'];
            }
            // if ($addDrop != -1) $total  += $addDrop;
            $serviceFeeDetail = ['packageLevelFees' => [], 'serviceFeeGrandTotal' => $total, 'shipmentLevelFee' => []];
            $q['q'][] = [
                'estimateDelivery' => $estimateDelivery,
                'pickupBy' => $pickupBy,
                'rateEstimateId' => $rateEstimateId,
                'serviceCode' => $serviceCode,
                'serviceDescription' => $serviceDescription,
                'serviceFeeDetail' => $serviceFeeDetail,
                'CalenderDaysInTransit' => $transitTimeInDays,
                'TransitTimeInDays' => $transitTimeInDays,
                'DeliveryDate' => $deliveryDate
            ];
        }



        return $q;
    }





    function checkDimViolation($dimViolation, $l, $w, $h)
    {

        //if any side is bigger than 108 inches, don't quote as SP
        if ($l > 108 || $w > 108 || $h > 108) $dimViolation = 1;

        //if girth is bigger than 165, don't quote as SP
        if (($l + (($w * 2) + ($h * 2))) > 165) $dimViolation = 1;

        return $dimViolation;
    }

    function test_input($data)
    {
        $data = trim($data);
        $data = rtrim($data, ',');
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}
