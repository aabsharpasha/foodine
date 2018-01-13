<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of link_model
 * link hashes expiry 24 hrs
 * @author Amit Malakar
 */
class merge_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        $this->table = 'tbl_merge';
    }

    function createMergeLink($linkData) {
        try {
            if(count($linkData)) {
                $this->db->insert($this->table, $linkData);
                return true;
            }
            return false;
        } catch (Exception $ex) {
            exit('Link Model : Error in createMergeLink function - ' . $ex);
        }
    }

    function verifyMergeLink($token) {
        try {
            if(strlen($token)) {
                $this->db->select('iMergeID, iUserID, iToUserID, iFromUserID')->from($this->table)
                    ->where("vHash = '$token'")
                    ->where("iExpired = 0")
                    ->where("tCreatedAt >= DATE_SUB(NOW(), INTERVAL 1 DAY)");
                $record = $this->db->get()->row_array();
                if($record) {
                    $mergeId = $record['iMergeID'];
                    $userId = $record['iUserID'];
                    $toUserId = $record['iToUserID'];
                    $fromUserId = $record['iFromUserID'];
                    // link expire
                    $this->db->update($this->table, array('iExpired' => 1), array('iMergeID' => $mergeId));

                    // move points from one user to another
                    $this->load->model("user_points_model", "user_points_model");
                    $this->user_points_model->moveUserPointOnMerge($fromUserId, $toUserId);

                    // set fromUserId user Inactive
                    $this->load->model("user_model", "user_model");
                    $result = $this->user_model->deactivateUser($fromUserId);

                    return $result;
                }
                return false;
            }
            return false;
        } catch (Exception $ex) {
            exit('Link Model : Error in verifyMergeLink function - ' . $ex);
        }
    }

}