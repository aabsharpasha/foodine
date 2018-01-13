<?php

class AppCrash_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_app_crash_report';
        $this->load->library('DatatablesHelper');
    }

    /*
     * GET THE RECORD BY ID
     */

    function getCrashAppDataById($iDealID) {
        $result = $this->db->get_where($this->table, array('iCrashID' => $iDealID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    /*
     * GET PAGINATION RECORDS TO DISPLAY THE TABLE...
     */

    function get_paginationresult() {
        $fields = 'iCrashID';
        $fields .= ', iCrashID AS DT_RowId ';
        $fields .= ', eOsType';
        $fields .= ', errorValue';
        $fields .= ', activityName';
        $fields .= ', appVersion';
        $fields .= ', netType';
        $fields .= ', deviceSDK';
        $fields .= ', deviceRAM';
        $fields .= ', DATE_FORMAT(tCreatedAt , "%d %b %Y %h:%i %p") as tCreatedAt';

        $qry = "SELECT " . $fields . " FROM " . $this->table;

        $data = $this->datatableshelper->query($qry);

        return $data;
    }

}

?>
