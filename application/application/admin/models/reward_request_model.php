<?php

class Reward_Request_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_reward_request';
        $this->load->library('DatatablesHelper');
    }

    /*
     * GET ALL THE RECORDS FROM THE DEALS TABLE...
     */

    function getRewardRequestDataAll() {
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

    function getRewardRequestDataById($iRewardRequestID) {
        $result = $this->db->get_where($this->table, array('iRewardID' => $iRewardRequestID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    /*
     * GET PAGINATION RECORDS TO DISPLAY THE TABLE...
     */

    function get_paginationresult($requestId = '') {

        $table = $fields = $qry = '';
        $condition = array();

        $table = 'tbl_reward_request AS trp, tbl_reward AS tr, tbl_user AS tu';

        $fields .= 'vRewardTitle';
        $fields .= ', IF( vFullName = "", CONCAT(vFirstName, " ", vLastName), vFullName) AS vFullName';
        $fields .= ', vMobileNo';
        $fields .= ', iRewardPoint';
        $fields .= ', iRewardVoucher';
        $fields .= ", DATE_FORMAT(trp.tCreatedAt , '%d %b %Y %H:%i:%s') AS requestDate";
        $fields .= ', trp.iUserID AS iUserID';
        $fields .= ', vEmail';
        $fields .= ', trp.eStatus as eStatus';
        $fields .= ', iRewardRequestID';
        $fields .= ', trp.iRewardID AS iRewardID';
        $fields .= ', iRewardRequestID AS DT_RowId ';

        $condition[] = 'tr.iRewardID = trp.iRewardID';
        $condition[] = 'trp.iUserID = tu.iUserID';

        if ($requestId !== '') {
            $condition[] = 'trp.iRewardRequestID = \'' . $requestId . '\'';
        }

        if (!empty($condition)) {
            $condition = " WHERE " . implode(' AND ', $condition);
        } else {
            $condition = NULL;
        }

        $qry .= "SELECT " . $fields . " FROM " . $table . $condition;

        $data = $this->datatableshelper->query($qry);

        return $data;
    }

    /*
     * DELETE THE DEALS EITHER ONE OR MORE...
     */

    function getUserPointHistory($userId){
        $fields = array(
            "DATE_FORMAT(tup.tCreatedAt , '%d %b %Y %H:%i:%s') AS tCreatedAt",
            "tups.vType AS vType",
            "tups.iPoints AS iPoints",
            "tup.iUserPointsID"
        );
        $fieldStr   = implode(", ", $fields);
        
        $query  = "SELECT $fieldStr FROM tbl_user_points As tup LEFT JOIN tbl_user_point_system AS tups ON tup.iUserPointSystemID=tups.iUserPointSystemID WHERE tup.iUserID='$userId' ORDER BY tup.tCreatedAt";
        $result = $this->db->query($query)->result_array();
        return $result;
    }
    
    function removeRewardRequest($iRewardRequestID) {
        $adid = implode(',', $iRewardRequestID);

        $query = $this->db->query("DELETE from $this->table WHERE iRewardRequestID IN ($adid) ");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    /*
     * TO CHANGE THE STATUS OF THE SELECTED RECORD...
     */

    function changeRewardRequestStatus($iRewardRequestID, $rm) {
        $status_val = '';
        if ($rm === 'y') {
            $status_val = 'Accept';
        } else {
            $status_val = 'Reject';
        }
        
        
        $query = $this->db->query("UPDATE $this->table SET eStatus = '" . $status_val . "' WHERE iRewardRequestID = '" . $iRewardRequestID . "'");
        if ($this->db->affected_rows() > 0) {
            $res = $this->db->get_where($this->table, array('iRewardRequestID' => $iRewardRequestID));
            
            return $res->row_array();
        } else
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

}

?>
