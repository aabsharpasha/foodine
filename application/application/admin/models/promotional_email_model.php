<?php

class Promotional_Email_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_promotional_emails';
        $this->load->library('DatatablesHelper');
    }

    function getEmailDataAll() {
        $result = $this->db->get_where($this->table, array('eStatus' => 'Active'));
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function get_paginationresult() {
        $where_condition = '';
        $fields = array(
            "iPromotionalEmailId",
            "iPromotionalEmailId AS DT_RowId",
            "vSubject",
            "tContent",
            "DATE_FORMAT(tScheduledTime ,'%d %b %Y %h:%i %p') AS tScheduledTime",
            "eStatus",
            "DATE_FORMAT(tCreatedAt ,'%d %b %Y %h:%i %p') AS tCreatedAt"
        );
        
        $fieldsStr  = implode(", ",$fields);
        $data = $this->datatableshelper->query("SELECT $fieldsStr  FROM tbl_promotional_emails WHERE eStatus<>'Deleted'" . $where_condition);
        return $data;
    }
    
    function changeStatus($id) {
        $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iPromotionalEmailId = $id");
        if ($this->db->affected_rows() > 0)
            return true;
        else
            return false;
    }
    
    
    function softDelete($id) {
        $adid = implode(',', $id);
        $this->db->query("UPDATE $this->table SET eStatus = 'Deleted' WHERE iPromotionalEmailId in ($adid) ");
        if ($this->db->affected_rows() > 0) {
            return true;
        } else{
            return false;
        }
    }

    function getEmailDataById($id) {
        $result = $this->db->get_where($this->table, array('iPromotionalEmailId' => $id));
        if ($result->num_rows() > 0){
            $data   = $result->row_array();
            $data["scheduleDate"]   = date('j F, Y', strtotime($data["tScheduledTime"]));
            $data["scheduleTime"]   = date('g:i A', strtotime($data["tScheduledTime"]));
            $data["users"]          = explode(',', $data["tUsers"]);
            return $data;
        }else{
            return '';
        }
    }

    function getUserList(){
        $result = $this->db->query("SELECT iUserID,vFirstName,vLastName,eStatus FROM tbl_user where vFirstName != '' order by vFirstName ASC");
        return $result->result_array();
    }
    
    function ifEmailExistsAtTime($scheduledTime, $id=''){
        date('H:i:s', strtotime($scheduledTime));
        $conditions  = array("tScheduledTime='$scheduledTime'", "eStatus <> 'Deleted'");
        if($id){
            $conditions[]   = "iPromotionalEmailId <> $id ";
        }
        $conditionsStr = implode(" AND ", $conditions);
        $result = $this->db->query("SELECT * FROM tbl_promotional_emails WHERE $conditionsStr");
        if ($result->num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
    
    function addEmail($data) {
        $scheduleDate           = date('Y-m-d', strtotime($data['scheduleDate']));
        $scheduleTime           = date('H:i:s', strtotime($data['scheduleTime']));
        $data['tScheduledTime'] = $scheduleDate." ".$scheduleTime;
        $data['tUsers']         = implode(",", $data['tUsers']);
        $data['tCreatedAt']     = date("Y-m-d H:i:s");
        $data['tModifiedAt']    = date("Y-m-d H:i:s");
        unset($data['scheduleDate']);
        unset($data['scheduleTime']);
        unset($data['senditnow']);
        unset($data['action']);
        
        $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0){
            return $this->db->insert_id();
        }else{
            return '';
        }
    }
    
    function editEmail($data) {
        $iPromotionalEmailId    = $data['iPromotionalEmailId'];
        $scheduleDate           = date('Y-m-d', strtotime($data['scheduleDate']));
        $scheduleTime           = date('H:i:s', strtotime($data['scheduleTime']));
        $data['tScheduledTime'] = $scheduleDate." ".$scheduleTime;
        $data['tUsers']         = implode(",", $data['tUsers']);
        $data['tModifiedAt']    = date("Y-m-d H:i:s");
        unset($data['iPromotionalEmailId']);
        unset($data['scheduleDate']);
        unset($data['scheduleTime']);
        unset($data['senditnow']);
        unset($data['action']);
        
        $this->db->update($this->table, $data, array('iPromotionalEmailId' => $iPromotionalEmailId));
        if ($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
    
}

?>
