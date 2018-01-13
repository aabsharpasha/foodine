<?php

class General_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->role_tbl = 'admin';
    }

    /*
     * TO SAVE USER NOTIFICATION
     */

    function saveUserNotification($userId = '', $msg = '', $targetId = '', $recordId = '', $type = 'table', $vStatus = '', $uniqueCode = '') {
        try {
            $preferenceSql = "SELECT eTypeSms FROM tbl_user_push_notification_type WHERE iUserID='$userId' AND iNotificationID= 4";
            $preferenceRes = $this->db->query($preferenceSql)->row_array();
            //&& $preferenceRes["eTypeSms"] == "1"
            if ($userId != '' && $targetId != '' && $recordId != '') {
                $ins = array(
                    'iUserID' => $userId,
                    'iTargetID' => $targetId,
                    'iRecordID' => $recordId,
                    'vNotificationText' => $msg,
                    'eType' => $type,
                    'vStatus' => $vStatus,
                    'tCreatedAt' => date('Y-m-d H:i:s', time())
                );
                $this->db->insert('tbl_user_notification', $ins);

                $get_rec = $this->db->get_where('tbl_restaurant', array('iRestaurantID' => $targetId))->row_array();
                $restName = '';
                if (!empty($get_rec)) {
                    $restName = $get_rec['vRestaurantName'];
                }

                $currentDate = date('Y-m-d H:i:s');
                $data = array("bookingId" => $uniqueCode,
                    "restaurantName" => $restName,
                    "time" => $this->getTimeElapsed(strtotime($currentDate)),
                    "message" => $msg,
                    "notificationType" => 4
                );
                $insert = array(
                    'vMessage' => $msg,
                    'iUserID' => $userId,
                    'tData' => json_encode($data),
                    'tCreatedAt' => date('Y:m:d H:i:s')
                );

                $this->db->insert('tbl_user_notifications', $insert);


                $this->db->query('UPDATE tbl_user SET iNotifyCount = iNotifyCount + 1 WHERE iUserID IN(' . $userId . ')');
            }
        } catch (Exception $ex) {
            throw new Exception('Error in saveUserNotification function - ' . $ex);
        }
    }

    //************************************************************
    //Error Or success Msg View
    //************************************************************
    function getMessages() {
        if ($this->session->userdata('ERROR') && array_count_values($this->session->userdata('ERROR')) > 0)
            return $this->load->view('messages/error_view');
        else if ($this->session->userdata('SUCCESS') && array_count_values($this->session->userdata('SUCCESS')) > 0)
            return $this->load->view('messages/success_view');
    }

    //************************************************************
    //Check for Super Admin(If needed)
    //************************************************************
    function isSuperAdmin() {
        if ($this->session->userdata('ADMINTYPE') && $this->session->userdata('ADMINTYPE') == 'Super')
            return 1;
        return 0;
    }

    //************************************************************
    //If not find any record
    //************************************************************
    function noRecordsHere() {
        echo '<div class="alert i_magnifying_glass yellow"><strong>Opps!!&nbsp;&nbsp;:</strong>&nbsp;&nbsp;No Records available here.</div>';
    }

    //************************************************************
    //Truncatting string
    //************************************************************
    function myTruncate($string, $limit, $break = ".", $pad = ".") { // return with no change if string is shorter than $limit
        if (strlen($string) <= $limit)
            return $string; // is $break present between $limit and the end of the string?
        if (false !== ($breakpoint = strpos($string, $break, $limit))) {
            if ($breakpoint < strlen($string) - 1) {
                $string = substr($string, 0, $breakpoint) . $pad;
            }
        }
        return $string;
    }

    //**************************************************
    //BreadCrumb
    //**************************************************
    function getAdminBreadCrumb($arr) {
        $seg = $this->uri->segment(1);
        $str = '';
        foreach ($arr as $k => $v) {
            if (next($arr))
                $str .= "<li><a href='../$seg/'>" . $v . "</a></li>";
            else
                $str .="<li><a class='active'>" . $v . "</a></li>";
        }
        return $str;
    }

    function getAllusers() {
        $this->load->model();
    }

    function getcountries($id = "") {
        $strtoprint = "";
        $result = $this->db->get('countries');
        if ($result->num_rows() > 0) {
            $result = $result->result_array();
            foreach ($result as $key => $value) {
                $seltoprint = "";
                if ($id != "") {
                    $seltoprint = ($id == $value['idCountry']) ? "SELECTED" : "";
                }

                $strtoprint .= "<option  $seltoprint value='" . $value['idCountry'] . "' >" . $value['countryName'] . "</option>";
            }
            return $strtoprint;
        } else {
            return "";
        }
    }

    /*
     * ADD POINTS ENTRY TO THE DATABASE
     *  USER WILL GET POINTS WHILE HE / SHE 
     *      - 1. CHECK-IN
     *      - 2. USE DEAL
     *      - 3. BOOK TABLE
     *      - 4. RATE US
     *      - 5. SHARE ON FB
     *      - 6. INVITE FRIENDS
     */

    function addUserPointValue($userId = '', $pointsFor = 0, $recordId = 0) {
        try {
            if ($userId != '' && $pointsFor != 0) {
                if ($pointsFor == 4) {
                    /*
                     * NEED TO CHECK THAT THE USER HAS ALREADY RATE THE APPLICATION OR NOT
                     */
                    $res = $this->db->get_where('tbl_user_collect', array('iUserID' => $userId, 'iRewardPointID' => $pointsFor));
                    if ($res->num_rows() > 0) {
                        return 0;
                    }
                }
                /*
                 * MAKE A NEW ENTRY FOR THE USER POINT WHICH HE/SHE COLLECTED
                 */
                $hasRec = ($this->db->get_where('tbl_user_collect', array('iUserID' => $userId, 'iRewardPointID' => $pointsFor, 'iRecordID' => $recordId))->num_rows()) > 0;
                if (!$hasRec) {
                    $this->db->insert('tbl_user_collect', array('iUserID' => $userId, 'iRewardPointID' => $pointsFor, 'iRecordID' => $recordId, 'tCreatedAt' => date('Y-m-d H:i:s')));
                    return $this->db->insert_id();
                } return 0;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in addUserPointValue function - ' . $ex);
        }
    }

    /**
     * This function is used to get the elapsed time in string.
     * 
     * @author Garima <garima.chowrasia@kelltontech.com>
     * @param type $time
     * @return type
     */
    public function getTimeElapsed($time) {

        $time = time() - $time; // to get the time since that moment
        $time = ($time < 1) ? 1 : $time;
        $tokens = array(
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($tokens as $unit => $text) {
            if ($time < $unit)
                continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
        }
    }

}
