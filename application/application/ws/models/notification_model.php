<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of user_model
 * @author OpenXcell Technolabs
 */
class Notification_Model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /*
     * TO SAVE USER NOTIFICATION
     */

    function updateUserStep($request) {
        try {
            if (!empty($request)) {
                extract($request);

                if (@$userId != '' && @$userLat != '' && @$userLong != '' && @$plateForm != '' && @$deviceToken != '') {
                    $updt = array(
                        'ePlatform' => $plateForm,
                        'vDeviceToken' => $deviceToken,
                        'vLat' => $userLat,
                        'vLong' => $userLong
                    );
                    $this->db->update('tbl_user', $updt, array('iUserID' => $userId));
                }
                /*
                 * NEED TO SEND PUSH NOTIFICATION TO THE REQUESTED USER
                 */
                if (@$plateForm != '' && @$deviceToken != '') {
                    /*
                     * HERE PUSH NOTIFICATION WILL SEND IT TO VENDOR APP
                     */
                    $distanceType = 'km';
                    $multiplyer = 6371 * 1000;
                    if ($distanceType == 'miles') {
                        $multiplyer = 3959;
                    }

                    $fields[] = 'tr.vRestaurantName AS restaurantName';
                    $fields[] = 'tr.iRestaurantID AS restaurantId';
                    $fields[] = '(SELECT COUNT(*) FROM tbl_deals AS td WHERE tr.iRestaurantID IN(td.iRestaurantID)) AS dealCount';
                    $fields[] = '(ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                            . ' * cos( radians( tr.vLat) )'
                            . ' * cos( radians( tr.vLog ) - radians( ' . $userLong . ' ) )'
                            . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( tr.vLat ) ) ) ))'
                            . ' AS distance';

                    $tbl[] = 'tbl_restaurant AS tr';

                    $condition[] = 'tr.eStatus IN(\'active\')';

                    $fields = 'SELECT ' . implode(',', $fields);
                    $tbl = ' FROM ' . implode(',', $tbl);
                    $condition = ' WHERE ' . implode(' AND ', $condition);

                    $having = ' HAVING distance <= 500,dealCount > 0 ';

                    $qry = $fields . $tbl . $condition;

                    $foundRes = $this->db->query($qry);
                    $foundRecCount = $foundRes->num_rows();

                    if ($foundRecCount > 0) {
                        //$foundRec = $foundRes->row_array();
                        $foundRec = $foundRes->result_array();



                        //mprd($foundRec);
                        $this->load->library('pushnotify');


                        $osType = @$plateForm == 'ios' ? 2 : 1;
                        $deviceToken = @$deviceToken;

                        if ($deviceToken != '') {
                            //$BADGECOUNT = $this->db->query('SELECT iNotifyCount FROM tbl_user WHERE iUserID IN(' . $userId . ')')->row_array()['iNotifyCount'];
                            if ($foundRecCount <= 3) {
                                for ($i = 0; $i < count($foundRec); $i++) {
                                    $dealsCount = $foundRec[$i]['dealCount'];
                                    $restName = $foundRec[$i]['restaurantName'];
                                    $restId = $foundRec[$i]['restaurantId'];

                                    $mesg = $dealsCount . 'deal' . ($dealsCount > 1 ? 's' : '') . ' available for ' . $restName;

                                    $this->pushnotify->sendIt($osType, $deviceToken, $mesg, 1, array('type' => 'restaurant', 'id' => $restId));
                                }
                            } else {
                                $mesg = 'Multiple offers in this area';

                                $this->pushnotify->sendIt($osType, $deviceToken, $mesg, 1, array('type' => 'restaurant', 'id' => 1));
                            }

                            return 1;
                        } return -3;
                    } return -2;
                } return 0;
            } return -1;
        } catch (Exception $ex) {
            throw new Exception('Error in updateUserStep function - ' . $ex);
        }
    }

    function getNotificationTypes($postValue) {
        try {
            if (!empty($postValue)) {
                extract($postValue);
                $fields[] = 'tpt.iNotificationID AS id';
                $fields[] = 'tpt.vName AS title';
                $fields[] = 'tupt.iNotificationID AS notificationId';
                


                $tbl[] = 'tbl_push_notification_type AS tpt';
                $condition[] = 'tupt.iUserID =' . $userId;
                $condition[] = 'tpt.eStatus IN(\'Active\')';
                $join = ' INNER JOIN `tbl_user_push_notification_type` AS `tupt` ON `tupt`.`iNotificationID` = `tpt`.`iNotificationID`';

                $fields = 'SELECT ' . implode(',', $fields);
                $tbl = ' FROM ' . implode(',', $tbl);
                $condition = ' WHERE ' . implode(' AND ', $condition);

                $qry = $fields . $tbl . $join . $condition;
                $response = $this->db->query($qry)->result_array();
                if(count($response) == 0) {
//                    $json = '{
//	"userId": '.$userId.',
//	"result": "Success",
//	"resultCode": 200,
//	"notificationPrefs": [{
//		"preferenceId": "4",
//		"title": "Booking Status",
//		"messageUpdateAllow": "true",
//		"emailUpdateAllow": "true",
//		"email": "true",
//		"message": "true"
//	},{
//		"preferenceId": "6",
//		"title": "Order Status",
//		"messageUpdateAllow": "true",
//		"emailUpdateAllow": "true",
//		"email": "false",
//		"message": "true"
//	}]
//
//}';
                    
$str = '[{
	"preferenceId": "4",
	"title": "Booking Status",
	"messageUpdateAllow": "true",
	"emailUpdateAllow": "true",
	"email": "true",
	"message": "true"
}, {
	"preferenceId": "6",
	"title": "Order Status",
	"messageUpdateAllow": "true",
	"emailUpdateAllow": "true",
	"email": "false",
	"message": "true"
}]';
return json_decode($str);
              //  print_r(json_decode($json)); exit;
               //  $resp=   $this->postNotificationTypes((array) json_decode($json, true), 1);
                }
                if (!empty($response)) {
                    $fields1[] = 'tpt.iNotificationID AS preferenceId';
                    $fields1[] = 'tpt.vName AS title';
                    $fields1[] = 'tpt.messageUpdateAllow as messageUpdateAllow';
                    $fields1[] = 'tpt.emailUpdateAllow as emailUpdateAllow';
                    $fields1[] = 'tupt.eTypeEmail AS email';
                    $fields1[] = 'tupt.eTypeSms AS message';
                    $tbl1[] = 'tbl_push_notification_type AS tpt';
                    $condition1[] = 'tpt.eStatus IN(\'Active\')';
                    $condition1[] = 'tupt.iUserID =' . $userId;
                    $fields1 = 'SELECT ' . implode(',', $fields1);
                    $tbl1 = ' FROM ' . implode(',', $tbl1);
                    $condition1 = ' WHERE ' . implode(' AND ', $condition1);
                    $join1 = ' INNER JOIN `tbl_user_push_notification_type` AS `tupt` ON `tupt`.`iNotificationID` = `tpt`.`iNotificationID`';
                    $qry1 = $fields1 . $tbl1 . $join1 . $condition1;
             //   echo $qry1; exit;
                    $data = $this->db->query($qry1)->result_array();
               //     print_r($data); exit;
//                    foreach ($data as $k => $resp) {
//                        
//                        $data[$k]['email'] = 0;
//                        $data[$k]['message'] = 0;
//                    }
                    return $data;
                }
                return $response;
            }return false;
        } catch (Exception $ex) {
            throw new Exception('Error in getNotificationTypes function - ' . $ex);
        }
    }

    function postNotificationTypes($postValue, $is_internal_call = 0) {
      //print_r($postValue); exit;
        try {
            if (!empty($postValue)) {
                
                extract($postValue);
                if (!empty($postValue)) {
                      
                    if(is_string($typesData)){
                        
                        $typesData = ltrim($typesData,'"');
                        $typesData = rtrim($typesData,'"');
                        $typesDataArray = json_decode($typesData, true);   
                        $typesDataArr = $typesDataArray['notificationPrefs'];
                    }else{
                         
                        $typesDataArr = $postValue['notificationPrefs'];
                    }
                    
                  //print_r($typesDataArr); exit;
                    //echo $userId; exit;
                    foreach ($typesDataArr as $typeData) {
                        $hasRec = $this->db->get_where('tbl_user_push_notification_type', array('iUserID' => $userId, 'iNotificationID' => $typeData['preferenceId']));
                        if ($hasRec->num_rows() > 0) {
                            $data = array(
                                'eTypeEmail' => !empty($typeData['email']) ? $typeData['email'] : "false",
                                'eTypeSms' => !empty($typeData['message']) ? $typeData['message'] : "false",
                                'tModifiedAt' => date('Y-m-d H:i:s')
                            );
                            //print_r($data); exit;
                            $result = $this->db->update('tbl_user_push_notification_type', $data, array('iUserID' => $userId, 'iNotificationID' => $typeData['preferenceId']));
                           //echo $this->db->last_query(); exit;
                            //var_dump($result); exit;
                        } else {
                            $data = array(
                                'iUserID' => $userId,
                                'iNotificationID' => $typeData['preferenceId'],
                                'eTypeEmail' => !empty($typeData['email']) ? $typeData['email'] : "false",
                                'eTypeSms' => !empty($typeData['sms']) ? $typeData['message'] : "false",
                                'tCreatedAt' => date('Y-m-d H:i:s'),
                                'tModifiedAt' => date('Y-m-d H:i:s')
                            );
                            $result = $this->db->insert('tbl_user_push_notification_type', $data);
                    //  echo   $this->db->last_query(); exit;
                        }
                    }
                    return true;
                }
            } return false;
        } catch (Exception $ex) {
            throw new Exception('Error in getNotificationTypes function - ' . $ex);
        }
    }

}
