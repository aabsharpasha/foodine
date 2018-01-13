<?php

class Menu_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_restaurant_menu_image';
        $this->load->library('DatatablesHelper');
    }

    // **********************************************************************
    // Display List of Image
    // **********************************************************************
    function getImageDataAll() {
        $this->db->from($this->table);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    function getImageDataAllbyRes($iRestaurantID) {
        $this->db->from($this->table);
        $this->db->where('iRestaurantID', $iRestaurantID);

        $result = $this->db->get();
        if ($result->num_rows() > 0)
            return $result->result_array();
        else
            return '';
    }

    /**/
    /*     * **********************************************************************
      Image Setting(Getting user details by admin_id(Selected or inserted)
     * ********************************************************************** */

    function getImageDataById($iMenuPictureID) {
        $result = $this->db->get_where($this->table, array('iMenuPictureID' => $iMenuPictureID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    function getRestaurantDataById($iRestaurantID) {
        $result = $this->db->get_where('tbl_restaurant', array('iRestaurantID' => $iRestaurantID));
        if ($result->num_rows() > 0)
            return $result->row_array();
        else
            return '';
    }

    /*     * **********************************************************************
      Image Setting(Getting user details by admin_id(Selected or inserted)
     * ********************************************************************** */

    // **********************************************************************
    // Image Status
    // **********************************************************************
    function changeImageStatus($iMenuPictureID) {
        //$updateData = array('admin_status' => 'IF (admin_status = "Active", "Inactive","Active")');
        //$query = $this->db->update($this->admin_tbl,$updateData, array('admin_role ' => $admin_role));
        $query = $this->db->query("UPDATE $this->table SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE iMenuPictureID = $iMenuPictureID");
        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function removeImage($iMenuPictureID) {
        $adid = implode(',', $iMenuPictureID);

        $res_data = array();

        $result = $this->db->query("select iRestaurantID , iMenuPictureID , vPictureName from tbl_restaurant_menu_image where iMenuPictureID in ($adid)");
        if ($result->num_rows() > 0)
            $res_data = $result->result_array();
        else
            $res_data = array();

        //mprd($adid);
        $query = $this->db->query("DELETE from $this->table WHERE iMenuPictureID in ($adid) ");
        //$query = $this->db->delete($this->table, array('iMenuPictureID' => $iMenuPictureID));
        //mprd($this->db->last_query());
        if ($this->db->affected_rows() > 0) {
            $this->load->helper("file");
            if (!empty($res_data)) {
                foreach ($res_data as $value) {
                    $this->unlinkimage(UPLOADS . "restaurant/" . $value['iRestaurantID'] . "/menu/" . $value['vPictureName']);
                    $this->unlinkimage(UPLOADS . "restaurant/" . $value['iRestaurantID'] . "/menu/thumb/" . $value['vPictureName']);
                }
            }

            return $query;
        }
        else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function deleteFollowerAll($iMenuPictureID) {
        $adid = implode(',', $iMenuPictureID);
        //mprd($adid);
        //$uid = $this->input->post('uid');

        $query = $this->db->query("DELETE from tbl_restaurant_menu_image_follow WHERE iFollowID in ($adid)");
        if ($this->db->affected_rows() > 0) {
            return $query;
        }
        else
            return '';
    }

    // **********************************************************************
    // remove admin
    // **********************************************************************
    function deleteFollowingAll($iMenuPictureID) {
        $adid = implode(',', $iMenuPictureID);
        //mprd($adid);
        //$uid = $this->input->post('uid');


        $query = $this->db->query("DELETE from tbl_restaurant_menu_image_follow WHERE iFollowID in ($adid)");
        if ($this->db->affected_rows() > 0) {
            return $query;
        }
        else
            return '';
    }

    // **********************************************************************
    // add admin
    // **********************************************************************
    function addImage($postData) {
        extract($postData);
        $vPassword = md5($vPassword);

        $data = array(
            'vImageName' => $vImageName,
            'vEmail' => $vEmail,
            'eStatus' => 'Active',
            'vPassword' => $vPassword,
            'vCityName' => $vCityName,
            'vStateName' => $vStateName,
            'vCountryName' => $vCountryName,
            'tAddress' => $tAddress,
            'vContactNo' => $vContactNo,
            'tDescription' => $tDescription,
            'vFbLink' => $vFbLink,
            'vInstagramLink' => $vInstagramLink,
            'tCreatedAt' => date('Y-m-d H:i:s')
        );
        $query = $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0) {
            $iMenuPictureID = $this->db->insert_id();
            if (isset($iCategoryID)) {
                if (!empty($iCategoryID)) {
                    foreach ($iCategoryID as $key => $value) {
                        $data1 = array('iCategoryID' => $value, 'iMenuPictureID' => $iMenuPictureID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                        $query = $this->db->insert('tbl_restaurant_menu_image_category', $data1);
                    }
                }
            }
            return $iMenuPictureID;
        }
        else
            return '';
    }

    // **********************************************************************
    // Edit admin
    // **********************************************************************
    function editImage($postData) {

        extract($postData);
        $data = array(
            'vImageName' => $vImageName,
            'vEmail' => $vEmail,
            'vPassword' => $vPassword,
            'vCityName' => $vCityName,
            'vStateName' => $vStateName,
            'vCountryName' => $vCountryName,
            'tAddress' => $tAddress,
            'vContactNo' => $vContactNo,
            'tDescription' => $tDescription,
            'vFbLink' => $vFbLink,
            'vInstagramLink' => $vInstagramLink
        );
        if (isset($vPassword) && $vPassword != "") {
            $data['vPassword'] = md5($vPassword);
        }

        $query = $this->db->update($this->table, $data, array('iMenuPictureID' => $iMenuPictureID));


        if (isset($iCategoryID)) {
            if (!empty($iCategoryID)) {
                $query = $this->db->query("DELETE from tbl_restaurant_menu_image_category WHERE iMenuPictureID = $iMenuPictureID");
                foreach ($iCategoryID as $key => $value) {
                    $data1 = array('iCategoryID' => $value, 'iMenuPictureID' => $iMenuPictureID, 'tCreatedAt' => date('Y-m-d H:i:s'));
                    $query = $this->db->insert('tbl_restaurant_menu_image_category', $data1);
                }
            }
        }

        if ($this->db->affected_rows() > 0)
            return $query;
        else
            return '';
    }

    function editImageData($data, $iMenuPictureID = '') {
        if ($iMenuPictureID != '') {
            $query = $this->db->update($this->table, $data, array('iMenuPictureID' => $iMenuPictureID));
            if ($this->db->affected_rows() > 0)
                return $query;
            else
                return '';
        }else {
            return '';
        }
    }

    ############################################################
    #Check EMail#
############################################################

    public function checkImageEmailAvailable($vEmail, $iMenuPictureID = '') {
        if ($iMenuPictureID != "") {
            $result = $this->db->query("SELECT `iMenuPictureID`  FROM `tbl_restaurant_menu_image` WHERE (`vEmail` = '" . $vEmail . "') AND  `iMenuPictureID` != $iMenuPictureID");
        } else {
            $this->db->where('vEmail', $vEmail);
            //$this->db->or_where('vImagename', $vEmail);
            $result = $this->db->get($this->table);
        }

        if ($result->num_rows() >= 1)
            return 0;
        else
            return 1;
    }

    function get_paginationresult($iRestaurantID) {
        $data = $this->datatableshelper->query("SELECT vPictureName , eStatus ,  DATE_FORMAT(tCreatedAt , '%d %b %Y %h:%i %p') as tCreatedAt , iMenuPictureID, iRestaurantID , iMenuPictureID AS DT_RowId FROM tbl_restaurant_menu_image as r where iRestaurantID = $iRestaurantID");
        return $data;
    }

    function unlinkimage($path) {
        if (file_exists($path))
            unlink($path);
    }

}

?>
