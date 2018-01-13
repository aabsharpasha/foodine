<?php

class Location_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_location';
        $this->load->library('DatatablesHelper');
        $this->load->helper('string');
    }

    /*
     * GET ALL THE RECORDS FROM THE DEALS TABLE...
     */

    function getLocationDataAll() {
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

    function getLocationDataById($iLocationID) {
        $result = $this->db->get_where($this->table, array('iLocationID' => $iLocationID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    /*
     * GET PAGINATION RECORDS TO DISPLAY THE TABLE...
     */

    function get_paginationresult() {

        $fields[] = 'tl.iLocationID AS iLocationID';
        $fields[] = 'tl.iLocationID AS DT_RowId';
        $fields[] = "(SELECT COUNT(tr1.iRestaurantID) "
                . "FROM tbl_restaurant tr1 "
                . "WHERE tl.iLocationID IN(tr1.iLocationID) ) as total_restaurants";
        $fields[] = 'tl.vLocationName AS vLocationName';
        $fields[] = 'tlz.vZoneName AS vZoneName';
        $fields[] = 'tl.eStatus AS eStatus';

        $tbl[] = 'tbl_location AS tl';
        $tbl[] = 'tbl_location_zone AS tlz';

        $condition[] = 'tl.iLocZoneID = tlz.iLocZoneID';

        $tbl = implode(',', $tbl);
        $fields = implode(',', $fields);
        $condition = ' WHERE ' . implode(',', $condition);

        $qry = "SELECT " . $fields . ' FROM ' . $tbl . $condition;

        $data = $this->datatableshelper->query($qry);
        return $data;
    }

    /*
     * DELETE THE DEALS EITHER ONE OR MORE...
     */

    function removeLocation($iLocationID) {
        $adid = implode(',', $iLocationID);

        $query = $this->db->query("DELETE from $this->table WHERE iLocationID IN ($adid) ");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    /*
     * TO CHANGE THE STATUS OF THE SELECTED RECORD...
     */

    function changeLocationStatus($iLocationID) {
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iLocationID = '" . $iLocationID . "'");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    /*
     * TO INSERT THE DEAL RECORD
     */

    function addLocation($fieldRecord) {
        extract($fieldRecord);

        $data = array(
            'vLocationName' => $vLocationName,
            'iLocZoneID' => $iLocZoneID,
            'eStatus' => 'Active',
            'tCreatedAt' => date('Y-m-d H:i:s')
        );

        //$data['vDealCode'] = $this->checkDealCode();

        $query = $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0)
            return $this->db->insert_id();
        else
            return '';
    }

    /*
     * TO EDIT THE RECORDS...
     */

    function editLocation($fieldRecord) {
        extract($fieldRecord);

        $data = array(
            'vLocationName' => $vLocationName,
            'iLocZoneID' => $iLocZoneID
        );

        $query = $this->db->update($this->table, $data, array('iLocationID' => $iLocationID));
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    function getZone() {
        $res = $this->db->get_where('tbl_location_zone', array('eStatus' => 'Active'));

        return $res->result_array();
    }

}

?>
