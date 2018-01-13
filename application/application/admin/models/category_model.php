<?php

class Category_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_category';
        $this->load->library('DatatablesHelper');
        $this->load->library('aws_sdk');
    }

    function getCategoryOrderData() {
        try {
            $qry = 'SELECT iCategoryID, vCategoryName FROM tbl_category ORDER BY iOrder ASC';
            $res = $this->db->query($qry);
            if ($res->num_rows() > 0)
                return $res->result_array();
            else
                return '';
        } catch (Exception $ex) {
            throw new Exception('Error in getCategoryOrderData function - ' . $ex);
        }
    }

    function saveOrder($catOrder) {
        for ($i = 0; $i < count($catOrder); $i++) {
            $this->db->update($this->table, array('iOrder' => ($i + 1)), array('iCategoryID' => $catOrder[$i]));
        }
    }

    /*
     * TO UPDATE THE CUISINE IMAGE
     */

    function updateCategoryImage($id, $imageName = '') {
        try {
            if (@$imageName !== '') {
                $this->db->update($this->table, array('vCategoryImage' => $imageName), array('iCategoryID' => $id));
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (Exception $ex) {
            throw new Exception('Error in updateCategoryImage function - ' . $ex);
        }
    }

    // **********************************************************************
    // Display List of Category
    // **********************************************************************
    function getCategoryDataAll() {
        $this->db->from($this->table);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    /*     * **********************************************************************
      Category Setting(Getting category details by admin_id(Selected or inserted)
     * ********************************************************************** */

    function getCategoryDataById($iCategoryID) {
        $result = $this->db->get_where($this->table, array('iCategoryID' => $iCategoryID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    // **********************************************************************
    // Category Status
    // **********************************************************************
    function changeCategoryStatus($iCategoryID) {
        //$updateData = array('admin_status' => 'IF (admin_status = "Active", "Inactive","Active")');
        //$query = $this->db->update($this->admin_tbl,$updateData, array('admin_role ' => $admin_role));
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iCategoryID = $iCategoryID");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function removeCategory($iCategoryID) {
        $adid = implode(',', $iCategoryID);
        //mprd($adid);
        if ($adid !== '') {
            $query = $this->db->query("DELETE from $this->table WHERE iCategoryID in ($adid) ");
            //$query = $this->db->delete($this->table, array('iCategoryID' => $iCategoryID));
            //mprd($this->db->last_query());
            if ($this->db->affected_rows() > 0) {
                /*
                 * REMOVE DIRECTORY
                 */
                $ids = $iCategoryID;
                if (!empty($ids)) {
                    $basePath = DOC_ROOT . '/images/category/';
                    for ($i = 0; $i < count($ids); $i++) {
                        $subFold = $basePath . $ids[$i] . '/';
                        if (is_dir($subFold)) {
                            $subThumb = $subFold . 'thumb/';

                            if (is_dir($subThumb)) {
                                $thumbFile = glob($subThumb . '*');
                                foreach ($thumbFile as $file) {
                                    if (is_file($file)) {
                                        unlink($file);
                                    }
                                    
                                }
                                rmdir($subThumb);
                            }

                            $files = glob($subFold . '*');
                            foreach ($files as $f) {
                                if (is_file($f)) {
                                    unlink($f);
                                }
                                rmdir($subFold);
                            }
                        }
                    }
                }
                return $query;
            } else
                return '';
        }
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

    public function checkCategoryNameAvailable($vCategoryName, $iCategoryID = '') {
        if ($iCategoryID != "") {
            $result = $this->db->query("SELECT `iCategoryID`  FROM `tbl_category` WHERE (`vCategoryName` = '" . $vCategoryName . "') AND  `iCategoryID` != $iCategoryID");
        } else {
            $this->db->where('vCategoryName', $vCategoryName);
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
    function addCategory($categoryData) {
        extract($categoryData);

        $data = array(
            'vCategoryName' => $vCategoryName,
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
    function editCategory($categoryData) {

        extract($categoryData);
        $data = array(
            'vCategoryName' => $vCategoryName
        );

        $query = $this->db->update($this->table, $data, array('iCategoryID' => $iCategoryID));
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    function editCategoryData($data, $iCategoryID = '') {
        if ($iCategoryID != '') {
            $query = $this->db->update($this->table, $data, array('iCategoryID' => $iCategoryID));
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
                . "vCategoryName , "
                . "(SELECT COUNT(trc.iRestaurantID) "
                . "FROM tbl_restaurant_category trc "
                . "WHERE trc.iCategoryID IN(tc.iCategoryID)) as total_restaurants, "
                . "tCreatedAt as tCreatedAt1, "
                . "vCategoryImage, "
                . "eStatus, "
                . "iCategoryID, "
                . "DATE_FORMAT(tCreatedAt , '%d %b %Y %h:%i %p') as tCreatedAt, "
                . "iCategoryID AS DT_RowId "
                . "FROM tbl_category AS tc";

        $data = $this->datatableshelper->query($qry);
        return $data;
    }

    function getCommentList() {
        /*        $iCategoryID = $this->input->post('iCategoryID');
          if($iCategoryID != ''){
          $str = "SELECT iCommentID , CONCAT(vFirstName, ' ', vLastName) as vName   , vCategoryName as vCategory ,  vCommentText , u.iUserID  , u.vProfilePicture , cm.iCategoryID , DATE_FORMAT(cm.tCreatedAt,'%d %b %Y %h:%i %p') as tCreatedAt  from tbl_comment cm , tbl_category p , tbl_user u , tbl_category as s where s.iCategoryID = p.iCategoryID and u.iUserID = cm.iUserID and p.iCategoryID = cm.iCategoryID and cm.iCategoryID = $iCategoryID ";
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

    function getMappedRestaurants($category) {
        $query = "SELECT iRestaurantID FROM tbl_restaurant_category WHERE iCategoryID=$category";
        $result = $this->db->query($query)->result_array();
        $data = array();
        foreach ($result AS $row) {
            $data[] = $row["iRestaurantID"];
        }
        return $data;
    }

    function mapCategoryRestaurants($data) {
        $category = $data["iCategoryID"];
        $mappings = $this->getMappedRestaurants($category);
        $removedRestaurants = array_diff($mappings, $data["iRestaurantID"]);
        foreach ($removedRestaurants AS $iRestaurantID) {
            $query = "SELECT count(iRestaurantID) as recordCount FROM tbl_restaurant_category WHERE iRestaurantID=$iRestaurantID AND iCategoryID <> " . $category;
            $result = $this->db->query($query)->row_array();
            if (empty($result["recordCount"])) {
                $query = "SELECT vRestaurantName FROM tbl_restaurant WHERE iRestaurantID=$iRestaurantID";
                $result = $this->db->query($query)->row_array();
                return $result['vRestaurantName'] . " could not be removed from this category, As it is not mapped to any other catogory.";
            }
        }
        $this->db->query("DELETE FROM tbl_restaurant_category WHERE iCategoryID=" . $category);
        $date = date("Y-m-d H:i:s");
        $insert = array();
        foreach ($data["iRestaurantID"] AS $iRestaurantID) {
            $insert[] = "('$iRestaurantID', '$category', '$date', '$date')";
        }
        $insertValues = implode(", ", $insert);
        $this->db->query("INSERT INTO tbl_restaurant_category (iRestaurantID,iCategoryID,tCreatedAt,tModifiedAt) VALUES $insertValues");
        return true;
    }

    function getCategoryList() {
        $query = "SELECT iCategoryID,vCategoryName FROM tbl_category ORDER BY vCategoryName";
        $result = $this->db->query($query);
        return $result->result_array();
    }

    function getRestaurantList() {
        $query = "SELECT iRestaurantID,vRestaurantName FROM tbl_restaurant ORDER BY vRestaurantName";
        $result = $this->db->query($query);
        return $result->result_array();
    }

    function getRestaurantDataByCatId($category) {
        $query = "SELECT trc.iRestaurantID as restId,tr.vRestaurantName as restName FROM tbl_restaurant_category as trc LEFT JOIN tbl_restaurant as tr on trc.iRestaurantID = tr.iRestaurantID WHERE trc.iCategoryID=$category order by tr.vRestaurantName asc";
        $result = $this->db->query($query)->result_array();
        $data = array();
        foreach ($result AS $row) {
            $data[] = $row["restName"];
        }
        return $data;
    }

}

?>
