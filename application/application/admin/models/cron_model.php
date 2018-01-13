<?php

class Cron_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_push_notification';
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

}

?>
