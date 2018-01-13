<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of link_model
 * link hashes expiry 24 hrs
 * @author Amit Malakar
 */
class Offer_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_deals';

        date_default_timezone_set('Asia/Calcutta');
        $this->currHour = time();
        $this->glob = unserialize(RESTAURANT_OPEN_CLOSE_DAYS);
    }

    function getOfferDetail($postValue = null) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
            }
            if ($restId != '') {


                $fields = array(
                    'td.vOfferText AS offerText',
                    'td.tOfferDetail AS offerDetail',
                    'td.vDealCode AS offerCode',
                    'td.vDaysAllow AS daysAllow',
                    'td.tTermsOfUse AS termsOfUse',
                    'DATE_FORMAT(td.dtExpiryDate,"' . '%d-%m-%Y' . '") AS expiryDate'
                );

                $tbl = array(
                    'tbl_deals AS td',
                );

                $condition[] = 'td.eStatus = \'Active\'';
                $condition[] = 'CURDATE() between td.dtStartDate and td.dtExpiryDate';
                $condition[] = 'td.iRestaurantID = \'' . $restId . '\'';
                if (isset($dealId) && $dealId != '') {
                    $condition[] = 'td.iDealID = \'' . $dealId . '\'';
                }
                $tbl = implode(',', $tbl);
                $fields = implode(',', $fields);
                $condition = implode(' AND ', $condition);

                $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition;
                $res = $this->db->query($qry);
                $row = $res->result_array();
                $days = array(
                    '1' => 'Sun',
                    '2' => 'Mon',
                    '3' => 'Tue',
                    '4' => 'Wed',
                    '5' => 'Thu',
                    '6' => 'Fri',
                    '7' => 'Sat'
                );
                foreach ($row as $i => $value) {
                    $day = '';
                    $dayArray = $dayArr = explode(',', $value['daysAllow']);
                    asort($dayArr);

                    while ($dayKey = array_shift($dayArr)) {
                        if (count($dayArr)) {
                            $day .= $days[$dayKey] . ", ";
                        } else {
                            $day .= $days[$dayKey];
                        }
                    }
                    $row[$i]['daysAllowed'] = $day;
                }
                return $row;
            }return '';
        } catch (Exception $ex) {
            throw new Exception('Error in getOfferDetail function - ' . $ex);
        }
    }

    private function _getComboOffer($restaurantId) {
        $orderBy = '';
        $tbl = $fields = $join = $groupBy = '';
        $tbl .= '`tbl_combo_offers` AS `tco`';
        if (!empty($restaurantId)) {
            $condition[] = 'tco.iRestaurantID =' . $restaurantId;
        }

        $condition[] = 'tco.eStatus = "Active"';
        $condition[] = 'CURDATE() between tco.dtStartDate and tco.dtExpiryDate';
        $condition[] = 'tr.eStatus IN(\'Active\')';

        $fields .= 'tco.iComboOffersID, tco.iRestaurantID, tr.vRestaurantName as  restaurantName, tr.vCityName as city, tco.vOfferText, tcso.tActualPrice, tcso.tDiscountedPrice, tco.vDaysAllow, tco.dtStartDate, tco.dtExpiryDate';
        $fields .= ', CONCAT("' . BASEURL . 'images/combo/", IF(tco.vOfferImage =\'\', "default.png", CONCAT(`tco`.iRestaurantID,\'/\',`tco`.vOfferImage))) AS vOfferImage';
        $fields .= ', tc.vLocationName AS location';

        $fields .= ',"combo" AS offerType';
        $fields .= ',tco.iComboOffersID AS offerId';
        $fields .= ',tco.vOfferText AS offerText';
        $join .= ' INNER JOIN `tbl_combo_sub_offers` AS `tcso` ON `tcso`.`iComboOffersID` = `tco`.`iComboOffersID`';
        $join .= ' INNER JOIN `tbl_restaurant` as `tr` on  `tco`.`iRestaurantID` = `tr`.`iRestaurantID`';
        $join .= ' INNER JOIN `tbl_location` as `tc` on  `tc`.`iLocationID` = `tr`.`iLocationID`';
        $groupBy = ' GROUP BY tco.iComboOffersID ';
        $condition = (!empty($condition) ? ' WHERE ' . implode(' AND ', $condition) : '');

        $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . $join . $condition . $groupBy . $orderBy;

        $res = $this->db->query($qry);
        $row = $res->result_array();
        foreach ($row as $key => $value) {
            $perDiscount = round(((($row[$key]['tActualPrice'] - $row[$key]['tDiscountedPrice']) * 100) / $row[$key]['tActualPrice']), 0);
            $row[$key]['perDiscount'] = $perDiscount;
        }
        return $row;
    }

    function getAllOffer($postValue = null) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
            }

            $distanceType = 'km';
            $multiplyer = 6371 * 1000;
            $maxDistance = 160.934*1000;//offers in 100 miles
            if ($distanceType == 'miles') {
                $multiplyer = 3959;
            }

            $currenttime = date('Y-m-d H:i:s');

            $fields = $join = $groupBy = $orderBy = '';

            $tbl[] = 'tbl_restaurant AS tr';
            $tbl[] = 'tbl_deals AS td';
            $tbl[] = '`tbl_location` AS `tl`';
            $fields[] = 'tr.iRestaurantId AS restaurantId';
            $field[] = 'DISTINCT tr.iRestaurantId AS restaurantId';
            $fields[] = 'tr.vRestaurantName AS restaurantName';
            $fields[] = 'tr.vLat AS restaurantLatitude';
            $fields[] = 'tr.vLog AS restaurantLongitude';
            if (isset($userLat) && !empty($userLat) && isset($userLong) && !empty($userLong)) {
                $field[] = $fields[] = '(ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                        . ' * cos( radians( vLat) )'
                        . ' * cos( radians( vLog ) - radians( ' . $userLong . ' ) )'
                        . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( vLat ) ) ) ))'
                        . ' AS distance';
                $condition[] = '(ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                        . ' * cos( radians( vLat) )'
                        . ' * cos( radians( vLog ) - radians( ' . $userLong . ' ) )'
                        . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( vLat ) ) ) )) < '.$maxDistance;
            }
            
            if (isset($zone) && !empty($zone)){
                    if ($zone == 10) {
                        $zoneId = '1,2,3,4,5,6,7,8,9';
                    } else if ($zone == 9) {
                        $zoneId = '1,2,3,4,5,9';
                    } else {
                        $zoneId = $zone;
                    }
                    $condition[] = "tl.iLocZoneID IN($zoneId)";
             }
                
            $fields[] = 'IF(td.vDealImage =\'\',CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.png", CONCAT(tr.iRestaurantID,\'/thumb/\',tr.vRestaurantLogo)) ), CONCAT("' . BASEURL . 'images/deal/", IF(td.vDealImage =\'\', "default.png", CONCAT(`td`.iRestaurantID,\'/thumb/\',`td`.vDealImage)))) AS offerImage';
            $fields[] = 'CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.png", CONCAT(tr.iRestaurantID,\'/thumb/\',tr.vRestaurantLogo)) ) AS restaurantImage';
            $fields[] = 'td.iDealID AS offerId';
            if (isset($userId) && $userId !== '' && $userId > 0) {
                //$tbl[] = 'tbl_deals_code AS tdc';
                //$fields[] = 'IFNULL(tdc.vOfferCode, "") AS offerCode';
                $condition1[] = 'td.iDealID  = tdc.iDealID';
                $condition1[] = 'tdc.iUserId  = ' . $userId;
                $condition1[] = "tdc.eStatus  = 'availed'";
                $condition1[] = 'tdc.dtExpiryDate >=' . "'" . $currenttime . "'";
                $condition1[] = 'tdc.iTableBookID IS NULL';
                $conditionCode = implode(' AND ', $condition1);

                $fields[] = 'IFNULL((SELECT tdc.vOfferCode FROM tbl_deals_code AS tdc WHERE ' . $conditionCode . '), "") AS offerCode ';
            } else {
                $fields[] = '"" AS offerCode';
            }
            $fields[] = 'tl.vLocationName AS vLocationName';
            $fields[] = 'tr.vCityName AS vCityName';
            $fields[] = 'td.vOfferText AS offerText';
            $fields[] = 'td.tOfferDetail AS offerDetail';
            $fields[] = 'td.tTermsOfUse AS offerTerms';
            $fields[] = 'td.vDaysAllow AS daysAllow';
            $fields[] = 'td.vDealCode AS dealCode';
            $fields[] = 'DATE_FORMAT(td.dtStartDate,"' . MYSQL_DATE_FORMAT . '") AS offerStartDate';
            $fields[] = 'DATE_FORMAT(td.dtExpiryDate,"' . '%d-%m-%Y' . '") AS offerEndDate';
            'DATE_FORMAT(td.dtExpiryDate,"' . MYSQL_DATE_FORMAT . '") AS offerEndDate';
            $fields[] = 'DATE_FORMAT(td.dtExpiryDate,"%h:%i %p") AS exipryTime';

            $condition[] = 'td.eStatus = \'Active\'';
            $condition[] = 'td.iRestaurantID = tr.iRestaurantID';
            $condition[] = 'tl.iLocationID = tr.iLocationID';
            $condition[] = 'tl.eStatus IN(\'Active\')';
            $condition[] = 'tr.eStatus IN(\'Active\')';
            $condition[] = 'CURDATE() between td.dtStartDate and td.dtExpiryDate';
            
            $orderBy = ' ORDER BY td.dtExpiryDate ASC';
            if (isset($userLat) && !empty($userLat) && isset($userLong) && !empty($userLong)) {
                $orderBy = ' ORDER BY distance ASC';
            }

            //If user selecting location from filter
            if (isset($filterLocation) && $filterLocation !== '') {
                $condition[] = 'tr.iLocationID IN(' . $filterLocation . ')';
            }
            //If user selecting location from filter
            if (isset($filterOfferType) && $filterOfferType !== '') {
                $condition[] = 'td.iOfferType IN(' . $filterOfferType . ')';
            }

            if (!empty($restaurantId)) {
                $condition[] = 'td.iRestaurantID = \'' . $restaurantId . '\'';
            }

            if (isset($pageId)) {

                /* PAGINATION */
                $zeroPageRecord = 10;
                $otherPageRecord = 11;
                if ($pageId == 0) {
                    /* first page load records... */
                    $perPageValue = $zeroPageRecord;
                } else {
                    /* second to other page records... */
                    $perPageValue = $otherPageRecord;
                }
                $pageId = (int) $pageId;
                if ($pageId == 1) {
                    $pageId = $zeroPageRecord * $pageId;
                } else if ($pageId > 1) {
                    $pageId = (($zeroPageRecord) + ($perPageValue * ($pageId - 1)));
                }

                $limit = ' LIMIT ' . $pageId . ',' . $perPageValue;
            }


            $tbl = implode(',', $tbl);
            $fields = implode(',', $fields);
            $condition = implode(' AND ', $condition);


            $field = implode(',', $field);

            $qryR = 'SELECT ' . $field . ' FROM ' . $tbl . ' WHERE ' . $condition . $orderBy . $limit;

            $resR = $this->db->query($qryR);
            $rowR = $resR->result_array();
            $restId = array();
            foreach ($rowR as $dataRestId) {
                $restId[] = $dataRestId['restaurantId'];
            }
            $restId = implode(',',$restId);
            if (empty($restaurantId) && !empty($restId)) {
                $conditionR = ' AND td.iRestaurantID IN(' . $restId . ')';
                $limit = '';
            }
            if (isset($plateForm) && $plateForm == 'web') {
                $conditionR = '';
           } 
           
            $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition . $conditionR . $orderBy . $limit;
            
            $res = $this->db->query($qry);
            $row = $res->result_array();

            $countBy = 'td.iDealID';
            $qry1 = 'SELECT DISTINCT(' . $countBy . ') AS totalRows FROM ' . $tbl . ' WHERE ' . $condition . ' GROUP BY tr.iRestaurantId';
            $countRes = $this->db->query($qry1);
            $countRec = $countRes->result_array();
            $countRec = count($countRec);

            $totalPage = 0;
            if (isset($pageId)) {
                $totalPage = 1 + (floor($countRec / $otherPageRecord));
            }

            $rt = 0;
            $newArr = $restIds = array();
            foreach ($row as $k => $offerData) {
                $restAlias = strtolower(preg_replace("/\s+/", "-", trim($offerData['restaurantName'])));
                $offerData['restaurantAlias'] = str_replace("'", "", $restAlias) . '-' . $offerData['restaurantId'];
                $days = array();
                if ($offerData['daysAllow'] != '') {
                    $daysAllow = explode(',', $offerData['daysAllow']);
                    //mprd($openDays);

                    for ($i = 0; $i < count($daysAllow); $i++) {
                        $days[] = $this->glob[$daysAllow[$i]];
                    }
                }
                $offerData['daysAllowValue'] = $days;
                $offerData['totalRecord'] = $countRec;
                $offerData['totalPage'] = $totalPage;
                if (isset($plateForm) && $plateForm == 'web') {
                    $newArr[] = $offerData;
                } else {
                    if (!in_array($offerData['restaurantId'], $restIds)) {
                        $restIds[$rt] = $offerData['restaurantId'];

                        $newArr[$rt]['offer'][] = $offerData;
                        $rt++;
                    } else {
                        $key = array_search($offerData['restaurantId'], $restIds);
                        $newArr[$key]['offer'][] = $offerData;
                    }
                }
            }
            $row = $newArr;

            return $row;
            //}
            // return array();
        } catch (Exception $ex) {
            throw new Exception('Error in getRestaurantMenu function - ' . $ex);
        }
    }

    /*
     * Method to return all filters which can be applied on restaurant. 
     * 
     * @return array data with location listing
     * @author Anurag Srivastava (anurag.srivastava@kelltontech.com)
     */

    public function getAllOfferFilter($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
            }


            // Fetching offer type filter
            $offersql = 'select offerTypeId as id, offerTypeName as name from tbl_offer_type where status = "Active"';
            $offerres = $this->db->query($offersql);
            $allActiveOffers = $offerres->result_array();
            $resp['filters']['offerType'] = $allActiveOffers;

            // Fetching localities
            $tbl = $fields = $join = '';

            $tbl .= '`tbl_restaurant` AS `tr`';
            $tbl .= ' ,`tbl_location` AS `tl`';
            $tbl .= ', `tbl_deals` AS `td`';
            $condition[] = 'td.iRestaurantID = tr.iRestaurantID';
            $condition[] = 'tl.iLocationID = tr.iLocationID';
            $condition[] = 'td.eStatus = \'Active\'';
            $condition[] = 'tr.eStatus IN(\'Active\')';
            $condition[] = 'tl.eStatus IN(\'Active\')';
            $condition[] = 'CURDATE() between td.dtStartDate and td.dtExpiryDate';
            //City Based
