<?php

class Application_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_job_applications';
        $this->load->library('DatatablesHelper');
    }
    
    
    
    
     // **********************************************************************
    // Display List of Category
    // **********************************************************************
    function getApplicationDataAll() {
        $this->db->from($this->table);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    
   //get_paginationresult 
    
        function get_paginationresult() {
        
            $qry = "SELECT "
                . "iApplicationID, "
                . "tbl_job_applications.iJobDetailID as iJobDetailID, "
                . "tbl_job_applications.vApplicantName  as vApplicantName, "
                . "tbl_job_applications.vApplicantEmail as vApplicantEmail, "
                . "tbl_job_applications.iApplicantPhone as iApplicantPhone, "
                . "tbl_job_applications.vApplicantResume as vApplicantResume, "
                . "tbl_job_applications.eStatus as eStatus, "
                . "tbl_job_details.vJobTitle as vJobTitle, "
                . "tlz.vZoneName as vJobLocation, "
                . "tbl_job_details.iMinExp as iMinExp, "
                 . "DATE_FORMAT(tbl_job_applications.tCreatedAt , '%d %b %Y %h:%i %p') as tCreatedAt, "
                 . "tbl_job_applications.iApplicationID AS DT_RowId "
                . "FROM tbl_job_applications  INNER JOIN tbl_job_details  ON tbl_job_details.iJobDetailID = tbl_job_applications.iJobDetailID LEFT JOIN tbl_location_zone as tlz ON tbl_job_details.vJobLocation = tlz.iLocZoneID";
        
        
        $data = $this->datatableshelper->query("$qry");
        return $data;
    }
    
    
    
    
    // **********************************************************************
    // remove admin
    // **********************************************************************
    function removeApplication($iApplicationID) {
        $adid = implode(',', $iApplicationID);
        //mprd($adid);
        if ($adid !== '') {
            $query = $this->db->query("UPDATE $this->table SET eStatus = 'Deleted' WHERE iApplicationID in ($adid) ");
            if ($this->db->affected_rows() > 0) {
                return $query;
            } else
                return '';
        }
    }


}
?>
