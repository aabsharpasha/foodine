<?php

class SMSLane extends CI_Controller {

    var $controller, $uppercase, $title;

    function __construct() {
        parent::__construct();
    }

    function index() {
        $response = $_REQUEST;
        if (!empty($response)) {
            //$json_encode = json_encode($response);
            $request_code = $response['what'];

            $ins = array(
                'mobile' => $response['who'],
                'code' => $request_code,
                'operator' => $response['operator'],
                'circle' => $response['circle'],
                'time' => date('Y-m-d H:i:s', strtotime($response['time'])),
                'created' => date('Y-m-d H:i:s', time())
            );

            $this->db->insert('smslane_reply', $ins);
            $insert_id = $this->db->insert_id();
            /*
             * update table booking request
             */
            $arr_request_code = explode(' ', $request_code);
            $request_code = end($arr_request_code);
            $request_status = strtolower($arr_request_code[1]);
            $get_rec = $this->db->get_where('tbl_table_book', array('unique_code' => $request_code))->row_array();
            if (!empty($get_rec)) {
                $book_time = date('Y-m-d H:i:s', strtotime($get_rec['tDateTime']));
                $curr_time = date('Y-m-d H:i:s', time());
                if ($curr_time <= $book_time) {
                    $user_id = $get_rec['iUserID'];
                    $user_rec = $this->db->get_where('tbl_user', array('iUserID' => $user_id))->row_array();
                    $waitTime = 0;
                    switch ($request_status) {
                        case 'yes' :
                            $bookingStatus = 'Accept';
                            $pushMSG = 'Your reservation has been confirmed at ';
                            break;

                        case 'no':
                            $pushMSG = 'Your reservation request has been declined due to unavailability at ';
                            //$pushMSG .= 'has been ' . $requestStatus . 'ed';
                            $bookingStatus = 'Reject';
                            break;

                        case 'wait':
                            $pushMSG = 'Your reservation requested has been waitlisted by ' . $requestTime . ' mins. at ';
                            $bookingStatus = 'Waiting';
                            $waitTime = (int) $arr_request_code[2];
                            break;
                    }

                    $this->db->update('tbl_table_book', array('eBookingStatus' => $bookingStatus, 'iWaitTime' => $waitTime), array('iTableBookID' => $get_rec['iTableBookID']));
                    $restaurantName = $this->db->query('SELECT vRestaurantName FROM tbl_restaurant WHERE iRestaurantID IN(' . $get_rec['iRestaurantID'] . ')')->row_array()['vRestaurantName'];
                    $this->load->library('pushnotify');

                    $osType = $user_rec['ePlatform'] == 'ios' ? 2 : 1;
                    $deviceToken = $user_rec['vDeviceToken'];

                    $pushMSG .= $restaurantName . '.';

                    $this->general_model->saveUserNotification($user_id, $pushMSG, $get_rec['iRestaurantID'], $get_rec['iTableBookID'], 'table', $bookingStatus);

                    if ($deviceToken != '') {
                        /*
                         * IF ANY NOTIFICATION SEND FROM THE VENDOR APPLICATION SIDE THAN WE HAVE TO SEND BADGE 
                         * VALUE FROM THE OUR END... - BUT IT JUST FOR USER NOTIFICATION SIDE...
                         */
                        $BADGECOUNT = $user_rec['iNotifyCount'];
                        if ($user_rec['isNotify'] == 'yes') {
                            $this->pushnotify->sendIt($osType, $deviceToken, $pushMSG, 1, array('type' => 'table', 'id' => $get_rec['iTableBookID']), $BADGECOUNT);
                        }
                    }

                    /* SEND SMS */
                    $mobile_no = $user_rec['vMobileNo'];
                    if ($mobile_no != '' && strlen($mobile_no) == 10) {
                        //$this->load->library('smslane');
                        //$this->smslane->send(array('91' . $mobile_no), $pushMSG);
                        $this->load->model('Sms_model', 'sms_m');
                        $this->sms_m->destmobileno = $mobile_no;
                        $this->sms_m->msg = $pushMSG;
                        $this->sms_m->Send();
                        
                    }


                    /* update take action */
                    $this->db->update('smslane_reply', array('take_action' => 'yes'), array('lane_id' => $insert_id));
                }
            }
        }
    }

}

?>
