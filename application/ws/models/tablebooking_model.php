<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of link_model
 * link hashes expiry 24 hrs
 * @author Amit Malakar
 */
class Tablebooking_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_booking';
    }

    function getBookingCartDetails($userId = null) {
        try {
            if ($userId != '') {

                $fields = array(
                    'ttb.iTableBookID AS tableBookId',
                    'ttb.iSlotID AS slotId',
                    'ttb.iMealType AS mealType',
                    'ttb.iDealID AS selOffer',
                    'ttb.tDateTime AS bookDate',
                    'ttb.vUserRequest AS userRequest',
                    'ttb.vUserName AS userBookingName',
                    'ttb.vMobileNo AS userMobile',
                    'ttb.vUserName AS bookingName',
                    'ttb.iWaitTime AS waitingTime',
                    'CASE WHEN ttb.eBookingStatus = "Cancel" THEN "Canceled" ELSE ttb.eBookingStatus END AS bookingStatus',
                    'ttb.iPersonTotal AS totalPerson',
                    'DATE_FORMAT(ttb.tDateTime,\'' . MYSQL_DATE_FORMAT . '\') AS bookedDate',
                    'sm.tstartFrom AS slotTime',
                    'tr.iRestaurantID AS restaurantId',
                    'tr.vRestaurantName AS restaurantName',
                    'tr.tAddress AS restaurantAddress',
                    'CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.png", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantLogo)) ) AS restaurantImage',
                );

                $tbl = array(
                    'tbl_restaurant AS tr',
                    'tbl_table_book AS ttb',
                    'slot_master AS sm'
                );

                $condition = array(
                    'tr.iRestaurantID IN(ttb.iRestaurantID)',
                    'ttb.iUserID IN(' . $userId . ')',
                    'sm.iSlotID = ttb.iSlotID'
                );

                $condition[] = 'ttb.iUserID IN (' . $userId . ')';
                $condition[] = 'ttb.eBookingStatus = "Cart"';

                $fields = implode(',', $fields);
                $tbl = ' FROM ' . implode(',', $tbl);
                $condition = ' WHERE ' . implode(' AND ', $condition);

                $limit = ' LIMIT 1';

                $qry = 'SELECT ' . $fields . $tbl . $condition . $limit;
                //return $qry;
//                $qry = 'SELECT * FROM tbl_table_book'
//                        . ' WHERE iUserID IN(' . $userId . ') '
//                        . ' AND eBookingStatus = "Cart"'
//                        . ' LIMIT 1';

                $res = $this->db->query($qry);
                return $res->row_array();
            }return '';
        } catch (Exception $ex) {
            throw new Exception('Error in getBookingCartDetails function - ' . $ex);
        }
    }

    /**
     * Book restaurant table via WEB
     * @param array $postValue
     * @return int
     * @throws Exception
     */
    
    function saveBookTableInCart($postValue = array()) {
        try {
            if (!empty($postValue)) {

                extract($postValue);
                $time = explode('-', $bookTime);
                if (isset($time[0]) && isset($time[1]) && isset($time[2])) {
                    $minMaradian = $time[2] == '1' ? 'AM' : 'PM';

                    $minhr = strlen($time[0]) == 1 ? '0' . $time[0] : $time[0];

                    $minmin = strlen($time[1]) == 1 ? '0' . $time[1] : $time[1];
                    $openTimeNW = date('H:i', strtotime($minhr . ':' . $minmin . ' ' . $minMaradian));
                    $checkExactSlot = $this->db->query('SELECT iSlotID, tstartFrom FROM slot_master WHERE tstartFrom = \'' . $openTimeNW . '\'')->row_array();
                    if (!empty($checkExactSlot)) {
                        $slotId = isset($checkExactSlot['iSlotID']) ? $checkExactSlot['iSlotID'] : 0;
                        $slotTime = isset($checkExactSlot['tstartFrom']) ? $checkExactSlot['tstartFrom'] : '';
                    } else {
                        $openSlot = $this->db->query('SELECT iSlotID, tendTo FROM slot_master WHERE tstartFrom <= \'' . $openTimeNW . '\' AND tendTo > \'' . $openTimeNW . '\'')->row_array();
                        $slotId = isset($openSlot['iSlotID']) ? $openSlot['iSlotID'] + 1 : 0;
                        $slotTime = isset($openSlot['tendTo']) ? $openSlot['tendTo'] : '';
                    }
                   

                    $assignedTime = '';
                    if (isset($slotTime) && !empty($slotTime)) {
                        $assignedTime = date('h:i A', strtotime($slotTime));
                    }
                    $mealType = 2;
                    if ($slotId >= 17 && $slotId <= 49)
                        $mealType = 0;
                    else if ($slotId >= 51 && $slotId <= 71)
                        $mealType = 1;
                }
                
                // check if same rest., same slot and same time
                $qry = 'SELECT iTableBookID FROM tbl_table_book'
                        . ' WHERE iRestaurantID IN(' . $restaurantId . ') '
                        . ' AND iSlotID IN(' . $slotId . ') '
                        . ' AND tDateTime = \'' . date('Y-m-d', strtotime($bookDate)) . '\' ';

                $res = $this->db->query($qry);

                if ($res->num_rows() <= 0 || $res->num_rows() > 0) { //Removed validation on request
                    // check if max 5 accepted bookings for same rest. same day
                    $qry2 = 'SELECT iTableBookID FROM tbl_table_book'
                            . ' WHERE iRestaurantID IN(' . $restaurantId . ') '
                            . ' AND tDateTime = \'' . date('Y-m-d', strtotime($bookDate)) . '\' '
                            . ' AND eBookingStatus = "Accept"';

                    $res2 = $this->db->query($qry2);
                    if ($res2->num_rows < 5 || $res2->num_rows >= 5) {//Removed validation on request
                        // restaurant cut off time check
                        $slots = $this->getRestaurantSlots($restaurantId);
                        if (($slotId >= $slots['openSlot'] && $slotId <= $slots['closeSlot']) ? true : false) {
                            $ins = array(
                                'iUserID' => $userId,
                                'iRestaurantID' => $restaurantId,
                                'iSlotID' => $slotId,
                                'iWaitTime' => 0,
                                'iPersonTotal' => $totalPerson,
                                'iMealType' => $mealType,
                                'iDealID' => isset($selOffer) ? $selOffer : 0,
                                'vUserRequest' => isset($userRequest) ? $userRequest : '',
                                'vUserName' => $userBookingName,
                                'vMobileNo' => $userMobile,
                                'tDateTime' => date('Y-m-d', strtotime($bookDate)),
                                'tCreatedAt' => date('Y-m-d'),
                                'eBookingStatus' => 'Cart',
                                'ePlatform' => isset($plateForm) ? strtolower($plateForm) : 'app',
                            );

                            $this->db->insert('tbl_table_book', $ins);
                            $insId = $this->db->insert_id();
                            $returnArr['bookingId'] = $insId;
                            $returnArr['assignedSlot'] = '';
                            if (isset($assignedTime) && !empty($assignedTime)) {
                                $returnArr['assignedSlot'] = $assignedTime;
                            }
                            if ($insId) {
                                $this->db->query('UPDATE tbl_table_book SET unique_code = CONCAT("HMB",LPAD(iTableBookID, 5, "0")) WHERE iTableBookID = ' . $insId);
                                $query = 'SELECT unique_code FROM tbl_table_book'
                                        . ' WHERE iTableBookID IN(' . $insId . ') limit 1';

                                $resultCode = $this->db->query($query);
                                $data = $resultCode->row_array();
                                $uniqueCode = isset($data['unique_code']) ? $data['unique_code'] : '';
                                $returnArr['uniqueCode'] = isset($data['unique_code']) ? $data['unique_code'] : '';
                            }
                            return $returnArr; //Record can be saved
                        } return -4;
                    } return -3;
                } return -2;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in bookWebTable function - ' . $ex);
        }
    }

    /*
     * GET RESTAURANT SLOT VALUES
     */

    function getRestaurantSlots($restId = '') {
        try {
            if ($restId != '') {



                $row = $this->db->query('SELECT iMinTime AS restaurantMinTime, iMaxTime AS restaurantMaxTime FROM tbl_restaurant WHERE iRestaurantID IN(' . $restId . ')')->row_array();

                $minTime = explode('-', $row['restaurantMinTime']);
                $maxTime = explode('-', $row['restaurantMaxTime']);

                if (isset($minTime[0]) && isset($minTime[1]) && isset($minTime[2])) {
                    $minMaradian = $minTime[2] == '1' ? 'AM' : 'PM';
                    $maxMaradian = $maxTime[2] == '1' ? 'AM' : 'PM';

                    $minhr = strlen($minTime[0]) == 1 ? '0' . $minTime[0] : $minTime[0];
                    $maxhr = strlen($maxTime[0]) == 1 ? '0' . $maxTime[0] : $maxTime[0];

                    $minmin = strlen($minTime[1]) == 1 ? '0' . $minTime[1] : $minTime[1];
                    $maxmin = strlen($maxTime[1]) == 1 ? '0' . $maxTime[1] : $maxTime[1];

                    $openCloseTime = $minhr . ':' . $minmin . ' ' . $minMaradian . ' to ' . $maxhr . ':' . $maxmin . ' ' . $maxMaradian;
                    $openCloseTimeValue = $minhr . ':' . $minmin . ':' . $minMaradian . ' to ' . $maxhr . ':' . $maxmin . ':' . $maxMaradian;
                    $arry['info']['openCloseTime'] = $openCloseTime;
                    $arry['info']['openCloseTimeValue'] = $openCloseTimeValue;

                    $openTimeNW = date('H:i', strtotime($minhr . ':' . $minmin . ' ' . $minMaradian));
                    $closeTimeNW = date('H:i', strtotime($maxhr . ':' . $maxmin . ' ' . $maxMaradian));

                    //$openSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $openTimeNW . '\' AND tendTo > \'' . $openTimeNW . '\'')->row_array();
                    $openSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $openTimeNW . '\' order by `iSlotID` desc')->row_array();
                    //echo $this->db->last_query();
                    $openSlot = (int) @$openSlot['iSlotID'];

                    //$closeSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $closeTimeNW . '\' AND tendTo > \'' . $closeTimeNW . '\'')->row_array();
                    $closeSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $closeTimeNW . '\'  order by `iSlotID` desc')->row_array();
                    //echo $this->db->last_query();
                    // AS CLOSE TIME IF 12 AM SHOULD ONLY SELECT 11:45PM TO 12:00 AM SLOT WHICH IS 96
                    if ($closeTimeNW == '00:00' || $closeTimeNW == '23:45') {
                        $closeSlot = 96;
                    } else if ($closeTimeNW > '00:00' && $maxMaradian == 'AM') {
                        $closeSlot = (int) @$closeSlot['iSlotID'] + 96;
                    } else {
                        $closeSlot = (int) @$closeSlot['iSlotID'];
                    }
                    //$closeSlot = (int) @$closeSlot['iSlotID'];

                    $currentTimeNW = date('H:i', time());
                    //$currentSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $currentTimeNW . '\' AND tendTo > \'' . $currentTimeNW . '\'')->row_array();
                    $currentSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $currentTimeNW . '\' order by `iSlotID` desc')->row_array();
                    $currentSlot = (int) @$currentSlot['iSlotID'];

                    return array(
                        'openSlot' => $openSlot,
                        'closeSlot' => $closeSlot,
                        'currentSlot' => $currentSlot
                    );
                } return array();
            } return array();
        } catch (Exception $ex) {
            
        }
    }

    /*
     * TO DELETE THE CART
     */

    function deleteBookingCart($userId = '', $bookedId = '') {
        try {
            if ($userId != '' && $bookedId != '') {
                $this->db->delete('tbl_table_book', array('iTableBookID' => $bookedId, 'iUserID' => $userId, 'eBookingStatus' => 'Cart'));
                return 1;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in cancelReservation function - ' . $ex);
        }
    }

    function getLastBookingDetails($userId = null) {
        try {
            if ($userId != '') {
                $fields = array(
                    'ttb.iTableBookID AS tableBookId',
                    'ttb.unique_code AS bookingId',
                    'ttb.iSlotID AS slotId',
                    'ttb.iMealType AS mealType',
                    'ttb.iDealID AS selOffer',
                    'ttb.tDateTime AS bookDate',
                    'ttb.vUserRequest AS userRequest',
                    'ttb.vUserName AS userBookingName',
                    'ttb.vMobileNo AS userMobile',
                    'ttb.vUserName AS bookingName',
                    'ttb.iWaitTime AS waitingTime',
                    'CASE WHEN ttb.eBookingStatus = "Cancel" THEN "Canceled" ELSE ttb.eBookingStatus END AS bookingStatus',
                    'ttb.iPersonTotal AS totalPerson',
                    'DATE_FORMAT(ttb.tDateTime,\'' . MYSQL_DATE_FORMAT . '\') AS bookedDate',
                    'sm.tstartFrom AS slotTime',
                    'tr.iRestaurantID AS restaurantId',
                    'tr.vRestaurantName AS restaurantName',
                    'tr.tAddress AS restaurantAddress',
                    'CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.png", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantLogo)) ) AS restaurantImage',
                );

                $tbl = array(
                    'tbl_restaurant AS tr',
                    'tbl_table_book AS ttb',
                    'slot_master AS sm'
                );

                $condition = array(
                    'tr.iRestaurantID IN(ttb.iRestaurantID)',
                    'ttb.iUserID IN(' . $userId . ')',
                    'sm.iSlotID = ttb.iSlotID'
                );

                $condition[] = 'ttb.iUserID IN (' . $userId . ')';
                // $condition[] = 'ttb.eBookingStatus = "Cart"';

                $fields = implode(',', $fields);
                $tbl = ' FROM ' . implode(',', $tbl);
                $condition = ' WHERE ' . implode(' AND ', $condition);
                $orderBy = ' ORDER BY ttb.iTableBookID DESC';
                $limit = ' LIMIT 1';

                $qry = 'SELECT ' . $fields . $tbl . $condition . $orderBy . $limit;

                $res = $this->db->query($qry);
                return $res->row_array();
            }return '';
        } catch (Exception $ex) {
            throw new Exception('Error in getLastBookingDetails function - ' . $ex);
        }
    }

    function getBookingDetail($postValue = array()) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                if ($userId != '' && $bookingId !='') {
                    $fields = array(
                        'ttb.iTableBookID AS tableBookId',
                        'ttb.iSlotID AS slotId',
                        'ttb.iDealID AS selOffer',
                        'ttb.vUserRequest AS userRequest',
                        'ttb.vUserName AS userBookingName',
                        'ttb.vMobileNo AS userMobile',
                        'ttb.iPersonTotal AS totalPerson',
                        'DATE_FORMAT(ttb.tDateTime,\'' . MYSQL_DATE_FORMAT . '\') AS bookedDate',
                        'sm.tstartFrom AS slotTime',
                    );

                    $tbl = array(
                        'tbl_restaurant AS tr',
                        'tbl_table_book AS ttb',
                        'slot_master AS sm'
                    );

                    $condition = array(
                        'tr.iRestaurantID IN(ttb.iRestaurantID)',
                        'ttb.iUserID = ' . $userId,
                        'ttb.iTableBookID = ' . $bookingId,
                        'sm.iSlotID = ttb.iSlotID'
                    );

                    $condition[] = 'ttb.iUserID IN (' . $userId . ')';
                    // $condition[] = 'ttb.eBookingStatus = "Cart"';

                    $fields = implode(',', $fields);
                    $tbl = ' FROM ' . implode(',', $tbl);
                    $condition = ' WHERE ' . implode(' AND ', $condition);
                    $orderBy = ' ORDER BY ttb.iTableBookID DESC';
                    $limit = ' LIMIT 1';

                    $qry = 'SELECT ' . $fields . $tbl . $condition . $orderBy . $limit;

                    $res = $this->db->query($qry);
                    return $res->row_array();
                }return '';
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in getLastBookingDetails function - ' . $ex);
        }
    }

    function getComboCartDetails($userId = null) {
        try {
            if ($userId != '') {

                $fields = array(
                    'tuc.iComboOffersID AS iComboOffersID',
                    'tcso.vOfferTitle AS vOfferTitle',
                    'tcso.tOfferDetail AS tOfferDetail',
                    'tcso.tActualPrice AS tActualPrice',
                    'tcso.tDiscountedPrice AS tDiscountedPrice',
                    'tuc.tCreatedAt AS tCreatedAt',
                    'tuc.tModifiedAt AS tModifiedAt',
                    'tuc.iComboOffersID AS comboId',
                    'tuc.iComboSubOffersID AS subComboId',
                    'tuc.iComboSubOffersID AS iComboSubOffersID',
                    'tuc.qty AS qty',
                    'CASE WHEN tuc.eBookingStatus = "Cancel" THEN "Canceled" ELSE tuc.eBookingStatus END AS bookingStatus',
                    'tuc.iRestaurantID AS restId',
                    'DATE_FORMAT(tuc.dtExpiryDate,\'' . MYSQL_DATE_FORMAT . '\') AS dtExpiryDate',
                    'tuc.iUserID AS iUserID',
                    'tco.vOfferText AS offerText',
                    'tuc.vDaysAllow AS vDaysAllow',
                    'tr.vRestaurantName AS restaurantName',
                    'CONCAT("' . BASEURL . 'images/combo/", IF(tco.vOfferImage = \'\', "default.png", CONCAT(`tco`.iRestaurantID,\'/\',`tco`.vOfferImage)) ) AS offerImage',
                );

                $join = '';
                $tbl = array(
                    'tbl_user_combo AS tuc'
                );

                $join .= ' INNER JOIN `tbl_combo_offers` AS `tco` ON `tco`.`iComboOffersID` = `tuc`.`iComboOffersID`';
                $join .= ' INNER JOIN `tbl_combo_sub_offers` AS `tcso` ON `tcso`.`iComboSubOffersID` = `tuc`.`iComboSubOffersID`';
                $join .= 'INNER JOIN `tbl_restaurant` AS tr ON `tr`.`iRestaurantID` = `tco`.`iRestaurantID`';
                $condition[] = 'tuc.iUserID IN (' . $userId . ')';
                $condition [] = 'tuc.eBookingStatus = "Cart"';

                $fields = implode(',', $fields);
                $tbl = ' FROM ' . implode(',', $tbl);
                $condition = ' WHERE ' . implode(' AND ', $condition);

                $qry = 'SELECT ' . $fields . $tbl . $join . $condition;
                $res = $this->db->query($qry);
                return $res->result_array();
            }return '';
        } catch (Exception $ex) {
            throw new Exception('Error in getBookingCartDetails function - ' . $ex);
        }
    }

    function getTableBooking($restaurantId = '') {
        try {
            if ($restaurantId != '') {
                $tbl = $fields = $condition = array();

                $tbl = array(
                    'tbl_restaurant AS tr',
                    'tbl_deals AS td'
                );

                $fields = array(
                    'tr.iRestaurantID AS restaurantId',
                    'tr.vRestaurantName AS restaurantName',
                    'tr.tAddress2 AS restaurantAddress',
                    'IF(tr.vRestaurantLogo = "" , CONCAT("' . IMG_URL . 'restaurant/default.png"), CONCAT("' . IMG_URL . 'restaurant/", tr.iRestaurantID, "/thumb/", tr.vRestaurantLogo))  AS restaurantImage',
                    'td.iDealID AS offerId',
                    'td.eStatus AS offerStatus',
                    'IF(td.vDealimage = "" , CONCAT("' . IMG_URL . 'deal/default.png"), CONCAT("' . IMG_URL . 'deal/", td.iDealID, "/thumb/", td.vDealImage))  AS offerImage',
                    'td.vOfferText AS offerText',
                    'td.tOfferDetail AS offerDetail',
                    'td.tTermsOfUse AS offerTerms',
                    'td.dtStartDate AS offerStartDate',
                    'DATE_FORMAT(td.dtStartDate,"%l:%i %p") AS offerStartTime',
                    'DATE_FORMAT(td.dtExpiryDate,"' . MYSQL_DATE_FORMAT . '") AS offerEndDate',
                    'DATE_FORMAT(td.dtExpiryDate,"%l:%i %p") AS offerEndTime',
                    'td.vDealCode AS offerCode',
                    'tr.min_person AS minPer',
                    'tr.max_person AS maxPer',
                    'td.vDaysAllow AS daysAllowed',
                    'tr.iMinTime AS restaurantMinTime',
                    'tr.iMaxTime AS restaurantMaxTime',
                    'tr.bookingAvailable AS bookingAvailable',
                );

                $condition = array(
                    'tr.iRestaurantID IN(td.iRestaurantID)',
                    'td.eStatus IN(\'Active\')',
                    'td.iRestaurantID IN(' . $restaurantId . ')',
                    'CURDATE() between td.dtStartDate and td.dtExpiryDate'
                );

                $tbl = ' FROM ' . implode(',', $tbl);
                $fields = implode(',', $fields);
                $condition = ' WHERE ' . implode(' AND ', $condition);

                $qry = 'SELECT ' . $fields . $tbl . $condition;
//                return $qry;
                $res = $this->db->query($qry);
                $data = $res->result_array();
                if ($res->num_rows() > 0) {
                    $days = array(
                        '1' => 'Sun',
                        '2' => 'Mon',
                        '3' => 'Tue',
                        '4' => 'Wed',
                        '5' => 'Thu',
                        '6' => 'Fri',
                        '7' => 'Sat'
                    );
//                    mprd($data);
                    $result = [];
                    foreach ($data as $key => $value) {
                        $result['restaurant']['restaurantId'] = $value['restaurantId'];
                        $result['restaurant']['restaurantName'] = $value['restaurantName'];
                        $result['restaurant']['restaurantOpenTime'] = $value['restaurantMinTime'];
                        $result['restaurant']['restaurantCloseTime'] = $value['restaurantMaxTime'];
                        $result['restaurant']['restaurantAddress'] = $value['restaurantAddress'];
                        $result['restaurant']['restaurantImage'] = $value['restaurantImage'];
                        $result['restaurant']['bookingAvailable'] = $value['bookingAvailable'];
                        $minTime = explode('-', $value['restaurantMinTime']);
                        $maxTime = explode('-', $value['restaurantMaxTime']);
                        $time = $this->getSlotsFromTime($minTime, $maxTime);
                        $result['restaurant']['openTime'] = $time['openTime'];
                        $result['restaurant']['closeTime'] = $time['closeTime'];
                        $result['restaurant']['minPer'] = $value['minPer'];
                        $result['restaurant']['maxPer'] = $value['maxPer'];
                        $dayArr = explode(',', $value['daysAllowed']);
                        $result['offer'][$key]['offerId'] = $value['offerId'];
                        $result['offer'][$key]['offerImage'] = $value['offerImage'];
                        $result['offer'][$key]['offerText'] = $value['offerText'];
                        $result['offer'][$key]['offerDetail'] = $value['offerDetail'];
                        $result['offer'][$key]['offerTerms'] = $value['offerTerms'];
                        $result['offer'][$key]['offerStartDate'] = $value['offerStartDate'];
                        $result['offer'][$key]['offerStartTime'] = $value['offerStartTime'];
                        $result['offer'][$key]['offerEndDate'] = $value['offerEndDate'];
                        $result['offer'][$key]['offerEndTime'] = $value['offerEndTime'];
                        $result['offer'][$key]['offerCode'] = $value['offerCode'];

                        asort($dayArr);
                        $day = '';
                        $rangeFlag = false;
                        while ($dayKey = array_shift($dayArr)) {
                            if (count($dayArr)) {
                                $day .= $days[$dayKey] . ",";
                                //                            if ($dayKey + 1 == $dayArr[0]) {
                                //                                if (!$rangeFlag) {
                                //                                    $day .= $days[$dayKey] . "-";
                                //                                    $rangeFlag = true;
                                //                                }
                                //                            } else {
                                //                                if ($rangeFlag) {
                                //                                    $rangeFlag = false;
                                //                                }
                                //                                $day .= $days[$dayKey] . ",";
                                //                            }
                            } else {
                                $day .= $days[$dayKey];
                            }
                        }
                        $result['offer'][$key]['daysAllowed'] = $day;
                    }
                    return $result;
                } else {
                    $tbl = array(
                        'tbl_restaurant AS tr',
                    );

                    $fields = array(
                    'tr.iRestaurantID AS restaurantId',
                    'tr.vRestaurantName AS restaurantName',
                    'tr.tAddress2 AS restaurantAddress',
                    'IF(tr.vRestaurantLogo = "" , CONCAT("' . IMG_URL . 'restaurant/default.png"), CONCAT("' . IMG_URL . 'restaurant/", tr.iRestaurantID, "/thumb/", tr.vRestaurantLogo))  AS restaurantImage',
                    'tr.min_person AS minPer',
                    'tr.max_person AS maxPer',
                    'tr.iMinTime AS restaurantMinTime',
                    'tr.iMaxTime AS restaurantMaxTime',
                    'tr.bookingAvailable AS bookingAvailable',
                );

                    $condition = array(
                        'tr.iRestaurantID IN(' . $restaurantId . ')',
                    );

                    $tbl = ' FROM ' . implode(',', $tbl);
                    $fields = implode(',', $fields);
                    $condition = ' WHERE ' . implode(' AND ', $condition);

                    $qry = 'SELECT ' . $fields . $tbl . $condition;
                    $res = $this->db->query($qry);
                    $data = $res->row_array();
                    if (empty($data)) {
                        return array();
                    }
                    $result['restaurant']['restaurantId'] = $data['restaurantId'];
                    $result['restaurant']['restaurantName'] = $data['restaurantName'];
                    $result['restaurant']['restaurantAddress'] = $data['restaurantAddress'];
                    $result['restaurant']['restaurantImage'] = $data['restaurantImage'];
                    $result['restaurant']['restaurantOpenTime'] = $data['restaurantMinTime'];
                    $result['restaurant']['restaurantCloseTime'] = $data['restaurantMaxTime'];
                    $result['restaurant']['bookingAvailable'] = $data['bookingAvailable'];
                    $minTime = explode('-', $data['restaurantMinTime']);
                    $maxTime = explode('-', $data['restaurantMaxTime']);
                    $time = $this->getSlotsFromTime($minTime, $maxTime);
                    $result['restaurant']['openTime'] = $time['openTime'];
                    $result['restaurant']['closeTime'] = $time['closeTime'];
                    $result['restaurant']['minPer'] = $data['minPer'];
                    $result['restaurant']['maxPer'] = $data['maxPer'];
                    return $result;
                }
            } return array();
        } catch (Exception $ex) {
            throw new Exception('Error in getTableBooking function - ' . $ex);
        }
    }

    /**
      /**
     * Book restaurant table via WEB
     * @param array $postValue
     * @return int
     * @throws Exception
     */
    function bookTable($postValue = array()) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                // create slotId and mealtype using time.
