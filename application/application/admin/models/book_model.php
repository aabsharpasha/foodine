<?php

class Book_model extends CI_Model {

    var $table, $glob;

    function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Calcutta');
        $this->glob = unserialize(RESTAURANT_OPEN_CLOSE_DAYS);
    }

    function slot_val($restaurant_id = '') {
        $arry = array();
        if ($restaurant_id != '') {
            $row = $this->db->get_where('tbl_restaurant', array('iRestaurantID' => $restaurant_id))->row_array();
            if (isset($row['iMinTime']) && $row['iMinTime'] !== '') {
                if (isset($row['iMaxTime']) && $row['iMaxTime'] !== '') {
                    $minTime = explode('-', $row['iMinTime']);
                    $maxTime = explode('-', $row['iMaxTime']);

                    if (isset($minTime[0]) && isset($minTime[1]) && isset($minTime[2])) {
                        $minMaradian = $minTime[2] == '1' ? 'AM' : 'PM';
                        $maxMaradian = $maxTime[2] == '1' ? 'AM' : 'PM';

                        $minhr = strlen($minTime[0]) == 1 ? '0' . $minTime[0] : $minTime[0];
                        $maxhr = strlen($maxTime[0]) == 1 ? '0' . $maxTime[0] : $maxTime[0];

                        $minmin = strlen($minTime[1]) == 1 ? '0' . $minTime[1] : $minTime[1];
                        $maxmin = strlen($maxTime[1]) == 1 ? '0' . $maxTime[1] : $maxTime[1];

                        $openCloseTime = $minhr . ':' . $minmin . ' ' . $minMaradian . ' to ' . $maxhr . ':' . $maxmin . ' ' . $maxMaradian;
                        $openCloseTimeValue = $minhr . ':' . $minmin . ':' . $minMaradian . ' to ' . $maxhr . ':' . $maxmin . ':' . $maxMaradian;

                        $openTimeNW = date('H:i', strtotime($minhr . ':' . $minmin . ' ' . $minMaradian));
                        $closeTimeNW = date('H:i', strtotime($maxhr . ':' . $maxmin . ' ' . $maxMaradian));

                        //$openSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $openTimeNW . '\' AND tendTo > \'' . $openTimeNW . '\'')->row_array();
                        $openSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $openTimeNW . '\' order by `iSlotID` desc')->row_array();
                        $openSlotTendTo = $this->db->query('SELECT tendTo FROM slot_master WHERE iSlotID IN(' . @$openSlot['iSlotID'] . ')')->row_array()['tendTo'];

                        //echo $this->db->last_query();
                        $openSlot = (int) @$openSlot['iSlotID'];

                        //$closeSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $closeTimeNW . '\' AND tendTo > \'' . $closeTimeNW . '\'')->row_array();
                        $closeSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $closeTimeNW . '\'  order by `iSlotID` desc')->row_array();
                        //echo $this->db->last_query();
                        $closeSlot = (int) @$closeSlot['iSlotID'];

                        $currentSlot = $this->getCurrentSlot();

                        $arry['openSlot'] = $openSlot;
                        $arry['closeSlot'] = $closeSlot;
                        $arry['currentSlot'] = $currentSlot;
                    }
                }
            }
        } return $arry;
    }

    function getCurrentSlot() {
        $serverTime = time();
        $currentTimeNW = date('H:i', $serverTime);
        //$currentSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $currentTimeNW . '\' AND tendTo > \'' . $currentTimeNW . '\'')->row_array();
        $currentSlot = $this->db->query('SELECT iSlotID FROM slot_master WHERE tstartFrom <= \'' . $currentTimeNW . '\' order by `iSlotID` desc')->row_array();
        $currentSlot = (int) @$currentSlot['iSlotID'];

        return $currentSlot;
    }

    function time_val($slots = array(), $date='') {
        $return = array();
        if (!empty($slots)) {
            extract($slots);


            if ($openSlot < $closeSlot) {
                $time_interval = $this->db->query('SELECT tStartFrom, iSlotID FROM slot_master WHERE iSlotID >= "' . $openSlot . '" AND iSlotID <= "' . $closeSlot . '"')->result_array();
            } else {
                if(!empty($date) && date("Y-m-d",strtotime($date)) == date("Y-m-d")){
                    $startSlot = $currentSlot >= $openSlot ? $currentSlot : $openSlot;
                }else{
                    $startSlot=$openSlot;
                }
                $union1 = 'SELECT tStartFrom, iSlotID FROM slot_master WHERE iSlotID BETWEEN ' . $startSlot . ' AND 96';
                $union2 = 'SELECT tStartFrom, iSlotID FROM slot_master WHERE iSlotID BETWEEN 1 AND ' . $closeSlot;
                $time_interval = $this->db->query('(' . $union1 . ') UNION (' . $union2 . ')')->result_array();
            }
            foreach ($time_interval as $val) {
                $return[] = array(
                    'id' => $val['iSlotID'],
                    'val' => date('h:i A', strtotime($val['tStartFrom']))
                );
            }
        }
        return $return;
    }

    function offers($restaurant_id = 0) {
        if ($restaurant_id != '') {
            $tbl[] = 'tbl_restaurant AS tr';
            $tbl[] = 'tbl_deals AS td';

            $fields[] = 'tr.vRestaurantName AS restaurantName';
            $fields[] = 'CONCAT("' . BASEURL . 'images/restaurant/", IF(tr.vRestaurantLogo = \'\', "default.png", CONCAT(tr.iRestaurantID,\'/\',tr.vRestaurantLogo)) ) AS restaurantImage';
            $fields[] = 'td.vOfferText AS offerText';
            $fields[] = 'td.iDealID AS offerId';
            $fields[] = 'td.tOfferDetail AS offerDetail';
            $fields[] = 'td.tTermsOfUse AS offerTerms';
            $fields[] = 'td.vDaysAllow AS daysAllow';
            $fields[] = 'td.vDealCode AS dealCode';
            $fields[] = 'DATE_FORMAT(td.dtStartDate,"' . MYSQL_DATE_FORMAT . '") AS offerStartDate';
            $fields[] = 'DATE_FORMAT(td.dtExpiryDate,"' . MYSQL_DATE_FORMAT . '") AS offerEndDate';

            $condition[] = 'td.eStatus = \'Active\'';
            $condition[] = 'td.iRestaurantID = tr.iRestaurantID';
            $condition[] = 'CURDATE() between td.dtStartDate and td.dtExpiryDate';
            $condition[] = 'td.iRestaurantID = \'' . $restaurant_id . '\'';

            $tbl = implode(',', $tbl);
            $fields = implode(',', $fields);
            $condition = implode(' AND ', $condition);

            $qry = 'SELECT ' . $fields . ' FROM ' . $tbl . ' WHERE ' . $condition;
            $res = $this->db->query($qry);

            $row = $res->result_array();
            $currentDay = date('D', time());

            foreach ($row as $key => $val) {
                $days = array();
                if ($val['daysAllow'] != '') {
                    $daysAllow = explode(',', $val['daysAllow']);
                    //mprd($openDays);

                    for ($i = 0; $i < count($daysAllow); $i++) {
                        $days[] = $this->glob[$daysAllow[$i]];
                    }
                }
                if (!in_array(strtoupper($currentDay), $days)) {
                    unset($row[$key]);
                } else {
                    $row[$key]['daysAllowValue'] = $days;
                }
            }

            return $row;
        } return array();
    }

    function book_table($postValue = array()) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                //print_r($postValue);
                // exit;

                $qry = 'SELECT iTableBookID FROM tbl_table_book'
                        . ' WHERE iRestaurantID IN(' . $restaurant_id . ') '
                        . ' AND iSlotID IN(' . $slot_id . ') '
                        . ' AND iUserID IN(' . $user_id . ') '
                        . ' AND tDateTime >= \'' . date('Y-m-d', strtotime($book_date)) . '\' ';

                $res = $this->db->query($qry);

                if ($res->num_rows() <= 0) {
                    $unique_code = $this->_generateUniqueCode($restaurant_id);
                    $ins = array(
                        'iRestaurantID' => $restaurant_id,
                        'unique_code' => $unique_code,
                        'iUserID' => $user_id,
                        'iSlotID' => $slot_id,
                        'iWaitTime' => 0,
                        'iPersonTotal' => $total_person,
                        'iDealID' => (isset($offerId) ? $offerId : 0),
                        'tDateTime' => date('Y-m-d', strtotime($book_date)),
                        'tCreatedAt' => date('Y-m-d H:i:s', time())
                    );
                    $this->db->insert('tbl_table_book', $ins);
                    $insId = $this->db->insert_id();
                    //exit;
                    //$this->general_model->addUserPointValue($userId, 3, $insId);

                    /* SEND SMS */
                    $rest = $this->db->query('SELECT vContactNo FROM tbl_restaurant WHERE iRestaurantID = "' . $restaurant_id . '"')->row_array();
                    $mobile_no = $rest['vContactNo'];

                    if ($mobile_no != '') {

                        $mobiles = explode(',', $mobile_no);

                        foreach ($mobiles as $key => $val) {
                            if (strlen(trim($val)) == 10)
                                $mobiles[$key] = trim($val); //'91' . trim($val); 
                            else
                                unset($mobiles[$key]);
                        }
                        $mobiles = array_values($mobiles);
                        //var_dump($mobiles);

                        $mobile_msg = 'New booking request: ' . $unique_code
                                . ' from HungerMafia. '
                                . 'Reply MAFIA YES ' . $unique_code . ' to Confirm, '
                                . 'MAFIA NO ' . $unique_code . ' to Reject '
                                . 'and MAFIA WAIT 15 ' . $unique_code . ' to waitlist.';
                        //var_dump($mobile_msg);
                        if (!empty($mobiles)) {
                            //$this->load->library('smslane');
                            //$this->smslane->send($mobiles, $mobile_msg);
                            foreach ($mobiles as $mobile) {
                                $this->load->model('Sms_model', 'sms_m');
                                $this->sms_m->destmobileno = $mobile;
                                $this->sms_m->msg = $mobile_msg;
                                $this->sms_m->Send();
                            }
                            //exit;
                        }
                    }

                    /*
                     * HERE PUSH NOTIFICATION WILL SEND IT TO VENDOR APP
                     */
                    $record = $this->db->query('SELECT ePlatform, vDeviceToken FROM tbl_admin WHERE iRestaurantID IN(' . $restaurant_id . ')')->row_array();

                    if (isset($record['ePlatform']) && isset($record['vDeviceToken'])) {
                        $this->load->library('pushnotify');

                        $osType = $record['ePlatform'] == 'ios' ? 2 : 1;
                        $deviceToken = $record['vDeviceToken'];

                        $mesg = 'You have a new booking request.';

                        if ($deviceToken != '') {
                            $this->pushnotify->sendIt($osType, $deviceToken, $mesg, 2);
                        }
                    }

                    return $insId;
                } return -2;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in bookTable function - ' . $ex);
        }
    }

    /* Generate Unique Code */

    private function _generateUniqueCode($rest_id) {
        try {
            $unique_code = 'HM';
            $rest_name = $this->db->get_where('tbl_restaurant', array('iRestaurantID' => $rest_id))->row_array();
            $rest_name = explode(' ', $rest_name['vRestaurantName']);
            if (count($rest_name) > 1) {
                $unique_code .= substr($rest_name[0], 0, 1) . substr($rest_name[1], 0, 1);
            } else {
                $unique_code .= substr($rest_name[0], 0, 2);
            }
            $unique_code .= random_string('numeric', 3);

            return $unique_code;
        } catch (Exception $ex) {
            throw new Exception(__CLASS__ . '::' . __FUNCTION__ . '::' . $ex);
        }
    }

    function getUsers() {
        $res = $this->db->query("SELECT "
                        . 'IF( vFullName = "", CONCAT(u.vFirstName, " ", u.vLastName), vFullName) AS vName, '
                        //. "CONCAT(vFirstName,' ',vLastName) as vName, "
                        . "vUserName, vEmail, vMobileNo, "
                        . "vProfilePicture, eSubscriptionType, "
                        . "tCreatedAt as tCreatedAt1, "
                        . "(SELECT COUNT(*) from tbl_user_restaurant_favorite as uf where uf.iUserID = u.iUserID) as total_favorite , "
                        . "(SELECT SUM(trp.iRewardPoint) FROM tbl_user_collect AS tuc, tbl_reward_point AS trp WHERE tuc.iRewardPointId = trp.iRewardPointID AND tuc.iUserID = u.iUserID) AS total_points, "
                        . "eStatus, ePlatform, iUserID, "
                        . "DATE_FORMAT(tCreatedAt, '%d %b %Y %h:%i %p') as tCreatedAt, "
                        . "iUserID AS DT_RowId "
                        . "FROM tbl_user u ")->result_array();
        return $res;
    }

    function getRestaurant() {
        $qry = "SELECT "
                . "vRestaurantName, "
                . "vEmail, "
                . "vContactNo, "
                . "tAddress2 AS tAddress, "
                . "vRestaurantLogo, "
                //. "(SELECT COUNT(*) FROM tbl_user_checkin AS ci WHERE ci.iRestaurantID IN(r.iRestaurantID) ) AS total_checkin,  "
                //. "(SELECT COUNT(*) FROM tbl_restaurant_image AS rp WHERE rp.iRestaurantID IN(r.iRestaurantID) ) AS total_picture, "
                //. "(SELECT COUNT(*) FROM tbl_restaurant_review AS trr WHERE trr.iRestaurantID IN(r.iRestaurantID) ) AS total_review, "
                //. "(SELECT COUNT(*) FROM tbl_restaurant_like_dislike AS trld WHERE trld.iRestaurantID IN(r.iRestaurantID) AND trld.eLikeDislikeValue IN('like')) AS total_like, "
                //. "(SELECT COUNT(*) FROM tbl_restaurant_like_dislike AS trld WHERE trld.iRestaurantID IN(r.iRestaurantID) AND trld.eLikeDislikeValue IN('dislike')) AS total_dislike, "
                //. "(SELECT COUNT(*) FROM tbl_restaurant_menu_image AS rp1 WHERE rp1.iRestaurantID IN(r.iRestaurantID) ) AS total_menu, "
                //. "(SELECT TRUNCATE(AVG(tur.iRateValue), 2) FROM `tbl_user_ratting` AS tur WHERE tur.iRestaurantID IN(r.iRestaurantID) AND tur.iRateValue <> 0 GROUP BY tur.iRestaurantID) AS total_rate, "
                //. "(SELECT COUNT(tur.iRateValue) FROM `tbl_user_ratting` AS tur WHERE tur.iRestaurantID IN(r.iRestaurantID) GROUP BY tur.iRestaurantID) AS total_review, "
                . "eStatus, "
                . "vCityName, "
                . "vStateName, "
                . "vCountryName, "
                . "iRestaurantID, "
                . "iRestaurantID AS DT_RowId "
                . "FROM tbl_restaurant as r "
                . "WHERE r.eStatus = 'Active' "
                . "AND r.allow_book = 'yes'";

        return $this->db->query($qry)->result_array();
    }

}

?>
