<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of usernotifications_model
 *
 * @author user
 */
class UserNotifications_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_user_notifications';
    }

    function sendNotification($userId, $message, $data) {

        $preferenceSql = "SELECT eTypeSms FROM tbl_user_push_notification_type WHERE iUserID='$userId' AND iNotificationID=" . $data["notificationType"];
        $preferenceRes = $this->db->query($preferenceSql)->row_array();
        if ($preferenceRes["eTypeSms"] == "1") {
            $deviceSql = "SELECT ePlatform, vDeviceToken AS deviceToken FROM tbl_user WHERE iUserID='$userId'";
            $deviceRes = $this->db->query($deviceSql)->row_array();
            $platform = trim(strtolower($deviceRes["ePlatform"])) == "ios" ? 2 : 1;
            $data["message"] = $message;
            $insert = array(
                'vMessage' => $message,
                'iUserID' => $userId,
                'tData' => json_encode($data),
                'tCreatedAt' => date('Y:m:d H:i:s')
            );

            $this->db->insert('tbl_user_notifications', $insert);

            if (!empty($deviceRes["deviceToken"])) {
                $this->load->library('pushnotification', "live");
                $this->pushnotification->sendIt($platform, $deviceRes["deviceToken"], $message, $data);
            }
        }
    }

    function getNotifications($userId) {
        $data = array();
        $sql = "SELECT tData, vNotifyTitle, vMessage ,UNIX_TIMESTAMP(tCreatedAt)*1000 as date FROM tbl_user_notifications WHERE iUserID='$userId' AND eSeen='no' AND eStatus = 'Active' ORDER BY tCreatedAt DESC";
        $res = $this->db->query($sql)->result_array();
        //print_r($res); exit;
        if (!empty($res)) {
            foreach ($res AS $row) {
               // print_r($row); exit;
               // $detail = json_decode($row["tData"]);
              //  print_r($detail); exit;
                $this->load->model('restaurant_model');
                //$tdata->time = $this->restaurant_model->getTimeElapsed(strtotime($row["tCreatedAt"])) . " ago";
               $tdata['date'] = $row['date'];
               $tdata['description'] = $row['vMessage'];
               $tdata['title'] = $row['vNotifyTitle'];
                //$tdata->notificationId = $row['iNotificationID'];
                $data[] = $tdata;
            }
        }
        return $data;
    }

    function deleteNotification($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                if ($userId != '' && $notificationId != '') {
                    $UPDT = array('eStatus' => 'Deleted');
                    $this->db->update('tbl_user_notifications', $UPDT, array('iNotificationID' => $notificationId, 'iUserID' => $userId));
                    return 1;
                } return -1;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in getRewardList function - ' . $ex);
        }
    }

}
