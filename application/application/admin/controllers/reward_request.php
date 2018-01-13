<?php

class Reward_Request extends CI_Controller {

    var $controller, $uppercase, $title;

    function __construct() {
        parent::__construct();
        $this->load->library('DatatablesHelper');
        $this->load->model('reward_request_model');
        $this->load->helper('string');
        $this->controller = 'reward_request';
        $this->uppercase = 'Reward Request';
        $this->title = 'Reward Request Management';
    }

    /*
     * TO DISPLAY LIST OF DEALS TO THE TABLE FORMAT...
     */

    function index() {
        $getRecords = $this->reward_request_model->getRewardRequestDataAll();
        $viewData = array("title" => "Reward Request Management");
        $viewData['breadCrumbArr'] = array("reward" => "Reward Request Management");
        if ($getRecords != '')
            $viewData['record_set'] = $getRecords;
        else
            $viewData['record_set'] = '';


        $this->load->view('reward_request/reward_request_view', $viewData);
    }

    /*
     * TO LIST OUT THE LIST OF RECORDS...
     */

    function paginate() {
        $data = $this->reward_request_model->get_paginationresult();
        echo json_encode($data);
    }

    /*
     * DELETE EITHER ONE RECORD OR LIST OF RECORDS... 
     */

    function deleteAll() {
        $data = $_POST['rows'];
        $removeDeal = $this->reward_request_model->removeRewardRequest($_POST['rows']);
        if ($removeDeal != '') {
            echo '1';
        } else {
            echo '0';
        }
    }

    /*
     * TO CHANGE THE STATUS OF THE RECORD..
     */

    function status($iRewardRequestID = '', $rm = '') {
        if ($iRewardRequestID != '' && $rm != '') {
            $changeStatus = $this->reward_request_model->changeRewardRequestStatus($iRewardRequestID, $rm);

            if (!empty($changeStatus)) {

                $record = $this->reward_request_model->get_paginationresult($iRewardRequestID);
                $record = $record['aaData'][0];
                //mprd($record);

                /*
                 * SEND MAIL FUNCTIONALITY...
                 */
                $rewardMsg = '';
                $pushMSG = ' Your reward request has been ';
                if ($record['eStatus'] === 'Accept') {
                    $rewardMsg = 'Your reward request has been accepted successfully.';
                    $pushMSG .= 'accepted';
                } else if ($record['eStatus'] === 'Reject') {
                    $rewardMsg = 'Your reward request has been rejected.';
                    $pushMSG .= 'rejected.';
                }

                //$this->load->model('smtpmail_model');
                $this->load->library('maillib');
                $param = array(
                    '%MAILSUBJECT%' => 'Foodine : Reward Notification',
                    '%LOGO_IMAGE%' => DOMAIN_URL . '/images/blacklist.jpg',
                    '%REWARD_REQUEST_USER%' => $record['vFullName'],
                    '%REWARD_MSG%' => $rewardMsg
                );
                $tmplt = DIR_ADMIN_VIEW . $this->controller . '/email/notify.php';
                $subject = 'Foodine : Notification';
                $to = $record['vEmail'];
                //$this->smtpmail_model->send($to, $subject, $tmplt, $param);
                //$this->maillib->sendMail($to, $subject, $tmplt, $param);
                $this->load->model("smtpmail_model", "smtpmail_model");
               // $this->smtpmail_model->send($to, $subject, $tmplt, $param);

                /*
                 * HERE PUSH NOTIFICATION WILL SEND IT TO USER APP
                 */
                $userId = $this->db->query('SELECT iUserID FROM tbl_reward_request WHERE iRewardRequestID IN(' . $iRewardRequestID . ')')->row_array()['iUserID'];
                $isNotify = $this->db->query('SELECT isNotify FROM tbl_user WHERE iUserID IN(' . $userId . ')')->row_array()['isNotify'];


                $record2 = $this->db->query('SELECT ePlatform, vDeviceToken FROM tbl_user WHERE iUserID IN(' . $record['iUserID'] . ')')->row_array();

                $this->load->library('pushnotify');

                $osType = $record2['ePlatform'] == 'ios' ? 2 : 1;
                $deviceToken = $record2['vDeviceToken'];

                // $pushMSG = ' Your reward request has been ' . ($record['eStatus'] == 'Accept' ? 'accepted' : 'declined');

                if ($deviceToken != '') {
                    /*
                     * IF ANY NOTIFICATION SEND FROM THE VENDOR APPLICATION SIDE THAN WE HAVE TO SEND BADGE 
                     * VALUE FROM THE OUR END... - BUT IT JUST FOR USER NOTIFICATION SIDE...
                     */
                    $BADGECOUNT = $this->db->query('SELECT iNotifyCount FROM tbl_user WHERE iUserID IN(' . $userId . ')')->row_array()['iNotifyCount'];
                    //$pushMSG .= $BADGECOUNT;
                    if ($isNotify == 'yes') {
                        $this->pushnotify->sendIt($osType, $deviceToken, $pushMSG, 1, array('type' => 'reward', 'id' => $record['iRewardID']), $BADGECOUNT);
                    }
                    /*
                     * SAVE USER NOTIFICATION HISTORY
                     */
                    $vStatus = ($rm == 'y' ? 'Accept' : 'Reject');
                    //$this->general_model->saveUserNotification($userId, $pushMSG);
                    $this->general_model->saveUserNotification($userId, $pushMSG, $record['iRewardID'], $iRewardRequestID, 'reward', $vStatus);
                }
                echo $changeStatus['eStatus'];
            } else {
                echo '0';
            }
        }
    }

    function userPointHistory($recordId=''){
        if ($recordId !== '') {
            $data["data"]   = $this->reward_request_model->getUserPointHistory($recordId);
            $result = $this->db->query("SELECT CONCAT(vFirstName,' ',vLastName) AS name FROM tbl_user WHERE iUserID='$recordId'")->row_array();
            $data["userName"]   = $result['name'];

            $this->load->view('reward_request/view_point_history', array('viewData' => $data));
        } return '';
    }

}

?>
