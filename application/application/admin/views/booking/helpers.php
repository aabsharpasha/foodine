<?php

/**
 * Created by PhpStorm.
 * Project: hungermafia
 * User: Amit Malakar
 * Date: 1/10/15
 * Time: 3:52 PM
 */
function getMetaDetails($pagename = '', $restaurantId = '') {
    $seoData = array();
    if (!empty($pagename)) {
        $data['page'] = $pagename;
        $data['restaurantId'] = $restaurantId;
        $response = HM::send(Config::get('hm.HTTP.POST'), 'seo/getMetaTags', $data);
        $body = $response->json();
        $status = $body['STATUS'];
        if ($status == 200) {
            $seoData = $body['DATA'];
        }
    }
    return $seoData;
}

/**
 * Action to get time slots
 * @package Restaurant Controller
 * @access Public
 * @author 3031@Foodine :
 * @return array
 */
function getTimeSlots() {
    $slotData = array();
    $key = 'timeSlots';
    if (Cache::has($key)) {
        $slotData = Cache::get($key);
    } else {
        $response = HM::send(Config::get('hm.HTTP.POST'), 'restaurant/timeslots');
        if ($response) {
            $data = json_decode($response->getBody());
            if ($data) {
                foreach ($data as $slots) {
                    $slotData[$slots->iSlotId] = date("h:i A", strtotime($slots->tstartFrom));
                }
            }
        }
        Cache::forever($key, $slotData);
    }

    return $slotData;
}

/**
 * Calculate distance b/w two lat lng
 * @param $lat1
 * @param $lng1
 * @param $lat2
 * @param $lng2
 * @param string $unit
 * @return float
 */
function getLatLngDistance($lat1, $lng1, $lat2, $lng2, $unit = "K") {

    $theta = $lng1 - $lng2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
        return ($miles * 1.609344);
    } else if ($unit == "N") {
        return ($miles * 0.8684);
    } else {
        return $miles;
    }
}

/**
 * Distance b/w two lat lng
 * @param $lat1
 * @param $lng1
 * @param $lat2
 * @param $lng2
 * @return array
 */
function midpoint($lat1, $lng1, $lat2, $lng2) {

    $lat1 = deg2rad($lat1);
    $lng1 = deg2rad($lng1);
    $lat2 = deg2rad($lat2);
    $lng2 = deg2rad($lng2);

    $dlng = $lng2 - $lng1;
    $Bx = cos($lat2) * cos($dlng);
    $By = cos($lat2) * sin($dlng);
    $lat3 = atan2(sin($lat1) + sin($lat2), sqrt((cos($lat1) + $Bx) * (cos($lat1) + $Bx) + $By * $By));
    $lng3 = $lng1 + atan2($By, (cos($lat1) + $Bx));
    $pi = pi();
    return [
        geoLocationFormat(($lat3 * 180) / $pi),
        geoLocationFormat(($lng3 * 180) / $pi)
    ];
}

/**
 * Format geo lat lng to 6 digit decimal
 * @param $location
 * @return float
 */
function geoLocationFormat($location) {
    return number_format($location, 6);
}

/**
 * Rating format
 * @param $rating
 * @return string
 */
function ratingFormat($rating) {
    return (is_null($rating) || $rating == 0) ? 0 : number_format($rating, 1);
}

function formatAddress($address, $state) {
    if ($state != '') {
        $str = ', ' . $state;
        $strArr = list($firstWord) = explode($str, $address);
        return $strArr[0];
    } else {
        return $address;
    }

    /**
     * Action to get time slots
     * @package Restaurant Controller
     * @access Public
     * @author 3031@Foodine :
     * @return array
     */
    function getRestaurantAlias() {
        $aliasArr = array();
        $key = 'restaurant-alias';
        if (Cache::has($key)) {
            $aliasArr = Cache::get($key);
        } else {
            $responseAlias = HM::send(Config::get('hm.HTTP.POST'), 'restaurant/getRestaurantAlias');
            $bodyAlias = $responseAlias->json();
            $statusAlias = $bodyAlias['STATUS'];
            if ($statusAlias == 200) {
                $aliasArr = $bodyAlias['ALIAS'];
            }
            Cache::forever($key, $aliasArr);
        }

        return $aliasArr;
    }

}
