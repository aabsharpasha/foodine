<?php

class Voucher_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_vouchers';
        $this->load->library('DatatablesHelper');
    }

    function getVoucherDataAll() {
        $result = $this->db->get_where($this->table, array('eStatus' => 'Active'));
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function get_paginationresult() {
        $where_condition = '';

        $data = $this->datatableshelper->query("SELECT iVoucherID, iVoucherID AS DT_RowId, tv.vTitle as vTitle, tv.vCode as vCode, DATE_FORMAT(tv.tStartDate , '%d %b %Y') AS tStartDate, DATE_FORMAT(tv.tEndDate , '%d %b %Y') AS tEndDate, tv.eValueType as eValueType, tv.dValue as dValue, tv.vDescription as vDescription, tv.dMinOrderValue as dMinOrderValue, tv.eUserSpecific as eUserSpecific, tv.eOneTimeUsable as eOneTimeUsable, tv.ePublic as ePublic,tv.eStatus as eStatus, tv.iVoucherUseId as iVoucherUseId, tv.tCreatedAt as tCreatedAt, tv.tModifiedAt as tModifiedAt, (SELECT count(1) FROM tbl_availed_vouchers as tav WHERE tv.iVoucherID=tav.iVoucherID) AS useCount, tvu.vDescription AS voucherUse  FROM tbl_vouchers AS tv LEFT JOIN tbl_voucher_use AS tvu ON tv.iVoucherUseId=tvu.iVoucherUseId  WHERE tv.estatus<>'Deleted'" . $where_condition);
        return $data;
    }
    
    function softDeleteVoucher($id) {
        $adid = implode(',', $id);
        $this->db->query("UPDATE $this->table SET eStatus = 'Deleted' WHERE iVoucherID in ($adid) ");
        if ($this->db->affected_rows() > 0) {
            return true;
        } else{
            return false;
        }
    }
    
    function changeStatus($id) {
        $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iVoucherID = $id");
        if ($this->db->affected_rows() > 0)
            return true;
        else
            return false;
    }

    function getVoucherDataById($id) {
        $result = $this->db->get_where($this->table, array('iVoucherID' => $id));
        if ($result->num_rows() > 0){
            return $result->row_array();
        }else{
            return '';
        }
    }

    function getVoucherUseList(){
        $data = $this->datatableshelper->query("SELECT * FROM tbl_voucher_use");
        return $data['aaData'];
    }
    
    function addVoucher($data) {
        $data['tStartDate']     = date("Y-m-d H:i:s", strtotime($data['tStartDate']));
        $data['tEndDate']       = date("Y-m-d H:i:s", strtotime($data['tEndDate']));
        $data['tCreatedAt']     = date("Y-m-d H:i:s");
        $data['tModifiedAt']    = date("Y-m-d H:i:s");
        if($data['eUserSpecific'] == 'Yes') {
            $public = 'No';
        }else {
            $public = $data['publicvoucher'];
        }
        $data['ePublic']    = $public;
        unset($data['action']);
        unset($data['publicvoucher']);
        
        $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0){
            return $this->db->insert_id();
        }else{
            return '';
        }
    }
    
    function editVoucher($data) {
        
        $iVoucherID             = $data['iVoucherID'];
        $data['tStartDate']     = date("Y-m-d H:i:s", strtotime($data['tStartDate']));
        $data['tEndDate']       = date("Y-m-d H:i:s", strtotime($data['tEndDate']));
        $data['tModifiedAt']    = date("Y-m-d H:i:s");
        if($data['eUserSpecific'] == 'Yes') {
            $public = 'No';
        }else {
            $public = $data['publicvoucher'];
        }
        $data['ePublic']    = $public;
        unset($data['iVoucherID']);
        unset($data['action']);
        unset($data['publicvoucher']);
        
        $this->db->update($this->table, $data, array('iVoucherID' => $iVoucherID));
        if ($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
    
}

?>
