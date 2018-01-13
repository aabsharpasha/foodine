<?php

class Sponsers_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_restaurant_sponsered';
        $this->load->library('DatatablesHelper');
        $this->load->helper('string');
    }

    /*
     * GET ALL THE RECORDS FROM THE DEALS TABLE...
     */

    function getSponsersDataAll() {
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

    function getSponsersDataById($sponserId) {
        $result = $this->db->get_where($this->table, array('iSponserId' => $sponserId));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }
    
    function getSubDealsDataById($iComboSubOffersID) {
       
        $result = $this->db->get_where('tbl_combo_sub_offers', array('iComboSubOffersID' => $iComboSubOffersID));
         //return $result->num_rows();
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    /*
     * GET THE RECORD BY ID
     */

    function getSponsersDataViewById($iSponserId) {

        $fields[] = 'tr.vRestaurantName';
        $fields[] = 'ts.iRestaurantId';
        $fields[] = 'tr.iRestaurantID as id';
        $fields[] = 'ts.iSponserId';
        $fields[] = 'ts.iSponserKeywords';
        $fields[] = 'DATE_FORMAT(ts.dtStartDate , \'%d %b %Y %h:%i %p\') AS dtStartDate';
        $fields[] = 'DATE_FORMAT(ts.dtExpiryDate , \'%d %b %Y %h:%i %p\') AS dtExpiryDate';

        $qry = 'SELECT ' . implode(',', $fields) . ' FROM tbl_restaurant_sponsered ts, tbl_restaurant tr WHERE iSponserId = ' . $iSponserId . ' AND tr.iRestaurantID = ts.iRestaurantId';
        $result = $this->db->query($qry);
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
        $data = $this->datatableshelper->query("SELECT ts.iSponserKeywords as iSponserKeywords,  vRestaurantName, tr.iRestaurantID as iRestaurantID, ts.dtStartDate as dtStartDate1  , ts.dtExpiryDate as dtExpiryDate1 ,  DATE_FORMAT(ts.dtStartDate , '%d %b %Y') as dtStartDate, DATE_FORMAT(ts.dtExpiryDate , '%d %b %Y') as dtExpiryDate, ts.eStatus  as eStatus , ts.iSponserId as iSponserId , iSponserId AS DT_RowId FROM tbl_restaurant_sponsered AS ts, tbl_restaurant AS tr WHERE ts.iRestaurantID = tr.iRestaurantID " . $where_condition);
        return $data;
    }

    /*
     * DELETE THE DEALS EITHER ONE OR MORE...
     */

    function removeSponsers($iSponserId) {
        $adid = implode(',', $iSponserId);
        $query = $this->db->query("DELETE from $this->table WHERE iSponserId IN ($adid) ");
        
        if ($this->db->affected_rows() > 0) {
            return $query;
        }
        else {
            return '';
        }
    }

    /*
     * TO CHANGE THE STATUS OF THE SELECTED RECORD...
     */

    function changeSponsersStatus($iSponserId) {
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iSponserId = '" . $iSponserId . "'");
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

    function addSponser($fieldRecord) {
        extract($fieldRecord);
        $data = array(
            'iRestaurantId' => $iRestaurantId,
            'iSponserKeywords' => implode(',',array_unique($iSponserKeywords)),
            'dtStartDate' => date('Y-m-d H:i:s', strtotime($dtStartDate)),
            'dtExpiryDate' => date('Y-m-d H:i:s', strtotime($dtExpiryDate)),
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

    function editSponser($fieldRecord) {
        extract($fieldRecord);
        $data = array(
            'iRestaurantId' => $iRestaurantId,
            'iSponserKeywords' => implode(',',array_unique($iSponserKeywords)),
            'dtStartDate' => date('Y-m-d H:i:s', strtotime($dtStartDate)),
            'dtExpiryDate' => date('Y-m-d H:i:s', strtotime($dtExpiryDate))
        );
        $query = $this->db->update($this->table, $data, array('iSponserId' => $iSponserId));
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

}

?>
