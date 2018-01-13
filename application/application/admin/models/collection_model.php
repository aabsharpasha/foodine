<?php

class Collection_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_collection';
        $this->load->library('DatatablesHelper');
    }

    /*
     * TO UPDATE THE CUISINE IMAGE
     */

    function updateCollectionImage($id, $imageName = '') {
        try {
            if (@$imageName !== '') {
                $this->db->update($this->table, array('vCollectionImage' => $imageName), array('iCollectionID' => $id));
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (Exception $ex) {
            throw new Exception('Error in updateCollectionImage function - ' . $ex);
        }
    }

    // **********************************************************************
    // Display List of Collection
    // **********************************************************************
    function getCollectionDataAll() {
        $this->db->from($this->table);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    /*     * **********************************************************************
      Collection Setting(Getting collection details by admin_id(Selected or inserted)
     * ********************************************************************** */

    function getCollectionDataById($iCollectionID) {
        $result = $this->db->get_where($this->table, array('iCollectionID' => $iCollectionID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    // **********************************************************************
    // Collection Status
    // **********************************************************************
    function changeCollectionStatus($iCollectionID) {
        //$updateData = array('admin_status' => 'IF (admin_status = "Active", "Inactive","Active")');
        //$query = $this->db->update($this->admin_tbl,$updateData, array('admin_role ' => $admin_role));
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iCollectionID = $iCollectionID");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function removeCollection($iCollectionID) {
        $adid = implode(',', $iCollectionID);
        //mprd($adid);
        $query = $this->db->query("DELETE from $this->table WHERE iCollectionID in ($adid) ");
        //$query = $this->db->delete($this->table, array('iCollectionID' => $iCollectionID));
        //mprd($this->db->last_query());
        if ($this->db->affected_rows() > 0) {

            /*
             * REMOVE DIRECTORY
             */
            $ids = $iCollectionID;
            if (!empty($ids)) {
                $basePath = DOC_ROOT . '/images/collection/';
                for ($i = 0; $i < count($ids); $i++) {
                    $subFold = $basePath . $ids[$i] . '/';
                    if (is_dir($subFold)) {

                        $subThumb = $subFold . 'thumb/';
                        $thumbFile = glob($subThumb . '*');
                        foreach ($thumbFile as $file) {
                            if (is_file($file))
                                unlink($file);
                        }
                        rmdir($subThumb);

                        $files = glob($subFold . '*');
                        foreach ($files as $f) {
                            if (is_file($f))
                                unlink($f);
                        }
                        rmdir($subFold);
                    }
                }
            }
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

    public function checkCollectionNameAvailable($vCollectionName, $iCollectionID = '') {
        if ($iCollectionID != "") {
            $result = $this->db->query("SELECT `iCollectionID`  FROM `tbl_collection` WHERE (`vCollectionName` = '" . $vCollectionName . "') AND  `iCollectionID` != $iCollectionID");
        } else {
            $this->db->where('vCollectionName', $vCollectionName);
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
    function addCollection($collectionData) {
        extract($collectionData);

        $data = array(
            'vCollectionName' => $vCollectionName,
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
    function editCollection($collectionData) {

        extract($collectionData);
        $data = array(
            'vCollectionName' => $vCollectionName
        );

        $query = $this->db->update($this->table, $data, array('iCollectionID' => $iCollectionID));
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    function editCollectionData($data, $iCollectionID = '') {
        if ($iCollectionID != '') {
            $query = $this->db->update($this->table, $data, array('iCollectionID' => $iCollectionID));
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
                . "vCollectionName, "
                . "(SELECT COUNT(trc.iRestaurantID) "
                . "FROM tbl_restaurant_collection trc "
                . "WHERE trc.iCollectionID IN(tc.iCollectionID)) as total_restaurants, "
                . "tCreatedAt as tCreatedAt1, "
                . "eStatus, "
                . "iCollectionID, "
                . "vCollectionImage,"
                . "DATE_FORMAT(tCreatedAt, '%d %b %Y %h:%i %p') as tCreatedAt, "
                . "iCollectionID AS DT_RowId "
                . "FROM tbl_collection AS tc";
        $data = $this->datatableshelper->query($qry);

        return $data;
    }

    function getCommentList() {
        /*        $iCollectionID = $this->input->post('iCollectionID');
          if($iCollectionID != ''){
          $str = "SELECT iCommentID , CONCAT(vFirstName, ' ', vLastName) as vName   , vCollectionName as vCollection ,  vCommentText , u.iUserID  , u.vProfilePicture , cm.iCollectionID , DATE_FORMAT(cm.tCreatedAt,'%d %b %Y %h:%i %p') as tCreatedAt  from tbl_comment cm , tbl_collection p , tbl_user u , tbl_collection as s where s.iCollectionID = p.iCollectionID and u.iUserID = cm.iUserID and p.iCollectionID = cm.iCollectionID and cm.iCollectionID = $iCollectionID ";
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

    function getRestaurantDataByCuiId($collection) {
        $query = "SELECT trc.iRestaurantID as restId,tr.vRestaurantName as restName FROM tbl_restaurant_collection as trc LEFT JOIN tbl_restaurant as tr on trc.iRestaurantID = tr.iRestaurantID WHERE trc.iCollectionID=$collection order by tr.vRestaurantName asc";
        $result = $this->db->query($query)->result_array();
        $data = array();
        foreach ($result AS $row) {
            $data[] = $row["restName"];
        }
        return $data;
    }

}

?>
