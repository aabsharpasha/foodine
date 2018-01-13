<?php

require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login
 * @author KelltonTech
 */
class Cronsend extends REST_Controller {

    var $viewData = array();
    public $limit = 100;
    public $currentRunningProcess = 10;
    public $ANDIPlatform = 'android';
    public $IOSPlatform = 'ios';
    public $pendingStatus = '1';
    public $inprocessStatus = '2';
    public $completeStatus = '3';

    function __construct() {
        parent::__construct();
        $this->load->model('cron_model');

        date_default_timezone_set('Asia/Kolkata');
    }

    public function main_get($jobId, $notificationId, $limit, $offset, $platform) {
        $notification = $minage = $maxage = '';
        $criteria = 'All';
        $notificationArr = $this->cron_model->getNotificationByJobId($jobId);
        if (!empty($notificationArr)) {
            $notification = $notificationArr['parameter'];
            $criteria = $notificationArr['criteria'];
            $minage = $notificationArr['minage'];
            $maxage = $notificationArr['maxage'];
        }
        $allDeviceTokens = $this->getAllDeviceIds($platform, $limit, $offset, $criteria, $minage, $maxage);
        $androidArr = array();
        foreach ($allDeviceTokens as $k => $val) {
            $deviceTokenArr[$k]['deviceToken'] = $val['vDeviceToken'];
            $deviceTokenArr[$k]['userId'] = $val['iUserID'];
        }
        if (!empty($deviceTokenArr) && !empty($notification)) {
            if (strtolower($platform) == 'ios') {
                $responseArr = $this->sendIOSNotification($deviceTokenArr, $notification);
            }

            if (strtolower($platform) == 'android') {
                $responseArr = $this->sendAndroidNotification($deviceTokenArr, $notification);
            }
            $response = json_encode($responseArr, true);
            $this->cron_model->updateJobs($jobId, $this->completeStatus, $response);
        }
        return true;
    }

    /**
     * @method      getAllDeviceIds
     * @desc        this function is used to find out device ids as per specify limit
     * @params      $platform <IOS>, $limit <No of records>, $offset <Starting row>
     * @access      public
     * @author      3031@kelltontech.com
     * @return      array <device id>
     */
    private function getAllDeviceIds($platform, $limit, $offset, $criteria = 'All', $minage = '', $maxage = '') {
        $platform = strtolower($platform);
        $conditions = array('vDeviceToken <>' => '', 'ePlatform' => $platform);
        if (strtoupper($criteria) == "AGE" && !empty($minage) && !empty($maxage)) {
            $conditions = array('vDeviceToken <>' => '', 'ePlatform' => $platform, 'YEAR(CURDATE())-YEAR(dtDOB) >=' => $minage, 'YEAR(CURDATE())-YEAR(dtDOB) <=' => $maxage);
        }
        $data = $this->db->get_where('tbl_user', $conditions, $limit, $offset)->result_array();
        return $data;
    }

    /**
     * @method      sendIOSNotification
     * @desc        this function is being used to push notification to IOS devices
     * @params      $iosArr <device id array>, $message
     * @access      public
     * @author      3031@kelltontech.com
     * @return      no of notifications
     */
    private function sendIOSNotification($deviceTokenArr, $notification) {
        $notificationArr = json_decode($notification);
        $resultArray['results'] = array();
        $this->load->library('pushnotification', "live");
        $k = $sCount = 0;
        foreach ($deviceTokenArr as $deviceToken) {
            $insert = array(
                'vMessage' => $notificationArr->message,
                'iUserID' => $deviceToken['userId'],
                'tData' => $notification,
                'tCreatedAt' => date('Y:m:d H:i:s')
            );
            $this->db->insert('tbl_user_notifications', $insert);
            $result = $this->pushnotification->sendIt(2, $deviceToken['deviceToken'], $notificationArr->message, $notificationArr);
            if ($result) {
                $sCount++;
            }
            $resultArray['results'][$k]['device_token'] = $deviceToken['deviceToken'];
            $resultArray['results'][$k]['status'] = $result;
            $k++;
        }
        $resultArray['results']['success'] = $sCount;
        $resultArray['results']['failure'] = count($deviceTokenArr) - $sCount;
        return $resultArray;
    }

    /**
     * @method      __androidSendNotifications
     * @desc        this function is being used to push notification to IOS devices
     * @params      $iosArr <device id array>, $message
     * @access      public
     * @author      3031@kelltontech.com
     * @return      no of notifications
     */
    private function sendAndroidNotification($deviceTokenArr, $notification) {
        $notificationArr = json_decode($notification);
        $androidArr = array();
        foreach ($deviceTokenArr as $deviceToken) {
            
            $androidArr[] = $deviceToken['deviceToken'];
            $insert = array(
                'vMessage' => $notificationArr->message,
                'iUserID' => $deviceToken['userId'],
                'tData' => $notification,
                'tCreatedAt' => date('Y:m:d H:i:s')
            );

            $this->db->insert('tbl_user_notifications', $insert);
        }
        $this->load->library('pushnotification', "live");
        $data = $this->pushnotification->sendIt(1, $androidArr, $notificationArr->message, $notificationArr);
        return $data;
    }

}