//                 $bookTime = '04-30-2';
                if(isset($plateForm) && $plateForm == 'web') {
                    $slotId = $bookTime;
                    $checkExactSlot = $this->db->query('SELECT iSlotID, tstartFrom FROM slot_master WHERE iSlotID = '.$slotId)->row_array();
                    if (!empty($checkExactSlot)) {
                        $slotId = isset($checkExactSlot['iSlotID']) ? $checkExactSlot['iSlotID'] : 0;
                        $slotTime = isset($checkExactSlot['tstartFrom']) ? $checkExactSlot['tstartFrom'] : '';
                    }
                    $assignedTime = '';
                    if (isset($slotTime) && !empty($slotTime)) {
                        $assignedTime = date('h:i A', strtotime($slotTime));
                    }
                    $mealType = 2;
                    if ($slotId >= 17 && $slotId <= 49)
                        $mealType = 0;
                    else if ($slotId >= 51 && $slotId <= 71)
                        $mealType = 1;
                }else {
                $time = explode('-', $bookTime);
                if (isset($time[0]) && isset($time[1])) {
                    $minhr = strlen($time[0]) == 1 ? '0' . $time[0] : $time[0];
                    $minmin = strlen($time[1]) == 1 ? '0' . $time[1] : $time[1];
                    if(isset($time[2]) && !empty($time[2])) {
                    $minMaradian = $time[2] == '1' ? 'AM' : 'PM';
                    $openTimeNW = date('H:i', strtotime($minhr . ':' . $minmin . ' ' . $minMaradian));
                    }else {
                        $openTimeNW = $minhr . ':' . $minmin;
                    }
                    $checkExactSlot = $this->db->query('SELECT iSlotID, tstartFrom FROM slot_master WHERE tstartFrom = \'' . $openTimeNW . '\'')->row_array();
                    if (!empty($checkExactSlot)) {
                        $slotId = isset($checkExactSlot['iSlotID']) ? $checkExactSlot['iSlotID'] : 0;
                        $slotTime = isset($checkExactSlot['tstartFrom']) ? $checkExactSlot['tstartFrom'] : '';
                    } else {
                        //$openSlot = $this->db->query('SELECT iSlotID, tendTo FROM slot_master WHERE tstartFrom <= \'' . $openTimeNW . '\' AND tendTo > \'' . $openTimeNW . '\'')->row_array();
                        $openSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $openTimeNW . '\' order by `iSlotID` desc')->row_array();
                        $slotId = isset($openSlot['iSlotID']) ? $openSlot['iSlotID'] + 1 : 0;
                        $slotTime = isset($openSlot['tendTo']) ? $openSlot['tendTo'] : '';
                    }

                    $assignedTime = '';
                    if (isset($slotTime) && !empty($slotTime)) {
                        $assignedTime = date('h:i A', strtotime($slotTime));
                    }
                    $mealType = 2;
                    if ($slotId >= 17 && $slotId <= 49)
                        $mealType = 0;
                    else if ($slotId >= 51 && $slotId <= 71)
                        $mealType = 1;
                }
                }
                // check if same rest., same slot and same time
                $qry = 'SELECT iTableBookID FROM tbl_table_book'
                        . ' WHERE iRestaurantID IN(' . $restaurantId . ') '
                        . ' AND eBookingStatus != "Cart"'
                        . ' AND iSlotID IN(' . $slotId . ') '
                        . ' AND tDateTime = \'' . date('Y-m-d', strtotime($bookDate)) . '\' ';

                $res = $this->db->query($qry);

                if ($res->num_rows() <= 0 || $res->num_rows() > 0) {
                    // check if max 5 accepted bookings for same rest. same day
                    $qry2 = 'SELECT iTableBookID FROM tbl_table_book'
                            . ' WHERE iRestaurantID IN(' . $restaurantId . ') '
                            . ' AND tDateTime = \'' . date('Y-m-d', strtotime($bookDate)) . '\' '
                            . ' AND eBookingStatus = "Accept"';

                    $res2 = $this->db->query($qry2);

                    if ($res2->num_rows < 5 || $res2->num_rows >= 5) {
                        // restaurant cut off time check
                        $slots = $this->getRestaurantSlots($restaurantId);
                        //return $slots;
                        // if (($slotId >= $slots['openSlot'] && $slotId <= $slots['closeSlot']) ? true : false) {
                        // AS CLOSE TIME IF 12 AM SHOULD ONLY SELECT 11:30PM TO 12:00 AM SLOT WHICH IS 48
                        if (($slotId >= $slots['openSlot'] && $slotId <= $slots['closeSlot']) ? true : false) {

                            $ins = array(
                                'iUserID' => $userId,
                                'iRestaurantID' => $restaurantId,
                                'iSlotID' => $slotId,
                                'iWaitTime' => 0,
                                'iPersonTotal' => $totalPerson,
                                'iMealType' => $mealType,
                                'iDealID' => isset($selOffer) ? $selOffer : 0,
                                'vUserRequest' => isset($userRequest) ? $userRequest : '',
                                'vUserName' => $userBookingName,
                                'vMobileNo' => $userMobile,
                                'tDateTime' => date('Y-m-d', strtotime($bookDate)),
                                'tCreatedAt' => date('Y-m-d'),
                                'ePlatform' => isset($plateForm) ? strtolower($plateForm) : 'app',
                            );

                            $qry3 = 'SELECT iTableBookID FROM tbl_table_book'
                                    . ' WHERE iRestaurantID IN(' . $restaurantId . ') '
                                    . ' AND iUserID = \'' . $userId . '\' '
                                    . ' AND iSlotID = \'' . $slotId . '\' '
                                    . ' AND eBookingStatus = "Cart"';
                            $res3 = $this->db->query($qry3);
                            $tableId = $res3->result_array();
                            if ($res3->num_rows > 0) {
                                $ins['eBookingStatus'] = 'Pending';
                                $ins['tUpdatedAt'] = date('Y-m-d');

                                $data = $this->db->update('tbl_table_book', $ins, array('iRestaurantID' => $restaurantId,
                                    'iUserID' => $userId, 'iSlotID' => $slotId, 'eBookingStatus' => 'Cart'));
                                $insId = isset($tableId['0']['iTableBookID']) ? $tableId['0']['iTableBookID'] : 0;
                            } else {
                                if (isset($tableBookId) && !empty($tableBookId)) {
                                    $ins['eBookingStatus'] = 'Pending';
                                    $ins['tUpdatedAt'] = date('Y-m-d');

                                    $this->db->update('tbl_table_book', $ins, array('iTableBookID' => $tableBookId));
                                    $insId = $tableBookId;
                                } else {
                                    $this->db->insert('tbl_table_book', $ins);
                                    if (isset($comboId) && isset($fromPurchase) && $fromPurchase == 1) {
                                        $this->db->update('tbl_user_combo', array('iUserComboID' => $comboId), array('bookingStatus' => 1));
                                    }
                                    $insId = $this->db->insert_id();

                                    if ($insId) {
                                        $this->db->query('UPDATE tbl_table_book SET unique_code = CONCAT("HMB",LPAD(iTableBookID, 5, "0")) WHERE iTableBookID = ' . $insId);
                                        $query = 'SELECT unique_code FROM tbl_table_book'
                                                . ' WHERE iTableBookID IN(' . $insId . ') limit 1';

                                        $resultCode = $this->db->query($query);
                                        $data = $resultCode->row_array();
                                        $uniqueCode = isset($data['unique_code']) ? $data['unique_code'] : '';
                                    }

                                    //send push notification
                                    $this->load->model('usernotifications_model');
                                    $this->load->model('restaurant_model');
                                    $sql = "SELECT vRestaurantName FROM tbl_restaurant WHERE iRestaurantID='$restaurantId'";
                                    $restaurantName = $this->db->query($sql)->row_array();
                                    $message = "Your table booking request placed successfully. Current booking status is Pending.";
                                    $pushData = array("bookingId" => $uniqueCode,
                                        "restaurantName" => $restaurantName["vRestaurantName"],
                                        "time" => $this->restaurant_model->getTimeElapsed(strtotime($bookDate)),
                                        "notificationType" => 4
                                    );
                                    $this->usernotifications_model->sendNotification($userId, $message, $pushData);
                                }
                            }


                            $this->general_model->addUserPointValueWeb($userId, 2, $insId);

                            /*
                             * HERE PUSH NOTIFICATION WILL SEND IT TO VENDOR APP
                             */
                            $record = $this->db->query('SELECT ePlatform, vDeviceToken FROM tbl_admin WHERE iRestaurantID IN(' . $restaurantId . ')')->row_array();

                            $this->load->library('pushnotify');

                            $osType = $record['ePlatform'] == 'ios' ? 2 : 1;
                            $deviceToken = $record['vDeviceToken'];

                            $mesg = 'You have a new booking request.';

                            if ($deviceToken != '') {
                                $this->pushnotify->sendIt($osType, $deviceToken, $mesg, 2);
                            }
                            $returnArr['bookingId'] = $insId;
                            if (!empty($insId)) {
                                $query = 'SELECT unique_code FROM tbl_table_book'
                                        . ' WHERE iTableBookID IN(' . $insId . ') limit 1';

                                $resultCode = $this->db->query($query);
                                $data = $resultCode->row_array();
                                $returnArr['uniqueCode'] = isset($data['unique_code']) ? $data['unique_code'] : '';
                            }
                            //Get Offer Code
                            if (isset($insId) && $insId > 0 && isset($selOffer) && $selOffer > 0 && isset($userId) && $userId > 0) {
                                $offerCode = $this->_updateOfferCode($userId, $insId, $selOffer, $bookDate);
                                if (!empty($offerCode)) {
                                    $returnArr['offerCode'] = $offerCode;
                                }
                            }
                            $returnArr['assignedSlot'] = '';
                            if (isset($assignedTime) && !empty($assignedTime)) {
                                $returnArr['assignedSlot'] = $assignedTime;
                            }

                            $this->sendBookingEmail('pending', $insId, $userId);
                            return $returnArr;
                        } return -4;
                    } return -3;
                } return -2;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in bookWebTable function - ' . $ex);
        }
    }

    public function sendBookingEmail($type, $bookingId, $userId) {

        if ($bookingId != '') {
            $fields = array(
                'ttb.iTableBookID AS tableBookId',
                'ttb.unique_code AS bookingId',
                'td.vOfferText AS offerText',
                'ttb.iDealID AS selOffer',
                'ttb.tDateTime AS bookDate',
                'ttb.vUserRequest AS userRequest',
                'ttb.vUserName AS userBookingName',
                'ttb.vMobileNo AS userMobile',
                'ttb.iWaitTime AS waitingTime',
                'ttb.iPersonTotal AS totalPerson',
                'DATE_FORMAT(ttb.tDateTime,\'' . MYSQL_DATE_FORMAT . '\') AS bookedDate',
                'sm.tstartFrom AS slotTime',
                'tr.iRestaurantID AS restaurantId',
                'tr.vRestaurantName AS restaurantName',
                'tr.tAddress AS restaurantAddress'
            );


            $tbl = array(
                'tbl_table_book AS ttb',
            );

            $condition = array(
                'ttb.iTableBookID IN(' . $bookingId . ')',
            );

            $fields = implode(',', $fields);
            $tbl = ' FROM ' . implode(',', $tbl);
            $tbl .= ' LEFT JOIN  `tbl_restaurant` AS  `tr`  ON `ttb`.`iRestaurantID` =  `tr`.`iRestaurantID`';
            $tbl .= ' LEFT JOIN  `slot_master` AS  `sm`  ON `sm`.`iSlotID` =  `ttb`.`iSlotID`';
            $tbl .= ' LEFT JOIN  `tbl_deals` AS  `td`  ON `ttb`.`iDealID` =  `td`.`iDealID`';

            $condition = ' WHERE ' . implode(' AND ', $condition);
            $limit = ' LIMIT 1';

            $qry = 'SELECT ' . $fields . $tbl . $condition . $limit;

            $res = $this->db->query($qry);
            $data = $res->row_array();
        }

        if ($userId != '') {
            $userData = $this->general_model->getUserBasicRecordById($userId);
            $userEmail = $userData['userEmail'];
            $userFullName = $userData['userFirstName'];
        }

        switch ($type) {
            case 'pending':
                $subject = 'You just booked a table, check status.';
                $tmplt = DIR_VIEW . 'email/table_book_status.php';
                break;
            case 'cancel':
                $subject = 'You just cancelled a reservation, check status.';
                $tmplt = DIR_VIEW . 'email/cancel_booking.php';
                break;

            default:
                break;
        }

        $param = array(
            '%MAILSUBJECT%' => 'HungerMafia : You just booked a table, check status',
            '%BASEURL%' => BASEURL,
            '%NAME%' => @$userFullName,
            '%BOOKINGNAME%' => @$data['userBookingName'],
            '%BOOKINGNUMBER%' => @$data['userMobile'],
            '%BOOKINGID%' => @$data['bookingId'],
            '%When%' => @$data['bookedDate'] . ' ' . date('h:i A', strtotime($data['slotTime'])),
            '%Where%' => @$data['restaurantName'] . ', ' . @$data['restaurantAddress'],
            '%OfferAvailed%' => @($data['selOffer'] > 0) ? @$data['offerText'] : 'Offer not availed',
            '%Guests%' => @$data['totalPerson'],
        );
        //send email

        $subject = 'HungerMafia : ' . $subject;
        $to = @$userEmail;
        $this->load->model("smtpmail_model", "smtpmail_model");
        $this->smtpmail_model->send($to, $subject, $tmplt, $param);
        return 1;
    }

    private function _updateOfferCode($userId, $bookingId, $dealId, $bookDate) {
        if (!empty($userId) && !empty($bookingId) && !empty($dealId)) {
            $offerCode = 'HM' . random_string('numeric', 3);
            $bookingDate = date('Y-m-d', strtotime($bookDate));
            $expirytime = date('Y-m-d H:i:s', strtotime($bookingDate . "+1 days"));
            $data = array(
                'iDealID' => $dealId,
                'iUserId' => $userId,
                'iTableBookID' => $bookingId,
                'vOfferCode' => $offerCode,
                'eStatus' => 'availed',
                'dtCreatedDate' => date('Y-m-d H:i:s'),
                'dtAvailedDate' => date('Y-m-d H:i:s'),
                'dtExpiryDate' => $expirytime
            );
            $result = $this->db->insert('tbl_deals_code', $data);
        }
        if ($result == 1) {
            return $offerCode;
        }
        return '';
    }

    public function getSlotsFromTime($minTime, $maxTime) {
        $result = [];
        if (isset($minTime[0]) && isset($minTime[1]) && isset($minTime[2])) {
            $minMaradian = $minTime[2] == '1' ? 'AM' : 'PM';
            $maxMaradian = $maxTime[2] == '1' ? 'AM' : 'PM';

            $minhr = strlen($minTime[0]) == 1 ? '0' . $minTime[0] : $minTime[0];
            $maxhr = strlen($maxTime[0]) == 1 ? '0' . $maxTime[0] : $maxTime[0];

            $minmin = strlen($minTime[1]) == 1 ? '0' . $minTime[1] : $minTime[1];
            $maxmin = strlen($maxTime[1]) == 1 ? '0' . $maxTime[1] : $maxTime[1];

            $openTime = $minhr . ':' . $minmin . ' ' . $minMaradian;
            $closeTime = $maxhr . ':' . $maxmin . ' ' . $maxMaradian;

            $openTimeNW = date('H:i', strtotime($minhr . ':' . $minmin . ' ' . $minMaradian));
            $closeTimeNW = date('H:i', strtotime($maxhr . ':' . $maxmin . ' ' . $maxMaradian));
//                            print_r(date('H:i', strtotime($maxhr . ':' . $maxmin . ' ' . $maxMaradian)));
//                            die;
//                            $openSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $openTimeNW . '\' AND tendTo > \'' . $openTimeNW . '\'')->row_array();
//                            $openSlot = (int) @$openSlot['iSlotID'];
//
//                            $closeSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $closeTimeNW . '\' AND tendTo > \'' . $closeTimeNW . '\'')->row_array();
//                            $closeSlot = (int) @$closeSlot['iSlotID'];
//                            if ($openSlot > $closeSlot) {
//                                for ($i = 1; $i <= $closeSlot; $i++) {
//                                    $data[$key]['slot'][] = $i;
//                                }
//                            }
//                            for ($i = $openSlot; $i <= 48; $i++) {
//                                $data[$key]['slot'][] = $i;
//                            }
            $result['openTime'] = $openTime;
            $result['closeTime'] = $closeTime;
        }
        return $result;
    }

    /**
     * 
     * @param type $postValue
     * @return int|string
     * @throws Exception.
     */
    function partyBook($postValue = array()) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                $date = date('Y-m-d', strtotime($date));
                $slotId = '';
                if(isset($time) && !empty($time)) {
                $time = explode('-', $timeSlot);
                if (isset($time[0]) && isset($time[1]) && isset($time[2])) {
                    $minMaradian = $time[2] == '1' ? 'AM' : 'PM';

                    $minhr = strlen($time[0]) == 1 ? '0' . $time[0] : $time[0];

                    $minmin = strlen($time[1]) == 1 ? '0' . $time[1] : $time[1];
                    $openTimeNW = date('H:i', strtotime($minhr . ':' . $minmin . ' ' . $minMaradian));
                    $checkExactSlot = $this->db->query('SELECT iSlotID, tstartFrom FROM slot_master WHERE tstartFrom = \'' . $openTimeNW . '\'')->row_array();
                    if (!empty($checkExactSlot)) {
                        $slotId = isset($checkExactSlot['iSlotID']) ? $checkExactSlot['iSlotID'] : 0;
                        $slotTime = isset($checkExactSlot['tstartFrom']) ? $checkExactSlot['tstartFrom'] : '';
                    } else {
                        //$openSlot = $this->db->query('SELECT iSlotID, tendTo FROM slot_master WHERE tstartFrom <= \'' . $openTimeNW . '\' AND tendTo > \'' . $openTimeNW . '\'')->row_array();
                        $openSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $openTimeNW . '\' order by `iSlotID` desc')->row_array();
                        $slotId = isset($openSlot['iSlotID']) ? $openSlot['iSlotID'] + 1 : 0;
                        $slotTime = isset($openSlot['tendTo']) ? $openSlot['tendTo'] : '';
                    }
                    }
                }
                $ins = array(
                    'iUserID' => $userId,
                    'vCustomerName' => $name,
                    'iLocationID1' => isset($location1) ? $location1 : '',
                    'iLocationID2' => isset($location2) ? $location2 : '',
                    'iLocationID3' => isset($location3) ? $location3 : '',
                    'iRestaurantID1' => isset($rest1) ? $rest1 : '',
                    'iRestaurantID2' => isset($rest2) ? $rest2 : '',
                    'iRestaurantID3' => isset($rest3) ? $rest3 : '',
                    'vPackageType' => isset($package) ? $package : '',
                    'dMinPrice' => $priceMin,
                    'dMaxPrice' => $priceMax,
                    'iSlotID' => isset($time) ? $slotId : $timeSlot,
                    'iNumberOfPeople' => $numberPeople,
                    'dtBookingDate' => $date,
                    'vOcassion' => isset($ocassion) ? $ocassion : '',
                    'tNote' => isset($note) ? $note : '',
                    'eStatus' => 'Pending',
                    'tCreatedAt' => date('Y-m-d H:i:s'),
                    'tModifiedAt' => date('Y-m-d H:i:s')
                );
                $this->db->insert('tbl_book_party', $ins);
                //send sms
                $user = $this->db->query('SELECT vMobileNo, vFirstName, vLastName FROM tbl_user WHERE iUserID IN(' . $userId . ')')->row_array();
                $mobile_msg = "Dear {$user['vFirstName']} {$user['vLastName']},\nYour party enquiry has been received. Our experts are working on your request and shall get back to you shortly.\nCheers,\nHungerMafia";
                $this->load->model('Sms_model', 'sms_m');
                $this->sms_m->destmobileno = $user['vMobileNo'];
                $this->sms_m->msg = $mobile_msg;
                $this->sms_m->Send();
                return 1;
            } return '';
        } catch (Exception $ex) {
            throw new Exception('Error in partyBook function - ' . $ex);
        }
    }

    /*
     * SEARCH RESTAURANT LIST
     */

    function searchRestaurantForBooking($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                $orderBy = 'ORDER BY vRestaurantName ASC';
                $fields = $tbl = $condition = array();
                $tbl = array(
                    'tbl_restaurant AS tr',
                );

                $fields = array(
                    'DISTINCT tr.iRestaurantID AS id',
                    'tr.vRestaurantName AS name',
                    'tl.vLocationName AS location',
                );
                if(!empty($people)) {
                $condition = array(
                    'tr.min_person <= ' . $people,
                    'tr.max_person >= ' . $people,
                );
                }
                
                if (isset($userLat) && isset($userLong) && !empty($userLat) && !empty($userLong)) {
                    $multiplyer = 3959; //in miles
                    $distFields = ' (ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                            . ' * cos( radians( vLat) )'
                            . ' * cos( radians( vLog ) - radians( ' . $userLong . ' ) )'
                            . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( vLat ) ) ) ))'
                            . ' AS distance';
                    $orderBy = ' ORDER BY distance ASC';
                    $condition[] = '(ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                            . ' * cos( radians( vLat) )'
                            . ' * cos( radians( vLog ) - radians( ' . $userLong . ' ) )'
                            . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( vLat ) ) ) )) <= 100';
                }

                if ($distFields != '') {
                    $fields[] = $distFields;
                }
                $condition[] = 'tr.eStatus IN(\'Active\')';
                
                if (!empty($zone)) {
                    if ($zone == 10) {
                        $zoneId = '1,2,3,4,5,6,7,8,9';
                    } else if ($zone == 9) {
                        $zoneId = '1,2,3,4,5,9';
                    } else {
                        $zoneId = $zone;
                    }
                    $condition[] = "tl.iLocZoneID IN($zoneId)";
                }
                
                $fields = implode(',', $fields);
                $tbl = ' FROM ' . implode(',', $tbl);
                $condition = ' WHERE ' . implode(' AND ', $condition);

                $qry = 'SELECT ' . $fields . $tbl . ' LEFT JOIN `tbl_location` AS `tl` ON `tl`.`iLocationID` = `tr`.`iLocationID`' . $condition . $orderBy;

                $row = $this->db->query($qry)->result_array();
                foreach ($row as $k => $row11) {
                    $row[$k]['type'] = 'OUTLET';
                }
                $row1 = array();
                if (!empty($zone)) {
                    if ($zone == 10) {
                        $zoneId = '1,2,3,4,5,6,7,8,9';
                    } else if ($zone == 9) {
                        $zoneId = '1,2,3,4,5,9';
                    } else {
                        $zoneId = $zone;
                    }
                    $qry1 = "select DISTINCT tl.iLocationID AS id,tl.vLocationName AS name FROM tbl_location AS tl WHERE tl.eStatus IN('Active') AND tl.iLocZoneID IN($zoneId)";
                    $row1 = $this->db->query($qry1)->result_array();
                }
                foreach ($row1 as $l => $row21) {
                    $row1[$l]['type'] = 'LOCATION';
                }
                $row = array_merge($row, $row1);
                return $row;
            } return array();
        } catch (Exception $ex) {
            throw new Exception('Error in searchRestaurant function - ' . $ex);
        }
    }

}
