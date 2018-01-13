<?php

class Reward_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_reward';
        $this->load->library('DatatablesHelper');
    }

    /*
     * GET ALL THE RECORDS FROM THE DEALS TABLE...
     */

    function getRewardDataAll() {
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

    function getRewardDataById($iDealID) {
        $result = $this->db->get_where($this->table, array('iRewardID' => $iDealID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    /*
     * GET PAGINATION RECORDS TO DISPLAY THE TABLE...
     */

    function get_paginationresult() {

        $table = $fields = $qry = '';
        $condition = array();

        $table = 'tbl_reward';

        $fields .= 'iRewardID';
        $fields .= ', iRewardID AS DT_RowId ';
        $fields .= ', vRewardTitle';
        $fields .= ', iRewardPoint';
        $fields .= ', iRewardVoucher';
        $fields .= ', vRewardImage';
        $fields .= ', eStatus';

        //$condition[] = 'tb.iRestaurantID = tr.iRestaurantID';
        //$condition[] = 'tb.iUserID = tu.iUserID';

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

    function removeReward($iDealID) {
        $adid = implode(',', $iDealID);

        $query = $this->db->query("DELETE from $this->table WHERE iRewardID IN ($adid) ");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    /*
     * TO CHANGE THE STATUS OF THE SELECTED RECORD...
     */

    function changeRewardStatus($iDealID) {
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iRewardID = '" . $iDealID . "'");
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
     * TO INSERT THE DEAL RECORD
     */

    function addReward($fieldRecord) {
        extract($fieldRecord);

        $data = array(
            'vRewardTitle' => $vRewardTitle,
            'iRewardPoint' => $iRewardPoint,
            'tRewardDesc' => $tRewardDesc,
            'dtRewardValidUpto' => date('Y-m-d H:i:s', strtotime($dtRewardValidUpto)),
            'iRewardVoucher' => $iRewardVoucher,
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

    function editReward($fieldRecord) {
        extract($fieldRecord);

        $data = array(
            'vRewardTitle' => $vRewardTitle,
            'iRewardPoint' => $iRewardPoint,
            'tRewardDesc' => $tRewardDesc,
            'dtRewardValidUpto' => date('Y-m-d H:i:s', strtotime($dtRewardValidUpto)),
            'iRewardVoucher' => $iRewardVoucher
        );

        $query = $this->db->update($this->table, $data, array('iRewardID' => $iRewardID));
        //$this->db->last_query();

        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return 1;
    }

    function editRewardData($data, $iRewardID = '') {
        if ($iRewardID != '') {
            $query = $this->db->update($this->table, $data, array('iRewardID' => $iRewardID));
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
