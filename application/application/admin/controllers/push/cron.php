<?php

class Cron extends CI_Controller {

    var $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->model('cron_model');

        date_default_timezone_set('Asia/Kolkata');
    }

    function index() {
        try { return;
            /*
             * NEED TO CHECK THAT ANY SCHEDULED JOB IS 
             * ON TIME TO EXECUTE OR NOT??
             */
            $qry = 'SELECT * FROM tbl_push_notification '
                    . 'WHERE eNotifyType IN(\'schedule\') '
                    . 'AND tScheduleDate = \'' . date('Y-m-d H:i', time()) . '\'';

            $rec = $this->db->query($qry)->result_array();


            if (!empty($rec)) {
                foreach ($rec as $key => $val) {
                    $vNotifyText = $val['vNotifyText'];
                    /*
                     * BRAODCAST USER TO SNS
                     * iOS
                     */
                    $DEFAULT_ARR = array(
                        'IS_LIVE' => IS_NOTIFICATION_LIVE,
                        'PLATEFORM_TYPE' => 'ios'
                    );

                    $this->load->library('sns', $DEFAULT_ARR);
                    $this->sns->broadCast($vNotifyText, TOPIC_ARN);

                    /*
                     * BRAODCAST USER TO SNS
                     * android
                     */
                    $DEFAULT_ARR = array(
                        'IS_LIVE' => IS_NOTIFICATION_LIVE,
                        'PLATEFORM_TYPE' => 'android'
                    );

                    $this->load->library('sns', $DEFAULT_ARR);
                    //$this->sns->broadCast($vNotifyText, TOPIC_ARN);
                }
            }
        } catch (Exception $ex) {
            throw new Exception('Error in index function - ' . $ex);
        }
    }

    function sendNotification() {

        $qry = 'SELECT * FROM tbl_push_notification '
                . 'WHERE eSent IN(\'No\') '
                . 'AND tScheduleDate <= \'' . date('Y-m-d H:i:s') . '\' AND eStatus=\'Active\'';

        $records = $this->db->query($qry)->result_array();
        foreach ($records as $record) {
            $androidArr = $iosArr = array();
                
            $message        = $record['vNotifyText'];
            $linked_id      = "";
            $restaurant_id  = "";
            $vLinkData   = (Array)json_decode($record["vLinkData"]);
            switch ($record["eLink"]) {
                case "Restaurant":
                    if(!empty($vLinkData["linkedRestaurant"])){
                        $linked_id      = $vLinkData["linkedRestaurant"];
                        $restaurant_id  = $linked_id;
                    }
                    break;
                case "Featured":
                    if(!empty($vLinkData["linkedFeaturedRestaurant"])){
                        $linked_id      = $vLinkData["linkedFeaturedRestaurant"];
                        $restaurant_id  = $linked_id;
                    }
                    break;
                case "Events":
                    if(!empty($vLinkData["linkedEvent"])){
                        $linked_id      = $vLinkData["linkedEvent"];
                        $restaurant_id  = $vLinkData["linkedRestaurant"];
                    }
                    break;
                case "Combo":
                    if(!empty($vLinkData["linkedCombo"])){
                        $linked_id  = $vLinkData["linkedCombo"];
                        $restaurant_id  = $vLinkData["linkedRestaurant"];
                    }
                    break;
                case "Offer":
                    if(!empty($vLinkData["linkedOffer"])){
                        $linked_id  = $vLinkData["linkedOffer"];
                        $restaurant_id  = $vLinkData["linkedRestaurant"];
                    }
                    break;

                default:
                    break;
            }
            $pushData = array(
                "time"      => "2 sec ago",
                "notificationType" => 0,
                "message"   => $message,
                "link"      => $record['eLink'],
                "linked_id" => $linked_id,
                "image"     => !empty($record['vImage']) ? (DOMAIN_URL . '/images/pushNotification/' . $record['iPushNotifyID'] . '/thumb/' . $record['vImage']) : ''
            );
            if(!empty($restaurant_id)){
                $restQuery  = "SELECT vRestaurantName FROM tbl_restaurant WHERE iRestaurantID='$restaurant_id'";
                $rest       = $this->db->query($restQuery)->row_array();
                $pushData["restaurantId"]   = $restaurant_id;
                $pushData["restaurantName"] = $rest["vRestaurantName"];
            }
            if((strtoupper($record['eCriteria'])=="ALL") ||(strtoupper($record['eCriteria'])=="AGE" && !empty($record['iMinAge']) && !empty($record['iMaxAge'])) ){
                
                $ageCondition   = '';
                if( strtoupper($record['eCriteria'])=="AGE" && !empty($record['iMinAge']) && !empty($record['iMaxAge']) ){
                    $ageCondition    = " AND (YEAR(CURDATE())-YEAR(dtDOB)) >='".$record['iMinAge']."' AND (YEAR(CURDATE())-YEAR(dtDOB)) <='".$record['iMaxAge']."'";
                }
                $iosDeviceTokenQry = "SELECT vDeviceToken AS deviceToken, iUserID FROM tbl_user WHERE vDeviceToken != '' and ePlatform = 'ios' $ageCondition";
                
                $iosDeviceToken = $this->db->query($iosDeviceTokenQry)->result_array();
                
                $this->load->library('pushnotify');
          
                foreach($iosDeviceToken as $iosDeviceToken) {
                    $iosArr[] = $iosDeviceToken['deviceToken'];
                    $insert = array(
                        'vMessage'      => $message,
                        'iUserID'       => $iosDeviceToken['iUserID'],
                        'tData'         => json_encode($pushData),
                        'tCreatedAt'    => date('Y:m:d H:i:s')
                    );

                    $this->db->insert('tbl_user_notifications', $insert);
                    echo $this->pushnotify->sendIt(2, $iosDeviceToken['deviceToken'], $message, 1, $pushData);
                }
                //Android Device Token
                $andiDeviceTokenQry = "SELECT vDeviceToken AS deviceToken, iUserID FROM tbl_user WHERE vDeviceToken != '' and ePlatform = 'android' $ageCondition";
                $andiDeviceToken = $this->db->query($andiDeviceTokenQry)->result_array();
                foreach($andiDeviceToken as $andiDeviceToken) {
                    $androidArr[] = $andiDeviceToken['deviceToken'];
                    $insert = array(
                        'vMessage'      => $message,
                        'iUserID'       => $andiDeviceToken['iUserID'],
                        'tData'         => json_encode($pushData),
                        'tCreatedAt'    => date('Y:m:d H:i:s')
                    );

                    $this->db->insert('tbl_user_notifications', $insert);
                }
                if($androidArr){
    //                $deviceToken = array('APA91bFieZP1DBVaxU1E_4klPiAukYmXnKNupqSGv_cGL16zA-QT9E4hgWwi_ulF0A_AfrZvWUQIBJri7O96zn0xCmZJ5zWqNvgkgQqp2frv3_W2Xo1QIdf_fg9X5E0W0x1o1mnYiwlX','APA91bEGet8PfCaful6jB7bqlS8xhouCxGlqIvv-sHw-DZlakTeWZ9p4xNQj0RZaBoFxzKcdhhLJBroJISPZdnTmLsjqR1HJR3bwChNTo_lCm1mzlQ-wwrycH8Mg2lQzuQxG_ahSYmv6');
//                    $deviceToken = @$deviceToken;
                    $this->pushnotify->sendIt(1, $androidArr, $message, 1, $pushData);
                }
            }
            
            $this->db->query("UPDATE tbl_push_notification SET eSent='Yes' WHERE iPushNotifyID='".$record['iPushNotifyID']."'");

        }
        die;
    }

    function sendPromotionalEmail() {

        $qry = 'SELECT * FROM tbl_promotional_emails '
                . 'WHERE eSent IN(\'No\') '
                . 'AND tScheduledTime <= \'' . date('Y-m-d H:i:s') . '\' AND eStatus=\'Active\'';

        $records = $this->db->query($qry)->result_array();
        foreach ($records as $record) {
            
            $userQry    = "SELECT vEmail, iUserID, CONCAT(vFirstName,' ',vLastName) AS name FROM tbl_user WHERE iUserID IN (".$record["tUsers"].")";
            $users      = $this->db->query($userQry)->result_array();
            foreach($users AS $user){
                
                if(!empty($user['vEmail'])){

                    /*
                     * SEND MAIL FUNCTIONALITY...
                     */
                    $this->load->model('smtpmail_model');
                    $param = array(
                        '%MAILSUBJECT%'     => 'HungerMafia : Welcome',
                        '%FONT1%'           => DOMAIN_URL . '/images/fonts/HarabaraMais.otf',
                        '%FONT2%'           => DOMAIN_URL . '/images/fonts/NexaLight.otf',
                        '%LOGO_IMAGE%'      => DOMAIN_URL . '/images/hungermafia.png',
                        '%BACK_IMAGE%'      => DOMAIN_URL . '/images/email/new_restaurant.jpg',
                        '%FT1%'             => DOMAIN_URL . '/images/email/fb.png',
                        '%FT2%'             => DOMAIN_URL . '/images/email/twitter.png',
                        '%FT3%'             => DOMAIN_URL . '/images/email/linkedin.png',
                        '%FT4%'             => DOMAIN_URL . '/images/email/youtube.png',
                        '%USERNAME%'        => ucfirst($user['name']),
                        '%CONTENT%'         => $record["tContent"]
                    );
                    $tmplt = DIR_ADMIN_VIEW . 'template/promotional_email.php';
                    $subject = 'HungerMafia';
                    $to = $user['vEmail'];
                    $this->smtpmail_model->send($to, $subject, $tmplt, $param);

                }
                
            }
            $this->db->query("UPDATE tbl_promotional_emails SET eSent='Yes' WHERE iPushNotifyID='".$record['iPushNotifyID']."'");

        }
        die();
    }

}