//            if (isset($cityName) && $cityName != '') {
//                $condition[] = 'tr.vCityName LIKE "%' . $cityName . '%" ';
//            }
            //Location Based
//            if (isset($locName) && $locName != '') {
//                $condition[] = 'tl.vLocationName LIKE "%' . $locName . '%" ';
//            }
            if (isset($zone) && !empty($zone)){
                    if ($zone == 10) {
                        $zoneId = '1,2,3,4,5,6,7,8,9';
                    } else if ($zone == 9) {
                        $zoneId = '1,2,3,4,5,9';
                    } else {
                        $zoneId = $zone;
                    }
                    $condition[] = "tl.iLocZoneID IN($zoneId)";
             }
            $groubBy = ' GROUP BY tr.iLocationID';
            $fields .= ' `tr`.iLocationID as id, `tl`.`vLocationName` AS name';
            $fields .= ' ,`tr`.iRestaurantID as restId, `tr`.`vRestaurantName` AS restName';
            $condition = (!empty($condition) ? ' WHERE ' . implode(' AND ', $condition) : '');
            $localitysql = 'SELECT ' . $fields . ' FROM ' . $tbl . $join . $condition . $groubBy;
            $localityres = $this->db->query($localitysql);
            $allActiveLocalities = $localityres->result_array();
            
            $groubBy = ' GROUP BY tr.iRestaurantID';
            $restsql = 'SELECT ' . $fields . ' FROM ' . $tbl . $join . $condition . $groubBy;
            $res = $this->db->query($restsql);
            $allActiveRest = $res->result_array();

            $locArr = $restArr = array();
            $i = 0;
            foreach ($allActiveLocalities as $restLocArr) {
                $locArr[$i]['id'] = $restLocArr['id'];
                $locArr[$i]['name'] = $restLocArr['name'];
                $i++;
            }
            
            $j = 0;
            foreach ($allActiveRest as $restArray) {
                $restArr[$j]['id'] = $restArray['restId'];
                $restArr[$j]['name'] = $restArray['restName'].', '.$restArray['name'];
                $j++;
            }
            
            $resp['filters']['locality'] = $locArr;
            $resp['filters']['restaurant'] = $restArr;

            return $resp;
        } catch (Exception $ex) {
            // If execution stop unexpectedly send blank array
            return array();
        }
    }

    /*
     * Method to return all filters which can be applied on restaurant. 
     * 
     * @return array data with location listing
     * @author Anurag Srivastava (anurag.srivastava@kelltontech.com)
     */

    public function getAllComboFilter($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
            }
            // Fetching localities
            $tbl = $fields = $join = '';

            $tbl .= '`tbl_restaurant` AS `tr`';
            $tbl .= ' ,`tbl_location` AS `tl`';
            $tbl .= ', `tbl_combo_offers` AS `tco`';
            $condition[] = 'tco.iRestaurantID = tr.iRestaurantID';
            $condition[] = 'tl.iLocationID = tr.iLocationID';
            $condition[] = 'tco.eStatus = \'Active\'';
            $condition[] = 'tr.eStatus = \'Active\'';
            $condition[] = 'tl.eStatus = \'Active\'';
            $condition[] = 'CURDATE() between tco.dtStartDate and tco.dtExpiryDate';
            if (isset($zone) && !empty($zone)){
                    if ($zone == 10) {
                        $zoneId = '1,2,3,4,5,6,7,8,9';
                    } else if ($zone == 9) {
                        $zoneId = '1,2,3,4,5,9';
                    } else {
                        $zoneId = $zone;
                    }
                    $condition[] = "tl.iLocZoneID IN($zoneId)";
            }
            $groubBy = ' GROUP BY tr.iLocationID';
            $fields .= ' `tr`.iLocationID as id, `tl`.`vLocationName` AS name';
            $fields .= ' ,`tr`.iRestaurantID as restId, `tr`.`vRestaurantName` AS restName';
            $condition = (!empty($condition) ? ' WHERE ' . implode(' AND ', $condition) : '');
            $localitysql = 'SELECT ' . $fields . ' FROM ' . $tbl . $join . $condition . $groubBy;
            $localityres = $this->db->query($localitysql);
            $allActiveLocalities = $localityres->result_array();

            $locArr = $restArr = array();
            $i = 0;
            foreach ($allActiveLocalities as $restLocArr) {
                $locArr[$i]['id'] = $restLocArr['id'];
                $locArr[$i]['name'] = $restLocArr['name'];
                $restArr[$i]['id'] = $restLocArr['restId'];
                $restArr[$i]['name'] = $restLocArr['restName'];
                $i++;
            }
            $resp['filters']['locality'] = $locArr;
            $resp['filters']['restaurant'] = $restArr;

            return $resp;
        } catch (Exception $ex) {
            // If execution stop unexpectedly send blank array
            return array();
        }
    }

    function saveCode($postValue) {
        if (!empty($postValue)) {
            extract($postValue);
            if (!empty($userId) && !empty($offerId)) {
                $sql = 'select vMobileNo as mobile from tbl_user where iUserID = ' . $userId;
                $res = $this->db->query($sql)->row_array();
                $mobileNo = $res['mobile'];
                if (!empty($mobileNo)) {
                    $rand = random_string('numeric', 4);
                    $offerCode = 'HM' . random_string('numeric', 3);
                    $data = array(
                        'iDealID' => $offerId,
                        'iUserId' => $userId,
                        'iDealCode' => $rand,
                        'vOfferCode' => $offerCode,
                        'eStatus' => 'unavailed',
                        'dtCreatedDate' => date('Y-m-d H:i:s'),
                    );
                    $result = $this->db->insert('tbl_deals_code', $data);
                }
            }
        }
        if ($result == 1) {
            return array('rand' => $rand, 'mobile' => $mobileNo);
        }
        return '';
    }

    function verifyCode($data) {
        $sql = 'select * from tbl_deals_code where `iTableBookID` IS NULL AND iDealId ' . '= ' . $data['offerId'] . " and iUserId = " . $data['userId'] . " and iDealCode = " . $data['offerCode'];
        $res = $this->db->query($sql)->row_array();
        $currenttime = date('Y-m-d H:i:s');
        $expirytime = date('Y-m-d H:i:s', strtotime("+1 hour"));
        $resArr = array();
        if (!empty($res)) {
            switch ($res['eStatus']) {
                case 'unavailed' :

                    $resArr = array('');
                    $data = array(
                        'eStatus' => 'availed',
                        'dtAvailedDate' => date('Y-m-d H:i:s'),
                        'dtExpiryDate' => $expirytime
                    );
                    $result = $this->db->update('tbl_deals_code', $data, array('iCodeId' => $res['iCodeId']));
                    $resArr = array('status' => '1', 'msg' => 'Code Verified successfully.', 'offercode' => $res['vOfferCode']);
                    break;

                case 'availed' :
                    if (!empty($res['dtExpiryDate']) && $res['dtExpiryDate'] < $currenttime) {
                        $data = array(
                            'eStatus' => 'expired',
                        );
                        $result = $this->db->update('tbl_deals_code', $data, array('iCodeId' => $res['iCodeId']));
                        $resArr = array('status' => '3', 'msg' => 'Code Expired');
                    } else {
                        $resArr = array('status' => '2', 'msg' => 'Code Already Availed', 'offercode' => $res['vOfferCode']);
                    }
                    break;

                case 'expired' :
                    $resArr = array('status' => '3', 'msg' => 'Invalid Code');
                    break;
            }
        } else {
            $resArr = array('status' => '4', 'msg' => 'Invalid Code');
        }
        return $resArr;
    }

    public function savecomboCartDetails($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                /*
                 * INSERT THE RECORD 
                 */
                $response = array();
//                print_r($postValue); exit;
                foreach ($comboData as $key => $data) {
                    $INS = array(
                        'iRestaurantID' => $data['restaurantId'],
                        'iUserID' => $userId,
                        'iComboSubOffersID' => $data['subOfferId'],
                        'iComboOffersID' => $data['offerId'],
                        'tDiscountedPrice' => $data['discountedPrice'],
                        'qty' => $data['qty'],
                        'dtExpiryDate' => $data['expiryDate'],
                        'vDaysAllow' => $data['daysAllow'],
                        'iTotal' => $data['totalPrice'],
                        'eBookingStatus' => 'Cart',
                        'eSaleThrough' => 'Mobile',
                        'tCreatedAt' => date('Y:m:d H:i:s')
                    );
                    $this->db->insert('tbl_user_combo', $INS);
                    $response[] = array('subComboOfferId' => $data['subOfferId'], 'userComboId' => $this->db->insert_id(), 'quantity' => $data['qty']);
                }
                return $response;
            } return false;
        } catch (Exception $ex) {
            throw new Exception('Error in savecomboCartDetail function - ' . $ex);
        }
    }

}
