<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Description of login_model
 * @author OpenXcell Technolabs
 */
class Vendor_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_deals';
        date_default_timezone_set('Asia/Calcutta');
    }

    /*
     * TO GET LIST OF OFFERS
     * OR NOT...
     */

    function getAllOffers($restId = '') {
        try {
            if ($restId !== '') {

                $tbl = $fields = $condition = array();

                $tbl = array(
                    'tbl_restaurant AS tr',
                    'tbl_deals AS td'
                );

                $fields = array(
                    'tr.vRestaurantName AS vendorName',
                    'td.iDealID AS offerId',
                    'td.eStatus AS offerStatus',
                    'IF(td.vDealimage = "" , CONCAT("' . IMG_URL . 'deal/default.png"), CONCAT("' . IMG_URL . 'deal/", td.iDealID, "/thumb/", td.vDealImage))  AS offerImage',
                    'td.vOfferText AS offerText',
                    'td.tOfferDetail AS offerDetail',
                    'td.tTermsOfUse AS offerTerms',
                    'td.dtStartDate AS offerStartDate',
                    'DATE_FORMAT(td.dtExpiryDate,"' . MYSQL_DATE_FORMAT . '") AS offerEndDate',
                    'DATE_FORMAT(td.dtExpiryDate,"%l:%i %p") AS offerEndTime',
                    'td.vDealCode AS offerCode',
                    'tr.iMinPerson AS minPer',
                    'tr.iMaxPerson AS maxPer',
                );

                $condition = array(
                    'tr.iRestaurantID IN(td.iRestaurantID)',
                    'td.eStatus IN(\'Active\')',
                    'td.iRestaurantID IN(' . $restId . ')',
                    'CURDATE() between td.dtStartDate and td.dtExpiryDate'
                );

                $tbl = ' FROM ' . implode(',', $tbl);
                $fields = implode(',', $fields);
                $condition = ' WHERE ' . implode(' AND ', $condition);

                $qry = 'SELECT ' . $fields . $tbl . $condition;

                $res = $this->db->query($qry);
                if ($res->num_rows() > 0) {
                    return $res->result_array();
                } return array();
            } return array();
        } catch (Exception $ex) {
            throw new Exception('Error in getAllOffers function - ' . $ex);
        }
    }
    
    function getMinMaxPerson($restId = ''){
        try {
            if ($restId !== '') {

                $tbl = $fields = $condition = array();

                $tbl = array(
                    'tbl_restaurant AS tr'
                );

                $fields = array(
                    'tr.min_person AS minPer',
                    'tr.max_person AS maxPer',
                );

                $condition = array(
                    'tr.iRestaurantID = '. $restId
                );

                $tbl = ' FROM ' . implode(',', $tbl);
                $fields = implode(',', $fields);
                $condition = ' WHERE ' . implode(' AND ', $condition);

                $qry = 'SELECT ' . $fields . $tbl . $condition;
                $res = $this->db->query($qry);
                if ($res->num_rows() > 0) {
                    return $res->result_array();
                } return array();
            } return array();
        } catch (Exception $ex) {
            throw new Exception('Error in getAllOffers function - ' . $ex);
        }
    }

    /*
     * TO DELETE AN OFFER
     */

    function deleteOffer($restId = '', $offerId = '', $needDelete = 0) {
        try {
            if ($offerId != '') {
                if ($needDelete == 1) {
                    $this->db->delete('tbl_deals', array('iRestaurantID' => $restId, 'iDealID' => $offerId));
                    return TRUE;
                } else {
                    $this->db->update('tbl_deals', array('eStatus' => 'DelPending'), array('iRestaurantID' => $restId, 'iDealID' => $offerId));
                    if ($this->db->affected_rows() > 0) {
                        $this->db->insert('tbl_notification', array('iRestaurantID' => $restId, 'iRecordID' => $offerId, 'iActivityID' => 3, 'tCreatedAt' => date('Y-m-d H:i:s', time())));
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                }
            } return array();
        } catch (Exception $ex) {
            throw new Exception('Error in deleteOffer function - ' . $ex);
        }
    }

    /*
     * TO ADD AN OFFER
     */

    function addOffer($postValue = array()) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                //mprd($_FILES);

                /*
                 * TO CHECK OFFER CODE IS ALREADY EXISTS OR NOT..
                 */
                $isExists = $this->db->get_where('tbl_deals', array('vDealCode' => @$offerCode));
                if ($isExists->num_rows() > 0) {
                    return -2;
                } else {
                    $insData = array(
                        'iRestaurantID' => @$vId,
                        'vOfferText' => @$offerText,
                        'tOfferDetail' => @$offerDetail,
                        'tTermsOfUse' => @$offerTerms,
                        'dtStartDate' => @$offerStart,
                        'dtExpiryDate' => @$offerEnd,
                        'vDealCode' => @$offerCode
                    );
                    $this->db->insert('tbl_deals', $insData);
                    $insId = $this->db->insert_id();
                    $this->db->insert('tbl_notification', array('iRestaurantID' => $vId, 'iRecordID' => $insId, 'iActivityID' => 2, 'tCreatedAt' => date('Y-m-d H:i:s', time())));

                    if ($insId > 0) {
                        /*
                         * FILE WILL UPLOAD HERE...
                         */

                        if (isset($_FILES) && !empty($_FILES)) {
                            $PARAM = array(
                                'fType' => 'image',
                                'fLimit' => 20,
                                'fLoc' => array(
                                    'key' => 'deal',
                                    'id' => $insId
                                ),
                                'fThumb' => TRUE,
                                'fCopyText' => FALSE
                            );
                            $this->load->library('fupload', $PARAM);
                            $resp = $this->fupload->fUpload($_FILES, 'offerImage');

                            if (!empty($resp)) {
                                for ($i = 0; $i < count($resp); $i++) {
                                    $this->db->update('tbl_deals', array('vDealimage' => $resp[$i]), array('iDealID' => $insId));
                                }
                            }
                        }

                        return $insId;
                    } else {
                        return 0;
                    }
                }
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in addOffer function - ' . $ex);
        }
    }

    /*
     * SEND REQUEST TO THE ADMIN THAT RESTAURANT IS REQUESTING FOR CHANGE..
     * IN THIS CASE WE WILL SEND THE MAIL TO THE ADMIN..
     */

    function changeForRequest($postValue = array()) {
        try {
            if (!empty($postValue)) {
                extract($postValue);

                if (@$vId != '' && @$requestText != '') {
                    /*
                     * TO GET RESTAURANT NAME..
                     */
                    $res = $this->general_model->getVendorBasicRecordById($vId);
                    $vendorName = $res['vendorName'];
                    $vendorEmail = $res['vendorEmail'];

                    /*
                     * TO GET THE ADMIN EMAIL ADDRESS
                     */
                    $res = $this->db->get_where('tbl_admin', array('eAdminType' => 1));
                    $row = $res->row_array();
                    $adminName = $row['vFirstName'] . ' ' . $row['vLastName'];
                    $adminEmail = $row['vEmail'];
                    //$adminEmail = 'chintan.goswami@openxcelltechnolabs.com';


                    /*
                     * SEND MAIL FUNCTIONALITY...
                     */
                    $mail_subject = 'HungerMafia: Request for change';
                    $this->load->library('maillib');
                    $param = array(
                        '%MAILSUBJECT%' => $mail_subject,
                        '%LOGO_IMAGE%' => $_SERVER['HTTP_HOST'] . '/application/images/hungermafia.png',
                        '%REQUEST_TEXT%' => $requestText,
                        '%VENDOR_NAME%' => $vendorName
                    );
                    $tmplt = DIR_VIEW . 'email/request_change.php';
                    $subject = $mail_subject;
                    $to = $adminEmail;

                    $vendorEmail = 'vendorapp@hungermafia.com';
                    $this->maillib->sendMail($to, $subject, $tmplt, $param, FALSE, array(), array(), array('email' => $vendorEmail, 'name' => $vendorName));

                    return 1;
                } return 0;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in changeForRequest function - ' . $ex);
        }
    }

    /*
     * TO GET THE LIST OF SPCIALTY OF THE EXISTING RESTAURANT
     */

    function getSpecialty($vId = '') {
        try {
            if ($vId != '') {
                $this->db->where_not_in('eStatus', array('reject'));
                $res = $this->db->get_where('tbl_restaurant_specialty', array('iRestaurantID' => $vId));

                return $res->result_array();
            } return array();
        } catch (Exception $ex) {
            throw new Exception('Erro rin getSpecialty fucntion - ' . $ex);
        }
    }

    /*
     * TO ADD NEW SPECIALTY
     */

    function addSpecialty($postValue = array()) {
        try {
            if (!empty($postValue)) {
                extract($postValue);

                $hasRec = $this->db->get_where('tbl_restaurant_specialty', array('iRestaurantID' => $vId, 'vSpecialtyName' => $specialtyName, 'eSpecialtyType' => $specialtyType));

                if ($hasRec->num_rows() <= 0) {
                    $ins = array(
                        'iRestaurantID' => $vId,
                        'vSpecialtyName' => $specialtyName,
                        'eSpecialtyType' => $specialtyType
                    );
                    $this->db->insert('tbl_restaurant_specialty', $ins);
                    $insId = $this->db->insert_id();
                    $this->db->insert('tbl_notification', array('iRestaurantID' => $vId, 'iRecordID' => $insId, 'iActivityID' => 1, 'tCreatedAt' => date('Y-m-d H:i:s', time())));
                    return $insId;
                } else {
                    return -2;
                }
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in addSpecialty function - ' . $ex);
        }
    }

    /*
     * TO DELETE THE SPECIALTY
     */

    function deleteSpecialty($id = '', $needDelete = 0) {
        try {
            if ($id != '') {
                if ($needDelete == 1) {
                    $this->db->delete('tbl_restaurant_specialty', array('iRestSpecialtyID' => $id));
                    return 1;
                } else {
                    $this->db->update('tbl_restaurant_specialty', array('eStatus' => 'delpending'), array('iRestSpecialtyID' => $id));
                    $restId = $this->db->get_where('tbl_restaurant_specialty', array('iRestSpecialtyID' => $id))->row_array()['iRestaurantID'];
                    $this->db->insert('tbl_notification', array('iRestaurantID' => $restId, 'iRecordID' => $id, 'iActivityID' => 4, 'tCreatedAt' => date('Y-m-d H:i:s', time())));
                    return 1;
                }
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in deleteSpecialty function - ' . $ex);
        }
    }

    /*
     * TO GET THE LIST OF TABLE REQUEST
     */

    function tableRequestList($vendorId = '', $listType = '') {
        try {
            if ($vendorId !== '') {

                if ($listType == '') {
                    $returnArr = array(
                        'pending' => $this->tableRequestList($vendorId, 'pending'),
                        'acceptwait' => $this->tableRequestList($vendorId, 'accept\',\'waiting'),
                        'reject' => $this->tableRequestList($vendorId, 'reject')
                    );

                    return $returnArr;
                } else {
                    $fields = array(
                        'ttb.iTableBookID AS bookedId',
                        'ttb.iSlotID AS slotId',
                        '(SELECT tStartFrom FROM slot_master WHERE iSlotID IN(ttb.iSlotID)) AS slotTime',
                        'ttb.iWaitTime AS waitingTime',
                        'ttb.eBookingStatus AS bookingStatus',
                        'ttb.iPersonTotal AS totalPerson',
                        'DATE_FORMAT(ttb.tDateTime,\'' . MYSQL_DATE_FORMAT . '\') AS bookedDate',
                        'tu.iUserID AS userId',
                        'CONCAT(tu.vFirstName,\' \',tu.vLastName) AS userName',
                        'IF(tu.vProfilePicture = "" , CONCAT("' . USER_IMG . 'dM.png"), CONCAT("' . USER_IMG . '", tu.iUserID, "/thumb/", tu.vProfilePicture))  AS profilePic',
                    );

                    $tbl = array(
                        'tbl_user AS tu',
                        'tbl_table_book AS ttb'
                    );

                    $condition = array(
                        'tu.iUserID IN(ttb.iUserID)',
                        'ttb.iRestaurantID IN(' . $vendorId . ')',
                        'ttb.eBookingStatus IN(\'' . $listType . '\')',
                        'ttb.tDateTime >= CURDATE()',
                    );

                    $fields = implode(',', $fields);
                    $tbl = ' FROM ' . implode(',', $tbl);
                    $condition = ' WHERE ' . implode(' AND ', $condition);

                    $orderBy = ' ORDER BY ttb.tDateTime ASC ';

                    $qry = 'SELECT ' . $fields . $tbl . $condition . $orderBy;
                       
                    $row = $this->db->query($qry)->result_array();

                    $breakFast = range(25, 48);//range(13, 24);
                    $lunch = range(49, 72);//range(25, 36);
                    $dinner1 = range(73, 96);//range(37, 48);
                    $dinner2 = range(1, 24);
                    $dinner = array_merge($dinner1, $dinner2);

                    for ($i = 0; $i < count($row); $i++) {
                        foreach ($row[$i] as $k => $v) {
                            if (in_array($k, array('bookedId', 'slotId', 'waitingTime', 'totalPerson'))) {
                                $v = (int) $v;
                                $row[$i][$k] = $v;
                                if ($k === 'slotId') {
                                    if (in_array($v, $breakFast)) {
                                        $row[$i]['timeType'] = 'Breakfast';
                                    } else if (in_array($v, $lunch)) {
                                        $row[$i]['timeType'] = 'Lunch';
                                    } else {
                                        $row[$i]['timeType'] = 'Dinner';
                                    }
                                }
                            }
                        }
                    }

                    return $row;
                }
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in tableRequest function - ' . $ex);
        }
    }

    /*
     * UPDATE THE TABLE REQUEST STATUS
     */

    function updateTableRequest($postValue = array()) {
        try {
            if (!empty($postValue)) {
                extract($postValue);

                $pushMSG = 'Your table request ';

                $waitTime = 0;
                switch ($requestStatus) {
                    case 'Accept' :
                    case 'Reject':
                        $pushMSG .= 'has been ' . $requestStatus . 'ed';
                        $bookingStatus = $requestStatus;
                        break;

                    case 'Waiting':
                        $pushMSG = 'You have to wait ' . $requestTime . ' minutes';
                        $bookingStatus = $requestStatus;
                        $waitTime = @$requestTime;
                        break;
                }

                $this->db->update('tbl_table_book', array('eBookingStatus' => $bookingStatus, 'iWaitTime' => $waitTime), array('iTableBookID' => $bookingId));

                /*
                 * HERE PUSH NOTIFICATION WILL SEND IT TO USER APP
                 */
                $userId = $this->db->query('SELECT iUserID FROM tbl_table_book WHERE iTableBookID IN(' . $bookingId . ')')->row_array()['iUserID'];
                $isNotify = $this->db->query('SELECT isNotify FROM tbl_user WHERE iUserID IN(' . $userId . ')')->row_array()['isNotify'];


                $record = $this->db->query('SELECT ePlatform, vDeviceToken FROM tbl_user WHERE iUserID IN(' . $userId . ')')->row_array();

                $restaurantName = $this->db->query('SELECT vRestaurantName FROM tbl_restaurant WHERE iRestaurantID IN(' . $vId . ')')->row_array()['vRestaurantName'];
                $this->load->library('pushnotify');

                $osType = $record['ePlatform'] == 'ios' ? 2 : 1;
                $deviceToken = $record['vDeviceToken'];

                $pushMSG .= ' for ' . $restaurantName;

                /*
                 * SAVE USER NOTIFICATION
                 */
                $this->general_model->saveUserNotification($userId, $pushMSG, $vId, $bookingId, 'table');



                if ($deviceToken != '') {
                    /*
                     * IF ANY NOTIFICATION SEND FROM THE VENDOR APPLICATION SIDE THAN WE HAVE TO SEND BADGE 
                     * VALUE FROM THE OUR END... - BUT IT JUST FOR USER NOTIFICATION SIDE...
                     */
                    $BADGECOUNT = $this->db->query('SELECT iNotifyCount FROM tbl_user WHERE iUserID IN(' . $userId . ')')->row_array()['iNotifyCount'];
                    if ($isNotify == 'yes') {
                        $this->pushnotify->sendIt($osType, $deviceToken, $pushMSG, 1, array('type' => 'table', 'id' => $bookingId), $BADGECOUNT);
                    }
                }

                return 1;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in updateTableRequest function - ' . $ex);
        }
    }

}

?>
