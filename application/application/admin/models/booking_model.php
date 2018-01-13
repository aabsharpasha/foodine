<?php

class Booking_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_table_book';
        $this->load->library('DatatablesHelper');
    }

    /*
     * GET ALL THE RECORDS FROM THE DEALS TABLE...
     */

    function getBookingDataAll() {
        $this->db->from($this->table);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    /*
     * GET THE RECORD BY ID
     */

    function getBookingDataById($iDealID) {
        $result = $this->db->get_where($this->table, array('iTableBookID' => $iDealID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    /*
     * GET PAGINATION RECORDS TO DISPLAY THE TABLE...
     */

    function get_paginationresult($dtdb = TRUE) {
        $extra_condition = '';

        $iRestaurantID = (int) $this->session->userdata('iRestaurantID');
        if ($iRestaurantID != 0) {
            $extra_condition .= ' AND tb.iRestaurantID = "' . $iRestaurantID . '"';
        }

        $table = $fields = $qry = '';
        $condition = array();

        $table = 'tbl_table_book AS tb, tbl_restaurant AS tr, tbl_location AS tl,tbl_user as tuser';

        $fields .= 'vRestaurantName';
        $fields .= ', tAddress2 as address_line';
        $fields .= ', tl.vLocationName AS location_name';
        $fields .= ', tb.vUserName AS vFullName';
        $fields .= ', tb.vMobileNo AS userContact';
        //$fields .= ', IF( (SELECT vFullName FROM tbl_user tu WHERE tu.iUserID IN(tb.iUserID)) = "",(SELECT CONCAT(tu.vFirstName," ",tu.vLastName) FROM tbl_user tu WHERE tu.iUserID IN(tb.iUserID)),(SELECT vFullName FROM tbl_user tu WHERE tu.iUserID IN(tb.iUserID))) AS vFullName';
        //$fields .= ', (SELECT tu.vMobileNo FROM tbl_user tu WHERE tu.iUserID IN(tb.iUserID)) AS userContact';
//        $fields .= ', tu.vMobileNumber AS restaurantContact';
        $fields .= ', tb.eVisited AS eVisited';
        $fields .= ', iPersonTotal AS total_person ';
        $fields .= ', DATE_FORMAT(tb.tDateTime , "%d/%c/%Y %H:%i:%s") as dtBookingDate_sort';
        $fields .= ', (SELECT vOfferText FROM tbl_deals td WHERE td.vDealCode like tb.appliedOfferCode) AS offer_name';
        $fields .= ', iTableBookID AS iBookingID';
        $fields .= ', unique_code AS BookingID';
        $fields .= ', iTableBookID AS DT_RowId ';
        $fields .= ", DATE_FORMAT(tb.tCreatedAt , '%d/%c/%Y %H:%i:%s') AS bookingCaptureDate";
        /* $fields .= ', vRestaurantName';
          $fields .= ', vFullName'; */
        $fields .= ', eBookingStatus';
        $fields .= ', iWaitTime';
        $fields .= ", case 
                when tb.ePlatform ='web' then 'Web'
                when tb.ePlatform ='ios' then 'iOS'
                when tb.ePlatform ='android' then 'Android'
                else 'App'
              end as platform";
        $fields .= ', IF((tuser.dtDOB = tb.tDateTime and tuser.dtAnniversary = tb.tDateTime), "Birthday & Anniversary", (IF ((tuser.dtDOB = tb.tDateTime),"Birthday",(IF ((tuser.dtAnniversary = tb.tDateTime),"Anniversary","N/A"))))) AS occasion';
        $fields .= ',  tuser.iUserID as iUserID, tuser.dtDOB as dtDOB, tuser.dtAnniversary as dtAnniversary, tb.tDateTime as tDateTime';
        $condition[] = 'tb.iRestaurantID = tr.iRestaurantID';
        $condition[] = 'tr.iLocationID  = tl.iLocationID';
        $condition[] = 'tb.iUserID = tuser.iUserID';
        $condition[] = 'tb.tDateTime >= CURDATE()';
        $condition[] = 'tb.isDeleted IN(\'no\')';

        $condition = implode(' AND ', $condition) . $extra_condition;

        $qry .= "SELECT " . $fields . " FROM " . $table . " WHERE " . $condition;
        //echo $qry; exit;
        $data = $dtdb ? $this->datatableshelper->query($qry) : $this->db->query($qry)->result_array();
        return $data;
    }

    function get_paginationhistoryresult($dtdb = TRUE) {
        $extra_condition = '';

        $iRestaurantID = (int) $this->session->userdata('iRestaurantID');
        if ($iRestaurantID != 0) {
            $extra_condition .= ' AND tb.iRestaurantID = "' . $iRestaurantID . '"';
        }

        $table = $fields = $qry = '';
        $condition = array();

        $table = 'tbl_table_book AS tb, tbl_restaurant AS tr, tbl_location AS tl';

        $fields .= 'vRestaurantName';
        $fields .= ', tl.vLocationName AS location_name';
        $fields .= ', tAddress2 as address_line';
        $fields .= ", CONCAT(IF(vPrimRestManagerPhone<>'0',CONCAT(vPrimRestManagerPhone,', '),''), IF(vSecRestManagerPhone<>'0',CONCAT(vSecRestManagerPhone,', '),''),IF(vThirdRestManagerPhone<>'0',vThirdRestManagerPhone,'')) AS managerPhone";
        $fields .= ', IF( (SELECT vFullName FROM tbl_user tu WHERE tu.iUserID IN(tb.iUserID)) = "",(SELECT CONCAT(tu.vFirstName," ",tu.vLastName) FROM tbl_user tu WHERE tu.iUserID IN(tb.iUserID)),(SELECT vFullName FROM tbl_user tu WHERE tu.iUserID IN(tb.iUserID))) AS vFullName';
        $fields .= ', unique_code AS unique_code';
//        $fields .= ', (SELECT tu.vMobileNo FROM tbl_user tu WHERE tu.iUserID IN(tb.iUserID)) AS contact_number';
        $fields .= ', tb.vMobileNo AS contact_number';
        $fields .= ', iPersonTotal AS total_person ';
        $fields .= ', CONCAT(DATE_FORMAT(tb.tDateTime , "%d/%c/%Y"), " ",(SELECT DATE_FORMAT(tstartFrom , "%H:%i:%s")  FROM slot_master sm WHERE sm.iSlotID IN(tb.iSlotID))) as dtBookingDate_sort';
        $fields .= ', IF( iDealID = 0, "N/A", (SELECT vOfferText FROM tbl_deals td WHERE td.iDealID IN(tb.iDealID)) ) AS offer_name';
        $fields .= ', iTableBookID AS iBookingID';
        $fields .= ', unique_code AS BookingID';
        $fields .= ", DATE_FORMAT(tb.tCreatedAt , '%d/%c/%Y %H:%i:%s') AS bookingCaptureDate";
        $fields .= ", tb.tCreatedAt AS tCreatedAt";
        $fields .= ', eBookingStatus';
        $fields .= ', tb.eVisited AS eVisited';
        $fields .= ", case 
                when tb.ePlatform ='web' then 'Web'
                when tb.ePlatform ='ios' then 'iOS'
                when tb.ePlatform ='android' then 'Android'
                else 'App'
              end as platform";

        $condition[] = 'tb.iRestaurantID = tr.iRestaurantID';
        $condition[] = 'tr.iLocationID  = tl.iLocationID';
        //$condition[] = 'tb.tDateTime >= CURDATE()';

        $condition = implode(' AND ', $condition) . $extra_condition;

        $qry .= "SELECT " . $fields . " FROM " . $table . " WHERE " . $condition;

        $data = $dtdb ? $this->datatableshelper->query($qry) : $this->db->query($qry)->result_array();

        return $data;
    }

    function getPaginatePartyRequests($dtdb = TRUE) {
        $extra_condition = '';
        $iRestaurantID = (int) $this->session->userdata('iRestaurantID');
        if ($iRestaurantID != 0) {
            $extra_condition .= ' AND tr1.iRestaurantID = "' . $iRestaurantID . '"';
        }
        $fields[] = 'tbp.vCustomerName AS vCustomerName';
        $fields[] = 'IFNULL(CONCAT(tu.vFirstName," ",tu.vLastName),IFNULL(tu.vFullName,"")) AS accountName';
        $fields[] = 'tu.vMobileNo AS mobile';
        $fields[] = 'tbp.iNumberOfPeople AS iNumberOfPeople';
        $fields[] = 'tbp.dMinPrice AS dMinPrice';
        $fields[] = 'tbp.dMaxPrice AS dMaxPrice';

        $fields[] = 'tl1.vLocationName AS vLocationName1';
        $fields[]   = 'tbp.iRestaurantID1 AS iRestaurantID1';
       $fields[]   = 'tbp.iRestaurantID2 AS iRestaurantID2';
        $fields[] = 'tr1.vRestaurantName AS vRestaurantName1';
//        $fields[]   = 'tr2.vRestaurantName AS vRestaurantName2';
//        $fields[]   = 'tr3.vRestaurantName AS vRestaurantName3';
        $fields[] = 'tbp.vOcassion AS vOcassion';
        $fields[] = 'tbp.tCreatedAt AS tCreatedAt';
        $fields[] = 'DATE_FORMAT(FROM_UNIXTIME(CAST(dtBookingdate/1000 as UNSIGNED)), \'%d %b, %Y\') AS dtBookingDate';
        $fields[] = 'DATE_FORMAT(FROM_UNIXTIME(CAST(dtBookingdate/1000 as UNSIGNED)), \'%h:%i %p\') AS timeSlot';
        $fields[] = 'tbp.eStatus AS eStatus';
        $fields[] = 'DATE_FORMAT(tbp.tCreatedAt , \'%d %b, %Y\')  AS createdDate';
        $fields[] = 'DATE_FORMAT(tbp.dtBookingDate , \'%d %b, %Y\')  AS bookingDate';

        $fields[] = 'tbp.iBookPartyID AS iBookPartyID';
        $fields[] = 'tbp.iBookPartyID AS DT_RowId';

        $fieldStr = implode(", ", $fields);

        $tl1Join = "LEFT JOIN tbl_location AS tl1 ON tbp.iLocationID1=tl1.iLocationID";
        $tl2Join = "LEFT JOIN tbl_location AS tl2 ON tbp.iLocationID2=tl2.iLocationID";
        $tl3Join = "LEFT JOIN tbl_location AS tl3 ON tbp.iLocationID3=tl3.iLocationID";
        $tr1Join = "LEFT JOIN tbl_restaurant AS tr1 ON tbp.iRestaurantID1=tr1.iRestaurantID";
        $tr2Join = "LEFT JOIN tbl_restaurant AS tr2 ON tbp.iRestaurantID2=tr2.iRestaurantID";
        $tr3Join = "LEFT JOIN tbl_restaurant AS tr3 ON tbp.iRestaurantID3=tr3.iRestaurantID";
        $tr3Join = "LEFT JOIN tbl_user AS tu ON tbp.iUserID=tu.iUserID";
        $smJoin = "LEFT JOIN slot_master AS sm ON tbp.iSlotID=sm.iSlotID";

        $qry = "SELECT " . $fieldStr . " FROM tbl_book_party AS tbp $tl1Join $tl2Join $tl3Join $tr1Join $tr2Join $tr3Join $smJoin WHERE tbp.eStatus<>'Deleted' $extra_condition";

        $data = $dtdb ? $this->datatableshelper->query($qry) : $this->db->query($qry)->result_array();

        return $data;
    }


    function getPaginateBanquetRequests($dtdb = TRUE) {
        $extra_condition = '';
        $iRestaurantID = (int) $this->session->userdata('iRestaurantID');
        if ($iRestaurantID != 0) {
            $extra_condition .= ' AND tr1.iRestaurantID = "' . $iRestaurantID . '"';
        }
        $fields[] = 'tbp.name AS vCustomerName';
       // $fields[] = 'IFNULL(CONCAT(tu.vFirstName," ",tu.vLastName),IFNULL(tu.vFullName,"")) AS accountName';
        $fields[] = 'tbp.mobile AS mobile';
       $fields[] = 'tbp.email AS email';
       // $fields[] = 'tbp.dMinPrice AS dMinPrice';
        //$fields[] = 'tbp.dMaxPrice AS dMaxPrice';

//         $fields[] = 'tl1.vLocationName AS vLocationName1';
//         $fields[]   = 'tbp.iRestaurantID1 AS iRestaurantID1';
//        $fields[]   = 'tbp.iRestaurantID2 AS iRestaurantID2';
//         $fields[] = 'tr1.vRestaurantName AS vRestaurantName1';
// //        $fields[]   = 'tr2.vRestaurantName AS vRestaurantName2';
// //        $fields[]   = 'tr3.vRestaurantName AS vRestaurantName3';
//         $fields[] = 'tbp.vOcassion AS vOcassion';
        $fields[] = 'tbp.created_date AS tCreatedAt';
        // $fields[] = 'DATE_FORMAT(FROM_UNIXTIME(CAST(dtBookingdate/1000 as UNSIGNED)), \'%d %b, %Y\') AS dtBookingDate';
        // $fields[] = 'DATE_FORMAT(FROM_UNIXTIME(CAST(dtBookingdate/1000 as UNSIGNED)), \'%h:%i %p\') AS timeSlot';
        // $fields[] = 'tbp.eStatus AS eStatus';
        // $fields[] = 'DATE_FORMAT(tbp.tCreatedAt , \'%d %b, %Y\')  AS createdDate';
        // $fields[] = 'DATE_FORMAT(tbp.dtBookingDate , \'%d %b, %Y\')  AS bookingDate';

        // $fields[] = 'tbp.iBookPartyID AS iBookPartyID';
        // $fields[] = 'tbp.iBookPartyID AS DT_RowId';

        $fieldStr = implode(", ", $fields);

        $qry = "SELECT ".$fieldStr." FROM tbl_banquet_enquiry as tbp";

        $data = $dtdb ? $this->datatableshelper->query($qry) : $this->db->query($qry)->result_array();

        return $data;
    }

    function getPartyRequestById($id) {
        $fields[] = 'tbp.iBookPartyID AS iBookPartyID';
        $fields[] = 'tbp.vCustomerName AS vCustomerName';
        $fields[] = 'tl1.vLocationName AS vLocationName1';
        $fields[] = 'tl2.vLocationName AS vLocationName2';
        $fields[] = 'tl3.vLocationName AS vLocationName3';
        $fields[] = 'tr1.vRestaurantName AS vRestaurantName1';
        $fields[] = 'tr2.vRestaurantName AS vRestaurantName2';
        $fields[] = 'tr3.vRestaurantName AS vRestaurantName3';
        $fields[] = 'tbp.dMinPrice AS dMinPrice';
        $fields[] = 'tbp.dMaxPrice AS dMaxPrice';
        $fields[] = 'TIME_FORMAT(sm.tstartFrom, \'%h:%i %p\') AS timeSlot';
        $fields[] = 'tbp.iNumberOfPeople AS iNumberOfPeople';
        $fields[] = 'DATE_FORMAT(FROM_UNIXTIME(CAST(dtBookingdate/1000 as UNSIGNED)), \'%d %b, %Y\') AS dtBookingDate';
        $fields[] = 'CAST(dtBookingdate/1000 as UNSIGNED) AS dtBookingDate_sort';
        $fields[] = 'tbp.vOcassion AS vOcassion';
        $fields[] = 'tbp.tNote AS tNote';
        $fields[] = 'tbp.eStatus AS eStatus';
        $fields[] = 'tbp.tCreatedAt AS tCreatedAt';

        $fieldStr = implode(", ", $fields);

        $tl1Join = "LEFT JOIN tbl_location AS tl1 ON tbp.iLocationID1=tl1.iLocationID";
        $tl2Join = "LEFT JOIN tbl_location AS tl2 ON tbp.iLocationID2=tl2.iLocationID";
        $tl3Join = "LEFT JOIN tbl_location AS tl3 ON tbp.iLocationID3=tl3.iLocationID";
        $tr1Join = "LEFT JOIN tbl_restaurant AS tr1 ON tbp.iRestaurantID1=tr1.iRestaurantID";
        $tr2Join = "LEFT JOIN tbl_restaurant AS tr2 ON tbp.iRestaurantID2=tr2.iRestaurantID";
        $tr3Join = "LEFT JOIN tbl_restaurant AS tr3 ON tbp.iRestaurantID3=tr3.iRestaurantID";
        $smJoin = "LEFT JOIN slot_master AS sm ON tbp.iSlotID=sm.iSlotID";

        $qry = "SELECT $fieldStr FROM tbl_book_party AS tbp $tl1Join $tl2Join $tl3Join $tr1Join $tr2Join $tr3Join $smJoin WHERE tbp.iBookPartyID='$id'";

        $result = $this->db->query($qry);
        if ($result->num_rows() > 0) {
            return $result->row_array();
        } else {
            return '';
        }
    }

    function changePartyBookingStatus($status, $id) {
        $status = urldecode($status);
        $qry = "UPDATE tbl_book_party SET eStatus='$status' WHERE iBookPartyID='$id'";
        $this->db->query($qry);
        $query = "SELECT eStatus,iUserID,DATE_FORMAT(tbp.dtBookingDate , '%d %b %Y')  AS dtBookingDate, iSlotID FROM tbl_book_party AS tbp WHERE tbp.iBookPartyID='$id'";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            $data = $result->row_array();

            // send push and sms when confirmed
            if ($data["eStatus"] == "Addressed") {
                $query = "SELECT * FROM tbl_user WHERE iUserID='" . $data["iUserID"] . "'";
                $result = $this->db->query($query);
                $record = $result->row_array();
                $osType = $record['ePlatform'] == 'ios' ? 2 : 1;
                $deviceToken = $record['vDeviceToken'];
                $time = $this->db->query("SELECT TIME_FORMAT(tstartFrom, '%h:%i %p') AS time FROM slot_master WHERE iSlotID='{$data['iSlotID']}'")->row_array()['time'];
                $pushMSG = "Dear {$record['vFirstName']} {$record['vLastName']},\nYour party booking has been addressed. {$data['dtBookingDate']} at $time.\nCheers,\nHungerMafia";
//                $this->general_model->saveUserNotification($data["iUserID"], $pushMSG, $restId, $iBookingID, 'table', $vStatus);

                /* SEND SMS */
                $mobile_no = $record['vMobileNo'];
                if ($mobile_no != '' && strlen($mobile_no) == 10) {
                    //$this->load->library('smslane');
                    //$this->smslane->send(array('91' . $mobile_no), $pushMSG);
                    $this->load->model('Sms_model', 'sms_m');
                    $this->sms_m->destmobileno = $mobile_no;
                    $this->sms_m->msg = $pushMSG;
                    $this->sms_m->Send();
                }

                if ($deviceToken != '') {
                    $BADGECOUNT = $record['iNotifyCount'];
                    if ($record['isNotify'] == 'yes') {
                        $this->load->library('pushnotify');
                        $this->pushnotify->sendIt($osType, $deviceToken, str_replace("\n", " ", $pushMSG), 1, array('type' => 'table', 'id' => $id), $BADGECOUNT);
                    }
                }
            }
            // send notification code ends

            return $data["eStatus"];
        } else {
            return '';
        }
    }

    /*
     * DELETE THE DEALS EITHER ONE OR MORE...
     */

    function removeBooking($iBookingID) {
        $adid = implode(',', $iBookingID);

        //$query = $this->db->query("DELETE FROM $this->table WHERE iTableBookID IN ($adid) ");
        $query = $this->db->query("UPDATE $this->table SET isDeleted = 'yes' WHERE iTableBookID IN ($adid) ");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    function removePartyBooking($iBookPartyID) {
        $adid = implode(',', $iBookPartyID);

        //$query = $this->db->query("DELETE FROM $this->table WHERE iTableBookID IN ($adid) ");
        $query = $this->db->query("UPDATE tbl_book_party SET eStatus = 'Deleted' WHERE iBookPartyID IN ($adid) ");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    /*
     * TO CHANGE THE STATUS OF THE SELECTED RECORD...
     */

    function changeBookingStatus($iBookingID) {

        $qry = 'UPDATE ' . $this->table . ' SET eBookingStatus = IF(eBookingStatus = "Pending", "Accept", IF(eBookingStatus = "Accept", "Reject", "Accept") ) WHERE iTableBookID = "' . $iBookingID . '"';
        $query = $this->db->query($qry);
        if ($this->db->affected_rows() > 0) {
            $booking = $this->db->query('SELECT vUserName,vMobileNo,eBookingStatus,iUserID,iRestaurantID,unique_code,iPersonTotal,DATE_FORMAT(tDateTime , \'%d %b %Y\') AS bookingDate,iSlotID,iDealID FROM ' . $this->table . ' WHERE iTableBookID IN(' . $iBookingID . ')')->row_array();
            $status = $booking['eBookingStatus'];
            $userId = $booking['iUserID'];
            $restId = $booking['iRestaurantID'];
            $uniqueCode = $booking['unique_code'];
            $date = $booking['bookingDate'];
            $guests = $booking['iPersonTotal'];
            $slot = $booking['iSlotID'];
            $dealID = $booking['iDealID'];
            $bookingName = $booking['vUserName'];
            $bookingMobile = $booking['vMobileNo'];

            /*
             * HERE PUSH NOTIFICATION WILL SEND IT TO USER APP
             */
            $isNotify = $this->db->query('SELECT isNotify FROM tbl_user WHERE iUserID IN(' . $userId . ')')->row_array()['isNotify'];
            $record = $this->db->query('SELECT ePlatform, vDeviceToken, vMobileNo, vFirstName, vLastName FROM tbl_user WHERE iUserID IN(' . $userId . ')')->row_array();
            $restaurantName = $this->db->query('SELECT vRestaurantName FROM tbl_restaurant WHERE iRestaurantID IN(' . $restId . ')')->row_array()['vRestaurantName'];

            $username = $record["vFirstName"] . " " . $record["vLastName"];
            $osType = $record['ePlatform'] == 'ios' ? 2 : 1;
            $deviceToken = $record['vDeviceToken'];

            //$pushMSG = 'Your table request has been ' . ($status == 'Accept' ? 'accepted' : 'declined') . ' for ' . $restaurantName;
            $pushMSG = '';
            $vStatus = $status;
            if ($status == 'Accept') {
                $time = $this->db->query("SELECT TIME_FORMAT(tstartFrom, '%h:%i %p') AS time FROM slot_master WHERE iSlotID='$slot'")->row_array()['time'];
                $deal = $this->db->query("SELECT vOfferCode FROM tbl_deals_code WHERE iDealID='$dealID' AND iUserId='$userId' AND `iTableBookID` IS NULL")->row_array();
                $pushMSG = "Dear $bookingName,\nYour reservation at $restaurantName has been confirmed. $date at $time, $guests guests.  Booking ID $uniqueCode";
                if (!empty($deal['vOfferCode'])) {
                    $pushMSG .= ", Deal Code {$deal['vOfferCode']}";
                }
                $pushMSG .= "\nCheers,\nHungerMafia";
            } else {
                /* change @2015-11-17/18:27 */
                //$pushMSG = 'Your reservation request has been declined due to unavailability at ';
//                $pushMSG = 'Your reservation has been declined due to unavailability at ';
//            $pushMSG .= $restaurantName . '.';
                $pushMSG = "Dear $bookingName,\nWe apologize but your reservation at $restaurantName has been declined. Please call us at 180118200 for more options.\nCheers,\nHungerMafia";
            }

            /*
             * SAVE USER NOTIFICATION HISTORY
             */
            $this->general_model->saveUserNotification($userId, $pushMSG, $restId, $iBookingID, 'table', $vStatus, $uniqueCode);

            /* SEND SMS */
            $mobile_no = $bookingMobile; //$record['vMobileNo'];
            if ($mobile_no != '' && strlen($mobile_no) == 10) {
                //$this->load->library('smslane');
                //$this->smslane->send(array('91' . $mobile_no), $pushMSG);
                $this->load->model('Sms_model', 'sms_m');
                $this->sms_m->destmobileno = $mobile_no;
                $this->sms_m->msg = $pushMSG;
                $this->sms_m->Send();
            }

            if ($deviceToken != '') {
                /*
                 * IF ANY NOTIFICATION SEND FROM THE VENDOR APPLICATION SIDE THAN WE HAVE TO SEND BADGE 
                 * VALUE FROM THE OUR END... - BUT IT JUST FOR USER NOTIFICATION SIDE...
                 */
                $BADGECOUNT = $this->db->query('SELECT iNotifyCount FROM tbl_user WHERE iUserID IN(' . $userId . ')')->row_array()['iNotifyCount'];
                //$pushMSG .= $BADGECOUNT;
                if ($isNotify == 'yes' || $isNotify == 'no') {
                    $this->load->library('pushnotify');
                    $this->pushnotify->sendIt($osType, $deviceToken, $pushMSG, 1, array('type' => 'table', 'notificationType' => 4, 'id' => $iBookingID, 'message' => $pushMSG), $BADGECOUNT);
                }
            }
            if ($status == 'Accept') {
                $this->sendBookingEmail('confirm', $iBookingID, $userId);
            } else {
                $this->sendBookingEmail('decline', $iBookingID, $userId);
            }
            return $query;
        } else {
            return '';
        }
    }

    public function sendBookingEmail($type, $bookingId, $userId) {

        if ($bookingId != '') {
            $fields = array(
                'ttb.iTableBookID AS tableBookId',
                'ttb.unique_code AS bookingId',
                'ttb.iDealID AS selOffer',
                'td.vOfferText AS offerText',
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
            $this->load->model('user_model');
            $userData = $this->user_model->getUserDataById($userId);
            $userEmail = $userData['vEmail'];
            $userFullName = $userData['vFirstName'];
        }

        switch ($type) {
            case 'confirm':
                $subject = 'Confirm Booking.';
                $tmplt = DIR_ADMIN_VIEW . 'email/accept_booking.php';
                break;
            case 'decline':
                $subject = 'Decline Booking';
                $tmplt = DIR_ADMIN_VIEW . 'email/decline_booking.php';
                break;

            default:
                break;
        }
        /*
         * SEND MAIL FUNCTIONALITY...
         */
        // $this->load->library('maillib');
        $this->load->model("smtpmail_model", "smtpmail_model");
        $param = array(
            '%MAILSUBJECT%' => 'HungerMafia : You just booked a table, check status',
            '%BASEURL%' => DOMAIN_URL . '/',
            '%NAME%' => @$userFullName,
            '%BOOKINGNAME%' => @$data['userBookingName'],
            '%BOOKINGNUMBER%' => @$data['userMobile'],
            '%BOOKINGID%' => @$data['bookingId'],
            '%When%' => @$data['bookedDate'] . ' ' . date('h:i A', strtotime($data['slotTime'])),
            '%Where%' => @$data['restaurantName'] . ', ' . @$data['restaurantAddress'],
            '%OfferAvailed%' => @($data['selOffer'] > 0) ? $data['offerText'] : 'Offer not availed',
            '%Guests%' => @$data['totalPerson'],
        );
        //send email
        $subject = 'HungerMafia : ' . $subject;
        $to = @$userEmail;
        $this->smtpmail_model->send($to, $subject, $tmplt, $param);
        // $this->maillib->sendMail($to, $subject, $tmplt, $param);
        return 1;
    }

    function changeBookingVisitedStatus($iBookingID) {
        $qry = 'UPDATE ' . $this->table . ' SET eVisited = IF(eVisited = "Yes", "No", "Yes" ) WHERE iTableBookID = "' . $iBookingID . '"';
        $this->db->query($qry);
        if ($this->db->affected_rows() > 0) {
            $isVisitedData = $this->db->query('SELECT eVisited, iUserID FROM ' . $this->table . ' WHERE iTableBookID IN(' . $iBookingID . ')')->row_array();
            $isVisited = $isVisitedData['eVisited'];
            $userId = $isVisitedData['iUserID'];
            if ($isVisited == 'Yes' && !empty($userId)) {
                $this->addUserPoints($userId, 2);
            }
            return true;
        } else {
            return '';
        }
    }

    function addUserPoints($userId, $pointSystemId) {

        $insert = array(
            'iUserPointSystemID' => $pointSystemId,
            'iUserID' => $userId,
            'eStatus' => "Active",
            'tCreatedAt' => date('Y:m:d H:i:s'),
        );
        $this->db->insert('tbl_user_points', $insert);
    }

    function waitStatus($iBookingID, $waitTime = 0) {
        //$qry = 'UPDATE ' . $this->table . ' SET iWaitTime = "' . $waitTime . '" WHERE iBookingID = "' . $iBookingID . '"';
        $this->db->update($this->table, array('iWaitTime' => $waitTime, 'eBookingStatus' => 'Waiting'), array('iTableBookID' => $iBookingID));

        if ($this->db->affected_rows() > 0) {
            /*
             * HERE PUSH NOTIFICATION WILL SEND IT TO USER APP
             */
            $booking = $this->db->query('SELECT vUserName, vMobileNo, iUserID,iRestaurantID,unique_code,iPersonTotal,DATE_FORMAT(tDateTime , \'%d %b %Y\') AS bookingDate,iSlotID,iDealID FROM ' . $this->table . ' WHERE iTableBookID IN(' . $iBookingID . ')')->row_array();
            $userId = $booking['iUserID'];
            $restId = $booking['iRestaurantID'];
            $uniqueCode = $booking['unique_code'];
            $date = $booking['bookingDate'];
            $guests = $booking['iPersonTotal'];
            $slot = $booking['iSlotID'];
            $dealID = $booking['iDealID'];
            $bookingName = $booking['vUserName'];
            $bookingMobile = $booking['vMobileNo'];

            $isNotify = $this->db->query('SELECT isNotify FROM tbl_user WHERE iUserID IN(' . $userId . ')')->row_array()['isNotify'];

            $record = $this->db->query('SELECT ePlatform, vDeviceToken, vMobileNo,vFirstName,vLastName FROM tbl_user WHERE iUserID IN(' . $userId . ')')->row_array();
            $restaurantName = $this->db->query('SELECT vRestaurantName FROM tbl_restaurant WHERE iRestaurantID IN(' . $restId . ')')->row_array()['vRestaurantName'];



            $osType = $record['ePlatform'] == 'ios' ? 2 : 1;
            $deviceToken = $record['vDeviceToken'];
            $username = $record["vFirstName"] . " " . $record["vLastName"];

            //$pushMSG = 'Your have to wait for  ' . ($status == 'Accept' ? 'accepted' : 'declined');
            //$pushMSG = 'You have to wait ' . $waitTime . ' minutes for ' . $restaurantName;
            $vStatus = 'Waiting';
            $time = $this->db->query("SELECT TIME_FORMAT(tstartFrom, '%h:%i %p') AS time FROM slot_master WHERE iSlotID='$slot'")->row_array()['time'];
            $deal = $this->db->query("SELECT vOfferCode FROM tbl_deals_code WHERE iDealID='$dealID' AND iUserId='$userId' AND `iTableBookID` IS NULL")->row_array();
            $pushMSG = "Dear $bookingName,\nYour reservation at $restaurantName has been waitlisted by $waitTime min. $date at $time, $guests guests.  Booking ID $uniqueCode";
            if (!empty($deal['vOfferCode'])) {
                $pushMSG .= ", Deal Code {$deal['vOfferCode']}";
            }
            $pushMSG .= "\nCheers,\nHungerMafia";
            if ($deviceToken != '') {
                if ($isNotify == 'yes') {
                    $this->load->library('pushnotify');
                    $BADGECOUNT = $this->db->query('SELECT iNotifyCount FROM tbl_user WHERE iUserID IN(' . $userId . ')')->row_array()['iNotifyCount'];
                    $this->pushnotify->sendIt($osType, $deviceToken, str_replace("\n", " ", $pushMSG), 1, array('type' => 'table', 'id' => $iBookingID), $BADGECOUNT);
                }
            }

            /*
             * SAVE USER NOTIFICATION HISTORY
             */
            $this->general_model->saveUserNotification($userId, $pushMSG, $restId, $iBookingID, 'table', $vStatus);

            /* SEND SMS */
            $mobile_no = $bookingMobile; //$record['vMobileNo'];
            if ($mobile_no != '' && strlen($mobile_no) == 10) {
                //$this->load->library('smslane');
                //$this->smslane->send(array('91' . $mobile_no), $pushMSG);
                $this->load->model('Sms_model', 'sms_m');
                $this->sms_m->destmobileno = $mobile_no;
                $this->sms_m->msg = $pushMSG;
                $this->sms_m->Send();
            }
            return 1;
        } else {
            return 0;
        }
    }

    function get_reportpaginationresult() {
        $where_condition = '';
        $iRestaurantID = (int) $this->session->userdata('iRestaurantID');
        $fields = array(
            "CONCAT(tu.vFirstName,' ',tu.vLastName) AS userName",
            "tu.vMobileNo AS userMobile",
            "tr.vRestaurantName AS vRestaurantName",
            "td.vOfferText AS vOfferText",
            "tdc.vOfferCode AS vDealCode",
            "dtAvailedDate",
            "DATE_FORMAT(tdc.dtAvailedDate , '%d %b %Y %h:%i %p') AS availedDate",
            "tdc.iCodeId AS iCodeId",
            "tdc.iCodeId AS DT_RowId",
            "tdc.eStatus AS eStatus"
        );
        $fieldStr = implode(", ", $fields);
        $tuJoin = "LEFT JOIN tbl_user AS tu ON tu.iUserID=tdc.iUserId";
        $tdJoin = "LEFT JOIN tbl_deals AS td ON td.iDealID=tdc.iDealID";
        $trJoin = "LEFT JOIN tbl_restaurant AS tr ON tr.iRestaurantID=td.iRestaurantID";
        $condition = "WHERE `iTableBookID` IS NULL";
        if ($iRestaurantID != 0) {
            $condition .= ' AND tr.iRestaurantID = "' . $iRestaurantID . '"';
        }
        $query = "SELECT $fieldStr FROM tbl_deals_code AS tdc $tuJoin $tdJoin $trJoin $condition";

        $data = $this->datatableshelper->query($query);
        return $data;
    }

    function changeAvailedStatus($id) {

        $qry = 'UPDATE tbl_deals_code SET eStatus = IF(eStatus = "availed", "unavailed", "availed" ) WHERE iCodeId = "' . $id . '"';
        $this->db->query($qry);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return '';
        }
    }

}

?>
