<?php

class Jobs_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_job_details';
        $this->load->library('DatatablesHelper');
    }
    
    
    
    
     // **********************************************************************
    // Display List of Category
    // **********************************************************************
    function getJobsDataAll() {
        $this->db->from($this->table);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    
    //getAllLocation
    function getAllLocation(){
         $this->db->select('*');
         $this->db->from('tbl_location_zone');
         $this->db->where('eStatus', 'Active');
         $q = $this->db->get();
            foreach($q->result_array() as $col)
            {
            $data[] = $col;
            }
            
            return $data;
    }
    
    
   //get_paginationresult 
    
        function get_paginationresult() {
        $qry = "SELECT "
                . "iJobDetailID, "
                . "tbl_job_details.vJobTitle , "
                . "tbl_job_details.vJobLocation, "
                . "tbl_job_details.iMinExp, "
                . "tbl_job_details.eStatus, "
                ."tbl_location_zone.vZoneName,"
                . "DATE_FORMAT(tbl_job_details.tCreatedAt , '%d %b %Y %h:%i %p') as tCreatedAt, "
                 . "iJobDetailID AS DT_RowId "
                . "FROM tbl_job_details INNER JOIN tbl_location_zone ON tbl_job_details.vJobLocation = tbl_location_zone.iLocZoneID WHERE tbl_job_details.eStatus != 'Deleted'";

        $data = $this->datatableshelper->query($qry);
        return $data;
    }
    
    
    
    
    /*     * **********************************************************************
      Category Setting(Getting member details by admin_id(Selected or inserted)
     * ********************************************************************** */

    function getJobDataById($iJobID) {
        $result = $this->db->get_where($this->table, array('iJobDetailID' => $iJobID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }
    
  
    
    function addJob($jobData) {
        extract($jobData);

        $data = array(
            'vJobTitle' => $vJobTitle,
            'vJobLocation' =>$vJobLocation,
            'iMinExp' => $iMinExp,
            'iEmploymentType' => $iEmploymentType,
            'tJobDescription' =>$tJobDescription,
            'eStatus' => 'Active',
            'tCreatedAt' => date('Y-m-d H:i:s')
        );
        $query = $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0)
            return $this->db->insert_id();
        else
            return '';
    }
    
    
    
    
    
     // **********************************************************************
    // Edit editJob
    // **********************************************************************
    function editJob($jobData) {

        extract($jobData);
        $data = array(
            'vJobTitle' => $vJobTitle,
            'vJobLocation' =>$vJobLocation,
            'iMinExp' => $iMinExp,
            'iEmploymentType' => $iEmploymentType,
            'tJobDescription' =>$tJobDescription
            
        );

        $query = $this->db->update($this->table, $data, array('iJobDetailID' => $iJobDetailID));
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }
    
    

    
    
      // **********************************************************************
    // Category Status
    // **********************************************************************
    function changeJobStatus($iJobDetailID) {
        //$updateData = array('admin_status' => 'IF (admin_status = "Active", "Inactive","Active")');
        //$query = $this->db->update($this->admin_tbl,$updateData, array('admin_role ' => $admin_role));
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iJobDetailID = $iJobDetailID");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }
    
    
    
    // **********************************************************************
    // remove admin
    // **********************************************************************
    function removeJob($iJobID) {
        $adid = implode(',', $iJobID);
        //mprd($adid);
        if ($adid !== '') {
            $query = $this->db->query("UPDATE $this->table set eStatus='Deleted' WHERE iJobDetailID in ($adid) ");
            //$query = $this->db->delete($this->table, array('iCategoryID' => $iCategoryID));
            //mprd($this->db->last_query());
            if ($this->db->affected_rows() > 0) {
                return $query;
            } else
                return '';
        }
    }


}
?>
