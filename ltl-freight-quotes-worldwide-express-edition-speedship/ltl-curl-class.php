<?php

/**
 * WWE LTL Curl Class
 * 
 * @package     WWE LTL Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Curl Response Class
 */
class Speed_WWE_LTL_Curl_Request
{
    /**
     * Get Curl Response 
     * @param $url
     * @param $postData
     * @return json
     */
    function wwe_ltl_get_curl_response($url, $postData)
    {

        if (!empty($url) && !empty($postData)) {

            $totalWeight = 0;
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
            foreach ($postData['speed_freight_product_weight'] as $key => $value) {
                if ($value == 0) $value = 1;
                $request['request']['boxes'][$key]['weight'] = $value;
                $totalWeight += $value * $postData['speed_freight_post_quantity_array'][$key];
            }
            foreach ($postData['speed_freight_post_quantity_array'] as $key => $value) {
                $request['request']['boxes'][$key]['count'] = $value;
            }
            foreach ($postData['speed_freight_class'] as $key => $value) {
                $request['request']['boxes'][$key]['name'] = $key;
            }

            $request['request']['containers'] = [];
            foreach ($postData['pallets'] as $key => $value) {
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

            if (!empty($request['request']['boxes']) && !empty($request['request']['containers'])) {
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
                    error_log(print_r(['Packaging Request' => $json], true));
                    error_log(print_r(['Packaging Error' => $errormessage], true));
                } else {
                    //if more than 50 containers, rate ltl
                    $numberOfPackages = 0;
                    foreach ($packaging['response']['containers'] as $containers) {
                        $numberOfPackages++;
                    }
                    foreach ($packaging['response']['boxesWontFitList'] as $containers) {
                        $numberOfPackages++;
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
                error_log(print_r(['rating by weight (using default packaging)' => $packaging], true));
            }


            //build request
            $ltl = $this->ltl($packaging, $postData);
            if (!$ltl) {
                return false;
            }
            $xmlResponseLTL = $this->doQuoteRequest($ltl, $oauthtoken, $postData);

            if (!$xmlResponseLTL) {
                return false;
            }
            $getLowestPrice = 0;
            $rates = $this->getLTLRates($xmlResponseLTL, $postData);
            $i = 1;
            foreach ($request['request']['boxes'] as $containers) {

                $l = $containers['length'];
                $w = $containers['width'];
                $h = $containers['height'];
                $weight = $containers['weight'];
                $class = $postData['speed_freight_class'][$containers['name']];

                $rates['requestedLineItems'][] = [
                    "lineItemLength" => $l,
                    "lineItemHeight" => $h,
                    "lineItemWidth" => $w,
                    "lineItemQuantity" => $w,
                    "lineItemWeight" => $weight,
                    "lineItemClass" => $class,
                    "lineItemDescription" => $i,
                    "lineItemNMFC" => "",
                ];
                $i++;
            }


            $rates['hazmatStatus'] = 'n';
            $rates['residentialStatus'] = 'n';
            $rates['liftGateStatus'] = 'l';
            $rates['notifyBeforeDeliveryStatus'] = 'n';

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

    function ltl($packaging, $postData)
    {

        //Begin building rates
        $originA = explode(':', $postData['sender_origin']);
        $origin  = explode(' ', trim($originA[1]));
        $destination = [$postData['freight_reciver_city'], $postData['freight_receiver_state'], $postData['freight_receiver_zip_code']];

        $opostal_code = isset($origin[2]) ? $this->test_input($origin[2]) : '';
        $oprovince = isset($origin[1]) ? $this->test_input($origin[1]) : '';
        $ocity = isset($origin[0]) ? $this->test_input($origin[0]) : '';

        $dpostal_code = isset($destination[2]) ? $this->test_input($destination[2]) : '';
        $dprovince = isset($destination[1]) ? $this->test_input($destination[1]) : '';
        $dcity = isset($destination[0]) ? $this->test_input($destination[0]) : '';
        

        if($dpostal_code == "") return false;

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


        $residentialdelivery = 0;
        if ($postData["speed_freight_residential_delivery"] == 'Y') $residentialdelivery = 1;

        $liftgatedelivery = 0;
        if ($postData["speed_freight_lift_gate_delivery"] == 'Y') $liftgatedelivery = 1;

        $notifyBeforeDeliveryFlag = 0;
        if ($postData["speed_freight_notify_before_delivery"] == 'Y') $notifyBeforeDeliveryFlag = 1;

        //LTL rate request xml
        $json = [
            "correlationId" => $corrID,
            "request" => [
                "productType" => "LTL",
                "shipment" => [
                    "shipmentReferenceList" => [
                        [
                            "type" => "PO 1",
                            "value" => "65421002 1761",
                            "isPrintAsBarCode" => false
                        ]
                    ],
                    "originAddress" => [
                        "address" => [
                            "addressLineList" => [
                                "1571 Hunter Dr",
                                "2C"
                            ],
                            "locality"  => $ocity,
                            "region"  => $oprovince,
                            "postalCode"  => $opostal_code,
                            "countryCode" => "US",
                            "stopType" => "PICKUP",
                            "companyName" => "Keiths Kites",
                            "contactList" => [
                                [
                                    "firstName" => "",
                                    "lastName" => "Keith Kramer",
                                    "phone" => "12028528759",
                                    "email" => "keith@wwex.com",
                                    "fax" => null,
                                    "contactType" => "SENDER"
                                ]
                            ]
                        ],
                        "locationType" => "COMMERCIAL",
                        "readyTime" => "09:00:00",
                        "closeTime" => "14:00:00"
                    ],
                    "destinationAddress" => [
                        "address" => [
                            "addressLineList" => [
                                "6060 Village Bend Dr",
                                "600"
                            ],
                            "locality"  => $dcity,
                            "region"  => $dprovince,
                            "postalCode"  => $dpostal_code,
                            "countryCode" => "US",
                            "stopType" => "DROP",
                            "companyName" => "Kite Warehouse",
                            "contactList" => [
                                [
                                    "firstName" => "",
                                    "lastName" => "Julie Bowen",
                                    "phone" => "12028528825",
                                    "email" => "julie@wwex.com",
                                    "fax" => null,
                                    "contactType" => "RECEIVER"
                                ]
                            ]
                        ],
                        "locationType" => "COMMERCIAL"
                    ],
                    "shipmentDate" => $shipmentDate,
                    "constructionSitePickupFlag" => false,
                    "constructionSiteDeliveryFlag" => false,
                    "isInternationalShipment" => false,
                    "insideDeliveryFlag" => false,
                    "insidePickupFlag" => false,
                    "liftgatePickupFlag" => false,
                    "liftgateDeliveryFlag" => $liftgatedelivery,
                    "notifyBeforeDeliveryFlag" => $notifyBeforeDeliveryFlag,
                    "protectionFromColdFlag" => false,
                    "residentialDeliveryFlag" => $residentialdelivery,
                    "residentialPickupFlag" => false,
                    "protectionFromHeatFlag" => false,
                    "limitedAccessPickupFlag" => false,
                    "limitedAccessDeliveryFlag" => false,
                    "blindSenderFlag" => false,
                    "blindReceiverFlag" => false,
                    "sortAndSegregateFlag" => false,
                    "tradeshowPickupFlag" => false,
                    "tradeshowDeliveryFlag" => false,
                    "tradeshowPickupName" => "Aviation TradeShow",
                    "tradeshowDeliveryName" => "Aviation TradeShow",
                    "notifyBeforePickupFlag" => false,
                    "appointmentDeliveryFlag" => false,
                    "holdAtTerminalFlag" => false,
                    "isGuaranteed" => false,
                    "insuranceRequestFlag" => false,
                    "isSelfScheduled" => false,
                    "specialInstructions" => "codes are required",
                    "numberOfHandlingUnits" => 1
                ]
            ]
        ];

        foreach ($packaging['response']['containers'] as $containers) {
            $l = $containers['length'];
            $w = $containers['width'];
            $h = $containers['height'];
            $weight = (int)$containers['weight'];
            // $class = $containers['class'];
            $handlingUnitID = 'woocommerceHU-' . time();
            $json['request']['shipment']['handlingUnitList'][] =
                [
                    "billedDimension" => [
                        "length"  => [
                            "value"  => $l,
                            "unit"  => "in"
                        ],
                        "width"  => [
                            "value"  => $w,
                            "unit"  => "in"
                        ],
                        "height"  => [
                            "value"  => $h,
                            "unit"  => "in"
                        ],
                        "dimensionType" => "NET"
                    ],
                    "handlingUnitId" => $handlingUnitID,
                    "isCOD" => false,
                    "isMixedClass" => false,
                    "isStackable" => true,
                    "marksAndNumbers" => null,
                    "packagingType" => "BOX",
                    "quantity" => 1,
                    "referenceList" => null,
                    "shippedItemList" => [
                        [
                            "commodityClass" => "60",
                            "commodityDescription" => "bricks",
                            "commodityType" => "BOX",
                            "dimensions" => [
                                "length" => [
                                    "value" => "45",
                                    "unit" => "in"
                                ],
                                "width" => [
                                    "value" => "45",
                                    "unit" => "in"
                                ],
                                "height" => [
                                    "value" => "45",
                                    "unit" => "in"
                                ],
                                "dimensionType" => "NET"
                            ],
                            "quantity" => "1",
                            "weight" => [
                                "value" => $weight,
                                "unit" => "LB"
                            ]
                        ]
                    ],
                    "sortAndSegregateFlag" => false,
                    "weight" => [
                        "value" => $weight,
                        "unit" => "LB"
                    ]
                ];
        }
        if (isset($packaging['response']['boxesWontFitList'])) {
            foreach ($packaging['response']['boxesWontFitList'] as $containers) {
                $l = $containers['length'];
                $w = $containers['width'];
                $h = $containers['height'];
                $count = $containers['count'];
                $weight = $containers['weight'];
                $handlingUnitID = 'shopifyHU-' . time();
                $json['request']['shipment']['handlingUnitList'][] =
                    [
                        "billedDimension" => [
                            "length"  => [
                                "value"  => $l,
                                "unit"  => "in"
                            ],
                            "width"  => [
                                "value"  => $w,
                                "unit"  => "in"
                            ],
                            "height"  => [
                                "value"  => $h,
                                "unit"  => "in"
                            ],
                            "dimensionType" => "NET"
                        ],
                        "handlingUnitId" => "WWEX-UI-043ac787-50ee-4b92-93f0-47ceba349b80",
                        "isCOD" => false,
                        "isMixedClass" => false,
                        "isStackable" => true,
                        "marksAndNumbers" => null,
                        "packagingType" => "BOX",
                        "quantity" => 1,
                        "referenceList" => null,
                        "shippedItemList" => [
                            [
                                "commodityClass" => "60",
                                "commodityDescription" => "bricks",
                                "commodityType" => "BOX",
                                "dimensions" => [
                                    "length" => [
                                        "value" => "45",
                                        "unit" => "in"
                                    ],
                                    "width" => [
                                        "value" => "45",
                                        "unit" => "in"
                                    ],
                                    "height" => [
                                        "value" => "45",
                                        "unit" => "in"
                                    ],
                                    "dimensionType" => "NET"
                                ],
                                "quantity" => "1",
                                "weight" => [
                                    "value" => $weight,
                                    "unit" => "LB"
                                ]
                            ]
                        ],
                        "sortAndSegregateFlag" => false,
                        "weight" => [
                            "value" => $weight,
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
        global $debugData;
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
        $debugData["Google Distance Matrix Response"] = $distanceMatrix;

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

    function getLTLRates($xmlResponseLTL, $postData)
    {


        $quotes = json_decode($xmlResponseLTL, true);

        $quotes = $quotes['response'];
        $quoteListSize = is_array($quotes['offerList']) ? count($quotes['offerList']) : 0;

        //create Shopify Rate Response
        $totalSum = 0;
        $q = ['q' => []];
        for ($i = 0; $i < $quoteListSize; $i++) {

            $rateEstimateId = (string)$quotes['offerList'][$i]['offeredProductList'][0]['offeredProductId'];

            // $carrierSCAC = (string)$quotes['offerList'][$i]['offeredProductList'][0]['shopRQShipment']['timeInTransit']['scac'];
            $carrierSCAC = (string)$quotes['offerList'][$i]['primaryVendor']['vendorId'];

            if (isset($quotes['offerList'][$i]['primaryVendor']['preferredName'])) {
                $carrierName = (string)$quotes['offerList'][$i]['primaryVendor']['preferredName'];
            } else {
                $carrierName = (string)$quotes['offerList'][$i]['primaryVendor']['vendorId'];
            }

            $totalPrice = floatval(preg_replace("/[^-0-9\.]/", "", $quotes['offerList'][$i]['totalOfferPrice']['value']));

            $transitDays = (string)$quotes['offerList'][$i]['offeredProductList'][0]['shopRQShipment']['timeInTransit']['transitDays'];


            if ($quotes['offerList'][$i]['offeredProductList'][0]['shopRQShipment']['isGuaranteed']) {
                $guaranteedService = 'Y';
            } else {
                $guaranteedService = 'N';
            }

            $deliveryTimestamp = (string)$quotes['offerList'][$i]['offeredProductList'][0]['shopRQShipment']['timeInTransit']['estimatedDeliveryDate'];
            // $deliveryTimestamp = date('Y-m-d H:i:s O', strtotime((string)$quotes['offerList'][$i]['offeredProductList'][0]['shopRQShipment']['timeInTransit']['estimatedDeliveryDate']));

            $q['q']['quoteSpeedFreightShipmentReturn']['freightShipmentQuoteResults']['freightShipmentQuoteResult'][] = [
                "shipmentQuoteId" => $rateEstimateId,
                "carrierSCAC" => $carrierSCAC,
                "carrierName" => $carrierName,
                "totalPrice" => $totalPrice,
                "transitDays" => $transitDays,
                "guaranteedService" => $guaranteedService,
                "highCostDeliveryShipment" => "N",
                "interline" => "N",
                "nmfcRequired" => "N",
                "carrierNotifications" => ["freightShipmentCarrierNotification" => []],
                "deliveryTimestamp" => $deliveryTimestamp,
                "totalTransitTimeInDays" => $transitDays
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
