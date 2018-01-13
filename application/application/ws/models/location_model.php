<?php

class Location_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_location';
        $this->load->library('DatatablesHelper');
        $this->load->helper('string');
    }

    function getZone() {
        try {
            $qry = 'SELECT vZoneName AS name,iLocZoneID FROM tbl_location_zone WHERE eStatus = "Active"';
            $res = $this->db->query($qry)->result_array();
            $resp = array();
            foreach($res as $res) {
                $resp[$res['iLocZoneID']] = $res['name'];
            }
            return $resp;
        } catch (Exception $ex) {
            throw new Exception('Error in getZone function - ' . $ex);
        }
    }

}

?>
