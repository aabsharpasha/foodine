<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login
 * @author OpenXcell Technolabs
 */
class Notification extends REST_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('notification_model');
    }

    function userNotify_post() {
        /*
         * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT 
         */
        $allowParam = array(
            'userId', 'userLat',
            'userLong', 'plateForm', 'deviceToken'
        );
        /*
         * TO SET DEFAULT VARIABLE VALUES...
         */
        $MESSAGE = INSUFF_DATA;
        $STATUS = FAIL_STATUS;

        if (checkselectedparams($this->post(), $allowParam)) {

            $resp = $this->notification_model->updateUserStep($this->post());

            switch ($resp) {
                case -1:
                    break;

                case 0 :
                    $MESSAGE = 'Device token is not available';
                    break;

                case -2 :
                    $MESSAGE = 'There no restaurant available nearer to you.';
                    break;

                case -3 :
                    $MESSAGE = 'Notification could not be sent';
                    break;

                default :
                    $STATUS = SUCCESS_STATUS;
                    $MESSAGE = 'Notification has been sent successfully.';
                    break;
            }
        }

        $resp = array(
            'MESSAGE' => $MESSAGE,
            'STATUS' => $STATUS
        );

        $this->response($resp, 200);


        exit;
    }

    function getNotificationTypes_post() {
        try {
            $allowParam = array(
                'userId'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */

            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $REDEEMAMOUNT = 0;
            $response = array();
            if (checkselectedparams($this->post(), $allowParam)) {
                $response = $this->notification_model->getNotificationTypes($this->post());
                if (!empty($response)) {
                    $MESSAGE = '';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $STATUS = FAIL_STATUS;
                    $MESSAGE = 'No Record Found';
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
                'Types' => $response
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getNotificationTypes function - ' . $ex);
        }
    }

    /*
     * TO CHECK USER CHECKIN FUNCTINALITY
     */

    function postNotificationType_post() {
        try {
            /*
             * TO CHECK ALL THE PARAMETER ARE BEING PASSED OR NOT ?
             */

            $allowParam = array(
                'userId', 'typesData'
            );

            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */

            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $REDEEMAMOUNT = 0;
            if (checkselectedparams($this->post(), $allowParam)) {
                $response = $this->notification_model->postNotificationTypes($this->post());
                if ($response) {
                    $MESSAGE = 'Notification updated successfully.';
                    $STATUS = SUCCESS_STATUS;
                } else {
                    $MESSAGE = "Unable to update data. Please try again !";
                }
            }
            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS,
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in postNotificationType function - ' . $ex);
        }
    }

    function sendNotifications_post() {
        $pushData = array("bookindId" => "1",
            "restaurantName" => "test",
            "time" => "2 second",
            "notificationType" => 2
        );

        $this->load->model('usernotifications_model');
        $data = $this->usernotifications_model->sendNotification($this->post('userId'), "testing notification", $pushData);
    }

    function getUserNotifications_post() {
        try {
            $allowParam = array('userId');
            $message = INSUFF_DATA;
            $status = FAIL_STATUS;
            $data = [];
            if (checkselectedparams($this->post(), $allowParam)) {
                try {
                    $this->load->model('usernotifications_model');
                    $data = $this->usernotifications_model->getNotifications($this->post('userId'));
                    if (!empty($data)) {
                        $message = '';
                        $status = SUCCESS_STATUS;
                    } else {
                        $message = 'No Recod Found';
                    }
                } catch (Exception $ex) {
                    $message = $ex->getMessage();
                }
            }
            $resp = array(
                'MESSAGE' => $message,
                'STATUS' => $status,
                'DATA' => $data
            );
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getUserNotifications_post function - ' . $ex);
        }
    }

    function deleteNotification_post() {
        try {
            $allowParam = array('userId', 'notificationId');
            $message = INSUFF_DATA;
            $status = FAIL_STATUS;
            $data = [];
            if (checkselectedparams($this->post(), $allowParam)) {
                try {
                    $this->load->model('usernotifications_model');
                    $response = $this->usernotifications_model->deleteNotification($this->post());
                    switch ($response) {
                        case -1 :
                            $MESSAGE = 'Some error occured! Please try again.';
                            break;

                        default :
                            $STATUS = SUCCESS_STATUS;
                            $MESSAGE = 'Notification deleted successfully.';
                            break;
                    }
                } catch (Exception $ex) {
                    $message = $ex->getMessage();
                }
            }
            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in deleteNotification_post function - ' . $ex);
        }
    }

}
