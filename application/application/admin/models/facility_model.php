<?php

class Facility_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_facility';
        $this->load->library('DatatablesHelper');
    }

    // **********************************************************************
    // Display List of Facility
    // **********************************************************************
    function getFacilityDataAll() {
        $this->db->from($this->table);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    /*     * **********************************************************************
      Facility Setting(Getting cuisine details by admin_id(Selected or inserted)
     * ********************************************************************** */

    function getFacilityDataById($iFacilityID) {
        $result = $this->db->get_where($this->table, array('iFacilityID' => $iFacilityID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    // **********************************************************************
    // Facility Status
    // **********************************************************************
    function changeFacilityStatus($iFacilityID) {
        //$updateData = array('admin_status' => 'IF (admin_status = "Active", "Inactive","Active")');
        //$query = $this->db->update($this->admin_tbl,$updateData, array('admin_role ' => $admin_role));
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iFacilityID = $iFacilityID");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function removeFacility($iFacilityID) {
        $adid = implode(',', $iFacilityID);
        //mprd($adid);
        $query = $this->db->query("DELETE from $this->table WHERE iFacilityID in ($adid) ");
        //$query = $this->db->delete($this->table, array('iFacilityID' => $iFacilityID));
        //mprd($this->db->last_query());
        if ($this->db->affected_rows() > 0) {
            return $query;
        } else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function removeComment() {

        /*        $iCommentID = $this->input->post('iCommentID');
          if($iCommentID != ''){
          $query=$this->db->query("DELETE from tbl_comment WHERE iCommentID  = $iCommentID ");
          if ($this->db->affected_rows() > 0)
          {
          return '1';
          }
          else
          return '';
          }
          else */
        return '';
    }

    public function checkFacilityNameAvailable($vFacilityName, $iFacilityID = '') {
        if ($iFacilityID != "") {
            $result = $this->db->query("SELECT `iFacilityID`  FROM `tbl_facility` WHERE (`vFacilityName` = '" . $vFacilityName . "') AND  `iFacilityID` != $iFacilityID");
        } else {
            $this->db->where('vFacilityName', $vFacilityName);
            $result = $this->db->get($this->table);
        }

        if ($result->num_rows() >= 1)
            return 0;
        else
            return 1;
    }

    // **********************************************************************
    // add admin
    // **********************************************************************
    function addFacility($cuisineData) {
        extract($cuisineData);

        $data = array(
            'vFacilityName' => $vFacilityName,
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
    // Edit admin
    // **********************************************************************
    function editFacility($cuisineData) {

        extract($cuisineData);
        $data = array(
            'vFacilityName' => $vFacilityName
        );

        $query = $this->db->update($this->table, $data, array('iFacilityID' => $iFacilityID));
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    function editFacilityData($data, $iFacilityID = '') {
        if ($iFacilityID != '') {
            $query = $this->db->update($this->table, $data, array('iFacilityID' => $iFacilityID));
            if ($this->db->affected_rows() > 0)
                return $query;
            else
                return '';
        }else {
            return '';
        }
    }

    function get_paginationresult() {
        $qry = "SELECT "
                . "vFacilityName, "
                . "(SELECT COUNT(trf.iRestaurantID) "
                . "FROM tbl_restaurant_facility trf "
                . "WHERE trf.iFacilityID IN(tf.iFacilityID)) as total_restaurants, "
                . "tCreatedAt as tCreatedAt1, "
                . "eStatus, "
                . "iFacilityID, "
                . "DATE_FORMAT(tCreatedAt, '%d %b %Y %h:%i %p') as tCreatedAt, "
                . "iFacilityID AS DT_RowId "
                . "FROM tbl_facility AS tf";

        $data = $this->datatableshelper->query($qry);
        return $data;
    }

    function getCommentList() {
        /*        $iFacilityID = $this->input->post('iFacilityID');
          if($iFacilityID != ''){
          $str = "SELECT iCommentID , CONCAT(vFirstName, ' ', vLastName) as vName   , vFacilityName as vFacility ,  vCommentText , u.iUserID  , u.vProfilePicture , cm.iFacilityID , DATE_FORMAT(cm.tCreatedAt,'%d %b %Y %h:%i %p') as tCreatedAt  from tbl_comment cm , tbl_facility p , tbl_user u , tbl_facility as s where s.iFacilityID = p.iFacilityID and u.iUserID = cm.iUserID and p.iFacilityID = cm.iFacilityID and cm.iFacilityID = $iFacilityID ";
          $result = $this->db->query($str);
          if ($result->num_rows() > 0)
          return $result->result_array();
          else
          return 'nocomment';
          }else
          return '';
         */
        return '';
    }

    function getRestaurantDataByFclId($facility) {
        $query = "SELECT trc.iRestaurantID as restId,tr.vRestaurantName as restName FROM tbl_restaurant_facility as trc LEFT JOIN tbl_restaurant as tr on trc.iRestaurantID = tr.iRestaurantID WHERE trc.iFacilityID=$facility order by tr.vRestaurantName asc";
        $result = $this->db->query($query)->result_array();
        $data = array();
        foreach ($result AS $row) {
            $data[] = $row["restName"];
        }
        return $data;
    }

}

?>
