<?php

class Hiring_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_job_team_member';
        $this->load->library('DatatablesHelper');
    }
    
    
    
    
     // **********************************************************************
    // Display List of Category
    // **********************************************************************
    function getMemberDataAll() {
        $this->db->from($this->table);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    
    
    /*     * **********************************************************************
      Category Setting(Getting member details by admin_id(Selected or inserted)
     * ********************************************************************** */

    function getMemberDataById($iMemberID) {
        $result = $this->db->get_where($this->table, array('iMemberID' => $iMemberID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }
    
    
      /*
     * TO UPDATE THE Member IMAGE
     */

    function updateMemberImage($id, $imageName = '') {
        try {
            if (@$imageName !== '') {
                $this->db->update($this->table, array('vMemberImage' => $imageName), array('iMemberID' => $id));
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (Exception $ex) {
            throw new Exception('Error in updateCategoryImage function - ' . $ex);
        }
    }
    
    
    public function checkMemberNameAvailable($vMemberName, $iMemberID = '') {
        if ($iMemberID != "") {
            $result = $this->db->query("SELECT `iMemberID`  FROM `tbl_job_team_member` WHERE (`vMemberName` = '" . $vMemberName . "') AND  `iMemberID` != $iMemberID");
        } else {
            $this->db->where('vMemberName', $vMemberName);
            $result = $this->db->get($this->table);
        }

        if ($result->num_rows() >= 1)
            return 0;
        else
            return 1;
    }
    
    
    function addMember($memberData) {
        extract($memberData);

        $data = array(
            'vMemberName' => $vMemberName,
            'vMemberTagline' =>$vMemberTagline,
            'eStatus' => '1',
            'tCreatedAt' => date('Y-m-d H:i:s')
        );
        $query = $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0)
            return $this->db->insert_id();
        else
            return '';
    }
    
    
    
    
    
     // **********************************************************************
    // Edit member
    // **********************************************************************
    function editMember($memberData) {

        extract($memberData);
        $data = array(
            'vMemberName' => $vMemberName,
            'vMemberTagline' =>$vMemberTagline
        );

        $query = $this->db->update($this->table, $data, array('iMemberID' => $iMemberID));
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }
    
    
    function get_paginationresult() {
        $qry = "SELECT "
                . "vMemberName , "
                . "vMemberTagline, "
                . "vMemberImage, "
                . "eStatus, "
                . "iMemberID, "
                . "DATE_FORMAT(tCreatedAt , '%d %b %Y %h:%i %p') as tCreatedAt, "
                 . "iMemberID AS DT_RowId "
                . "FROM tbl_job_team_member AS tm where eStatus != 'Deleted'";

        $data = $this->datatableshelper->query($qry);
        return $data;
    }
    
    
    
    
      // **********************************************************************
    // Category Status
    // **********************************************************************
    function changeMemberStatus($iMemberID) {
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iMemberID = $iMemberID");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }
    
    
    
    // **********************************************************************
    // remove admin
    // **********************************************************************
    function removeMember($iMemberID) {
        $adid = implode(',', $iMemberID);
        //mprd($adid);
        if ($adid !== '') {
            $query = $this->db->query("UPDATE $this->table SET eStatus = 'Deleted' WHERE iMemberID in ($adid) ");
            //mprd($this->db->last_query());
            if ($this->db->affected_rows() > 0) {
                /*
                 * REMOVE DIRECTORY
                 */
                $ids = $iMemberID;
//                if (!empty($ids)) {
//                    $basePath = DOC_ROOT . '/images/team/';
//                    for ($i = 0; $i < count($ids); $i++) {
//                        $subFold = $basePath . $ids[$i] . '/';
//                        if (is_dir($subFold)) {
//
//                            $subThumb = $subFold . 'thumb/';
//                            $thumbFile = glob($subThumb . '*');
//                            foreach ($thumbFile as $file) {
//                                if (is_file($file))
//                                    unlink($file);
//                            }
//                            rmdir($subThumb);
//
//                            $files = glob($subFold . '*');
//                            foreach ($files as $f) {
//                                if (is_file($f))
//                                    unlink($f);
//                            }
//                            rmdir($subFold);
//                        }
//                    }
//                }
                return $query;
            } else
                return '';
        }
    }


}
?>
