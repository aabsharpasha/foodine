<?php

class QRPoint_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_qrcode';
        $this->load->library('DatatablesHelper');
    }

    /*
     * GET ALL THE RECORDS FROM THE DEALS TABLE...
     */

    function getQRPointDataAll() {
        $this->db->from($this->table);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    /*
     * GET THE RECORD BY ID
     */

    function getQRPointDataById($iQRCodeID) {
        $result = $this->db->get_where($this->table, array('iQRCodeID' => $iQRCodeID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    /*
     * GET PAGINATION RECORDS TO DISPLAY THE TABLE...
     */

    function get_paginationresult() {
        $where_condition = '';
        $iRestaurantID = (int) $this->session->userdata('iRestaurantID');
        if ($iRestaurantID != 0) {
            $where_condition .= ' AND td.iRestaurantID = "' . $iRestaurantID . '"';
        }

        $data = $this->datatableshelper->query("SELECT iQRCodeID, iQRCodePoitns, vRestaurantName, vQRCodeImage, CONCAT('Min ', iMinBillAmount, ' - Max ', iMaxBillAmount) AS vQRCodeAmount, tq.eStatus, DATE_FORMAT(tq.tCreatedAt , '%d %b %Y %h:%i %p') as tCreatedAt, iQRCodeID AS DT_RowId FROM tbl_qrcode AS tq, tbl_restaurant AS tr WHERE tq.iRestaurantID = tr.iRestaurantID " . $where_condition);
        return $data;
    }

    /*
     * DELETE THE DEALS EITHER ONE OR MORE...
     */

    function removeQRPoint($iQRCodeID) {
        $adid = implode(',', $iQRCodeID);

        $query = $this->db->query("DELETE from $this->table WHERE iQRCodeID IN ($adid) ");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    /*
     * TO CHANGE THE STATUS OF THE SELECTED RECORD...
     */

    function changeQRPointStatus($iQRCodeID) {
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iQRCodeID = '" . $iQRCodeID . "'");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    /*
     * GET THE LIST OF RESTAURANTS...
     */

    function getRestaurantList() {
        $where_condition = '';
        $iRestaurantID = (int) $this->session->userdata('iRestaurantID');
        if ($iRestaurantID != 0) {
            $where_condition .= ' AND iRestaurantID = "' . $iRestaurantID . '"';
        }

        $data = $this->datatableshelper->query("SELECT * FROM tbl_restaurant WHERE eStatus = 'Active' " . $where_condition);

        return $data['aaData'];
    }

    /*
     * GET THE LIST OF RESTAURANTS...
     */

    function getRestaurantById($iRestaurantID) {
        $where_condition = '';
        //$iRestaurantID = (int) $this->session->userdata('iRestaurantID');
        if ($iRestaurantID != '') {
            $where_condition .= ' AND iRestaurantID = "' . $iRestaurantID . '"';
        }

        $data = $this->datatableshelper->query("SELECT * FROM tbl_restaurant WHERE eStatus = 'Active' " . $where_condition);

        return $data['aaData'];
    }

    /*
     * TO INSERT THE DEAL RECORD
     */

    function addQRPoint($fieldRecord) {
        extract($fieldRecord);

        $data = array(
            'iRestaurantID' => $iRestaurantID,
            'iQRCodePoitns' => $iQRCodePoitns,
            'iMinBillAmount' => $iMinBillAmount,
            'iMaxBillAmount' => $iMaxBillAmount,
            'eStatus' => 'Active',
            'tCreatedAt' => date('Y-m-d H:i:s')
        );

        $query = $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0)
            return $this->db->insert_id();
        else
            return '';
    }

    /*
     * TO EDIT THE RECORDS...
     */

    function editQRPoint($fieldRecord) {
        extract($fieldRecord);

        $data = array(
            'iRestaurantID' => $iRestaurantID,
            'iQRCodePoitns' => $iQRCodePoitns,
            'iMinBillAmount' => $iMinBillAmount,
            'iMaxBillAmount' => $iMaxBillAmount,
        );

        $query = $this->db->update($this->table, $data, array('iQRCodeID' => $iQRCodeID));
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    function editQRCodeData($data, $iQRCodeID = '') {
        if ($iQRCodeID != '') {
            $query = $this->db->update($this->table, $data, array('iQRCodeID' => $iQRCodeID));
            if ($this->db->affected_rows() > 0)
                return $query;
            else
                return '';
        } else {
            return '';
        }
    }

}

?>
