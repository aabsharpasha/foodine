<?php

require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login
 * @author KelltonTech
 */
class Cron extends REST_Controller {

    var $viewData = array();
    public $limit = 100;
    public $currentRunningProcess = 10;
    public $ANDIPlatform = 'Android';
    public $IOSPlatform = 'IOS';
    public $pendingStatus = '1';
    public $inprocessStatus = '2';
    public $completeStatus = '3';

    function __construct() {
        parent::__construct();
        $this->load->model('cron_model');

        date_default_timezone_set('Asia/Kolkata');
    }

    function sendNotification_get() {
        $qry = 'SELECT * FROM tbl_push_notification '
                . 'WHERE eSent = \'0\' '
                . 'AND tScheduleDate <= \'' . date('Y-m-d H:i:s') . '\' AND eStatus=\'Active\'';
        $records = $this->db->query($qry)->result_array();
        foreach ($records as $record) {
            /* Change the status as in-process of selected push notification
             * Rohit Shivhare
             */
            $update_query = 'UPDATE tbl_push_notification '
                    . 'SET eSent = \'2\' '
                    . 'WHERE iPushNotifyID = ' . $record['iPushNotifyID'];
            $this->db->query($update_query);
            //
            $androidArr = $iosArr = array();

            $message = $record['vNotifyText'];
            $linked_id = "";
            $restaurant_id = "";
            $vLinkData = (Array) json_decode($record["vLinkData"]);
            switch ($record["eLink"]) {
                case "Restaurant":
                    if (!empty($vLinkData["linkedRestaurant"])) {
                        $linked_id = $vLinkData["linkedRestaurant"];
                        $restaurant_id = $linked_id;
                    }
                    break;
                case "Featured":
                    if (!empty($vLinkData["linkedFeaturedRestaurant"])) {
                        $linked_id = $vLinkData["linkedFeaturedRestaurant"];
                        $restaurant_id = $linked_id;
                    }
                    break;
                case "Events":
                    if (!empty($vLinkData["linkedEvent"])) {
                        $linked_id = $vLinkData["linkedEvent"];
                        $restaurant_id = $vLinkData["linkedRestaurant"];
                    }
                    break;
                case "Combo":
                    if (!empty($vLinkData["linkedCombo"])) {
                        $linked_id = $vLinkData["linkedCombo"];
                        $restaurant_id = $vLinkData["linkedRestaurant"];
                    }
                    break;
                case "Offer":
                    if (!empty($vLinkData["linkedOffer"])) {
                        $linked_id = $vLinkData["linkedOffer"];
                        $restaurant_id = $vLinkData["linkedRestaurant"];
                    }
                    break;

                default:
                    break;
            }
            $pushData = array(
                "time" => "2 sec ago",
                "notificationType" => 0,
                "message" => $message,
                "link" => $record['eLink'],
                "linked_id" => $linked_id,
                "image" => !empty($record['vImage']) ? (DOMAIN_URL . '/images/pushNotification/' . $record['iPushNotifyID'] . '/thumb/' . $record['vImage']) : ''
            );
            if (!empty($restaurant_id)) {
                $restQuery = "SELECT vRestaurantName FROM tbl_restaurant WHERE iRestaurantID='$restaurant_id'";
                $rest = $this->db->query($restQuery)->row_array();
                $pushData["restaurantId"] = $restaurant_id;
                $pushData["restaurantName"] = $rest["vRestaurantName"];
            }
            if ((strtoupper($record['eCriteria']) == "ALL") || (strtoupper($record['eCriteria']) == "AGE" && !empty($record['iMinAge']) && !empty($record['iMaxAge']))) {
                $ageCondition = '';
                if (strtoupper($record['eCriteria']) == "AGE" && !empty($record['iMinAge']) && !empty($record['iMaxAge'])) {
                    $ageCondition = " AND (YEAR(CURDATE())-YEAR(dtDOB)) >='" . $record['iMinAge'] . "' AND (YEAR(CURDATE())-YEAR(dtDOB)) <='" . $record['iMaxAge'] . "'";
                }
                //IOS Device Token
                $iosDeviceTokenQry = "SELECT count(1) as count FROM tbl_user WHERE vDeviceToken != '' and ePlatform = 'ios' $ageCondition";
                $iosDeviceToken = $this->db->query($iosDeviceTokenQry)->row_array();
                $iosDeviceCount = $iosDeviceToken['count'];
                //IOS Device Token Devide in chunks
                $this->savejob($iosDeviceCount, $record['iPushNotifyID'], $this->IOSPlatform, json_encode($pushData), $record['eCriteria'], $record['iMinAge'], $record['iMaxAge']);

                //Android Device Token
                $andiDeviceTokenQry = "SELECT count(1) as count FROM tbl_user WHERE vDeviceToken != '' and ePlatform = 'android' $ageCondition";
                $andiDeviceToken = $this->db->query($andiDeviceTokenQry)->row_array();
                $andiDeviceCount = $andiDeviceToken['count'];
                //Android Device Token Devide in chunks
                $this->savejob($andiDeviceCount, $record['iPushNotifyID'], $this->ANDIPlatform, json_encode($pushData), $record['eCriteria'], $record['iMinAge'], $record['iMaxAge']);
            }

            $this->db->query("UPDATE tbl_push_notification SET eSent='1' WHERE iPushNotifyID='" . $record['iPushNotifyID'] . "'");
        }
        $this->main();
    }

    private function savejob($count, $notificationId, $platform, $pushData, $criteria, $minage='', $maxage='') {
        $limit = $this->limit;
        $max = ceil(($count / $limit));
        for ($i = 0; $i < $max; $i++) {
            $offset = $i * $limit;
            $this->createJob($notificationId, $platform, $limit, $offset, $pushData, $criteria, $minage, $maxage);
        }
    }

    private function createJob($notificationId, $platform, $limit, $offset, $pushData, $criteria, $minage, $maxage) {
        $jobData['notification_id'] = $notificationId;
        $jobData['criteria'] = $criteria;
        $jobData['minage'] = $minage;
        $jobData['maxage'] = $maxage;
        $jobData['start_limit'] = $offset;
        $jobData['end_limit'] = $offset + $limit;
        $jobData['platform'] = $platform;
        $jobData['created'] = time();
        $jobData['status'] = $this->pendingStatus;
        $jobData['parameter'] = $pushData;
        $this->cron_model->addPushNotificaton($jobData);
    }

    private function getPendingJob() {
        return $this->cron_model->getPendingJobs();
    }

    public function main() {
        $pendingJobData = $this->getPendingJob();
        if (is_array($pendingJobData)) {
            while ($pendingJobData) {
                $jobId = $pendingJobData['id'];
                $notificationId = $pendingJobData['notification_id'];
                $limit = $pendingJobData['end_limit'] - $pendingJobData['start_limit'];
                $offset = $pendingJobData['start_limit'];
                $platform = $pendingJobData['platform'];
                $runningJobs = $this->getRunningJob(); // check running job
                if ($runningJobs < $this->currentRunningProcess) {
                    $this->cron_model->updateJobs($jobId, $this->inprocessStatus);
                    $params = "$jobId/$notificationId/$limit/$offset/$platform";
                    $url = BASEURLHM . 'ws/cronsend/main/' . $params;
                    exec("wget $url");
                } else {
                    $this->_stop();
                }
                $pendingJobData = $this->getPendingJob();
            }
        }
    }

    private function getRunningJob() {
        return $this->cron_model->getRunningJobs();
    }

}
