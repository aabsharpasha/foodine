<?php

class Cron_model extends CI_Model {

    var $table;
    public $pendingStatus = '1';
    public $inprocessStatus = '2';
    public $completeStatus = '3';

    function __construct() {
        parent::__construct();
        $this->table = 'push_notification_jobs';
    }

    function sendNotifications() {
        try {
            /*
             * GET THOSE RECORD FROM THE DATABASE
             */
            $rec = $this->db->get_where($this->table, array('eNotifyType' => 'schedule'))->result_array();
            if (!empty($rec)) {
                $this->db->where_not_in(array('ePlatform' => '', 'vDeviceToken' => ''));
                $DATA = $this->db->get_where('tbl_user', array('eStatus' => 'active'))->result_array();
                $this->load->library('pushnotify');

                foreach ($rec as $mk => $mv) {
                    if (!empty($DATA)) {
                        $mesg = $mv['vNotifyText'];

                        foreach ($DATA AS $k => $v) {
                            $osType = $v['ePlatform'] == 'ios' ? 2 : 1;
                            $deviceToken = $v['vDeviceToken'];

                            if ($deviceToken != '') {
                                $BADGECOUNT = $this->db->query('SELECT iNotifyCount FROM tbl_user WHERE iUserID IN(' . $v['iUserID'] . ')')->row_array()['iNotifyCount'];
                                if ($v['isNotify'] == 'yes') {
                                    $this->pushnotify->sendIt($osType, $deviceToken, $mesg, 1, array('type' => 'restaurant', 'id' => $INS_ID), $BADGECOUNT);
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $ex) {
            throw new Exception('Error in sendNotifications function - ' . $ex);
        }
    }

    public function addPushNotificaton($jobData) {
        try {
            extract($jobData);
            $data = array(
                'notification_id' => $notification_id,
                'criteria' => $criteria,
                'minage' => $minage,
                'maxage' => $maxage,
                'start_limit' => $start_limit,
                'end_limit' => $end_limit,
                'platform' => $platform,
                'created' => $created,
                'status' => $status,
                'parameter' => $parameter,
            );
            $query = $this->db->insert($this->table, $data);
        } catch (Exception $ex) {
            throw new Exception('Error in sendNotifications function - ' . $ex);
        }
    }

    public function getPendingJobs() {
        $this->db->from($this->table);
        $this->db->where('status', $this->pendingStatus);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    public function getRunningJobs() {
        $this->db->from($this->table);
        $this->db->where('status', $this->inprocessStatus);
        $result = $this->db->get();
        return $result->num_rows();
    }

    public function updateJobs($jobId, $status , $response=null) {
        try {
            $condition['id'] = $jobId;
            $jobData['status'] = $status;
            $jobData['response'] = $response;
            $this->db->update($this->table, $jobData, $condition);
        } catch (Exception $ex) {
            throw new Exception('Error in updateJobs function - ' . $ex);
        }
    }

    /**
     * @method      getNotificationByJobId
     * @desc        this function is used to find out notifications based on id.
     * @access      public
     * @author      3031@kelltontech.com
     * @return      notification in json format
     */
    public function getNotificationByJobId($jobId) {
        $conditions = array('id' => $jobId);
        $fields = array('notification_id', 'parameter','criteria','minage','maxage');
        $this->db->from($this->table);
        $this->db->select($fields);
        $this->db->where($conditions);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            $pushNotificationData = $result->row_array();
        else
            return array();
        
        if (!empty($pushNotificationData)) {
            $parameterArr['parameter'] = $pushNotificationData['parameter'];
            $parameterArr['criteria'] = $pushNotificationData['criteria'];
            $parameterArr['minage'] = $pushNotificationData['minage'];
            $parameterArr['maxage'] = $pushNotificationData['maxage'];
            return $parameterArr;
        }
        return array();
    }

}

?>
