<?php

class Cuisine_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_cuisine';
        $this->load->library('DatatablesHelper');
    }

    /*
     * TO UPDATE THE CUISINE IMAGE
     */

    function updateCuisineImage($id, $imageName = '') {
        try {
            if (@$imageName !== '') {
                $this->db->update($this->table, array('vCuisineImage' => $imageName), array('iCuisineID' => $id));
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (Exception $ex) {
            throw new Exception('Error in updateCuisineImage function - ' . $ex);
        }
    }

    // **********************************************************************
    // Display List of Cuisine
    // **********************************************************************
    function getCuisineDataAll() {
        $this->db->from($this->table);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    /*     * **********************************************************************
      Cuisine Setting(Getting cuisine details by admin_id(Selected or inserted)
     * ********************************************************************** */

    function getCuisineDataById($iCuisineID) {
        $result = $this->db->get_where($this->table, array('iCuisineID' => $iCuisineID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    // **********************************************************************
    // Cuisine Status
    // **********************************************************************
    function changeCuisineStatus($iCuisineID) {
        //$updateData = array('admin_status' => 'IF (admin_status = "Active", "Inactive","Active")');
        //$query = $this->db->update($this->admin_tbl,$updateData, array('admin_role ' => $admin_role));
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iCuisineID = $iCuisineID");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function removeCuisine($iCuisineID) {
        $adid = implode(',', $iCuisineID);
        //mprd($adid);
        $query = $this->db->query("DELETE from $this->table WHERE iCuisineID in ($adid) ");
        //$query = $this->db->delete($this->table, array('iCuisineID' => $iCuisineID));
        //mprd($this->db->last_query());
        if ($this->db->affected_rows() > 0) {

            /*
             * REMOVE DIRECTORY
             */
            $ids = $iCuisineID;
            if (!empty($ids)) {
                $basePath = DOC_ROOT . '/images/cuisine/';
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

    public function checkCuisineNameAvailable($vCuisineName, $iCuisineID = '') {
        if ($iCuisineID != "") {
            $result = $this->db->query("SELECT `iCuisineID`  FROM `tbl_cuisine` WHERE (`vCuisineName` = '" . $vCuisineName . "') AND  `iCuisineID` != $iCuisineID");
        } else {
            $this->db->where('vCuisineName', $vCuisineName);
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
    function addCuisine($cuisineData) {
        extract($cuisineData);

        $data = array(
            'vCuisineName' => $vCuisineName,
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
    function editCuisine($cuisineData) {

        extract($cuisineData);
        $data = array(
            'vCuisineName' => $vCuisineName
        );

        $query = $this->db->update($this->table, $data, array('iCuisineID' => $iCuisineID));
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    function editCuisineData($data, $iCuisineID = '') {
        if ($iCuisineID != '') {
            $query = $this->db->update($this->table, $data, array('iCuisineID' => $iCuisineID));
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
                . "vCuisineName, "
                . "(SELECT COUNT(trc.iRestaurantID) "
                . "FROM tbl_restaurant_cuisine trc "
                . "WHERE trc.iCuisineID IN(tc.iCuisineID)) as total_restaurants, "
                . "tCreatedAt as tCreatedAt1, "
                . "eStatus, "
                . "iCuisineID, "
                . "vCuisineImage,"
                . "DATE_FORMAT(tCreatedAt, '%d %b %Y %h:%i %p') as tCreatedAt, "
                . "iCuisineID AS DT_RowId "
                . "FROM tbl_cuisine AS tc";
        $data = $this->datatableshelper->query($qry);

        return $data;
    }

    function getCommentList() {
        /*        $iCuisineID = $this->input->post('iCuisineID');
          if($iCuisineID != ''){
          $str = "SELECT iCommentID , CONCAT(vFirstName, ' ', vLastName) as vName   , vCuisineName as vCuisine ,  vCommentText , u.iUserID  , u.vProfilePicture , cm.iCuisineID , DATE_FORMAT(cm.tCreatedAt,'%d %b %Y %h:%i %p') as tCreatedAt  from tbl_comment cm , tbl_cuisine p , tbl_user u , tbl_cuisine as s where s.iCuisineID = p.iCuisineID and u.iUserID = cm.iUserID and p.iCuisineID = cm.iCuisineID and cm.iCuisineID = $iCuisineID ";
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

    function getRestaurantDataByCuiId($cuisine) {
        $query = "SELECT trc.iRestaurantID as restId,tr.vRestaurantName as restName FROM tbl_restaurant_cuisine as trc LEFT JOIN tbl_restaurant as tr on trc.iRestaurantID = tr.iRestaurantID WHERE trc.iCuisineID=$cuisine order by tr.vRestaurantName asc";
        $result = $this->db->query($query)->result_array();
        $data = array();
        foreach ($result AS $row) {
            $data[] = $row["restName"];
        }
        return $data;
    }

}

?>
