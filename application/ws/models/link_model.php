<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Description of link_model
 * link hashes expiry 24 hrs
 * @author Amit Malakar
 */
class link_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();

        $this->table = 'link';
    }

    function createSingupLink($linkData) {
        try {
            if (count($linkData)) {
                $this->db->insert($this->table, $linkData);
                return true;
            }
            return false;
        } catch (Exception $ex) {
            exit('Link Model : Error in createSingupLink function - ' . $ex);
        }
    }

    function verifySignupLink($token) {
        try {
            if (strlen($token)) {
                $this->db->select('id, userid')->from($this->table)
                        ->where("hash = '$token'")
                        ->where("expired = 0")
                        ->where("datecreated >= DATE_SUB(NOW(), INTERVAL 1 DAY)");
                $record = $this->db->get()->row_array();
                if ($record) {
                    $userId = $record['userid'];
                    $id = $record['id'];
                    // update the link as expired
                    $this->db->update($this->table, array('expired' => 1), array('id' => $id));
                    // activate the user
                    $this->load->model("user_model", "user_model");
                    $this->user_model->activateUser($userId);
                    return $userId;
                }
                return false;
            }
            return false;
        } catch (Exception $ex) {
            exit('Link Model : Error in createSingupLink function - ' . $ex);
        }
    }

    function createForgotPwdLink($linkData) {
        try {
            if (count($linkData)) {
                $this->db->insert($this->table, $linkData);
                return true;
            }
            return false;
        } catch (Exception $ex) {
            exit('Link Model : Error in createForgotPwdLink function - ' . $ex);
        }
    }

    function verifyForgotPwdLink($token) {
        try {
            if (strlen($token)) {
                $this->db->select('id, userid')->from($this->table)
                        ->where("hash = '$token'")
                        ->where("expired = 0")
                        ->where("datecreated >= DATE_SUB(NOW(), INTERVAL 1 DAY)");
                $record = $this->db->get()->row_array();
                if ($record) {
                    $userId = $record['userid'];
                    $id = $record['id'];
                    return $id.'-'.$userId;
                }
                return false;
            }
            return false;
        } catch (Exception $ex) {
            exit('Link Model : Error in verifyForgotPwdLink function - ' . $ex);
        }
    }

}
