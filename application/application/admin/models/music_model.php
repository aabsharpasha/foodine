<?php

class Music_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_music';
        $this->load->library('DatatablesHelper');
    }

    // **********************************************************************
    // Display List of Music
    // **********************************************************************
    function getMusicDataAll() {
        $this->db->from($this->table);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    /*     * **********************************************************************
      Music Setting(Getting cuisine details by admin_id(Selected or inserted)
     * ********************************************************************** */

    function getMusicDataById($iMusicID) {
        $result = $this->db->get_where($this->table, array('iMusicID' => $iMusicID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    // **********************************************************************
    // Music Status
    // **********************************************************************
    function changeMusicStatus($iMusicID) {
        //$updateData = array('admin_status' => 'IF (admin_status = "Active", "Inactive","Active")');
        //$query = $this->db->update($this->admin_tbl,$updateData, array('admin_role ' => $admin_role));
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iMusicID = $iMusicID");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function removeMusic($iMusicID) {
        $adid = implode(',', $iMusicID);
        //mprd($adid);
        $query = $this->db->query("DELETE from $this->table WHERE iMusicID in ($adid) ");
        //$query = $this->db->delete($this->table, array('iMusicID' => $iMusicID));
        //mprd($this->db->last_query());
        if ($this->db->affected_rows() > 0) {
            return $query;
        }
        else
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

    public function checkMusicNameAvailable($vMusicName, $iMusicID = '') {
        if ($iMusicID != "") {
            $result = $this->db->query("SELECT `iMusicID`  FROM `tbl_music` WHERE (`vMusicName` = '" . $vMusicName . "') AND  `iMusicID` != $iMusicID");
        } else {
            $this->db->where('vMusicName', $vMusicName);
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
    function addMusic($cuisineData) {
        extract($cuisineData);

        $data = array(
            'vMusicName' => $vMusicName,
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
    function editMusic($cuisineData) {

        extract($cuisineData);
        $data = array(
            'vMusicName' => $vMusicName
        );

        $query = $this->db->update($this->table, $data, array('iMusicID' => $iMusicID));
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    function editMusicData($data, $iMusicID = '') {
        if ($iMusicID != '') {
            $query = $this->db->update($this->table, $data, array('iMusicID' => $iMusicID));
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
                . "vMusicName,  "
                . "(SELECT COUNT(trm.iRestaurantID) "
                . "FROM tbl_restaurant_music trm "
                . "WHERE trm.iMusicID IN(tm.iMusicID)) as total_restaurants, "
                . "tCreatedAt as tCreatedAt1, "
                . "eStatus, "
                . "iMusicID, "
                . "DATE_FORMAT(tCreatedAt , '%d %b %Y %h:%i %p') as tCreatedAt, "
                . "iMusicID AS DT_RowId "
                . "FROM tbl_music AS tm";

        $data = $this->datatableshelper->query($qry);
        return $data;
    }

    function getCommentList() {
        /* $iMusicID = $this->input->post('iMusicID');
          if($iMusicID != ''){
          $str = "SELECT iCommentID , CONCAT(vFirstName, ' ', vLastName) as vName   , vMusicName as vMusic ,  vCommentText , u.iUserID  , u.vProfilePicture , cm.iMusicID , DATE_FORMAT(cm.tCreatedAt,'%d %b %Y %h:%i %p') as tCreatedAt  from tbl_comment cm , tbl_music p , tbl_user u , tbl_music as s where s.iMusicID = p.iMusicID and u.iUserID = cm.iUserID and p.iMusicID = cm.iMusicID and cm.iMusicID = $iMusicID ";
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

}

?>
