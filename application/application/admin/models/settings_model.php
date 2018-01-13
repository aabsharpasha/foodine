<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Settings_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        //Do your magic here
    }

    public function getalldata() {
        $admindata = $this->db->get('tbl_setting');
        if ($admindata->num_rows() > 0) {
            return $admindata->row_array();
        } else {
            return "";
        }
    }

    public function updatesetting() {
        $settingdata = $this->db->get('tbl_setting');
        if ($settingdata->num_rows() > 0) {
            $data = array('vContactmail' => $_POST['vContactmail'], 'vCompanymail' => $_POST['vCompanymail']);
            $this->db->update('tbl_setting', $data);
            if ($this->db->affected_rows() > 0) {
                return true;
            }
            else
                return "";
        }
        else {
            $data = array('vContactmail' => $_POST['vContactmail'], 'vCompanymail' => $_POST['vCompanymail'], 'tCreatedAt' => date('Y-m-d H:i:s'));
            $this->db->insert('tbl_setting', $data);
            if ($this->db->insert_id() != '') {
                return true;
            }
            else
                return "";
        }
    }

}

/* End of file settings_model.php */
/* Location: ./application/admin/models/settings_model.php */