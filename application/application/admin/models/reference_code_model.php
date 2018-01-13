<?php

class Reference_Code_Model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_reference_codes';
        $this->load->library('DatatablesHelper');
    }


    function get_paginationresult() {
        $where_condition = '';

        $data = $this->datatableshelper->query("SELECT trc.iReferenceCodeID AS iReferenceCodeID, iReferenceCodeID AS DT_RowId, trc.vTitle AS vTitle, trc.vReferenceCode AS vReferenceCode, DATE_FORMAT(trc.tStartDate , '%d %b %Y') AS tStartDate, DATE_FORMAT(trc.tEndDate , '%d %b %Y') AS tEndDate, trc.eHasVoucher AS eHasVoucher, trc.iVoucherID AS iVoucherID, trc.eStatus AS eStatus, trc.tCreatedAt AS tCreatedAt, trc.tModifiedAt AS tModifiedAt, IFNULL(tv.vCode,'') AS voucherCode, (SELECT count(1) FROM tbl_user_reference_codes WHERE trc.iReferenceCodeID=tbl_user_reference_codes.iReferenceCodeID) AS useCount  FROM tbl_reference_codes AS trc LEFT JOIN tbl_vouchers AS tv ON tv.iVoucherID=trc.iVoucherID  WHERE trc.estatus<>'Deleted'" . $where_condition);
        return $data;
    }
    
    function softDelete($id) {
        $adid = implode(',', $id);
        $this->db->query("UPDATE $this->table SET eStatus = 'Deleted' WHERE iReferenceCodeID in ($adid) ");
        if ($this->db->affected_rows() > 0) {
            return true;
        } else{
            return false;
        }
    }
    
    function changeStatus($id) {
        $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iReferenceCodeID = $id");
        if ($this->db->affected_rows() > 0)
            return true;
        else
            return false;
    }

    function getReferenceCodeDataById($id) {
        $result = $this->db->get_where($this->table, array('iReferenceCodeID' => $id));
        if ($result->num_rows() > 0){
            return $result->row_array();
        }else{
            return '';
        }
    }

    function getVoucherList(){
        $data = $this->datatableshelper->query("SELECT iVoucherID, vTitle, vCode, vDescription FROM tbl_vouchers WHERE eStatus='Active'");
        return $data['aaData'];
    }
    
    function addReferenceCode($data) {
        $data['tStartDate']     = date("Y-m-d H:i:s", strtotime($data['tStartDate']));
        $data['tEndDate']       = date("Y-m-d H:i:s", strtotime($data['tEndDate']));
        $data['tCreatedAt']     = date("Y-m-d H:i:s");
        $data['tModifiedAt']    = date("Y-m-d H:i:s");
        unset($data['action']);
        
        if( empty($data["iVoucherID"]) ){
            $data['eHasVoucher']    = 'no';
        }else{
            $data['eHasVoucher']    = 'yes';
        }
        $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0){
            return $this->db->insert_id();
        }else{
            return '';
        }
    }
    
    function editReferenceCode($data) {
        
        $iReferenceCodeID       = $data['iReferenceCodeID'];
        $data['tStartDate']     = date("Y-m-d H:i:s", strtotime($data['tStartDate']));
        $data['tEndDate']       = date("Y-m-d H:i:s", strtotime($data['tEndDate']));
        $data['tModifiedAt']    = date("Y-m-d H:i:s");
        
        unset($data['iReferenceCodeID']);
        unset($data['action']);
        
        if( empty($data["iVoucherID"]) ){
            $data['eHasVoucher']    = 'no';
        }else{
            $data['eHasVoucher']    = 'yes';
        }
        $this->db->update($this->table, $data, array('iReferenceCodeID' => $iReferenceCodeID));
        if ($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
    
}

?>
